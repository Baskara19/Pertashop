<?php
require __DIR__ . '/../auth_operator.php';
require __DIR__ . '/../db.php';
$id      = (int)($_GET['id'] ?? 0);
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM penjualan_harian WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: laporan_operator.php?tanggal=$tanggal"); exit;
