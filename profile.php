<?php
include 'includes/auth.php';
include 'includes/config.php';


// Inisialisasi variabel untuk pesan
$message = '';
$messageType = '';

// Jika ada request update profil
if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user']['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // Cek apakah username sudah digunakan oleh user lain
    $checkUsername = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' AND id != $user_id");
    if (mysqli_num_rows($checkUsername) > 0) {
        $message = 'Username sudah digunakan oleh pengguna lain';
        $messageType = 'danger';
    } else {
        // Update username
        $sql = "UPDATE users SET username='$username' WHERE id=$user_id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['user']['username'] = $username;
            $message = 'Username berhasil diperbarui';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui username: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }
}

// Jika ada request update password
if (isset($_POST['update_password'])) {
    $user_id = $_SESSION['user']['id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verifikasi password lama
    $result = mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id");
    $user_data = mysqli_fetch_assoc($result);
    
    if (!password_verify($old_password, $user_data['password'])) {
        $message = 'Password lama tidak sesuai';
        $messageType = 'danger';
    } else if ($new_password != $confirm_password) {
        $message = 'Konfirmasi password baru tidak cocok';
        $messageType = 'danger';
    } else {
        // Hash password baru dan update
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$hashed_password' WHERE id=$user_id";
        if (mysqli_query($conn, $sql)) {
            $message = 'Password berhasil diperbarui';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui password: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    }
}

// Jika ada upload foto profil
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $user_id = $_SESSION['user']['id'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_picture']['name'];
    $temp = $_FILES['profile_picture']['tmp_name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if (!in_array(strtolower($ext), $allowed)) {
        $message = 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF';
        $messageType = 'danger';
    } else {
        // Buat direktori profiles jika belum ada
        $upload_dir = 'assets/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Buat nama file unik dengan user ID
        $new_filename = $user_id . '_' . time() . '.' . $ext;
        $destination = $upload_dir . $new_filename;
        
        // Hapus foto lama jika ada
        $result = mysqli_query($conn, "SELECT profile_picture FROM users WHERE id=$user_id");
        $user_data = mysqli_fetch_assoc($result);
        if (!empty($user_data['profile_picture']) && file_exists($upload_dir . $user_data['profile_picture'])) {
            unlink($upload_dir . $user_data['profile_picture']);
        }
        
        if (move_uploaded_file($temp, $destination)) {
            // Update database dengan nama file baru
            $sql = "UPDATE users SET profile_picture='$new_filename' WHERE id=$user_id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['user']['profile_picture'] = $new_filename;
                $message = 'Foto profil berhasil diperbarui';
                $messageType = 'success';
            } else {
                $message = 'Gagal memperbarui foto profil: ' . mysqli_error($conn);
                $messageType = 'danger';
            }
        } else {
            $message = 'Gagal mengupload foto profil';
            $messageType = 'danger';
        }
    }
}
// Ambil data user untuk ditampilkan
$user_id = $_SESSION['user']['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($result);
?>

<!doctype html>
<html lang="en">
<head>
    <title>Profil Saya - Enkripsi File</title>
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
    <div style="background: rgba(52, 58, 64, 0.8); padding: 30px; border-radius: 15px; max-width: 800px; width:100%; z-index:50;">
        <h2 style="color:white; font-weight:bold; margin-bottom:20px; text-align:center;">Profil Saya</h2>
        <hr style="border-color:white;">

        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Foto Profil -->
            <div class="col-md-4 text-center mb-4">
                <div class="form-section">
                    <h4 class="mb-3">Foto Profil</h4>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="profile-picture-container">
                            <img src="<?php echo !empty($user_data['profile_picture']) ? 'assets/profiles/' . $user_data['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                                class="profile-picture" alt="Foto Profil" id="profilePreview">
                            <label for="profile_picture" class="profile-picture-upload">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" style="display:none;" 
                                accept=".jpg, .jpeg, .png, .gif" onchange="previewImage(this);">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Foto</button>
                    </form>

                    <div class="card shadow-sm border-0 mt-4" style="background-color: #1c1f24; border-radius: 10px;">
                    <div class="card-header text-white text-center fw-bold" style="background-color: #007bff; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        Informasi Akun
                    </div>
                    <div class="card-body px-4 text-white">
                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-1" style="font-size: 13px; color: #aaa;">Username</label>
                            <div style="font-size: 15px; border-bottom: 1px solid #444; padding-bottom: 6px;">
                                <?php echo htmlspecialchars($user_data['username']); ?>
                            </div>
                        </div>
                        <!-- Role -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-1" style="font-size: 13px; color: #aaa;">Role</label>
                            <div style="font-size: 15px; border-bottom: 1px solid #444; padding-bottom: 6px;">
                                <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
                            </div>
                        </div>
                        <!-- Tanggal Bergabung -->
                        <div>
                            <label class="form-label fw-semibold mb-1" style="font-size: 13px; color: #aaa;">Bergabung Sejak</label>
                            <div style="font-size: 15px; border-bottom: 1px solid #444; padding-bottom: 6px;">
                                <?php echo date('d M Y', strtotime($user_data['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Form Ubah Data -->
            <div class="col-md-8">
                <!-- Username -->
                <div class="form-section mb-3">
                    <h4 class="mb-3">Ubah Username</h4>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username Baru</label>
                            <input type="text" name="username" id="username" class="form-control" 
                                value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-success">Perbarui Username</button>
                    </form>
                </div>

                <!-- Password -->
                <div class="form-section mb-3">
                <h4 class="mb-3">Ubah Password</h4>
                <form method="POST">
                    <div class="form-group profile-password-container">
                        <label for="old_password">Password Lama</label>
                        <div class="input-with-icon">
                            <input type="password" name="old_password" id="old_password" class="form-control" required>
                            <button type="button" class="profile-password-toggle" onclick="togglePassword('old_password', 'toggleIcon1')">
                                <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group profile-password-container">
                        <label for="new_password">Password Baru</label>
                        <div class="input-with-icon">
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                            <button type="button" class="profile-password-toggle" onclick="togglePassword('new_password', 'toggleIcon2')">
                                <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group profile-password-container">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <div class="input-with-icon">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <button type="button" class="profile-password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon3')">
                                <i id="toggleIcon3" class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-warning">Perbarui Password</button>
                </form>
            </div>
        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary btn-lg">Kembali ke Dashboard</a>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(inputId, iconId) {
        const passwordField = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

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