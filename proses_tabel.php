<?php
// backend_arsip_dimusnahkan.php
require_once "koneksi.php";

$error = "";
$rows  = [];

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
  ORDER BY created_at DESC
";

try {
  // pakai $dbhandle (PDO) dari koneksi.php
  $stmt = $dbhandle->query($sql);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $error = "Gagal ambil data: " . $e->getMessage();
  $rows  = [];
}
