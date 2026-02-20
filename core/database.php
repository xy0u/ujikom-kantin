<?php
// Pengaturan Koneksi
$host = "localhost";
$user = "root";
$pass = "";
$db = "ujikom_kantin_digital";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
     die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Set Zona Waktu Indonesia (Penting untuk laporan pesanan)
date_default_timezone_set('Asia/Jakarta');

// Set Charset agar karakter simbol seperti Rp atau lainnya terbaca benar
mysqli_set_charset($conn, "utf8mb4");