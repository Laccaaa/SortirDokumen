<?php
session_start();
$dbhandle = require __DIR__ . "/../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input kosong (per-field)
    if (empty($username) || empty($password)) {
        $fieldErrors = [];
        if (empty($username)) {
            $fieldErrors['username'] = 'Kolom ini wajib diisi';
        }
        if (empty($password)) {
            $fieldErrors['password'] = 'Kolom ini wajib diisi';
        }
        $_SESSION['field_errors'] = $fieldErrors;
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

                header("Location: /SortirDokumen/pages/homepage.php");
                exit;
            } else {
                $_SESSION['error'] = 'Username atau password salah';
            }

        } else {
            $_SESSION['error'] = 'Username atau password salah';
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
