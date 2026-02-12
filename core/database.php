<?php
$conn = mysqli_connect("localhost", "root", "", "ujikom_kantin_digital");
if (!$conn) {
     die("Koneksi gagal: " . mysqli_connect_error());
}
