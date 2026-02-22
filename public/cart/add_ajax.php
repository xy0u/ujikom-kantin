<?php
session_start();
require "../../core/database.php";

header('Content-Type: application/json');

$product_id = (int) ($_POST['product_id'] ?? 0);
$qty = max(1, (int) ($_POST['qty'] ?? 1));

if ($product_id <= 0) {
     echo json_encode(['success' => false, 'message' => 'Produk tidak valid']);
     exit;
}

$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id"));

if (!$product) {
     echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
     exit;
}

if ($product['stock'] < $qty) {
     echo json_encode(['success' => false, 'message' => 'Stok tidak cukup']);
     exit;
}

$key = $product_id . "_0";

if (!isset($_SESSION['cart'])) {
     $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$key])) {
     $_SESSION['cart'][$key] += $qty;
} else {
     $_SESSION['cart'][$key] = $qty;
}

echo json_encode([
     'success' => true,
     'message' => 'Produk ditambahkan',
     'cartCount' => array_sum($_SESSION['cart'])
]);
?>