<?php
// auth_check.php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Opsional: timeout session (30 menit)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: /SortirDokumen/auth/login.php");
    exit;
}

$_SESSION['last_activity'] = time();
?>