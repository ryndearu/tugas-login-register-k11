<?php
require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username)) {
        $error = "Silakan masukkan nama pengguna.";
    } elseif (empty($email)) {
        $error = "Silakan masukkan email.";
    } elseif (empty($password)) {
        $error = "Silakan masukkan kata sandi.";
    } elseif (empty($confirm_password)) {
        $error = "Silakan konfirmasi kata sandi.";
    } elseif ($password !== $confirm_password) {
        $error = "Kata sandi dan konfirmasi kata sandi tidak cocok.";
    } else {
        // Cek apakah nama pengguna atau email sudah ada
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_email);
            $param_username = $username;
            $param_email = $email;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $error = "Nama pengguna atau email sudah digunakan.";
                } else {
                    // Menyimpan data pengguna baru
                    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
                        $param_username = $username;
                        $param_email = $email;
                        $param_password = $password; 

                        if (mysqli_stmt_execute($stmt)) {
                            // Registrasi berhasil, redirect ke halaman login
                            header("location: ../login.php?success=1");
                            exit();
                        } else {
                            $error = "Terjadi kesalahan. Silakan coba lagi nanti.";
                        }
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi nanti.";
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Jika ada kesalahan, redirect ke halaman registrasi dengan pesan kesalahan
if (!empty($error)) {
    header("location: ../register.php?error=" . urlencode($error));
    exit();
}

mysqli_close($link);
?> 