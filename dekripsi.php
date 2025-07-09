<?php include 'includes/auth.php'; ?>

<!doctype html>
<html lang="en">
<head>
    <title>Dekripsi File</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/enkrip.css">
    <link rel="icon" type="image/jpg" href="assets/img/logo-dark.jpg">
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("encryptedText");
            copyText.select();
            document.execCommand("copy");
            alert("Teks telah disalin ke clipboard!");
        }
        
        function togglePassword() {
            const passwordField = document.getElementById("passwordField");
            const toggleIcon = document.getElementById("toggleIcon");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("glyphicon-eye-open");
                toggleIcon.classList.add("glyphicon-eye-close");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("glyphicon-eye-close");
                toggleIcon.classList.add("glyphicon-eye-open");
            }
        }
    </script>
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
                    <li><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
                    <li><a href="enkripsi.php">ENKRIPSI</a></li>
                    <li class="active"><a href="#dekripsi">DEKRIPSI</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-cover d-flex align-items-center justify-content-center" style="min-height:100vh; padding-top:70px;">
        <div class="text-light" style="background: rgba(52, 58, 64, 0.85); padding: 30px; border-radius: 15px; max-width: 600px; width:100%;">
            <h3 class="text-center mb-4" style="color:white;"><b>DEKRIPSI FILE</b></h3>
            <p class="text-center">Masukkan teks salinan hasil enkripsi dan password untuk mendekripsi file yang telah dienkripsi sebelumnya.</p>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="encrypted_text">Teks Enkripsi:</label>
                    <textarea name="encrypted_text" id="encrypted_text" class="form-control" rows="8" required placeholder="Masukkan teks hasil enkripsi"></textarea>
                </div>

                <div class="form-group">
                    <label>Password Enkripsi:</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" id="passwordField" name="master_password" placeholder="Minimal 8 karakter" required minlength="8">
                        <span class="input-group-addon toggle-password" onclick="togglePassword()">
                            <i id="toggleIcon" class="glyphicon glyphicon-eye-open"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" name="submit" class="btn btn-primary btn-block">
                        Dekripsi <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                    </button>
                </div>
            </form>

            <?php
            require_once 'BlockchainSystem.php';

            if (isset($_POST['submit'])) {
                try {
                    // Ambil master password
                    $master_password = $_POST['master_password'];
                    
                    if (strlen($master_password) < 8) {
                        throw new Exception('Password minimal 8 karakter');
                    }
                    
                    // Generate semua key dari master password (sama seperti di enkripsi.php)
                    $caesar_shift = (strlen($master_password) % 25) + 1; // 1-25
                    $vigenere_key = $master_password;
                    $password_hash = hash('sha256', $master_password, true);
                    $aes_key = substr($password_hash, 0, 16); // 16 byte untuk AES-128
                    $xor_key = $master_password;
                    
                    $encrypted_text = $_POST['encrypted_text'];

                    // Decode base64
                    $encrypted_data = base64_decode($encrypted_text);
                    if ($encrypted_data === false) {
                        throw new Exception('Format teks enkripsi tidak valid.');
                    }

                    $blockchainLogger = new BlockchainLogger();
                    $blockchain = $blockchainLogger->getChain();

                    if (empty($blockchain)) {
                        throw new Exception('Blockchain kosong. Tidak ada data untuk dekripsi.');
                    }

                    // Ambil blok terakhir dari blockchain sebagai referensi file asli
                    $lastBlock = end($blockchain);
                    $original_filename = $lastBlock['fileName'] ?? 'file_decrypted';

                    // Fungsi dekripsi tetap sama
                    function aes_gcm_decrypt($data, $key) {
                        $iv = substr($data, 0, 12);
                        $tag = substr($data, 12, 16);
                        $ciphertext = substr($data, 28);
                        return openssl_decrypt($ciphertext, 'aes-128-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
                    }

                    function xor_decrypt($data, $key) {
                        $key_length = strlen($key);
                        return implode('', array_map(
                            fn($i) => chr(ord($data[$i]) ^ ord($key[$i % $key_length])),
                            range(0, strlen($data) - 1)
                        ));
                    }

                    function aes_ctr_decrypt($data, $key) {
                        $nonce = substr($data, 0, 8);
                        $counter = str_repeat("\0", 8);
                        $ciphertext = substr($data, 8);
                        $iv = $nonce . $counter;
                        return openssl_decrypt($ciphertext, 'aes-128-ctr', $key, OPENSSL_RAW_DATA, $iv);
                    }

                    function vigenere_decrypt($data, $key) {
                        $key_length = strlen($key);
                        return implode('', array_map(
                            fn($i) => chr((ord($data[$i]) - ord($key[$i % $key_length]) + 256) % 256),
                            range(0, strlen($data) - 1)
                        ));
                    }

                    function caesar_decrypt($data, $shift) {
                        return implode('', array_map(
                            fn($byte) => chr(($byte - $shift + 256) % 256),
                            unpack('C*', $data)
                        ));
                    }

                    // Proses dekripsi berurutan
                    $xor_encrypted = aes_gcm_decrypt($encrypted_data, $aes_key);
                    if ($xor_encrypted === false) {
                        throw new Exception('Dekripsi AES-GCM gagal. Pastikan password benar.');
                    }

                    $aes_ctr_encrypted = xor_decrypt($xor_encrypted, $xor_key);
                    $vigenere_encrypted = aes_ctr_decrypt($aes_ctr_encrypted, $aes_key);
                    if ($vigenere_encrypted === false) {
                        throw new Exception('Dekripsi AES-CTR gagal. Pastikan password benar.');
                    }

                    $caesar_encrypted = vigenere_decrypt($vigenere_encrypted, $vigenere_key);
                    $decrypted_data = caesar_decrypt($caesar_encrypted, $caesar_shift);

                    // Identifikasi jenis file
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_buffer($finfo, $decrypted_data);
                    finfo_close($finfo);

                    // Tampilkan hasil file di halaman
                    echo "<div class='result-output'>";
                    echo "<div class='alert alert-success'>Dekripsi berhasil!</div>";

                    if ($mime_type === 'application/pdf') {
                        echo "<a href='data:application/pdf;base64," . base64_encode($decrypted_data) . "' download='$original_filename' class='btn btn-success'>Unduh PDF</a>";
                    } else if (substr($mime_type, 0, 6) === 'image/') {
                        echo "<img src='data:$mime_type;base64," . base64_encode($decrypted_data) . "' alt='Hasil Dekripsi' style='max-width:100%;'>";
                        echo "<br><br><a href='data:$mime_type;base64," . base64_encode($decrypted_data) . "' download='$original_filename' class='btn btn-success'>Unduh Gambar</a>";
                    } else {
                        throw new Exception('Format file tidak didukung. Hanya mendukung PDF dan gambar.');
                    }

                    echo "</div>";
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
            }
            ?>
        </div>
    </div>

    <footer class="container-fluid text-center">
        <p>&copy; 2025 Wisnu Ardiansyah. All rights reserved.</p>
    </footer>
</body>
</html>