<?php
// proses_tabel.php
require_once "koneksi.php";

$error = "";
$rows  = [];

$q = trim($_GET['q'] ?? '');

// base query
$sql = "
  SELECT
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

// fitur search
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
