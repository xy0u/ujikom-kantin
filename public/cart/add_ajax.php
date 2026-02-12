<?php
session_start();
require "../../core/database.php";

$product_id = (int) $_POST['product_id'];
$variant_id = (int) $_POST['variant_id'];
$qty = (int) $_POST['qty'];

if ($product_id <= 0 || $qty <= 0) {
     echo 0;
     exit;
}

$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id"));
if (!$product || $product['is_coming_soon'] == 1) {
     echo 0;
     exit;
}

if ($qty > $product['stock']) {
     $qty = $product['stock'];
}

$key = $product_id . "_" . $variant_id;

if (!isset($_SESSION['cart']))
     $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$key])) {
     $_SESSION['cart'][$key] += $qty;
} else {
     $_SESSION['cart'][$key] = $qty;
}

echo array_sum($_SESSION['cart']);
