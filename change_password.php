<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?error=Silakan masuk terlebih dahulu");
    exit;
}

// Koneksi database
require_once "includes/config.php";

// Definisi variabel dan set dengan nilai kosong
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";
$success_message = "";

// Proses form saat data disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validasi current password
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Silakan masukkan password saat ini.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }
    
    // Validasi new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Silakan masukkan password baru.";     
    } elseif(strlen(trim($_POST["new_password"])) < 4) {
        $new_password_err = "Password harus memiliki minimal 4 karakter.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validasi confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Silakan konfirmasi password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password tidak cocok.";
        }
    }
    
    // Cek input errors sebelum update database
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        
        // Siapkan statement untuk select
        $sql = "SELECT password FROM users WHERE id = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables ke prepared statement
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameter
            $param_id = $_SESSION["id"];
            
            // Eksekusi prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Simpan hasil
                mysqli_stmt_store_result($stmt);
                
                // Cek apakah user ada, jika ya verifikasi password
                if (mysqli_stmt_num_rows($stmt) == 1) {                      // Bind hasil variabel
                    mysqli_stmt_bind_result($stmt, $stored_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Verifikasi password plaintext (membandingkan langsung)
                        if ($current_password === $stored_password) {
                            // Password benar, update password user
                            $sql = "UPDATE users SET password = ? WHERE id = ?";
                            
                            if ($stmt_update = mysqli_prepare($link, $sql)) {
                                // Bind variables ke prepared statement
                                mysqli_stmt_bind_param($stmt_update, "si", $param_password, $param_id);
                                
                                // Set parameters
                                // Simpan password baru sebagai plaintext
                                $param_password = $new_password;
                                $param_id = $_SESSION["id"];
                                
                                // Eksekusi prepared statement
                                if (mysqli_stmt_execute($stmt_update)) {
                                    // Password berhasil diperbarui, tampilkan pesan sukses
                                    $success_message = "Password berhasil diperbarui.";
                                    $current_password = $new_password = $confirm_password = "";
                                } else {
                                    echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
                                }
                                
                                // Close statement
                                mysqli_stmt_close($stmt_update);
                            }
                        } else {
                            // Password salah
                            $current_password_err = "Password saat ini tidak valid.";
                        }
                    }
                }
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .password-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .password-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error-text {
            color: #e53935;
            font-size: 14px;
            margin-top: 5px;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2196f3;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            margin: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1976d2;
        }
        .btn-back {
            background-color: #757575;
        }
        .btn-back:hover {
            background-color: #616161;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container password-container">
            <div class="password-header">
                <h2>Ubah Password</h2>
                <p>Silakan isi formulir di bawah ini untuk mengubah password Anda.</p>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" value="<?php echo $current_password; ?>">
                    <span class="error-text"><?php echo $current_password_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" value="<?php echo $new_password; ?>">
                    <span class="error-text"><?php echo $new_password_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                    <span class="error-text"><?php echo $confirm_password_err; ?></span>
                </div>
                
                <div class="button-group">
                    <input type="submit" class="btn" value="Ubah Password">
                    <a href="dashboard.php" class="btn btn-back">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
