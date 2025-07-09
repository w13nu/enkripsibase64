<?php include 'includes/auth.php'; ?>

<!doctype html>
<html lang="en">
<head>
    <title>Enkripsi File</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/enkrip.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                    <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
                    <li><a href="#enkripsi">ENKRIPSI</a></li>
                    <li><a href="dekripsi.php">DEKRIPSI</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-cover d-flex align-items-center justify-content-center" style="min-height:100vh; padding-top:70px;">
    <div class="text-light" style="background: rgba(52, 58, 64, 0.85); padding: 30px; border-radius: 15px; max-width: 600px; width:100%;">

        <h3 class="text-center mb-4" style="color:white;"><b>ENKRIPSI FILE</b></h3>
        <p class="text-center">Masukkan file dan password untuk memulai proses enkripsi multi-layer.</p>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Masukkan File :</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-file"></i></span>
                    <input id="upload" type="file" class="form-control" name="file" accept="image/*,.pdf" required>
                </div>
            </div>
            <br>
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
            <br>
            <button class="btn btn-primary btn-block" type="submit" name="submit">
                Enkripsi <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </button>
        </form>
        <br>

        <?php
        require_once 'BlockchainSystem.php';

        if (isset($_POST['submit']) && $_FILES['file']['size'] > 0) {
            $master_password = $_POST['master_password'];
            if (strlen($master_password) < 8) {
                echo '<div class="alert alert-danger mt-3">Password minimal 8 karakter</div>';
                exit;
            }

            $caesar_shift = (strlen($master_password) % 25) + 1;
            $vigenere_key = $master_password;
            $password_hash = hash('sha256', $master_password, true);
            $aes_key = substr($password_hash, 0, 16);
            $xor_key = $master_password;

            $file = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_data = file_get_contents($file);

            function caesar_encrypt($data, $shift) {
                return array_map(fn($b) => ($b + $shift) % 256, unpack('C*', $data));
            }
            function vigenere_encrypt($data, $key) {
                $result = '';
                for ($i = 0; $i < strlen($data); $i++) {
                    $result .= chr((ord($data[$i]) + ord($key[$i % strlen($key)])) % 256);
                }
                return $result;
            }
            function aes_ctr_encrypt($data, $key) {
                $nonce = openssl_random_pseudo_bytes(8);
                $counter = str_repeat("\0", 8);
                $iv = $nonce . $counter;
                return $nonce . openssl_encrypt($data, 'aes-128-ctr', $key, OPENSSL_RAW_DATA, $iv);
            }
            function xor_encrypt($data, $key) {
                $result = '';
                for ($i = 0; $i < strlen($data); $i++) {
                    $result .= chr(ord($data[$i]) ^ ord($key[$i % strlen($key)]));
                }
                return $result;
            }
            function aes_gcm_encrypt($data, $key) {
                $iv = openssl_random_pseudo_bytes(12);
                $ciphertext = openssl_encrypt($data, 'aes-128-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
                return $iv . $tag . $ciphertext;
            }

            $caesar_encrypted = pack('C*', ...caesar_encrypt($file_data, $caesar_shift));
            $vigenere_encrypted = vigenere_encrypt($caesar_encrypted, $vigenere_key);
            $aes_ctr_encrypted = aes_ctr_encrypt($vigenere_encrypted, $aes_key);
            $xor_encrypted = xor_encrypt($aes_ctr_encrypted, $xor_key);
            $aes_gcm_encrypted = aes_gcm_encrypt($xor_encrypted, $aes_key);
            $base64_encrypted = base64_encode($aes_gcm_encrypted);

            $blockchainLogger = new BlockchainLogger();
            $blockData = $blockchainLogger->addBlock($file_data, $_FILES['file']['name'], $_FILES['file']['type']);

            echo '<div class="alert alert-success mt-4" style="border-radius: 5px; position: relative; z-index: 50;">File berhasil dienkripsi menggunakan 5 layer keamanan!</div>';
            echo '<form method="post" action="getTxt.php">';
            echo '<textarea name="txt" class="form-control" rows="8" id="encryptedText">' . htmlspecialchars($base64_encrypted) . '</textarea><br>';
            echo '<button type="button" class="btn btn-warning" onclick="copyToClipboard()">Salin Teks</button><br><br>';
            echo '<button type="submit" class="btn btn-success">Unduh .txt</button>';
            echo '</form>';
            echo '<div class="alert alert-info mt-4">';
            echo '<h4>Informasi Blockchain:</h4>';
            echo '<ul class="list-unstyled">';
            echo '<li><strong>Waktu:</strong> ' . $blockData['timestamp'] . '</li>';
            echo '<li><strong>Nama File:</strong> ' . $blockData['fileName'] . '</li>';
            echo '<li><strong>Tipe File:</strong> ' . $blockData['fileType'] . '</li>';
            echo '<li><strong>File Hash:</strong> ' . substr($blockData['fileHash'], 0, 32) . '...</li>';
            echo '<li><strong>Block Hash:</strong> ' . substr($blockData['blockHash'], 0, 32) . '...</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
</div>

</body>
</html>