<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM penjualan_harian WHERE tanggal = ? ORDER BY shift");
$stmt->execute([$tanggal]);
$rows = $stmt->fetchAll();
$total_liter=$total_rp=$total_untung=0;
?>
<!DOCTYPE html><html><head><title>Laporan Penjualan Pertashop</title>
<style>body{font-family:Arial,sans-serif;padding:20px}table{border-collapse:collapse;width:100%;margin-top:20px}
th,td{border:1px solid #ccc;padding:8px;text-align:center}th{background:#f2f2f2}h2{text-align:center}
.hapus{color:red;text-decoration:none;font-weight:bold}</style></head><body>
<h2>Laporan Penjualan Pertashop</h2>
<table>
<tr><th>Nama</th><th>Shift</th><th>Odo Awal</th><th>Odo Akhir</th>
<th>Ukur Awal</th><th>Ukur Akhir</th><th>Stok (L)</th><th>Harga</th>
<th>Penjualan (L)</th><th>Penghasilan (Rp)</th><th>Aksi</th></tr>
<?php if ($rows): foreach ($rows as $row):
    $stok_pertamax=($row['ukur_awal']-$row['ukur_akhir'])*21.23;
    $keuntungan=$row['penjualan_liter']*748.8395;
    $total_liter+=$row['penjualan_liter']; $total_rp+=$row['penghasilan_rp']; $total_untung+=$keuntungan;
?>
<tr><td><?= htmlspecialchars($row['nama']) ?></td><td><?= $row['shift'] ?></td>
<td><?= $row['odo_awal'] ?></td><td><?= $row['odo_akhir'] ?></td>
<td><?= number_format($row['ukur_awal'],2,',','.') ?></td><td><?= number_format($row['ukur_akhir'],2,',','.') ?></td>
<td><?= number_format($stok_pertamax,2,',','.') ?></td><td>Rp <?= number_format($row['harga_pertamax'],0,',','.') ?></td>
<td><?= $row['penjualan_liter'] ?></td><td>Rp <?= number_format($row['penghasilan_rp'],0,',','.') ?></td>
<td><a class='hapus' href='hapus_penjualan.php?id=<?= $row['id'] ?>&tanggal=<?= $tanggal ?>' onclick="return confirm('Yakin hapus?')">❌ Hapus</a></td>
</tr>
<?php endforeach;
echo "<tr style='font-weight:bold;background:#eaeaea;'><td colspan='8'>TOTAL</td><td>$total_liter</td><td>Rp ".number_format($total_rp,0,',','.')."</td><td>-</td></tr>";
else: echo "<tr><td colspan='11'>Tidak ada data pada tanggal ini.</td></tr>"; endif; ?>
</table>
<div style="text-align:center;margin-top:20px;">
    <a href="../cetak_pdf.php?tanggal=<?= $tanggal ?>" target="_blank" style="display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:6px;font-weight:bold;margin:5px;">📄 Download PDF</a>
    <a href="dashboard.php" style="display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:6px;font-weight:bold;margin:5px;">⬅️ Dashboard</a>
    <a href="input_penjualan.php" style="display:inline-block;padding:10px 20px;background:#ffc107;color:black;text-decoration:none;border-radius:6px;font-weight:bold;margin:5px;">➕ Input Data</a>
</div></body></html>
