<?php
$conn = new mysqli(
     'localhost',          // host
     'root',               // username (default XAMPP)
     '',                   // password (default XAMPP kosong)
     'ujikom_kantin_digital' // ← sesuaikan nama database ini
);

if ($conn->connect_error) {
     die('Koneksi database gagal: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');