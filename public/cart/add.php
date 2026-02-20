<?php
session_start();
require "../../core/database.php";

$id = (int) ($_POST['id'] ?? 0);
$qty = (isset($_POST['qty']) && (int) $_POST['qty'] > 0) ? (int) $_POST['qty'] : 1;

if ($id > 0) {
     // Check stock
     $check = mysqli_query($conn, "SELECT stock FROM products WHERE id = $id");
     $product = mysqli_fetch_assoc($check);

     if ($product && $product['stock'] >= $qty) {
          if (!isset($_SESSION['cart']))
               $_SESSION['cart'] = [];

          $key = $id . "_0";

          if (isset($_SESSION['cart'][$key])) {
               $_SESSION['cart'][$key] += $qty;
          } else {
               $_SESSION['cart'][$key] = $qty;
          }

          echo array_sum($_SESSION['cart']);
     } else {
          echo "0"; // Stock tidak cukup
     }
} else {
     echo "0";
}
exit;
?>