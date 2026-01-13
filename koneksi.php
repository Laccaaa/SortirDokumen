<?php

$DB_HOST = "localhost";
$DB_PORT = "5433";
$DB_NAME = "surat";
$DB_USER = "postgres";
$DB_PASS = "cantikitu5";

try {
    $dbhandle = new PDO(
        "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]
    );
} catch (PDOException $e) {
    // Jangan tampilkan detail error ke user
    error_log("DB Connection Error: " . $e->getMessage());
    die("Koneksi database gagal. Silakan hubungi administrator.");
}
