<?php
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'surat';
$DB_USER = getenv('DB_USER') ?: 'postgres';
$DB_PASS = getenv('DB_PASS') ?: 'cantikitu5';

$conn = pg_connect(
    "host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS"
);

if (!$conn) {
    die("Koneksi database gagal");
}
