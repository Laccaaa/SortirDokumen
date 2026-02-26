<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
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
    id, nomor_berkas, kode_klasifikasi, nama_berkas, no_isi, pencipta, tujuan_surat, no_surat,
    uraian, uraian_informasi_1, uraian_informasi_2,
    tanggal, tanggal_surat, kurun_waktu,
    jumlah, skkad, tingkat, lokasi, keterangan, created_at
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
    'Nomor Berkas',
    'Kode Klasifikasi',
    'Nama Berkas',
    'No. Isi Berkas',
    'Pencipta',
    'Tujuan Surat',
    'No Surat',
    'Uraian Informasi 1',
    'Uraian Informasi 2',
    'Tanggal Surat',
    'Kurun Waktu',
    'Jumlah',
    'SKKAD',
    'Tingkat Perkembangan',
    'Boks',
    'Keterangan',
    'Created At'
]);

foreach ($rows as $i => $row) {
    $uraian1 = $row['uraian_informasi_1'] ?? ($row['uraian'] ?? '');
    $uraian2 = $row['uraian_informasi_2'] ?? '';
    $tanggalSurat = $row['tanggal_surat'] ?? '';
    $kurunWaktu = $row['kurun_waktu'] ?? '';
    $tanggalLegacy = (string)($row['tanggal'] ?? '');
    if ($tanggalSurat === '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
        $tanggalSurat = $tanggalLegacy;
    }
    if ($kurunWaktu === '' && $tanggalLegacy !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
        $kurunWaktu = $tanggalLegacy;
    }

    fputcsv($out, [
        $i + 1,
        $row['nomor_berkas'] ?? '',
        $row['kode_klasifikasi'] ?? '',
        $row['nama_berkas'] ?? '',
        $row['no_isi'] ?? '',
        $row['pencipta'] ?? '',
        $row['tujuan_surat'] ?? '',
        $row['no_surat'] ?? '',
        $uraian1,
        $uraian2,
        $tanggalSurat,
        $kurunWaktu,
        $row['jumlah'] ?? '',
        $row['skkad'] ?? '',
        $row['tingkat'] ?? '',
        $row['lokasi'] ?? '',
        $row['keterangan'] ?? '',
        $row['created_at'] ?? ''
    ]);
}

fclose($out);
exit;
