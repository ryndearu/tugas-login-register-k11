<?php
// Konfigurasi Database
$host = "localhost";
$username = "root";
$password = "";
$database = "login_register_k11";

// Membuat koneksi
$link = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$link) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?> 