<?php
session_start();
require "../../core/database.php";

header('Content-Type: application/json');

$product_id = (int) ($_POST['product_id'] ?? 0);
$variant_id = (int) ($_POST['variant_id'] ?? 0);
$qty = (isset($_POST['qty']) && (int) $_POST['qty'] > 0) ? (int) $_POST['qty'] : 1;

if ($product_id <= 0) {
     echo json_encode(['success' => false, 'message' => 'Invalid product']);
     exit;
}

// Cek stok
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
     echo json_encode(['success' => false, 'message' => 'Product not found']);
     exit;
}

if ($product['stock'] < $qty) {
     echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
     exit;
}

$key = $product_id . "_" . $variant_id;

if (!isset($_SESSION['cart'])) {
     $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$key])) {
     $_SESSION['cart'][$key] += $qty;
} else {
     $_SESSION['cart'][$key] = $qty;
}

$cartCount = array_sum($_SESSION['cart']);

echo json_encode([
     'success' => true,
     'message' => 'Product added to cart',
     'cartCount' => $cartCount
]);
exit;