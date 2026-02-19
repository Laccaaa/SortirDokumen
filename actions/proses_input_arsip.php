<?php
session_start();
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

function validasiInput($data) {
    $errors = [];

    if (empty($data['kode_klasifikasi'])) {
        $errors[] = "Kode Klasifikasi wajib diisi!";
    }
    if (empty($data['nama_berkas'])) {
        $errors[] = "Nama Berkas wajib diisi!";
    }
    if (empty($data['no_isi'])) {
        $errors[] = "No. Isi wajib diisi!";
    }
    if (!empty($data['no_isi']) && !is_numeric($data['no_isi'])) {
        $errors[] = "No. Isi harus berupa angka!";
    }
    if (!empty($data['tanggal'])) {
    if (preg_match('/^\d{4}$/', $data['tanggal'])) {
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['tanggal'])) {
        $d = DateTime::createFromFormat('Y-m-d', $data['tanggal']);
        if (!$d || $d->format('Y-m-d') !== $data['tanggal']) {
            $errors[] = "Format tanggal tidak valid!";
        }
    } else {
        $errors[] = "Tanggal harus format YYYY atau YYYY-MM-DD";
    }
}


    return $errors;
}

function insertArsip($data) {
    global $dbhandle;

    $sql = "
        INSERT INTO arsip_dimusnahkan (
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
            :kode_klasifikasi,
            :nama_berkas,
            :no_isi,
            :pencipta,
            :no_surat,
            :uraian,
            :tanggal,
            :jumlah,
            :tingkat,
            :lokasi,
            :keterangan,
            CURRENT_TIMESTAMP
        )
        RETURNING id
    ";

    $stmt = $dbhandle->prepare($sql);
    $stmt->execute([
        ':kode_klasifikasi' => $data['kode_klasifikasi'],
        ':nama_berkas'      => $data['nama_berkas'],
        ':no_isi'           => (int)$data['no_isi'],
        ':pencipta'         => $data['pencipta'] ?: null,
        ':no_surat'         => $data['no_surat'] ?: null,
        ':uraian'           => $data['uraian'] ?: null,
        ':tanggal'          => $data['tanggal'] ?: null,
        ':jumlah'           => $data['jumlah'] ?: null,
        ':tingkat'          => $data['tingkat'] ?: null,
        ':lokasi'           => $data['lokasi'] ?: null,
        ':keterangan'       => $data['keterangan'] ?: null
    ]);

    return $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'kode_klasifikasi' => trim($_POST['kode_klasifikasi'] ?? ''),
        'nama_berkas'      => trim($_POST['nama_berkas'] ?? ''),
        'no_isi'           => trim($_POST['no_isi'] ?? ''),
        'pencipta'         => trim($_POST['pencipta'] ?? ''),
        'no_surat'         => trim($_POST['no_surat'] ?? ''),
        'tanggal'          => trim($_POST['tanggal'] ?? ''),
        'uraian'           => trim($_POST['uraian'] ?? ''),
        'jumlah'           => trim($_POST['jumlah'] ?? ''),
        'tingkat'          => trim($_POST['tingkat'] ?? ''),
        'lokasi'           => trim($_POST['lokasi'] ?? ''),
        'keterangan'       => trim($_POST['keterangan'] ?? '')
    ];

    $no_isi = intval($data['no_isi']);

    if ($no_isi < 1) {
        $_SESSION['error'] = 'Nomor isi harus berupa angka lebih dari 0';
        $_SESSION['error_field'] = 'no_isi';
        $_SESSION['old_input'] = $data;
        header("Location: input_arsip.php");
        exit;
    }

    if (!empty($data['tanggal'])) {
        if (!preg_match('/^\d{4}(-\d{2}-\d{2})?$/', $data['tanggal'])) {
            $_SESSION['error'] = 'Tanggal harus diisi dalam format YYYY atau YYYY-MM-DD';
            $_SESSION['error_field'] = 'tanggal';
            $_SESSION['old_input'] = $data;
            header("Location: input_arsip.php");
            exit;
        }
    }

    // VALIDASI
    $errors = validasiInput($data);

    if ($errors) {
        $_SESSION['error'] = "Masih ada bagian yang belum diisi. Silakan periksa kembali.";

        // tentukan field pertama yang error
        if (empty($data['kode_klasifikasi'])) {
            $_SESSION['error'] = "Kode klasifikasi belum diisi";
            $_SESSION['error_field'] = 'kode_klasifikasi';
        } elseif (empty($data['nama_berkas'])) {
            $_SESSION['error'] = "Nama berkas belum diisi";
            $_SESSION['error_field'] = 'nama_berkas';
        } elseif (empty($data['no_isi'])) {
            $_SESSION['error'] = "Nomor isi belum diisi";
            $_SESSION['error_field'] = 'no_isi';
        }

        $_SESSION['old_input'] = $data;
        header("Location: input_arsip.php");
        exit;
    }

    // INSERT
    try {
        $id = insertArsip($data);

        $_SESSION['success'] = "Data arsip pemusnahan berhasil disimpan";
        unset($_SESSION['old_input']);

    } catch (PDOException $e) {
        error_log("Insert arsip error: " . $e->getMessage());
        $_SESSION['error'] = "Gagal menyimpan data ke database!";
        $_SESSION['old_input'] = $data;
    }

    header("Location: input_arsip.php");
    exit;
}

// AKSES LANGSUNG
header("Location: input_arsip.php");
exit;