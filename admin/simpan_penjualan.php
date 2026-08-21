<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama      = $_POST['nama']; $tanggal = $_POST['tanggal']; $shift = $_POST['shift'];
    $odo_awal  = (int)$_POST['odo_awal']; $odo_akhir = (int)$_POST['odo_akhir'];
    $ukur_awal = (float)$_POST['pengukuran_awal']; $ukur_akhir = (float)$_POST['pengukuran_akhir'];
    $penjualan_liter = $odo_akhir - $odo_awal;
    $penghasilan_rp  = $penjualan_liter * 12400;
    $stok_hari_ini   = ($ukur_awal - $ukur_akhir) * 21.23;
    $stmt = $pdo->prepare("INSERT INTO penjualan_harian (nama,tanggal,shift,odo_awal,odo_akhir,penjualan_liter,penghasilan_rp,ukur_awal,ukur_akhir,stok_hari_ini) VALUES (?,?,?,?,?,?,?,?,?,?)");
    if ($stmt->execute([$nama,$tanggal,$shift,$odo_awal,$odo_akhir,$penjualan_liter,$penghasilan_rp,$ukur_awal,$ukur_akhir,$stok_hari_ini])) {
        header("Location: laporan.php?tanggal=$tanggal"); exit;
    } else { echo "Error menyimpan data."; }
}
