<?php
session_start();

$key = $_GET['key'] ?? '';

if (isset($_SESSION['cart'][$key])) {
     unset($_SESSION['cart'][$key]);

     // Jika cart kosong, hapus session cart
     if (empty($_SESSION['cart'])) {
          unset($_SESSION['cart']);
     }
}

header("Location: index.php");
exit;