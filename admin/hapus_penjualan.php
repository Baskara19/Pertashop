<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
$id      = (int)($_GET['id'] ?? 0);
$tanggal = $_GET['tanggal'] ?? '';
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM penjualan_harian WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: laporan_admin.php?tanggal=" . urlencode($tanggal)); exit;
