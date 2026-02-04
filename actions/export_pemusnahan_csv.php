<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

$yearExpr = "CASE
    WHEN tanggal ~ '^[0-9]{4}$' THEN tanggal::int
    WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,1,4)::int
    ELSE NULL
END";
$monthExpr = "CASE
    WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,6,2)::int
    ELSE NULL
END";

$sql = "SELECT
    id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat,
    uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at
    FROM arsip_dimusnahkan WHERE 1=1";
$params = [];

if ($tahun !== '') {
    $sql .= " AND $yearExpr = ?";
    $params[] = (int)$tahun;
}
if ($bulan !== '') {
    $sql .= " AND $monthExpr = ?";
    $params[] = (int)$bulan;
}

$sql .= " ORDER BY $yearExpr DESC NULLS LAST, $monthExpr NULLS LAST, id DESC";

$stmt = $dbhandle->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filenameParts = ["export_dokumen_musnah"];
if ($tahun !== '') $filenameParts[] = $tahun;
if ($bulan !== '') $filenameParts[] = $bulan;
$filename = implode('_', $filenameParts) . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');

fputcsv($out, [
    'No',
    'ID',
    'Kode',
    'Nama Berkas',
    'No Isi',
    'Pencipta',
    'No Surat',
    'Uraian',
    'Tanggal',
    'Jumlah',
    'Tingkat',
    'Lokasi',
    'Keterangan',
    'Created At'
]);

foreach ($rows as $i => $row) {
    fputcsv($out, [
        $i + 1,
        $row['id'] ?? '',
        $row['kode_klasifikasi'] ?? '',
        $row['nama_berkas'] ?? '',
        $row['no_isi'] ?? '',
        $row['pencipta'] ?? '',
        $row['no_surat'] ?? '',
        $row['uraian'] ?? '',
        $row['tanggal'] ?? '',
        $row['jumlah'] ?? '',
        $row['tingkat'] ?? '',
        $row['lokasi'] ?? '',
        $row['keterangan'] ?? '',
        $row['created_at'] ?? ''
    ]);
}

fclose($out);
exit;
