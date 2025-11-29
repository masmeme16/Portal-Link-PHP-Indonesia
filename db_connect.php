<?php
$host = 'localhost'; // Ganti dengan nama host Anda
$user = 'root'; // Ganti dengan username database Anda
$pass = ''; // Ganti dengan password database Anda
$db_name = 'shortlink_db'; // Ganti dengan nama database Anda

// Membuat koneksi baru
$conn = new mysqli($host, $user, $pass, $db_name);

// Mengecek koneksi
if ($conn->connect_error) {
    // Hentikan eksekusi dan tampilkan pesan error jika koneksi gagal
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Opsional: Atur charset ke utf8mb4 untuk mendukung karakter khusus
$conn->set_charset("utf8mb4");

// Di titik ini, $conn adalah objek koneksi yang siap digunakan.
?>