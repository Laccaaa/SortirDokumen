<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: /SortirDokumen/auth/login.php");
    exit;
}

// Timeout 30 menit
$timeout = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: /SortirDokumen/auth/login.php");
    exit;
}

$_SESSION['last_activity'] = time();