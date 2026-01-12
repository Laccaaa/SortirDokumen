<?php
session_start();

// Include koneksi database
require_once "koneksi.php";

/**
 * Fungsi untuk validasi input data arsip
 */
function validasiInput($data) {
    $errors = [];
    
    // Validasi field wajib
    if (empty($data['kode_klasifikasi'])) {
        $errors[] = "Kode Klasifikasi wajib diisi!";
    }
    
    if (empty($data['nama_berkas'])) {
        $errors[] = "Nama Berkas wajib diisi!";
    }
    
    if (empty($data['no_isi'])) {
        $errors[] = "No. Isi wajib diisi!";
    }
    
    // Validasi tipe data numerik
    if (!empty($data['no_isi']) && !is_numeric($data['no_isi'])) {
        $errors[] = "No. Isi harus berupa angka!";
    }
    
    // Validasi format tanggal (jika diisi)
    if (!empty($data['tanggal'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['tanggal']);
        if (!$date || $date->format('Y-m-d') !== $data['tanggal']) {
            $errors[] = "Format tanggal tidak valid!";
        }
    }
    
    return $errors;
}

/**
 * Fungsi untuk sanitasi input
 */
function sanitasiInput($data, $conn) {
    $sanitized = [];
    
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            // Escape string untuk PostgreSQL
            $sanitized[$key] = pg_escape_string($conn, trim($value));
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}

/**
 * Fungsi untuk insert data arsip ke database
 */
function insertArsip($data, $conn) {
    // Sanitasi data
    $clean = sanitasiInput($data, $conn);
    
    // Siapkan nilai untuk query (gunakan NULL untuk field kosong)
    $no_isi = !empty($clean['no_isi']) ? (int)$clean['no_isi'] : 0;
    $pencipta = !empty($clean['pencipta']) ? "'{$clean['pencipta']}'" : "NULL";
    $no_surat = !empty($clean['no_surat']) ? "'{$clean['no_surat']}'" : "NULL";
    $uraian = !empty($clean['uraian']) ? "'{$clean['uraian']}'" : "NULL";
    $tanggal = !empty($clean['tanggal']) ? "'{$clean['tanggal']}'" : "NULL";
    $jumlah = !empty($clean['jumlah']) ? "'{$clean['jumlah']}'" : "NULL";
    $tingkat = !empty($clean['tingkat']) ? "'{$clean['tingkat']}'" : "NULL";
    $lokasi = !empty($clean['lokasi']) ? "'{$clean['lokasi']}'" : "NULL";
    $keterangan = !empty($clean['keterangan']) ? "'{$clean['keterangan']}'" : "NULL";
    
    // Query INSERT
    $query = "INSERT INTO arsip_dimusnahkan (
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
        keterangan,
        created_at
    ) VALUES (
        '{$clean['kode_klasifikasi']}',
        '{$clean['nama_berkas']}',
        $no_isi,
        $pencipta,
        $no_surat,
        $uraian,
        $tanggal,
        $jumlah,
        $tingkat,
        $lokasi,
        $keterangan,
        CURRENT_TIMESTAMP
    ) RETURNING id";
    
    // Eksekusi query
    $result = pg_query($conn, $query);
    
    if (!$result) {
        error_log("Error insert arsip: " . pg_last_error($conn));
        return false;
    }
    
    // Ambil ID yang baru di-insert
    $row = pg_fetch_assoc($result);
    return $row['id'];
}

/**
 * Fungsi untuk mendapatkan nomor berkas terakhir
 */
function getLastNoBerkas($conn) {
    $query = "SELECT COALESCE(MAX(no_berkas), 0) as last_no FROM arsip_dimusnahkan";
    $result = pg_query($conn, $query);
    
    if (!$result) {
        return 0;
    }
    
    $row = pg_fetch_assoc($result);
    return (int)$row['last_no'];
}

// ========== PROSES FORM SUBMIT ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];
    
    // Koneksi ke database
    $conn = koneksiDB();
    
    if (!$conn) {
        $response['message'] = "Koneksi database gagal!";
        $_SESSION['error'] = $response['message'];
        header("Location: input_arsip.php");
        exit();
    }
    
    // Ambil data dari form
    $data = [
        'kode_klasifikasi' => $_POST['kode_klasifikasi'] ?? '',
        'nama_berkas' => $_POST['nama_berkas'] ?? '',
        'no_isi' => $_POST['no_isi'] ?? '',
        'pencipta' => $_POST['pencipta'] ?? '',
        'no_surat' => $_POST['no_surat'] ?? '',
        'tanggal' => $_POST['tanggal'] ?? '',
        'uraian' => $_POST['uraian'] ?? '',
        'jumlah' => $_POST['jumlah'] ?? '',
        'tingkat' => $_POST['tingkat'] ?? '',
        'lokasi' => $_POST['lokasi'] ?? '',
        'keterangan' => $_POST['keterangan'] ?? ''
    ];
    
    // Validasi input
    $errors = validasiInput($data);
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = implode('<br>', $errors);
        $_SESSION['error'] = $response['message'];
        $_SESSION['old_input'] = $data;
        
        pg_close($conn);
        header("Location: input_arsip.php");
        exit();
    }
    
    // Insert data ke database
    $insert_id = insertArsip($data, $conn);
    
    if ($insert_id) {
        $response['success'] = true;
        $response['message'] = "Data arsip berhasil disimpan dengan ID: " . $insert_id;
        $_SESSION['success'] = $response['message'];
        
        // Hapus old input jika berhasil
        unset($_SESSION['old_input']);
    } else {
        $response['message'] = "Gagal menyimpan data ke database!";
        $_SESSION['error'] = $response['message'];
        $_SESSION['old_input'] = $data;
    }
    
    pg_close($conn);
    
    // Redirect kembali ke form
    header("Location: input_arsip.php");
    exit();
}

// Jika bukan POST request, redirect ke form
header("Location: input_arsip.php");
exit();
?>