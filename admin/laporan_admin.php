<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
if ($_SESSION['role'] !== 'admin') { die("❌ Akses ditolak."); }

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM penjualan_harian WHERE tanggal = ? ORDER BY shift");
$stmt->execute([$tanggal]);
$rows = $stmt->fetchAll();

$total_liter = $total_rp = $total_hasil_pengukuran = 0;
$rasio_keuntungan = 748.8396;
?>
<!DOCTYPE html><html><head>
<title>Laporan Penjualan Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
body{font-family:Arial,sans-serif;padding:15px;background:#f4f4f9}
h2{text-align:center;margin-bottom:20px}
form{text-align:center;margin-bottom:20px}
form input,form button{padding:8px 12px;border-radius:6px;border:1px solid #ccc}
form button{background:#007bff;color:white;border:none;cursor:pointer}
.table-container{overflow-x:auto}
table{border-collapse:collapse;width:100%;margin-top:15px;background:white;border-radius:8px}
th,td{border:1px solid #ccc;padding:10px;text-align:center}
th{background:#343a40;color:white}tr.total-row{font-weight:bold;background:#e9ecef}
a.edit-btn,a.delete-btn{display:inline-block;padding:6px 12px;margin:2px;color:white;text-decoration:none;border-radius:6px;font-size:14px}
a.edit-btn{background:#007bff}a.delete-btn{background:#dc3545}
.btn-download,.btn-back{display:inline-block;padding:10px 20px;border-radius:6px;font-weight:bold;text-decoration:none;margin:10px 5px}
.btn-download{background:#007bff;color:white}.btn-back{background:#28a745;color:white}
</style></head><body>
<h2>Laporan Penjualan (Admin)</h2>
<form method="GET">
    Pilih Tanggal: <input type="date" name="tanggal" value="<?= $tanggal ?>">
    <button type="submit">Cari</button>
</form>
<div class="table-container"><table>
<tr><th>Nama</th><th>Shift</th><th>Odo Awal</th><th>Odo Akhir</th>
<th>Ukur Awal</th><th>Ukur Akhir</th><th>Stok (L)</th><th>Harga</th>
<th>Penjualan (L)</th><th>Hasil Ukur (L)</th><th>Penghasilan (Rp)</th><th>Aksi</th></tr>
<?php if ($rows): foreach ($rows as $row):
    $hasil_pengukuran = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
    $stok_pertamax    = $row['ukur_akhir'] * 21.23;
    $total_liter += $row['penjualan_liter'];
    $total_hasil_pengukuran += $hasil_pengukuran;
    $total_rp += $row['penghasilan_rp'];
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
        <a class="edit-btn" href="edit_penjualan.php?id=<?= $row['id'] ?>&tanggal=<?= $tanggal ?>">✏️ Edit</a>
        <a class="delete-btn" href="hapus_penjualan.php?id=<?= $row['id'] ?>&tanggal=<?= $tanggal ?>" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
    </td>
</tr>
<?php endforeach;
echo "<tr class='total-row'><td colspan='8'>TOTAL</td><td>$total_liter</td><td>".number_format($total_hasil_pengukuran,2,',','.')."</td><td>Rp ".number_format($total_rp,0,',','.')."</td><td></td></tr>";
else: echo "<tr><td colspan='12'>Tidak ada data pada tanggal ini.</td></tr>"; endif; ?>
</table></div>

<?php if ($total_liter > 0): $keuntungan = $total_liter * $rasio_keuntungan; ?>
<div class="table-container"><table>
<tr><th>Total (L)</th><th>Rasio Keuntungan</th><th>Keuntungan (Rp)</th></tr>
<tr><td><?= number_format($total_liter,0,',','.') ?></td>
<td>Rp <?= number_format($rasio_keuntungan,2,',','.') ?></td>
<td><b>Rp <?= number_format($keuntungan,0,',','.') ?></b></td></tr>
</table></div>
<?php endif; ?>

<div style="text-align:center;margin-top:20px;">
    <a href="../cetak_pdf.php?tanggal=<?= $tanggal ?>" target="_blank" class="btn-download">📄 Download PDF</a>
    <a href="dashboard.php" class="btn-back">⬅️ Kembali ke Dashboard</a>
</div></body></html>
