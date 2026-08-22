<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
require __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

$bulan = $_GET['bulan'] ?? date('Y-m');
$stmt = $pdo->prepare("
    SELECT tanggal, SUM(penjualan_liter) AS total_liter, SUM(penghasilan_rp) AS total_rp
    FROM penjualan_harian
    WHERE TO_CHAR(tanggal, 'YYYY-MM') = ?
    GROUP BY tanggal ORDER BY tanggal
");
$stmt->execute([$bulan]);
$rows = $stmt->fetchAll();

$html = "<h2 style='text-align:center;'>Rekap Penjualan Bulanan Pertashop ($bulan)</h2>
<table border='1' cellspacing='0' cellpadding='6' width='100%' style='border-collapse:collapse;font-size:12px;'>
<thead><tr style='background:#f2f2f2;text-align:center;'>
<th>Tanggal</th><th>Penjualan (L)</th><th>Penghasilan (Rp)</th><th>Keuntungan (Rp)</th>
</tr></thead><tbody>";

$grand_liter = $grand_rp = $grand_keuntungan = 0;
if ($rows) {
    foreach ($rows as $row) {
        $keuntungan = $row['total_liter'] * 748.839;
        $html .= "<tr><td align='center'>{$row['tanggal']}</td><td align='right'>{$row['total_liter']}</td>
        <td align='right'>Rp ".number_format($row['total_rp'],0,',','.')."</td>
        <td align='right'>Rp ".number_format($keuntungan,0,',','.')."</td></tr>";
        $grand_liter += $row['total_liter']; $grand_rp += $row['total_rp']; $grand_keuntungan += $keuntungan;
    }
    $html .= "<tr style='font-weight:bold;background:#eaeaea;'>
    <td align='center'>TOTAL</td><td align='right'>$grand_liter</td>
    <td align='right'>Rp ".number_format($grand_rp,0,',','.')."</td>
    <td align='right'>Rp ".number_format($grand_keuntungan,0,',','.')."</td></tr>";
} else { $html .= "<tr><td colspan='4' align='center'>Tidak ada data.</td></tr>"; }
$html .= "</tbody></table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html); $dompdf->setPaper('A4','portrait'); $dompdf->render();
$dompdf->stream("rekap_bulanan_$bulan.pdf", ["Attachment" => true]);
