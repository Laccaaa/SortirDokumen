<?php
// koneksi.php (PDO)
$host = "127.0.0.1";
$port = "5432";
$dbname = "surat";
$user = "postgres";
$pass = "muhammad";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("Koneksi DB gagal: " . $e->getMessage());
}
