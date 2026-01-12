<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Sudah login → ke homepage
    header("Location: homepage.php");
    exit;
} else {
    // Belum login → ke login
    header("Location: login.php");
    exit;
}
?>
