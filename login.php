<?php 
include 'includes/config.php'; 
session_start();

// Load composer autoload
require_once 'vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Inisialisasi Google Client
$google_client = new Google_Client();
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$google_client->setRedirectUri($_ENV['APP_URL'] . '/google_callback.php');
$google_client->addScope('email');
$google_client->addScope('profile');

// Generate login URL untuk tombol login Google
$google_login_url = $google_client->createAuthUrl();

// Proses login normal
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = mysqli_real_escape_string($conn, $_POST['username']); 
    $password = $_POST['password']; 
    
    $query = "SELECT * FROM users WHERE username='$username'"; 
    $result = mysqli_query($conn, $query); 
    $user = mysqli_fetch_assoc($result); 
    
    if ($user && password_verify($password, $user['password'])) { 
        $_SESSION['user'] = $user; 
        header("Location: dashboard.php"); 
        exit(); 
    } else { 
        $error = "Username atau password salah!"; 
    } 
} 
?> 
<!doctype html> 
<html lang="en"> 
<head> 
  <title>Login - Enkripsi Gambar</title> 
  <meta charset="utf-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
  <link rel="stylesheet" href="assets/css/bootstrap.min.css"> 
  <link rel="stylesheet" href="assets/css/enkrip.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" type="image/jpg" href="assets/img/logo-dark.jpg">
  <script src="assets/js/jquery.min.js"></script> 
  <script src="assets/js/bootstrap.min.js"></script>
</head> 
<body> 
  <div class="container-fluid bg-cover d-flex align-items-center justify-content-center" style="min-height:100vh; padding-top:70px;"> 
    <div class="text-center" style="background: rgba(52, 58, 64, 0.8); padding: 30px; border-radius: 15px; max-width: 500px; width:100%;"> 
      <h2 style="color:white; font-weight:bold; margin-bottom:20px;">Login</h2> 
      <hr style="border-color:white;"> 
      
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?> 
      
      <!-- Google Login Button -->
      <a href="<?php echo $google_login_url; ?>" class="google-btn">
        <img src="assets/img/google-icon.svg" alt="Google" class="google-icon">
        Masuk dengan Google
      </a>
      
      <div class="login-divider">ATAU</div>
      
      <!-- Form Login Normal -->
      <form method="POST"> 
        <div class="form-group"> 
          <input type="text" name="username" class="form-control" placeholder="Username" required autofocus style="background: rgba(255,255,255,0.8); color: black;" autocomplete="username"> 
        </div> 
        <div class="form-group password-container"> 
          <input type="password" name="password" id="passwordField" class="form-control" placeholder="Password" required style="background: rgba(255,255,255,0.8); color: black; position: relative; z-index: 100;" autocomplete="current-password"> 
          <button type="button" class="password-toggle" onclick="togglePassword()">
            <i id="toggleIcon" class="bi bi-eye-fill"></i>
          </button>
        </div> 
        <button type="submit" class="btn btn-primary btn-block">Login</button> 
      </form> 
    </div> 
  </div> 
  
  <script> 
    function togglePassword() {
      const passwordField = document.getElementById("passwordField");
      const toggleIcon = document.getElementById("toggleIcon");
      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("bi-eye-fill");
        toggleIcon.classList.add("bi-eye-slash-fill");
      } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("bi-eye-slash-fill");
        toggleIcon.classList.add("bi-eye-fill");
      }
    }
  </script> 
</body> 
</html>
