<?php include 'includes/auth.php'; ?>

<!doctype html>
<html lang="en">
  <head>
    <title>Enkripsi Gambar</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Style -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/enkrip.css">
    <link rel="icon" type="image/jpg" href="assets/img/logo-dark.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- JS -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    
    <style>
  /* Tambahan styling untuk dropdown */
  .dropdown-menu {
    padding: 0;
    overflow: hidden;
    min-width: 300px;
    background-color: #002b5c; /* Warna biru gelap */
    color: white;
    border: none;
  }

  .dropdown-menu .card {
    margin: 0;
    border-radius: 0;
    background-color: #002b5c; /* Warna biru gelap */
    color: white;
    border: none;
  }

  .dropdown-toggle::after {
    display: inline-block;
    margin-left: 5px;
  }

  /* Header kartu */
  .card-header.bg-dark-blue {
    background-color: #002b5c; /* Warna biru gelap */
    color: white;
    border-bottom: 1px solid #003366;
  }

  /* Styling untuk list items */
  .list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #003366; /* Biru gelap lebih terang untuk kontras */
    color: white;
    border: none;
  }

  .list-group-item span.text-muted {
    color: #cce0ff; /* Warna teks yang lebih lembut agar kontras */
  }

  .fw-semibold {
    font-weight: 600;
  }

  /* Memastikan menu dropdown tidak tertutup saat diklik */
  .dropdown-menu.show {
    display: block;
  }

  /* Label role */
  .label-danger,
  .label-info {
    background-color: #00509e; /* Warna biru lebih terang untuk label */
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8em;
  }
</style>

  </head>
  <body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">
      <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span> 
          </button>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#home"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="enkripsi.php">ENKRIPSI</a></li>
            <li><a href="dekripsi.php">DEKRIPSI</a></li>
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <li><a href="blockchain_history.php">RIWAYAT</a></li>
            <?php endif; ?>
          </ul>
          
          <!-- Profil user di kanan -->
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span style="margin-right: 5px;"><?= htmlspecialchars($_SESSION['user']['username']); ?></span>
                <img src="<?= !empty($_SESSION['user']['profile_picture']) ? 'assets/profiles/' . $_SESSION['user']['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                  alt="Profile" class="img-circle" style="width: 30px; height: 30px;">
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li style="width: 100%;">
                  <div class="card mt-0 shadow-sm border-0">
                    <div class="card-header bg-dark-blue text-white text-start">
                      <h5 class="mb-0">Informasi Akun</h5>
                    </div>
                    <div class="text-center p-3">
                      <img src="<?= !empty($_SESSION['user']['profile_picture']) ? 'assets/profiles/' . $_SESSION['user']['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                        alt="Profile" class="img-circle" style="width: 80px; height: 80px;">
                      <h4 class="mt-2"><?= htmlspecialchars($_SESSION['user']['username']); ?></h4>
                      <span class="label label-<?= $_SESSION['user']['role'] == 'admin' ? 'danger' : 'info' ?>">
                        <?= ucfirst(htmlspecialchars($_SESSION['user']['role'])); ?>
                      </span>
                    </div>
                    <ul class="list-group list-group-flush text-start">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Username</span>
                        <span class="text-muted"><?= htmlspecialchars($_SESSION['user']['username']); ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Role</span>
                        <span class="text-muted"><?= ucfirst(htmlspecialchars($_SESSION['user']['role'])); ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Bergabung Sejak</span>
                        <span class="text-muted"><?= isset($_SESSION['user']['created_at']) ? date('d M Y', strtotime($_SESSION['user']['created_at'])) : 'N/A'; ?></span>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid bg-cover" id="home" style="margin-top:0px;">
      <div class="page-header">
        <h1><b>ENKRIPSI FILE</b></h1> 
      </div>
      
      <p>Menggunakan Algoritma Caesar,Vigenere,AES CTR,XOR dan AES GCM</p> 
      <a class="btn btn-primary lg btn-lg" href="enkripsi.php">Mulai Enkripsi</a>
      <a class="btn btn-success btn-lg" href="dashboard.php">Kembali ke Dashboard</a>
    </div>

    <!-- JavaScript -->
    <script>
      $(document).ready(function(){
        // Add smooth scrolling to all links in navbar + footer link
        $(".navbar a, footer a[href='#myPage']").on('click', function(event) {
          // Make sure this.hash has a value before overriding default behavior
          if (this.hash !== "") {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Store hash
            var hash = this.hash;

            // Using jQuery's animate() method to add smooth page scroll
            $('html, body').animate({
              scrollTop: $(hash).offset().top
            }, 900, function(){
              // Add hash (#) to URL when done scrolling (default click behavior)
              window.location.hash = hash;
            });
          } 
        });
        
        // Memastikan dropdown menu tidak tertutup saat kontennya diklik
        $('.dropdown-menu').on('click', function(e) {
          e.stopPropagation();
        });
        
        // Fix untuk Bootstrap 3: Memastikan dropdown berfungsi dengan benar
        $('.dropdown-toggle').dropdown();
      });
    </script>
  </body>
</html>