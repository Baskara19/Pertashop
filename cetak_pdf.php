<?php
require __DIR__ . '/auth_admin.php';
if ($_SESSION['role'] !== 'admin') { die("❌ Akses ditolak."); }
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';
use Dompdf\Dompdf; use Dompdf\Options;

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM penjualan_harian WHERE tanggal = ? ORDER BY shift");
$stmt->execute([$tanggal]);
$rows = $stmt->fetchAll();

$total_liter = $total_rp = $total_hasil_pengukuran = 0;
$rasio_keuntungan = 748.8396;

$html = "<h2 style='text-align:center;'>Laporan Penjualan (Admin)</h2>
<p style='text-align:center;'>Tanggal: $tanggal</p>
<table border='1' cellspacing='0' cellpadding='6' width='100%'>
<tr style='background:#343a40;color:white;'>
<th>Nama</th><th>Shift</th><th>Odo Awal</th><th>Odo Akhir</th>
<th>Ukur Awal</th><th>Ukur Akhir</th><th>Stok (L)</th>
<th>Harga</th><th>Penjualan (L)</th><th>Hasil Ukur (L)</th><th>Penghasilan (Rp)</th>
</tr>";

if ($rows) {
    foreach ($rows as $row) {
        $hasil_pengukuran = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
        $stok_pertamax    = $row['ukur_akhir'] * 21.23;
        $html .= "<tr>
            <td>{$row['nama']}</td><td>{$row['shift']}</td>
            <td>{$row['odo_awal']}</td><td>{$row['odo_akhir']}</td>
            <td>".number_format($row['ukur_awal'],2,',','.')."</td>
            <td>".number_format($row['ukur_akhir'],2,',','.')."</td>
            <td>".number_format($stok_pertamax,2,',','.')."</td>
            <td>Rp ".number_format($row['harga_pertamax'],0,',','.')."</td>
            <td>{$row['penjualan_liter']}</td>
            <td>".number_format($hasil_pengukuran,2,',','.')."</td>
            <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td>
        </tr>";
        $total_liter += $row['penjualan_liter'];
        $total_hasil_pengukuran += $hasil_pengukuran;
        $total_rp += $row['penghasilan_rp'];
    }
    $html .= "<tr style='font-weight:bold;background:#e9ecef;'>
        <td colspan='8'>TOTAL</td><td>$total_liter</td>
        <td>".number_format($total_hasil_pengukuran,2,',','.')."</td>
        <td>Rp ".number_format($total_rp,0,',','.')."</td>
    </tr>";
} else {
    $html .= "<tr><td colspan='11' style='text-align:center;'>Tidak ada data.</td></tr>";
}
$html .= "</table>";

if ($total_liter > 0) {
    $keuntungan = $total_liter * $rasio_keuntungan;
    $html .= "<br><table border='1' cellspacing='0' cellpadding='6' width='100%'>
    <tr style='background:#343a40;color:white;'><th>Total (L)</th><th>Rasio</th><th>Keuntungan (Rp)</th></tr>
    <tr><td>".number_format($total_liter,0,',','.')."</td>
    <td>Rp ".number_format($rasio_keuntungan,2,',','.')."</td>
    <td><b>Rp ".number_format($keuntungan,0,',','.')."</b></td></tr></table>";
}

$options = new Options(); $options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html); $dompdf->setPaper('A4','landscape'); $dompdf->render();
$dompdf->stream("Laporan Penjualan $tanggal", ["Attachment" => true]); exit;
