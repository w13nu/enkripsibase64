<?php
include 'includes/config.php';
session_start();
require_once 'vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Inisialisasi Google Client
$google_client = new Google_Client();
$google_client->setClientId(getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri(getenv('APP_URL') . '/google_callback.php');

try {
    // Mendapatkan token akses dari kode otorisasi
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['error'])) {
        $google_client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();
        
        $google_id = mysqli_real_escape_string($conn, $data['id']);
        $email = mysqli_real_escape_string($conn, $data['email']);
        $name = mysqli_real_escape_string($conn, $data['name']);
        $picture = isset($data['picture']) ? mysqli_real_escape_string($conn, $data['picture']) : '';
        
        $query = "SELECT * FROM users WHERE email = '$email' OR google_id = '$google_id'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (empty($user['google_id'])) {
                mysqli_query($conn, "UPDATE users SET google_id = '$google_id' WHERE id = {$user['id']}");
            }
            
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Email Google ini belum terdaftar. Harap hubungi admin untuk mendaftarkan akun.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Terjadi kesalahan saat login dengan Google. Silakan coba lagi.";
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    $_SESSION['login_error'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: login.php");
    exit();
}
