<?php
session_start();

require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username)) {
        $error = "Silakan masukkan nama pengguna.";
    } elseif (empty($password)) {
        $error = "Silakan masukkan kata sandi.";
    } else {
        $sql = "SELECT id, username, role, password FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $role, $hashed_password); // Hashed password adalah password yang disimpan di database dalam bentuk plain text
                    if (mysqli_stmt_fetch($stmt)) {
                        // Verifikasi password
                        if ($password === $hashed_password) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;

                            header("location: ../dashboard.php");
                        } else {
                            $error = "Nama pengguna atau kata sandi tidak valid.";
                        }
                    }
                } else {
                    $error = "Nama pengguna atau kata sandi tidak valid.";
                }
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi nanti.";
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Jika ada kesalahan, redirect ke halaman login dengan pesan kesalahan
if (!empty($error)) {
    header("location: ../login.php?error=" . urlencode($error));
    exit();
}

mysqli_close($link);
?> 