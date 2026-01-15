<?php
session_start();
require_once "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header("Location: login.php");
        exit;
    }

    /* ===============================
       QUERY USER (PDO)
       =============================== */
    try {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $dbhandle->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch();

        if ($user) {

            // Validasi password (hash / plaintext lama)
            if (password_verify($password, $user['password']) || $password === $user['password']) {

                // Login berhasil - simpan session
                $_SESSION['user_id']       = $user['id_user'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['nama_lengkap']  = $user['nama_lengkap'];
                $_SESSION['role']          = $user['role'];
                $_SESSION['last_activity'] = time();

                /* ===============================
                   UPDATE LAST LOGIN (PDO)
                   =============================== */
                $sqlUpdate = "UPDATE users SET last_login = NOW() WHERE id_user = :id_user";
                $stmtUpdate = $dbhandle->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':id_user' => (int)$user['id_user']
                ]);

                header("Location: homepage.php");
                exit;
            } else {
                $_SESSION['error'] = 'Password salah!';
            }

        } else {
            $_SESSION['error'] = 'Username tidak ditemukan!';
        }

    } catch (PDOException $e) {
        // Jangan bocorkan error ke user
        error_log("Login DB Error: " . $e->getMessage());
        $_SESSION['error'] = 'Terjadi kesalahan sistem!';
    }

    header("Location: login.php");
    exit;
}

// Jika akses langsung tanpa POST
header("Location: login.php");
exit;
