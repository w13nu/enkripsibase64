<?php
include 'includes/auth.php';
checkAccess('admin'); // hanya admin yang boleh akses halaman ini
require_once 'BlockchainSystem.php';
?>
<!doctype html>
<html lang="en">
<head>
    <title>Riwayat Blockchain</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/enkrip.css">
    <link rel="icon" type="image/jpg" href="assets/img/logo-dark.jpg">
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">

<!-- Navbar -->
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
                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="enkripsi.php">ENKRIPSI</a></li>
                <li><a href="dekripsi.php">DEKRIPSI</a></li>
                <li class="active"><a href="blockchain_history.php">RIWAYAT</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Konten Riwayat -->
<div class="container-fluid bg-cover" id="encryption-section" style="margin-top:0px;">
    <div class="page-header">
    <h3 style="color:white; font-weight:bold; margin-bottom:20px;position: relative; z-index: 100;">Riwayat Enkripsi File</h3>
    </div>

    <div class="table-responsive" style="padding: 20px; background-color: rgba(0, 0, 0, 0.5); border-radius: 10px; margin: 0 15px;position: relative; z-index: 50">
    <?php
    $blockchainLogger = new BlockchainLogger();
    $chain = $blockchainLogger->getChain();

    if (empty($chain)) {
        echo '<div class="alert alert-info text-center">
                <i class="glyphicon glyphicon-info-sign"></i> 
                Belum ada riwayat enkripsi file
              </div>';
    } else {
        echo '<table class="table table-custom table-hover" style="table-layout: fixed; width: 100%;">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 40px;">No</th>';
        echo '<th style="width: 140px;">Waktu</th>';
        echo '<th style="width: 180px;">Nama File</th>';
        echo '<th style="width: 100px;">Tipe File</th>';
        echo '<th style="width: 200px;">Metode Enkripsi</th>';
        echo '<th style="width: 130px;">Aksi</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $no = 1;
        foreach ($chain as $block) {
            echo '<tr>';
            echo '<td class="text-center">' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($block['timestamp']) . '</td>';
            echo '<td>' . htmlspecialchars($block['fileName']) . '</td>';
            echo '<td><span class="badge badge-info">' . htmlspecialchars($block['fileType']) . '</span></td>';
            echo '<td>' . htmlspecialchars($block['encryptionType'] ?? 'Multi-layer Encryption') . '</td>';
            echo '<td class="action-buttons">
                    <button class="btn btn-xs btn-primary btn-action" onclick="viewDetails(\'' . htmlspecialchars($block['fileHash']) . '\', \'' . htmlspecialchars($block['blockHash']) . '\')">
                        <i class="glyphicon glyphicon-eye-open"></i> Detail
                    </button>
                    <button class="btn btn-xs btn-danger btn-action" onclick="deleteRecord(\'' . htmlspecialchars($block['fileHash']) . '\')">
                        <i class="glyphicon glyphicon-trash"></i> Hapus
                    </button>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        // Validasi Blockchain
        if ($blockchainLogger->verifyChain()) {
            echo '<div class="alert alert-success text-center">
                    <i class="glyphicon glyphicon-ok-sign"></i> 
                    Status Blockchain: Valid
                  </div>';
        } else {
            echo '<div class="alert alert-danger text-center">
                    <i class="glyphicon glyphicon-warning-sign"></i> 
                    Status Blockchain: Tidak Valid
                  </div>';
        }
    }
    ?>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel">
  <div class="modal-dialog" role="document" style="margin-top: 80px;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0056b3; color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="detailModalLabel"><b>Detail Block</b></h4>
      </div>
      <div class="modal-body" id="detailModalBody" style="background-color: #f9f9f9; color: #333; padding: 20px;">
        <!-- Konten Modal diisi oleh JavaScript -->
      </div>
      <div class="modal-footer" style="background-color: #f5f5f5;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Script -->
<script>
function viewDetails(fileHash, blockHash) {
    let content = `
        <div class="panel panel-default detail-panel">
            <div class="panel-heading">Informasi File</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4"><strong>File Hash:</strong></div>
                    <div class="col-md-8 hash-display">${fileHash}</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong>Block Hash:</strong></div>
                    <div class="col-md-8 hash-display">${blockHash}</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong>Metode Enkripsi:</strong></div>
                    <div class="col-md-8">Multi-layer Encryption (Caesar + Vigenere + AES-CTR + XOR + AES-GCM)</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong>Waktu Enkripsi:</strong></div>
                    <div class="col-md-8">Diambil dari blockchain</div>
                </div>
            </div>
        </div>
    `;
    $('#detailModalBody').html(content);
    $('#detailModal').modal('show');
}

function deleteRecord(fileHash) {
    if (confirm('Apakah Anda yakin ingin menghapus riwayat ini?')) {
        $.ajax({
            url: 'delete_record.php',
            type: 'POST',
            data: { fileHash: fileHash },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload(); // Refresh halaman supaya update tabel
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menghapus data.');
            }
        });
    }
}

// Inisialisasi tooltip
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<!-- Footer -->
<footer class="container-fluid text-center">
    <p>&copy; 2025 Wisnu Ardiansyah. All rights reserved.</p>
</footer>

</body>
</html>