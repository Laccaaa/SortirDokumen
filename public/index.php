<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Sudah login → ke homepage
    header("Location: ../pages/homepage.php");
    exit;
} else {
    // Belum login → ke login
    header("Location: ../auth/login.php");
    exit;
}
?>