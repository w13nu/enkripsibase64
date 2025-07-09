<?php
include 'includes/auth.php';
checkAccess('admin');
require_once 'BlockchainSystem.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileHash = $_POST['fileHash'] ?? '';

    if (empty($fileHash)) {
        echo json_encode(['status' => 'error', 'message' => 'Hash file tidak ditemukan.']);
        exit;
    }

    $blockchainLogger = new BlockchainLogger();
    $success = $blockchainLogger->deleteBlockByFileHash($fileHash);

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Riwayat berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus riwayat. File tidak ditemukan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST allowed.']);
}
?>