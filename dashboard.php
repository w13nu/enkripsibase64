<?php
include 'includes/auth.php';
?>

<!doctype html>
<html lang="en">
<head>
  <title>Dashboard - Enkripsi Gambar</title>
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
    
    <h2 style="color:white; font-weight:bold; margin-bottom:20px; position: relative; z-index: 100;">Selamat Datang</h2>
    <hr style="border-color:white;">

    <div class="user-card shadow-card">
      <img src="<?= !empty($_SESSION['user']['profile_picture']) ? 'assets/profiles/' . $_SESSION['user']['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
          class="profile-picture" style="width: 100px; height: 100px;" alt="Foto Profil">

      <h5 class="username"><?= htmlspecialchars($_SESSION['user']['username']); ?></h5>

      <span class="badge badge-<?= $_SESSION['user']['role'] === 'admin' ? 'danger' : 'info' ?>">
        <?= ucfirst(htmlspecialchars($_SESSION['user']['role'])); ?>
      </span>
    </div>

    <div class="d-grid gap-2 mt-4">
      <a href="index.php" class="btn btn-info btn-block menu-button">
        <i class="bi bi-lock-fill"></i> Halaman Enkripsi
      </a>
      <a href="profile.php" class="btn btn-primary btn-block menu-button">
        <i class="bi bi-person-circle"></i> Kelola Profil
      </a>
      <?php if ($_SESSION['user']['role'] == 'admin'): ?>
        <a href="kelola_user.php" class="btn btn-success btn-block menu-button">
          <i class="bi bi-people-fill"></i> Kelola User
        </a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-danger btn-block menu-button">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
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