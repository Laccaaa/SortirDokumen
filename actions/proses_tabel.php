<?php
// actions/proses_tabel.php
require_once __DIR__ . "/../auth/auth_check.php";
require_once __DIR__ . "/../config/koneksi.php";

$error = "";
$rows = [];
$q = trim($_GET["q"] ?? "");

try {
  if ($q !== "") {
    $sql = "SELECT * FROM arsip_dimusnahkan
            WHERE kode_klasifikasi ILIKE :q
               OR nama_berkas ILIKE :q
               OR pencipta ILIKE :q
               OR no_surat ILIKE :q
               OR uraian ILIKE :q
               OR tanggal ILIKE :q
               OR keterangan ILIKE :q
            ORDER BY id DESC";
    $stmt = $dbhandle->prepare($sql);
    $stmt->execute([":q" => "%$q%"]);
  } else {
    $stmt = $dbhandle->prepare("SELECT * FROM arsip_dimusnahkan ORDER BY id DESC");
    $stmt->execute();
  }

  $rows = $stmt->fetchAll();
} catch (Throwable $e) {
  $error = "Query gagal: " . $e->getMessage();
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $postToken = $_POST['token'] ?? '';
  $id = $_POST['id'] ?? '';

  if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
    $error = "Token tidak valid. Coba refresh halaman.";
  } elseif ($id === '' || !ctype_digit((string)$id)) {
    $error = "ID tidak valid untuk dihapus.";
  } else {
    try {
      $del = $dbhandle->prepare("DELETE FROM arsip_dimusnahkan WHERE id = :id");
      $del->execute([':id' => (int)$id]);

      header("Location: /SortirDokumen/pages/tabel_arsip.php?msg=deleted");
      exit;
    } catch (PDOException $e) {
      $error = "Gagal hapus data: " . $e->getMessage();
    }
  }
}

/* ✅ SEARCH + LIST */
$q = trim($_GET['q'] ?? '');

$sql = "
  SELECT
    id,
    kode_klasifikasi,
    nama_berkas,
    no_isi,
    pencipta,
    no_surat,
    uraian,
    tanggal,
    jumlah,
    tingkat,
    lokasi,
    keterangan
  FROM arsip_dimusnahkan
";

$params = [];
if ($q !== '') {
  $sql .= "
    WHERE
      kode_klasifikasi ILIKE :q OR
      nama_berkas      ILIKE :q OR
      no_isi::text     ILIKE :q OR
      pencipta         ILIKE :q OR
      no_surat         ILIKE :q OR
      uraian           ILIKE :q OR
      tanggal::text    ILIKE :q OR
      jumlah::text     ILIKE :q OR
      tingkat          ILIKE :q OR
      lokasi::text     ILIKE :q OR
      keterangan       ILIKE :q
  ";
  $params[':q'] = '%' . $q . '%';
}

$sql .= " ORDER BY created_at DESC";

try {
  if ($q !== '') {
    $stmt = $dbhandle->prepare($sql);
    $stmt->execute($params);
  } else {
    $stmt = $dbhandle->query($sql);
  }
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $error = "Gagal ambil data: " . $e->getMessage();
  $rows  = [];
}
