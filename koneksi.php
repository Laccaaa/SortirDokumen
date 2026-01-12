<?php
function koneksiDB() {
    $host = "localhost";
    $port = "5433";
    $dbname = "surat";
    $user = "postgres";
    $password = "cantikitu5";

    // Connection string
    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
    
    // Koneksi ke database
    $db_handle = pg_connect($conn_string);

    if (!$db_handle) {
        // Log error untuk debugging (jangan tampilkan ke user)
        error_log("Koneksi database gagal: " . pg_last_error());
        
        // Return false atau throw exception (jangan die)
        return false;
    }

    return $db_handle; 
}
?>