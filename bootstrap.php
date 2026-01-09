<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/php_sessions');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
