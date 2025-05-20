<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?error=Silakan masuk terlebih dahulu");
    exit;
}

// Koneksi database
require_once "includes/config.php";

// Ambil data user dari database
$userId = $_SESSION["id"];
$sql = "SELECT * FROM users WHERE id = ?";

if($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
        } else {
            echo "Terjadi kesalahan saat mengambil data pengguna.";
            exit;
        }
    } else {
        echo "Terjadi kesalahan saat mengambil data.";
        exit;
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Terjadi kesalahan saat menyiapkan query.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-info {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-info p {
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .profile-info p:last-child {
            border-bottom: none;
        }
        .profile-info span {
            font-weight: bold;
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
            border-radius: 4px;
            margin: 5px;
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
        <div class="form-container profile-container">
            <div class="profile-header">
                <h2>Profil Saya</h2>
            </div>
            
            <div class="profile-info">
                <p><span>Username:</span> <?php echo htmlspecialchars($user["username"]); ?></p>
                <p><span>Email:</span> <?php echo htmlspecialchars($user["email"]); ?></p>
                <p><span>Tanggal Registrasi:</span> <?php echo htmlspecialchars($user["created_at"]); ?></p>
                <p><span>Role:</span> <?php echo htmlspecialchars($_SESSION["role"]); ?></p>
            </div>                <div class="button-group">
                <a href="dashboard.php" class="btn btn-back">Kembali ke Dashboard</a>
                <a href="change_password.php" class="btn">Ubah Password</a>
            </div>
        </div>
    </div>
</body>
</html>
