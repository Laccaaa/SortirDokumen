<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

$jenis = $_GET['jenis'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

$sql = "SELECT
    id_surat, to_jsonb(surat)->>'nomor_berkas' AS nomor_berkas, jenis_surat, nomor_surat, kode_utama, subkode,
    nomor_urut, unit_pengirim, bulan, tahun, nama_file,
    path_file, tanggal_upload, unit_pengolah, nama_berkas,
    nomor_isi, pencipta_arsip, tujuan_surat, perihal, uraian_informasi,
    tanggal_surat_kurun, jumlah, lokasi_simpan, tingkat, keterangan,
    skkad, jra_aktif, jra_inaktif, nasib
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

$headerTahun = '';
if ($tahun !== '') {
    $headerTahun = 'Tahun ' . $tahun;
} else {
    $years = [];
    foreach ($rows as $row) {
        $yearValue = isset($row['tahun']) ? (int)$row['tahun'] : 0;
        if ($yearValue > 0) {
            $years[] = $yearValue;
        }
    }

    if ($years) {
        $minYear = min($years);
        $maxYear = max($years);
        $headerTahun = $minYear === $maxYear
            ? 'Tahun ' . $minYear
            : 'Tahun ' . $minYear . ' s/d ' . $maxYear;
    } else {
        $headerTahun = 'Tahun -';
    }
}

if ($jenis === '' && $tahun === '' && $bulan === '') {
    $filename = "ekspor_keseluruhan_arsip.csv";
} else {
    $filenameParts = ["ekspor_arsip"];
    if ($jenis !== '') $filenameParts[] = $jenis;
    if ($tahun !== '') $filenameParts[] = $tahun;
    if ($bulan !== '') $filenameParts[] = $bulan;
    $filename = implode('_', $filenameParts) . ".csv";
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

$out = fopen('php://output', 'w');

fputcsv($out, ['Daftar Isi Berkas Arsip']);
fputcsv($out, ['Stasiun Meteorologi Kelas I Juanda Sidoarjo']);
fputcsv($out, ['']);
fputcsv($out, []);

fputcsv($out, [
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    $headerTahun,
    '',
    '',
    '',
    '',
    '',
    'JRA',
    '',
    'Nasib Akhir'
]);

fputcsv($out, [
    'Nomor',
    'Nomor Berkas',
    'Kode',
    'Unit Pengolah',
    'Nama Berkas',
    'Nomor Isi',
    'Pencipta Arsip',
    'Tujuan Surat',
    'Nomor Surat',
    'Perihal',
    'Uraian Informasi',
    'Tanggal Surat / Kurun Waktu',
    'Jumlah',
    'Lokasi Simpan',
    'Tingkat Perkembangan (Asli/Copy)',
    'Keterangan (Baik/Rusak)',
    'SKKAD (B/T/R)',
    'Aktif',
    'Inaktif',
    ''
]);

foreach ($rows as $i => $row) {
    $kodeParts = [];
    if (($row['kode_utama'] ?? '') !== '') {
        $kodeParts[] = trim((string)$row['kode_utama']);
    }
    if (($row['subkode'] ?? '') !== '') {
        $kodeParts[] = trim((string)$row['subkode']);
    }
    if (($row['nomor_urut'] ?? '') !== '') {
        $kodeParts[] = trim((string)$row['nomor_urut']);
    }
    $kodeGabung = implode('.', $kodeParts);

    fputcsv($out, [
        $i + 1,
        $row['nomor_berkas'] ?? '',
        $kodeGabung,
        (($row['unit_pengolah'] ?? '') !== '' ? $row['unit_pengolah'] : ($row['unit_pengirim'] ?? '')),
        (($row['nama_berkas'] ?? '') !== '' ? $row['nama_berkas'] : ($row['nama_file'] ?? '')),
        $row['nomor_isi'] ?? '',
        $row['pencipta_arsip'] ?? '',
        $row['tujuan_surat'] ?? '',
        $row['nomor_surat'] ?? '',
        $row['perihal'] ?? '',
        $row['uraian_informasi'] ?? '',
        $row['tanggal_surat_kurun'] ?? '',
        $row['jumlah'] ?? '',
        $row['lokasi_simpan'] ?? '',
        $row['tingkat'] ?? '',
        $row['keterangan'] ?? '',
        $row['skkad'] ?? '',
        $row['jra_aktif'] ?? '',
        $row['jra_inaktif'] ?? '',
        $row['nasib'] ?? ''
    ]);
}

fclose($out);
exit;
