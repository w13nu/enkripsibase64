<?php 
session_start(); 

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fungsi untuk memeriksa apakah pengguna memiliki akses ke halaman tertentu
function checkAccess($requiredRole = 'user') {
    // Admin memiliki akses ke semua halaman
    if ($_SESSION['user']['role'] == 'admin') {
        return true;
    }

    // Jika halaman memerlukan hak admin dan pengguna bukan admin, tolak akses
    if ($requiredRole == 'admin' && $_SESSION['user']['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }

    return true;
}
?>
