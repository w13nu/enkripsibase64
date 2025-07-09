<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("Akses ditolak");
}

// Tambah user
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $email = !empty($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : 'Tidak ada';
    
    // Perbaiki query INSERT - hapus kolom bio yang tidak ada nilainya
    $sql = "INSERT INTO users (username, password, role, email) VALUES ('$username', '$password', '$role', '$email')";
    mysqli_query($conn, $sql);
}

// Edit user
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $role = $_POST['role'];
    $email = !empty($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : 'Tidak ada';

    $sql = "UPDATE users SET username='$username', role='$role', email='$email'";
    if ($password) $sql .= ", password='$password'";
    $sql .= " WHERE id=$id";
    mysqli_query($conn, $sql);
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus foto profil terlebih dahulu
    $result = mysqli_query($conn, "SELECT profile_picture FROM users WHERE id=$id");
    $user_data = mysqli_fetch_assoc($result);
    if (!empty($user_data['profile_picture'])) {
        $file_path = 'assets/profiles/' . $user_data['profile_picture'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
}

// Detail user
$edit_user = null;
if (isset($_GET['detail'])) {
    $id = $_GET['detail'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit_user = mysqli_fetch_assoc($result);
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at ASC");
?>

<!doctype html>
<html lang="en">
<head>
    <title>Kelola User - Enkripsi File</title>
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
    <div class="text-center" style="background: rgba(52, 58, 64, 0.8); padding: 30px; border-radius: 15px; max-width: 800px; width:100%;">
        <h2 style="color:white; font-weight:bold; margin-bottom:20px;position: relative; z-index: 100;">Kelola User</h2>
        <hr style="border-color:white;">

        <!-- Form Tambah User -->
        <form method="POST" class="mb-4 p-3 rounded" style="background: rgba(255,255,255,0.9);">
            <input type="hidden" name="id" id="userId">
            <div class="form-group">
                <input type="text" name="username" id="username" class="form-control"
                    placeholder="Masukkan username" required
                    style="background: rgba(255,255,255,0.8); color: black;">
            </div>
            
            <!-- Tambah field email -->
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control"
                    placeholder="Masukkan email (opsional untuk login Google)" 
                    style="background: rgba(255,255,255,0.8); color: black;position: relative; z-index: 100;">
            </div>
            
            <div class="form-group password-container">
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Password (kosongkan jika tidak diubah)" style="background: rgba(255,255,255,0.8); color: black;position: relative; z-index: 100;"autocomplete="off">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i id="toggleIcon" class="bi bi-eye-fill"></i>
                </button>
            </div> 

            <div class="form-group">
                <select name="role" id="role" class="form-control"
                        style="background: rgba(255,255,255,0.8); color: black; position: relative; z-index: 100;">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="tambah" id="submitButton"
                    class="btn btn-success btn-block">Tambah User</button>
        </form>

        <!-- Tabel User -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="background: rgba(255,255,255,0.9); border-radius:10px; position: relative; z-index: 50;">
                <thead style="background-color: rgba(0,0,0,0.1); color: black;">
                    <tr class="text-center">
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Foto</th>
                        <th style="text-align: center;">Username</th>
                        <th style="text-align: center;">Email</th>
                        <th style="text-align: center;">Role</th>
                        <th style="text-align: center;">Dibuat</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="color: black;">
                    <?php 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($users)): ?>
                        <tr class="text-center">
                            <td class="align-middle"><?= $no++ ?></td>
                            <td class="align-middle">
                                <img src="<?= !empty($row['profile_picture']) ? 'assets/profiles/' . $row['profile_picture'] : 'assets/img/default-avatar.png'; ?>" 
                                     class="profile-picture"style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" alt="Foto Profil">
                            </td>
                            <td class="align-middle"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="align-middle"><?= htmlspecialchars($row['email'] ?? 'Tidak ada') ?></td>
                            <td class="align-middle">
                                <span class="badge badge-<?= $row['role'] == 'admin' ? 'danger' : 'info' ?>">
                                    <?= ucfirst(htmlspecialchars($row['role'])) ?>
                                </span>
                            </td>
                            <td class="align-middle"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                            <td class="align-middle">
                                <button class="btn btn-info btn-sm" style="font-size: 0.85rem; padding: 5px 10px;"
                                        data-toggle="modal" data-target="#detailModal" 
                                        onclick="showDetail(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <button class="btn btn-warning btn-sm" style="font-size: 0.85rem; padding: 5px 10px;"
                                        onclick="editUser(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                style="font-size: 0.85rem; padding: 5px 10px;"
                                onclick="return confirm('Hapus user ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-primary lg btn-lg">Kembali ke Dashboard</a>
    </div>
</div>

<!-- Modal Detail User -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="detailProfilePic" src="assets/img/default-avatar.png" 
                     class="profile-detail mb-3" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;" 
                     alt="Foto Profil">
                <h4 id="detailUsername">Username</h4>
                <span id="detailRole" class="badge badge-info mb-3">Role</span>
                
                <div class="card mb-3">
                    <div class="card-header"><strong>Info User</strong></div>
                    <div class="card-body">
                        <p class="card-text"><strong>Email:</strong> <span id="detailEmail">Email user</span></p>
                        <p class="card-text"><strong>Login Google:</strong> <span id="detailGoogleStatus">Tidak Terhubung</span></p>
                        <p class="card-text"><small class="text-muted">Bergabung: <span id="detailCreated"></span></small></p>
                        <p class="card-text"><small class="text-muted">Terakhir diperbarui: <span id="detailUpdated"></span></small></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById("password");
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
  
    function editUser(user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email || '';
        document.getElementById('role').value = user.role;
        document.getElementById('password').value = ""; // Kosongkan password
        const button = document.getElementById('submitButton');
        button.name = 'edit';
        button.textContent = 'Update User';
        button.classList.remove('btn-success');
        button.classList.add('btn-warning');
    }
    
    function showDetail(user) {
        console.log('User data:', user); // Debug log
        
        document.getElementById('detailUsername').textContent = user.username;
        document.getElementById('detailEmail').textContent = user.email || 'Tidak ada';
        document.getElementById('detailGoogleStatus').textContent = user.google_id ? 'Terhubung' : 'Tidak Terhubung';
        document.getElementById('detailGoogleStatus').className = user.google_id ? 'text-success' : 'text-muted';
        document.getElementById('detailRole').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
        document.getElementById('detailRole').className = user.role === 'admin' ? 'badge badge-danger mb-3' : 'badge badge-info mb-3';
        
        // Format tanggal created_at
        if (user.created_at) {
            document.getElementById('detailCreated').textContent = new Date(user.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Format tanggal updated_at
        if (user.updated_at) {
            document.getElementById('detailUpdated').textContent = new Date(user.updated_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } else {
            document.getElementById('detailUpdated').textContent = 'Belum pernah diperbarui';
        }
        
        // Set foto profil
        var profilePicSrc = 'assets/img/default-avatar.png'; // default
        if (user.profile_picture && user.profile_picture.trim() !== '') {
            profilePicSrc = 'assets/profiles/' + user.profile_picture;
        }
        
        console.log('Profile picture src:', profilePicSrc); // Debug log
        document.getElementById('detailProfilePic').src = profilePicSrc;
        
        // Handle error jika gambar tidak ditemukan
        document.getElementById('detailProfilePic').onerror = function() {
            console.log('Error loading image, using default'); // Debug log
            this.src = 'assets/img/default-avatar.png';
        };
    }
    
    // Update file input label with file name
    const profilePictureInput = document.getElementById('profilePicture');
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var label = document.querySelector('.custom-file-label');
            if (label) {
                label.textContent = fileName;
            }
        });
    }
</script>

</body>
</html>