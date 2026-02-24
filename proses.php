<?php
ob_start();
session_start();
$dbhandle = require __DIR__ . "/config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if (!isset($_POST['jenis_surat'], $_POST['nomor_surat'], $_FILES['fileInput'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Form tidak lengkap. Pastikan semua field wajib terisi.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$jenis_surat = $_POST['jenis_surat'];
$nomor_surat = trim($_POST['nomor_surat']);
$file = $_FILES['fileInput'];

$maxSize = 5 * 1024 * 1024; // 5MB

if (($file['size'] ?? 0) > $maxSize) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Ukuran file maksimal 5MB.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$kode_klasifikasi = trim($_POST['kode_klasifikasi'] ?? '');
$unit_pengolah    = trim($_POST['unit_pengolah'] ?? '');
$nama_berkas       = trim($_POST['nama_berkas'] ?? '');
$nomor_isi         = trim($_POST['no_isi'] ?? '');
$pencipta_arsip    = trim($_POST['pencipta'] ?? '');
$tujuan_surat      = trim($_POST['tujuan_surat'] ?? '');
$perihal           = trim($_POST['perihal'] ?? '');
$uraian_informasi  = trim($_POST['uraian'] ?? '');
$tanggal_surat     = trim($_POST['tanggal_surat'] ?? '');
$jumlah            = trim($_POST['jumlah'] ?? '');
$lokasi_simpan     = trim($_POST['lokasi'] ?? '');
$tingkat           = trim($_POST['tingkat'] ?? '');
$keterangan        = trim($_POST['keterangan'] ?? '');
$skkad             = trim($_POST['skkad'] ?? '');
$jra_aktif         = trim($_POST['jra_aktif'] ?? '');
$jra_inaktif       = trim($_POST['jra_inaktif'] ?? '');
$nasib             = trim($_POST['nasib'] ?? '');

if ($kode_klasifikasi === '' || $nama_berkas === '') {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Form tidak lengkap. Pastikan semua field wajib terisi.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if ($jenis_surat === '' || $nomor_surat === '') {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Form tidak lengkap. Pastikan semua field wajib terisi.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'File surat belum dipilih!';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if ($jra_aktif === '' || !ctype_digit($jra_aktif)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Input JRA Aktif tidak valid.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if ($jra_inaktif === '' || !ctype_digit($jra_inaktif)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Input JRA Inaktif tidak valid.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

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
    header("Location: /SortirDokumen/pages/form.php");
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
    preg_match('/^([A-Z]+)\.(.+)$/', $bagian_1, $kode_match);
    if ($kode_match) {
        $kode_utama = $kode_match[1];      // PL
        $subkode    = $kode_match[2];      // 01.00 (TANPA prefix PL)
    } else {
        $kode_utama = $bagian_1;
        $subkode    = '';
    }
    $nomor_urut    = $bagian_2;
    $unit_pengirim = $bagian_3;
} else {
    if (strpos($bagian_1, '.') !== false) {
        preg_match('/^([A-Z]+)\.(.+)$/', $bagian_1, $kode_match);
        if ($kode_match) {
            $kode_utama = $kode_match[1]; 
            $subkode    = $kode_match[2]; 
        } else {
            $kode_utama = $bagian_1;
            $subkode    = '';
        }
        $nomor_urut    = $bagian_2; 
        $unit_pengirim = $bagian_3; 
    } else {
        $kode_utama    = $bagian_1;
        $subkode       = ''; 
        $nomor_urut    = $bagian_2;
        $unit_pengirim = $bagian_3; 
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

$uploadDir = __DIR__ . "/uploads/$tahun/$bulan/$kode_utama/";
if (!empty($subkode)) {
    $uploadDir .= "$subkode/";
}
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Folder upload tidak bisa dibuat. Periksa izin folder uploads.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if (!is_writable($uploadDir)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Folder upload tidak punya izin tulis. Periksa izin folder uploads.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_ext = ['pdf','jpg','jpeg','png'];
if (!in_array($ext, $allowed_ext)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Format file tidak didukung. Gunakan PDF atau gambar (JPG, JPEG, PNG).';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$allowed_mime = [
    'application/pdf',
    'image/jpeg',
    'image/png'
];
$mime = mime_content_type($file['tmp_name']);
if (!in_array($mime, $allowed_mime)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Tipe file tidak valid. Gunakan PDF atau gambar (JPG, JPEG, PNG).';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$nama_asli = basename($file['name']);
$nama_file = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nama_asli);

/* auto rename jika duplikat */
$nama_file = namaFileUnik($uploadDir, $nama_file);
$destPath = $uploadDir . $nama_file;
$destPathRel = "uploads/$tahun/$bulan/$kode_utama/" . (!empty($subkode) ? "$subkode/" : "") . $nama_file;

if (!is_uploaded_file($file['tmp_name'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Upload tidak valid. Silakan pilih ulang file.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Gagal upload file. Periksa izin folder uploads.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$sql = "
INSERT INTO surat (
    jenis_surat, nomor_surat, kode_utama, subkode,
    nomor_urut, unit_pengirim, bulan, tahun,
    nama_file, path_file,
    unit_pengolah, nama_berkas, nomor_isi, pencipta_arsip,
    tujuan_surat, perihal, uraian_informasi, tanggal_surat_kurun,
    jumlah, lokasi_simpan, tingkat, keterangan,
    skkad, jra_aktif, jra_inaktif, nasib
) VALUES (
    ?,?,?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?
)";

try {
    $stmt = $dbhandle->prepare($sql);
    $result = $stmt->execute([
        $jenis_surat,
        $nomor_surat,
        $kode_utama,
        $subkode,
        $nomor_urut,
        $unit_pengirim,
        $bulan,
        $tahun,
        $nama_file,
        $destPathRel,
        $unit_pengolah !== '' ? $unit_pengolah : null,
        $nama_berkas !== '' ? $nama_berkas : null,
        $nomor_isi !== '' ? $nomor_isi : null,
        $pencipta_arsip !== '' ? $pencipta_arsip : null,
        $tujuan_surat !== '' ? $tujuan_surat : null,
        $perihal !== '' ? $perihal : null,
        $uraian_informasi !== '' ? $uraian_informasi : null,
        $tanggal_surat !== '' ? $tanggal_surat : null,
        $jumlah !== '' ? $jumlah : null,
        $lokasi_simpan !== '' ? $lokasi_simpan : null,
        $tingkat !== '' ? $tingkat : null,
        $keterangan !== '' ? $keterangan : null,
        $skkad !== '' ? $skkad : null,
        $jra_aktif !== '' ? $jra_aktif : null,
        $jra_inaktif !== '' ? $jra_inaktif : null,
        $nasib !== '' ? $nasib : null
    ]);
} catch (Throwable $e) {
    error_log("Insert surat gagal: " . $e->getMessage());
    $_SESSION['status'] = 'error';
    $_SESSION['pesan']  = 'Gagal menyimpan data. Pastikan semua kolom sudah terisi dengan benar.';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

if ($result) {
    $_SESSION['status'] = 'success';
    $_SESSION['pesan']  = 'Data berhasil disimpan';
    header("Location: /SortirDokumen/pages/form.php");
    exit;
}

$_SESSION['status'] = 'error';
$_SESSION['pesan']  = 'Gagal menyimpan data';
header("Location: /SortirDokumen/pages/form.php");
exit;
