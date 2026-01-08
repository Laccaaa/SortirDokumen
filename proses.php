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

$pattern = '/^
(?:([a-zA-Z]\.[A-Z])\/)?     
([A-Z]{2,5})                 
\.([0-9]{2}\.[0-9]{2})       
\/([0-9]{3})                 
\/([A-Z]{2,10})              
\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)
\/([0-9]{4})
$/x';

if (!preg_match($pattern, $nomor_surat, $m)) {
    $_SESSION['error_nomor'] = "Format nomor surat tidak valid";
    header("Location: index.php");
    exit;
}

$kode_utama    = $m[2];
$subkode       = $m[3];
$nomor_urut    = $m[4];
$unit_pengirim = $m[5];
$bulan_romawi  = $m[6];
$tahun         = $m[7];

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

$uploadDir = "uploads/$tahun/$bulan/$kode_utama/$subkode/";
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
    header("Location: index.php");
    exit;
}

$_SESSION['status'] = 'error';
$_SESSION['pesan']  = 'Gagal menyimpan data';
header("Location: index.php");
exit;
