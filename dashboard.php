<?php
session_start(); // Wajib: Mulai session

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Jika belum login, redirect ke halaman login
    header("location: login.php");
    exit;
}

// Ambil data dari session
$username = $_SESSION['username'];
$user_id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Selamat Datang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #e9ecef;
        }

        .jumbotron {
            padding: 2rem 1rem;
            margin-bottom: 2rem;
            background-color: #ffffff;
            border-radius: .3rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- <a class="navbar-brand" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a> -->
            <a href="menu.php" class="btn btn-lg btn-success mt-3">
                <i class="fa-solid fa-link"></i> Kelola Menu
            </a>
            <a href="link.php" class="btn btn-lg btn-warning mt-3">
                <i class="fas fa-list-alt"></i> Kelola Link
            </a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4 text-primary"><i class="fas fa-hands-clapping"></i> Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="lead">Ini adalah aplikasi portal SDN Pacarkembang IV Surabaya.</p>
            <hr class="my-4">
            <p>Anda telah login. Username Anda adalah: **<?php echo htmlspecialchars($username); ?>**</p>
            <p class="mb-0">Pastikan untuk selalu menjaga keamanan akun Anda dan keluar setelah selesai menggunakan sistem.</p>
        </div>

    </div>
    <center>
        <div>Created by (GANTI INI)</div>
    </center>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>