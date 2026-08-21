<?php
require __DIR__ . '/../auth_operator.php';
require __DIR__ . '/../db.php';
if ($_SESSION['role'] !== 'operator') { die("❌ Akses ditolak."); }
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf; use Dompdf\Options;

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM penjualan_harian WHERE tanggal = ? ORDER BY shift");
$stmt->execute([$tanggal]);
$rows = $stmt->fetchAll();

$total_liter=$total_rp=$total_ukur=0;
$html="<style>body{font-family:Arial,sans-serif;font-size:12px}h2{text-align:center}p{text-align:center}
table{border-collapse:collapse;width:100%;margin-top:10px}th,td{border:1px solid #ccc;padding:6px;text-align:center}
th{background:#6c757d;color:white}tr.total-row{font-weight:bold;background:#e9ecef}</style>
<h2>Laporan Penjualan Operator</h2><p><b>Tanggal:</b> $tanggal</p>
<table><tr><th>Nama</th><th>Shift</th><th>Odo Awal</th><th>Odo Akhir</th>
<th>Ukur Awal</th><th>Ukur Akhir</th><th>Stok (L)</th><th>Harga</th>
<th>Penjualan (L)</th><th>Hasil Ukur (L)</th><th>Penghasilan (Rp)</th></tr>";

if ($rows) {
    foreach ($rows as $row) {
        $hasil_pengukuran=($row['ukur_awal']-$row['ukur_akhir'])*21.23;
        $stok_pertamax=$row['ukur_akhir']*21.23;
        $html.="<tr><td>{$row['nama']}</td><td>{$row['shift']}</td><td>{$row['odo_awal']}</td><td>{$row['odo_akhir']}</td>
        <td>".number_format($row['ukur_awal'],2,',','.')."</td><td>".number_format($row['ukur_akhir'],2,',','.')."</td>
        <td>".number_format($stok_pertamax,2,',','.')."</td><td>Rp ".number_format($row['harga_pertamax'],0,',','.')."</td>
        <td>{$row['penjualan_liter']}</td><td>".number_format($hasil_pengukuran,2,',','.')."</td>
        <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td></tr>";
        $total_liter+=$row['penjualan_liter']; $total_rp+=$row['penghasilan_rp']; $total_ukur+=$hasil_pengukuran;
    }
    $html.="<tr class='total-row'><td colspan='8'>TOTAL</td><td>".number_format($total_liter,0,',','.')."</td>
    <td>".number_format($total_ukur,2,',','.')."</td><td>Rp ".number_format($total_rp,0,',','.')."</td></tr>";
} else { $html.="<tr><td colspan='11'>Tidak ada data.</td></tr>"; }
$html.="</table>";

$options=new Options(); $options->set('isRemoteEnabled',true);
$dompdf=new Dompdf($options); $dompdf->loadHtml($html); $dompdf->setPaper('A4','landscape'); $dompdf->render();
$dompdf->stream("laporan_operator_$tanggal.pdf",["Attachment"=>true]);
