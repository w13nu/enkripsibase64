<?php
// File: includes/header.php
// Pastikan sudah ada session yang aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['user']);
?>

<div class="header-container">
    <div class="container-fluid py-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="dashboard.php" class="text-white text-decoration-none">
                    <h4 class="m-0"><i class="bi bi-shield-lock-fill"></i> Enkripsi File</h4>
                </a>
            </div>
            
            <?php if ($isLoggedIn): ?>
            <div class="dropdown">
                <button class="btn dropdown-toggle profile-dropdown" type="button" id="profileDropdown" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <span class="d-none d-md-inline-block"><?= htmlspecialchars($_SESSION['user']['username']); ?></span>
                            <small class="d-block d-md-none"><?= htmlspecialchars($_SESSION['user']['username']); ?></small>
                        </div>
                        <img src="<?= !empty($_SESSION['user']['profile_picture']) ? 'assets/profiles/' . $_SESSION['user']['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                             alt="Profile" class="profile-pic">
                    </div>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                    <div class="px-3 py-2 text-center border-bottom">
                        <img src="<?= !empty($_SESSION['user']['profile_picture']) ? 'assets/profiles/' . $_SESSION['user']['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                             alt="Profile" class="profile-pic-lg mb-2">
                        <h6 class="mb-0"><?= htmlspecialchars($_SESSION['user']['username']); ?></h6>
                        <span class="badge badge-<?= $_SESSION['user']['role'] == 'admin' ? 'danger' : 'info' ?> my-1">
                            <?= ucfirst(htmlspecialchars($_SESSION['user']['role'])); ?>
                        </span>
                        <?php if (!empty($_SESSION['user']['bio'])): ?>
                        <p class="small text-muted mb-0"><?= htmlspecialchars($_SESSION['user']['bio']); ?></p>
                        <?php endif; ?>
                    </div>
                    <a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 mr-2"></i> Dashboard</a>
                    <a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle mr-2"></i> Profil Saya</a>
                    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                    <a class="dropdown-item" href="kelola_user.php"><i class="bi bi-people-fill mr-2"></i> Kelola User</a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right mr-2"></i> Logout</a>
                </div>
            </div>
            <?php else: ?>
            <div>
                <a href="login.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>