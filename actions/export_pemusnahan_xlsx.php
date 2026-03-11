<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";
require_once __DIR__ . "/../lib/SimpleXlsxWriter.php";

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
    jumlah, skkad, tingkat, lokasi, keterangan
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

$headerTahun = '';
if ($tahun !== '') {
    $headerTahun = 'TAHUN ' . $tahun;
} else {
    $years = [];
    foreach ($rows as $row) {
        $candidates = [
            (string)($row['tanggal_surat'] ?? ''),
            (string)($row['tanggal'] ?? ''),
            (string)($row['kurun_waktu'] ?? '')
        ];

        foreach ($candidates as $value) {
            if (preg_match('/\\b(19|20)\\d{2}\\b/', $value, $m)) {
                $years[] = (int)$m[0];
                break;
            }
        }
    }

    if ($years) {
        $minYear = min($years);
        $maxYear = max($years);
        $headerTahun = $minYear === $maxYear ? 'TAHUN ' . $minYear : 'TAHUN ' . $minYear . ' s/d ' . $maxYear;
    } else {
        $headerTahun = 'TAHUN -';
    }
}

if ($tahun === '' && $bulan === '') {
    $filename = "ekspor_keseluruhan_arsip_musnah.xlsx";
} else {
    $filenameParts = ["export_dokumen_musnah"];
    if ($tahun !== '') $filenameParts[] = $tahun;
    if ($bulan !== '') $filenameParts[] = $bulan;
    $filename = implode('_', $filenameParts) . ".xlsx";
}

$headers = [
    'NO',
    'NOMOR BERKAS',
    'KODE KLASIFIKASI',
    'NAMA BERKAS',
    'NO. ISI BERKAS',
    'PENCIPTA',
    'TUJUAN SURAT',
    'NO SURAT',
    'URAIAN INFORMASI',
    'URAIAN INFORMASI',
    'TANGGAL SURAT',
    'KURUN WAKTU',
    'JUMLAH',
    'SKKAD',
    'TINGKAT PERKEMBANGAN',
    'BOKS',
    'KETERANGAN',
];

$writer = (new SimpleXlsxWriter('Export Musnah'))
    ->setColumnWidths([
        1 => 6,
        2 => 18,
        3 => 18,
        4 => 28,
        5 => 14,
        6 => 20,
        7 => 20,
        8 => 16,
        9 => 34,
        10 => 34,
        11 => 14,
        12 => 14,
        13 => 10,
        14 => 10,
        15 => 20,
        16 => 10,
        17 => 18,
    ]);

$writer->addRow(['DAFTAR ARSIP YANG DIMUSNAHKAN'], 1)->merge('A1:Q1');
$writer->addRow(['STASIUN METROLOGI KELAS I JUANDA - SIDOARJO'], 2)->merge('A2:Q2');
$writer->addRow([$headerTahun], 2)->merge('A3:Q3');
$writer->addRow([]);
$writer->addRow($headers, 3);

foreach ($rows as $i => $row) {
    $uraian1 = $row['uraian_informasi_1'] ?? ($row['uraian'] ?? '');
    $uraian2 = $row['uraian_informasi_2'] ?? '';
    $tanggalSurat = $row['tanggal_surat'] ?? '';
    $kurunWaktu = $row['kurun_waktu'] ?? '';
    $tanggalLegacy = (string)($row['tanggal'] ?? '');
    if ($tanggalSurat === '' && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tanggalLegacy)) {
        $tanggalSurat = $tanggalLegacy;
    }
    if ($kurunWaktu === '' && $tanggalLegacy !== '' && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tanggalLegacy)) {
        $kurunWaktu = $tanggalLegacy;
    }

    $writer->addRow([
        (string)($i + 1),
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
        $row['keterangan'] ?? ''
    ], 4);
}

$writer
    ->freezePanesAtRow(5)
    ->setAutoFilter('A5:Q5')
    ->send($filename);
