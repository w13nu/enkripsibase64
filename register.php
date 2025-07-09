<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("Akses ditolak");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>User berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan user.</div>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <title>Tambah User - Enkripsi Gambar</title>
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

    <h2 style="color:white; font-weight:bold; margin-bottom:20px;">Tambah User (Admin)</h2>
    <hr style="border-color:white;">

    <form method="POST" style="color:white; font-size:18px;">
        <div class="form-group">
            <input type="text" name="username" class="form-control mb-2" required placeholder="Username" style="position: relative; z-index: 10;">
        </div>
        <div class="form-group password-container"> 
          <input type="password" name="password" id="passwordField" class="form-control" placeholder="Password" required style="background: rgba(255,255,255,0.8); color: black; position: relative; z-index: 10;" autocomplete="current-password"> 
          <button type="button" class="password-toggle" onclick="togglePassword()">
            <i id="toggleIcon" class="bi bi-eye-fill"></i>
            </button>
        </div> 

        <div class="form-group">
            <select name="role" class="form-control mb-2" style="position: relative; z-index: 10;">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success btn-block mb-2">Register</button>
    </form>

    <a href="dashboard.php" class="btn btn-info btn-block">Kembali ke Dashboard</a>
    
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

  $(document).ready(function(){
    $("input, select").on('click', function(e) {
      e.stopPropagation();
    });
    
    // Pastikan dropdown dapat diklik dan difungsikan
    $("select[name='role']").on('focus', function() {
      $(this).css('z-index', '1000');
    });
    
    $(".navbar a, footer a[href='#myPage']").on('click', function(event) {
      if (this.hash !== "") {
        event.preventDefault();
        var hash = this.hash;
        $('html, body').animate({
          scrollTop: $(hash).offset().top
        }, 900, function(){
          window.location.hash = hash;
        });
      }
    });
  });
</script>

</body>
</html>