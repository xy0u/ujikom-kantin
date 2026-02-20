<?php
session_start();

require "../../core/database.php";
require "../../vendor/autoload.php";
require "../../kunci/xendit.php";

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
     header("Location: ../index.php");
     exit;
}

Configuration::setXenditKey(XENDIT_API_KEY);

$user_id = (int) $_SESSION['user_id'];
$total = 0;

mysqli_begin_transaction($conn);

try {

     /* ===== HITUNG TOTAL ===== */
     foreach ($_SESSION['cart'] as $key => $qty) {

          $parts = explode("_", $key);
          $product_id = (int) $parts[0];
          $variant_id = (int) $parts[1];

          $product = mysqli_fetch_assoc(
               mysqli_query(
                    $conn,
                    "SELECT * FROM products WHERE id=$product_id FOR UPDATE"
               )
          );

          if (!$product)
               throw new Exception("Product tidak ditemukan");

          if ($product['stock'] < $qty)
               throw new Exception("Stock tidak cukup");

          $price = (int) $product['price'];

          if ($variant_id > 0) {
               $variant = mysqli_fetch_assoc(
                    mysqli_query(
                         $conn,
                         "SELECT * FROM product_variants 
                 WHERE id=$variant_id AND product_id=$product_id"
                    )
               );

               if ($variant) {
                    $price += (int) $variant['extra_price']; // FIX
               }
          }

          $total += $price * $qty;
     }

     /* ===== INSERT ORDER ===== */
     mysqli_query($conn, "
        INSERT INTO orders(user_id,total,status)
        VALUES($user_id,$total,'PENDING')
    ");

     $order_id = mysqli_insert_id($conn);

     /* ===== INSERT ITEMS ===== */
     foreach ($_SESSION['cart'] as $key => $qty) {

          $parts = explode("_", $key);

          $product_id = (int) $parts[0];
          $variant_id = (int) $parts[1];

          $product = mysqli_fetch_assoc(
               mysqli_query(
                    $conn,
                    "SELECT * FROM products WHERE id=$product_id"
               )
          );

          $price = (int) $product['price'];

          if ($variant_id > 0) {
               $variant = mysqli_fetch_assoc(
                    mysqli_query(
                         $conn,
                         "SELECT * FROM product_variants 
                 WHERE id=$variant_id"
                    )
               );
               if ($variant) {
                    $price += (int) $variant['extra_price']; // FIX
               }
          }

          mysqli_query($conn, "
            INSERT INTO order_items
            (order_id,product_id,variant_id,quantity,price)
            VALUES($order_id,$product_id,$variant_id,$qty,$price)
        ");

          mysqli_query($conn, "
            UPDATE products
            SET stock = stock - $qty
            WHERE id=$product_id
        ");
     }

     /* ===== CREATE INVOICE ===== */
     $api = new InvoiceApi();

     $user = mysqli_fetch_assoc(
          mysqli_query(
               $conn,
               "SELECT email FROM users WHERE id=$user_id"
          )
     );

     $protocol = (!empty($_SERVER['HTTPS']) &&
          $_SERVER['HTTPS'] !== 'off')
          ? "https://" : "http://";

     $base = $protocol . $_SERVER['HTTP_HOST']
          . dirname(dirname($_SERVER['PHP_SELF']));

     $request = new CreateInvoiceRequest([
          'external_id' => 'ORDER-' . $order_id,
          'amount' => $total,
          'payer_email' => $user['email'],
          'description' => 'Kantin Digital Payment',
          'success_redirect_url' => $base . '/success.php?order=' . $order_id,
          'failure_redirect_url' => $base . '/../orders.php'
     ]);

     $invoice = $api->createInvoice($request);

     mysqli_query($conn, "
        UPDATE orders
        SET invoice_id='" . $invoice['id'] . "'
        WHERE id=$order_id
    ");

     mysqli_commit($conn);

     unset($_SESSION['cart']);

     header("Location: " . $invoice['invoice_url']);
     exit;

} catch (Exception $e) {

     mysqli_rollback($conn);

     echo "<h3>Checkout Error:</h3>";
     echo $e->getMessage();
}
