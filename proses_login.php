<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header("Location: login.php");
        exit;
    }

    // Koneksi database
    $conn = koneksiDB();
    
    if (!$conn) {
        $_SESSION['error'] = 'Koneksi database gagal!';
        header("Location: login.php");
        exit;
    }

    // Escape input untuk keamanan
    $username_safe = pg_escape_string($conn, $username);

    // Query cari user
    $query = "SELECT * FROM users WHERE username = '$username_safe' LIMIT 1";
    $result = pg_query($conn, $query);

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);

        // Debug: Cek password hash (hapus setelah testing)
        // echo "Input password: $password<br>";
        // echo "Hash di DB: " . $user['password'] . "<br>";
        // echo "Verify result: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE');
        // exit;

        // Verifikasi password
        // Untuk testing sementara, kita cek dua cara:
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Login berhasil - simpan session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Update last login
            $id = (int)$user['id_user'];
            $update = "UPDATE users SET last_login = NOW() WHERE id_user = $id";
            pg_query($conn, $update);

            pg_close($conn);

            // Redirect ke homepage
            header("Location: homepage.php");
            exit;
        } else {
            $_SESSION['error'] = 'Password salah!';
        }
    } else {
        $_SESSION['error'] = 'Username tidak ditemukan!';
    }

    pg_close($conn);
    header("Location: login.php");
    exit;
}

// Jika akses langsung tanpa POST
header("Location: login.php");
exit;
?>