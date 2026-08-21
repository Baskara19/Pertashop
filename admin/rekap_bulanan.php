<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';

$bulan = $_GET['bulan'] ?? date('Y-m');
$stmt = $pdo->prepare("
    SELECT tanggal, SUM(penjualan_liter) AS total_liter, SUM(penghasilan_rp) AS total_rp
    FROM penjualan_harian
    WHERE TO_CHAR(tanggal, 'YYYY-MM') = ?
    GROUP BY tanggal ORDER BY tanggal
");
$stmt->execute([$bulan]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Rekap Bulanan Pertashop</title>
<style>
body{font-family:Arial,sans-serif;padding:15px;background:#f8f9fa}
h2{text-align:center;margin-bottom:20px}
form{text-align:center;margin-bottom:20px}
form input,form button{padding:8px 12px;border-radius:6px;border:1px solid #ccc}
form button{background:#007bff;color:white;border:none;cursor:pointer}
.table-container{overflow-x:auto}
table{border-collapse:collapse;width:100%;margin-top:15px;background:white;border-radius:6px}
th,td{border:1px solid #ccc;padding:10px;text-align:center}
th{background:#343a40;color:white}tr.total-row{font-weight:bold;background:#e9ecef}
.btn{display:inline-block;padding:10px 20px;border-radius:6px;font-weight:bold;text-decoration:none;margin:8px 5px}
.btn-download{background:#28a745;color:white}.btn-back{background:#007bff;color:white}
</style></head><body>
<h2>Rekap Penjualan Bulanan Pertashop</h2>
<form method="get">
    <label>Pilih Bulan: </label>
    <input type="month" name="bulan" value="<?= $bulan ?>">
    <button type="submit">Tampilkan</button>
</form>
<div class="table-container"><table>
<tr><th>Tanggal</th><th>Penjualan (L)</th><th>Penghasilan (Rp)</th><th>Keuntungan (Rp)</th></tr>
<?php
$grand_liter = $grand_rp = $grand_keuntungan = 0;
if ($rows): foreach ($rows as $row):
    $keuntungan = $row['total_liter'] * 748.839;
    $grand_liter += $row['total_liter']; $grand_rp += $row['total_rp']; $grand_keuntungan += $keuntungan;
?>
<tr>
    <td><?= $row['tanggal'] ?></td><td><?= $row['total_liter'] ?></td>
    <td>Rp <?= number_format($row['total_rp'],0,',','.') ?></td>
    <td>Rp <?= number_format($keuntungan,0,',','.') ?></td>
</tr>
<?php endforeach;
echo "<tr class='total-row'><td>TOTAL</td><td>$grand_liter</td><td>Rp ".number_format($grand_rp,0,',','.')."</td><td>Rp ".number_format($grand_keuntungan,0,',','.')."</td></tr>";
else: echo "<tr><td colspan='4'>Tidak ada data pada bulan ini.</td></tr>"; endif; ?>
</table></div>
<div style="text-align:center;margin-top:20px;">
    <a href="rekap_bulanan_pdf.php?bulan=<?= $bulan ?>" target="_blank" class="btn btn-download">📄 Download PDF</a>
    <a href="dashboard.php" class="btn btn-back">⬅️ Kembali ke Dashboard</a>
</div></body></html>
