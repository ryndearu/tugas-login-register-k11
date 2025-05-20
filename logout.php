<?php
session_start();

// Hapus semua session variables
$_SESSION = array();

// destroy the session
session_destroy();

// lalu redirect ke halaman login dengan pesan sukses
header("location: login.php?logout=success");

exit;
?> 