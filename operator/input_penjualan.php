<?php
require __DIR__ . '/../auth_operator.php';
require __DIR__ . '/../db.php';
if ($_SESSION['role'] !== 'operator') { die("❌ Akses ditolak."); }
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$error="";
$prefill_nama    = $_POST['nama']             ?? ($_SESSION['nama'] ?? '');
$prefill_tanggal = $_POST['tanggal']          ?? date('Y-m-d');
$prefill_shift   = $_POST['shift']            ?? '';
$prefill_odo_awal    = $_POST['odo_awal']     ?? '';
$prefill_odo_akhir   = $_POST['odo_akhir']    ?? '';
$prefill_ukur_awal   = $_POST['pengukuran_awal']  ?? '';
$prefill_ukur_akhir  = $_POST['pengukuran_akhir'] ?? '';
$prefill_harga       = $_POST['harga_pertamax']   ?? '';

if ($_SERVER["REQUEST_METHOD"]==="POST") {
    $nama=(trim($_POST['nama']??'')); $tanggal=$_POST['tanggal']??''; $shift=$_POST['shift']??'';
    $odo_awal=(int)($_POST['odo_awal']??0); $odo_akhir=(int)($_POST['odo_akhir']??0);
    $pengukuran_awal=(float)($_POST['pengukuran_awal']??0); $pengukuran_akhir=(float)($_POST['pengukuran_akhir']??0);
    $harga_pertamax=(int)($_POST['harga_pertamax']??0);
    if ($nama===''||$tanggal===''||$shift==='') { $error="Nama, tanggal, dan shift wajib diisi."; }
    elseif ($odo_akhir<$odo_awal) { $error="Odo akhir tidak boleh lebih kecil dari odo awal."; }
    elseif ($pengukuran_awal<$pengukuran_akhir) { $error="Pengukuran awal tidak boleh lebih kecil dari pengukuran akhir."; }
    elseif ($harga_pertamax<=0) { $error="Harga pertamax harus lebih dari 0."; }
    else {
        $penjualan_liter=$odo_akhir-$odo_awal;
        $penghasilan=$penjualan_liter*$harga_pertamax;
        $hasil_pengukuran=($pengukuran_awal-$pengukuran_akhir)*21.23;
        $stok_pertamax=$pengukuran_akhir*21.23;
        $stmt=$pdo->prepare("INSERT INTO penjualan_harian (nama,tanggal,shift,odo_awal,odo_akhir,penjualan_liter,penghasilan_rp,ukur_awal,ukur_akhir,harga_pertamax,hasil_pengukuran,stok_hari_ini) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        if ($stmt->execute([$nama,$tanggal,$shift,$odo_awal,$odo_akhir,$penjualan_liter,$penghasilan,$pengukuran_awal,$pengukuran_akhir,$harga_pertamax,$hasil_pengukuran,$stok_pertamax])) {
            header("Location: laporan_operator.php?tanggal=".urlencode($tanggal)); exit;
        } else { $error="Gagal menyimpan data."; }
    }
}
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><title>Input Penjualan</title><meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
body{font-family:'Segoe UI',Tahoma,sans-serif;background:linear-gradient(135deg,#e6ecf3,#f8fbfd);color:#333;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;margin:0;padding:20px}
.form-container{background:#fff;padding:25px 30px;border-radius:18px;box-shadow:0 10px 28px rgba(0,0,0,0.12);width:100%;max-width:760px}
h2{text-align:center;margin:10px 0 18px 0;color:#0077cc;font-size:22px}
.desc{text-align:center;color:#555;margin-bottom:18px;font-size:13px}
.alert-error{background:#ffe5e5;color:#b30000;border:1px solid #f5b5b3;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
label{font-weight:600;font-size:14px;margin-bottom:4px;display:block;color:#333}
input,select{width:100%;padding:9px 11px;border-radius:6px;border:1px solid #ccc;font-size:14px;margin-bottom:15px;box-sizing:border-box}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:10px}
small.hint{color:#777;display:block;margin:4px 0 8px 0;font-size:12px}
.actions{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:10px;margin-top:20px}
.top-bar{display:flex;justify-content:flex-start;margin-bottom:15px}
.btn-primary,.btn-secondary{padding:11px 18px;border-radius:10px;font-weight:600;border:none;cursor:pointer;transition:all 0.25s;font-size:14px}
.btn-primary{background:linear-gradient(45deg,#007bff,#00c6ff);color:white}
.btn-secondary{background:linear-gradient(45deg,#28a745,#6edc82);color:white;text-decoration:none;display:inline-block}
@media(max-width:768px){.grid-2{grid-template-columns:1fr}.form-container{padding:20px}}
</style></head><body>
<div class="form-container">
<div class="top-bar"><a class="btn-secondary" href="dashboard_operator.php">⬅️ Kembali ke Dashboard</a></div>
<h2>📝 Input Penjualan Pertashop</h2>
<p class="desc">Masukkan data lengkap untuk menyimpan transaksi harian.</p>
<?php if (!empty($error)): ?><div class="alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST" novalidate>
    <label>Nama Operator</label><input type="text" name="nama" required value="<?= h($prefill_nama) ?>">
    <label>Tanggal</label><input type="date" name="tanggal" required value="<?= h($prefill_tanggal) ?>">
    <label>Shift</label>
    <select name="shift" required>
        <option value="">-- Pilih Shift --</option>
        <option value="Pagi" <?= $prefill_shift==='Pagi'?'selected':''; ?>>🌅 Pagi</option>
        <option value="Sore" <?= $prefill_shift==='Sore'?'selected':''; ?>>🌆 Sore</option>
    </select>
    <div class="grid-2">
        <div><label>Odo Awal</label><input type="number" name="odo_awal" required value="<?= h($prefill_odo_awal) ?>"></div>
        <div><label>Odo Akhir</label><input type="number" name="odo_akhir" required value="<?= h($prefill_odo_akhir) ?>"></div>
    </div>
    <div class="grid-2">
        <div><label>Pengukuran Awal</label><small class="hint">(pakai titik, misal 12.35)</small><input type="number" step="0.01" name="pengukuran_awal" required value="<?= h($prefill_ukur_awal) ?>"></div>
        <div><label>Pengukuran Akhir</label><small class="hint">(pakai titik, misal 11.87)</small><input type="number" step="0.01" name="pengukuran_akhir" required value="<?= h($prefill_ukur_akhir) ?>"></div>
    </div>
    <label>Harga Pertamax (Rp)</label><input type="number" name="harga_pertamax" required value="<?= h($prefill_harga) ?>">
    <div class="actions"><button type="submit" class="btn-primary">💾 Simpan Data</button></div>
</form>
</div></body></html>
