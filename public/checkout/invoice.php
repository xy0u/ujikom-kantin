<?php
session_start();
require "../../core/database.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$order_id = (int) $_GET['order'];

$order = mysqli_fetch_assoc(mysqli_query(
     $conn,
     "
SELECT * FROM orders 
WHERE id=$order_id 
AND user_id=" . $_SESSION['user_id']
));

if (!$order) {
     die("Order tidak ditemukan");
}

$items = mysqli_query($conn, "
SELECT oi.*, p.name 
FROM order_items oi
JOIN products p ON oi.product_id=p.id
WHERE oi.order_id=$order_id
");

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=Invoice_Order_" . $order_id . ".txt");

echo "========== INVOICE ==========\n";
echo "Order ID: #" . $order['id'] . "\n";
echo "Status: " . $order['status'] . "\n";
echo "-----------------------------\n";

$total = 0;

while ($i = mysqli_fetch_assoc($items)) {
     $subtotal = $i['price'] * $i['quantity'];
     $total += $subtotal;

     echo $i['name'] . " x " . $i['quantity'] . "\n";
     echo "Subtotal: Rp " . number_format($subtotal) . "\n\n";
}

echo "-----------------------------\n";
echo "TOTAL: Rp " . number_format($total) . "\n";
echo "=============================\n";
exit;
