<?php
require_once "bootstrap.php";

if (isset($_SESSION['user_id'])) {
  header("Location: homepage.php");
  exit;
}

header("Location: login.php");
exit;
