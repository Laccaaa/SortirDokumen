<?php
session_start();
include "koneksi.php";

$koneksi = koneksiDB();

if (!isset($_POST['jenis_surat'], $_POST['nomor_surat'], $_FILES['fileInput'])) {
    die("Form tidak lengkap");
}

$jenis_surat = $_POST['jenis_surat'];
$nomor_surat = trim($_POST['nomor_surat']);
$file = $_FILES['fileInput'];

/**
 * Regex Dinamis untuk Nomor Surat
 * Format: [prefix_opsional]/[kode]/[nomor]/[unit]/[BULAN_ROMAWI]/[TAHUN]
 * 
 * Yang WAJIB:
 * - Bulan Romawi (I-XII)
 * - Tahun (4 digit)
 * 
 * Sisanya bebas dan fleksibel
 */
$pattern = '/^
    (?:([a-zA-Z0-9.]+)\/)?      # Group 1: Prefix opsional (e.B, ME.002, dll)
    ([^\/]+)                     # Group 2: Bagian pertama
    \/([^\/]+)                   # Group 3: Bagian kedua
    \/([^\/]+)                   # Group 4: Bagian ketiga
    \/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)  # Group 5: BULAN ROMAWI (WAJIB)
    \/(\d{4})                    # Group 6: TAHUN 4 digit (WAJIB)
$/x';

if (!preg_match($pattern, $nomor_surat, $matches)) {
    $_SESSION['error_nomor'] = "Format nomor surat tidak valid";
    $_SESSION['old_jenis_surat'] = $jenis_surat;
    $_SESSION['old_nomor_surat'] = $nomor_surat;
    header("Location: form.php");
    exit;
}

// Parsing hasil regex
$prefix_kode   = $matches[1] ?? '';
$bagian_1      = $matches[2];
$bagian_2      = $matches[3];
$bagian_3      = $matches[4];
$bulan_romawi  = $matches[5];
$tahun         = $matches[6];

// Parsing cerdas untuk menentukan struktur
if (!empty($prefix_kode)) {
    // Format: prefix/kode/nomor/unit/bulan/tahun
    // Contoh: e.B/PL.01.00/001/KSUB/V/2024
    // Split kode menjadi huruf dan angka
    preg_match('/^([A-Z]+)\.(.+)$/', $bagian_1, $kode_match);
    if ($kode_match) {
        $kode_utama = $kode_match[1];      // PL
        $subkode    = $kode_match[2];      // 01.00 (TANPA prefix PL)
    } else {
        $kode_utama = $bagian_1;
        $subkode    = '';
    }
    $nomor_urut    = $bagian_2;            // 001
    $unit_pengirim = $bagian_3;            // KSUB
} else {
    // Format: kode/nomor/unit/bulan/tahun
    // Contoh: ME.002/003/DI/XII/2016
    if (strpos($bagian_1, '.') !== false) {
        preg_match('/^([A-Z]+)\.(.+)$/', $bagian_1, $kode_match);
        if ($kode_match) {
            $kode_utama = $kode_match[1];  // ME
            $subkode    = $kode_match[2];  // 002 (TANPA prefix ME)
        } else {
            $kode_utama = $bagian_1;
            $subkode    = '';
        }
        $nomor_urut    = $bagian_2;        // 003
        $unit_pengirim = $bagian_3;        // DI
    } else {
        // Format tanpa titik: ABC/123/DEF/bulan/tahun
        $kode_utama    = $bagian_1;        // ABC
        $subkode       = '';               // kosong
        $nomor_urut    = $bagian_2;        // 123
        $unit_pengirim = $bagian_3;        // DEF
    }
}

function romawiKeBulan($r) {
    return [
        'I'=>'Januari','II'=>'Februari','III'=>'Maret','IV'=>'April',
        'V'=>'Mei','VI'=>'Juni','VII'=>'Juli','VIII'=>'Agustus',
        'IX'=>'September','X'=>'Oktober','XI'=>'November','XII'=>'Desember'
    ][$r] ?? null;
}

function namaFileUnik($dir, $filename) {
    $pathinfo = pathinfo($filename);
    $nama = $pathinfo['filename'];
    $ext  = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
    $counter = 1;
    $newName = $filename;
    while (file_exists($dir . $newName)) {
        $newName = $nama . " ($counter)" . $ext;
        $counter++;
    }
    return $newName;
}

$bulan = romawiKeBulan($bulan_romawi);
if (!$bulan) die("Bulan tidak valid");

if ($file['error'] !== 0) die("File upload error");

$uploadDir = "uploads/$tahun/$bulan/$kode_utama/";
if (!empty($subkode)) {
    $uploadDir .= "$subkode/";
}
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_ext = ['pdf','doc','docx','jpg','jpeg','png'];
if (!in_array($ext, $allowed_ext)) {
    die("Ekstensi file tidak diizinkan");
}

$allowed_mime = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
];
$mime = mime_content_type($file['tmp_name']);
if (!in_array($mime, $allowed_mime)) {
    die("Tipe file tidak valid");
}

$nama_asli = basename($file['name']);
$nama_file = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nama_asli);

/* auto rename jika duplikat */
$nama_file = namaFileUnik($uploadDir, $nama_file);
$destPath = $uploadDir . $nama_file;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    die("Gagal upload file");
}

$sql = "
INSERT INTO surat (
    jenis_surat, nomor_surat, kode_utama, subkode,
    nomor_urut, unit_pengirim, bulan, tahun,
    nama_file, path_file
) VALUES (
    $1,$2,$3,$4,$5,$6,$7,$8,$9,$10
)";

$result = pg_query_params($koneksi, $sql, [
    $jenis_surat,
    $nomor_surat,
    $kode_utama,
    $subkode,
    $nomor_urut,
    $unit_pengirim,
    $bulan,
    $tahun,
    $nama_file,
    $destPath
]);

if ($result) {
    $_SESSION['status'] = 'success';
    $_SESSION['pesan']  = 'Data berhasil disimpan';
    header("Location: form.php");
    exit;
}

$_SESSION['status'] = 'error';
$_SESSION['pesan']  = 'Gagal menyimpan data';
header("Location: form.php");
exit;