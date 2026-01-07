<?php
include "koneksi.php";

$koneksi = koneksiDB();

// Cek form dan file
if(isset($_POST['jenis_surat'], $_POST['nomor_surat'], $_FILES['fileInput'])) {

    $jenis_surat = $_POST['jenis_surat'];
    $nomor_surat = $_POST['nomor_surat'];

    $file = $_FILES['fileInput'];

    if($file['error'] === 0) {
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];

        // folder upload
        $uploadDir = "uploads/";
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // pindahkan file
        $destPath = $uploadDir . $fileName;
        if(move_uploaded_file($fileTmp, $destPath)) {

            // insert ke database
            $sql = "INSERT INTO surat (jenis_surat, nomor_surat, nama_file, path_file)
                    VALUES ('$jenis_surat', '$nomor_surat', '$fileName', '$destPath')";

            $result = pg_query($koneksi, $sql);
            if($result) {
                echo "Data berhasil disimpan!";
            } else {
                echo "Error: " . pg_last_error($koneksi);
            }

        } else {
            echo "Gagal memindahkan file!";
        }

    } else {
        echo "File belum dipilih atau error upload!";
    }

} else {
    echo "Form belum lengkap!";
}
?>
