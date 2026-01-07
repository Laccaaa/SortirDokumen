<?php
function koneksiDB() {
    $host = "localhost";
    $port = "5433";
    $dbname = "surat";
    $user = "postgres";
    $password = "cantikitu5";

    $db_handle = pg_connect(
        "host=$host port=$port dbname=$dbname user=$user password=$password"
    );

    if (!$db_handle) {
        die("Koneksi gagal: " . pg_last_error());
    }

    return $db_handle; 
}
?>