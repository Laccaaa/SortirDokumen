<?php
session_start();

// Jika sudah login, redirect ke homepage
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistem Arsip Dokumen</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.login-container {
    width: 100%;
    max-width: 420px;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

.login-header {
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    color: white;
    padding: 35px 30px;
    text-align: center;
}

.login-header h1 {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 8px;
}

.login-header p {
    font-size: 14px;
    opacity: 0.9;
}

.login-body {
    padding: 35px 30px;
}

.alert-error {
    background: #ffe6e6;
    color: #b30000;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
    border: 1px solid #f5c2c2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: #2f3a5f;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    opacity: 0.5;
}

.form-group input {
    width: 100%;
    height: 48px;
    padding: 0 14px 0 44px;
    font-size: 14px;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #4a6cf7;
    box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
}

.btn-login {
    width: 100%;
    height: 48px;
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(74, 108, 247, 0.4);
}

.btn-login:active {
    transform: translateY(0);
}

.login-footer {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    font-size: 13px;
    color: #6c757d;
}

@media (max-width: 480px) {
    .login-container {
        max-width: 100%;
    }
    
    .login-header {
        padding: 30px 20px;
    }
    
    .login-body {
        padding: 30px 20px;
    }
}
</style>
</head>

<body>
<div class="login-container">
    <div class="login-header">
        <h1>🔐 Login</h1>
        <p>Sistem Arsip Dokumen Digital</p>
    </div>

    <div class="login-body">
        <?php if ($error): ?>
        <div class="alert-error">
            <span>❌</span>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form action="/SortirDokumen/auth/proses_login.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">MASUK</button>
        </form>
    </div>

    <div class="login-footer">
        © 2026 Sistem Arsip Dokumen
    </div>
</div>
</body>
</html>