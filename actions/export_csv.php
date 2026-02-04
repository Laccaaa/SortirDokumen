<?php
require_once __DIR__ . "/../config/koneksi.php";

$jenis = $_GET['jenis'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

$sql = "SELECT
    id_surat, jenis_surat, nomor_surat, kode_utama, subkode,
    nomor_urut, unit_pengirim, bulan, tahun, nama_file,
    path_file, tanggal_upload
    FROM surat WHERE 1=1";
$params = [];

if ($jenis !== '') {
    $sql .= " AND jenis_surat = ?";
    $params[] = $jenis;
}
if ($tahun !== '') {
    $sql .= " AND tahun = ?";
    $params[] = (int)$tahun;
}
if ($bulan !== '') {
    $sql .= " AND bulan = ?";
    $params[] = $bulan;
}

$sql .= "
    ORDER BY tahun DESC,
    CASE bulan
        WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
        WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
        WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
        WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
    END,
    kode_utama, nama_file
";

$stmt = $dbhandle->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filenameParts = ["export_arsip"];
if ($jenis !== '') $filenameParts[] = $jenis;
if ($tahun !== '') $filenameParts[] = $tahun;
if ($bulan !== '') $filenameParts[] = $bulan;
$filename = implode('_', $filenameParts) . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

$out = fopen('php://output', 'w');

fputcsv($out, [
    'No',
    'ID Surat',
    'Jenis Surat',
    'Nomor Surat',
    'Kode Utama',
    'Subkode',
    'Nomor Urut',
    'Unit Pengirim',
    'Bulan',
    'Tahun',
    'Nama File',
    'Path File',
    'Tanggal Upload'
]);

foreach ($rows as $i => $row) {
    fputcsv($out, [
        $i + 1,
        $row['id_surat'] ?? '',
        $row['jenis_surat'] ?? '',
        $row['nomor_surat'] ?? '',
        $row['kode_utama'] ?? '',
        $row['subkode'] ?? '',
        $row['nomor_urut'] ?? '',
        $row['unit_pengirim'] ?? '',
        $row['bulan'] ?? '',
        $row['tahun'] ?? '',
        $row['nama_file'] ?? '',
        $row['path_file'] ?? '',
        $row['tanggal_upload'] ?? ''
    ]);
}

fclose($out);
exit;
