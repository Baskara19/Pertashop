<?php
require __DIR__ . '/../auth_operator.php';
require __DIR__ . '/../db.php';
if ($_SESSION['role'] !== 'operator') { die("❌ Akses ditolak."); }
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM penjualan_harian WHERE tanggal = ? ORDER BY shift");
$stmt->execute([$tanggal]);
$rows = $stmt->fetchAll();
$total_liter = $total_rp = $total_hasil_pengukuran = 0;
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Laporan Penjualan Operator</title>
<style>
body{font-family:Arial,sans-serif;padding:15px;background:#f4f4f9}
h2{text-align:center;margin-bottom:15px;font-size:22px}
form{text-align:center;margin-bottom:15px}
form input,form button{padding:7px 10px;border-radius:6px;border:1px solid #ccc;font-size:14px}
form button{background:#007bff;color:white;border:none;cursor:pointer}
.table-wrapper{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{border-collapse:collapse;width:100%;margin-top:15px;background:white;border-radius:8px;font-size:14px}
th,td{border:1px solid #ccc;padding:8px;text-align:center}
th{background:#343a40;color:white}tr.total-row{font-weight:bold;background:#e9ecef}
a.edit-btn,a.delete-btn{display:inline-block;padding:5px 10px;margin:2px;color:white;text-decoration:none;border-radius:6px;font-size:13px}
a.edit-btn{background:#007bff}a.delete-btn{background:#dc3545}
.btn-download,.btn-back{display:inline-block;padding:8px 14px;border-radius:6px;font-weight:bold;text-decoration:none;margin:10px 5px;font-size:14px}
.btn-download{background:#007bff;color:white}.btn-back{background:#28a745;color:white}
</style></head><body>
<h2>Laporan Penjualan (Operator)</h2>
<form method="GET">
    Pilih Tanggal: <input type="date" name="tanggal" value="<?= $tanggal ?>">
    <button type="submit">Cari</button>
</form>
<div class="table-wrapper"><table>
<tr><th>Nama</th><th>Shift</th><th>Odo Awal</th><th>Odo Akhir</th>
<th>Ukur Awal</th><th>Ukur Akhir</th><th>Stok (L)</th><th>Harga</th>
<th>Penjualan (L)</th><th>Hasil Ukur (L)</th><th>Penghasilan (Rp)</th><th>Aksi</th></tr>
<?php if ($rows): foreach ($rows as $row):
    $hasil_pengukuran=($row['ukur_awal']-$row['ukur_akhir'])*21.23;
    $stok_pertamax=$row['ukur_akhir']*21.23;
    $total_liter+=$row['penjualan_liter']; $total_hasil_pengukuran+=$hasil_pengukuran; $total_rp+=$row['penghasilan_rp'];
?>
<tr>
    <td><?= htmlspecialchars($row['nama']) ?></td><td><?= $row['shift'] ?></td>
    <td><?= $row['odo_awal'] ?></td><td><?= $row['odo_akhir'] ?></td>
    <td><?= number_format($row['ukur_awal'],2,',','.') ?></td>
    <td><?= number_format($row['ukur_akhir'],2,',','.') ?></td>
    <td><?= number_format($stok_pertamax,2,',','.') ?></td>
    <td>Rp <?= number_format($row['harga_pertamax'],0,',','.') ?></td>
    <td><?= $row['penjualan_liter'] ?></td>
    <td><?= number_format($hasil_pengukuran,2,',','.') ?></td>
    <td>Rp <?= number_format($row['penghasilan_rp'],0,',','.') ?></td>
    <td>
        <a class="edit-btn" href="edit_penjualan.php?id=<?= $row['id'] ?>&tanggal=<?= $tanggal ?>">✏️</a>
        <a class="delete-btn" href="hapus_penjualan.php?id=<?= $row['id'] ?>&tanggal=<?= $tanggal ?>" onclick="return confirm('Yakin hapus?')">🗑️</a>
    </td>
</tr>
<?php endforeach;
echo "<tr class='total-row'><td colspan='8'>TOTAL</td><td>$total_liter</td><td>".number_format($total_hasil_pengukuran,2,',','.')."</td><td>Rp ".number_format($total_rp,0,',','.')."</td><td></td></tr>";
else: echo "<tr><td colspan='12'>Tidak ada data pada tanggal ini.</td></tr>"; endif; ?>
</table></div>
<div style="text-align:center;margin-top:15px;">
    <a href="laporan_operator_pdf.php?tanggal=<?= $tanggal ?>" target="_blank" class="btn-download">📄 Download PDF</a>
    <a href="dashboard_operator.php" class="btn-back">⬅️ Kembali ke Dashboard</a>
</div></body></html>
