<?php
session_start();
require "../../core/database.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
     header("Location: ../index.php");
     exit;
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Hitung total
foreach ($_SESSION['cart'] as $key => $qty) {
     $parts = explode("_", $key);
     $p_id = $parts[0];
     $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id=$p_id"));
     $total += $product['price'] * $qty;
}

mysqli_begin_transaction($conn);

try {
     // Simpan order
     mysqli_query($conn, "INSERT INTO orders (user_id, total, status, created_at) VALUES ($user_id, $total, 'PENDING', NOW())");
     $order_id = mysqli_insert_id($conn);

     // Simpan items dan update stok
     foreach ($_SESSION['cart'] as $key => $qty) {
          $parts = explode("_", $key);
          $p_id = $parts[0];
          $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id=$p_id"));

          mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $p_id, $qty, {$product['price']})");
          mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$p_id");
     }

     mysqli_commit($conn);

     // Hapus cart
     $_SESSION['cart'] = [];

     header("Location: success.php?order=" . $order_id);
     exit;

} catch (Exception $e) {
     mysqli_rollback($conn);
     die("Error: " . $e->getMessage());
}