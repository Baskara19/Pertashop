<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'operator') {
    header("Location: ../login.php");
    exit();
}
?>
