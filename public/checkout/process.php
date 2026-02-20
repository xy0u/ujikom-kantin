<?php
session_start();
require "../../core/database.php";
require "../../core/helpers.php";

// Check if vendor autoload exists
if (file_exists("../../vendor/autoload.php")) {
     require "../../vendor/autoload.php";
     require "../../kunci/xendit.php";
     $useXendit = true;
} else {
     $useXendit = false;
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
     header("Location: ../index.php");
     exit;
}

$user_id = (int) $_SESSION['user_id'];
$total = 0;

mysqli_begin_transaction($conn);
try {
     // Calculate Total & Validate Stock
     foreach ($_SESSION['cart'] as $key => $qty) {
          $parts = explode("_", $key);
          $p_id = (int) $parts[0];

          $res = mysqli_query($conn, "SELECT price, stock FROM products WHERE id=$p_id FOR UPDATE");
          $p = mysqli_fetch_assoc($res);

          if (!$p || $p['stock'] < $qty) {
               throw new Exception("Stok tidak mencukupi untuk salah satu produk!");
          }

          $price = $p['price'];
          if (isset($parts[1]) && (int) $parts[1] > 0) {
               $v_id = (int) $parts[1];
               $v_res = mysqli_query($conn, "SELECT extra_price FROM product_variants WHERE id=$v_id");
               $v = mysqli_fetch_assoc($v_res);
               if ($v)
                    $price += $v['extra_price'];
          }
          $total += $price * $qty;
     }

     // Save Order
     mysqli_query($conn, "INSERT INTO orders (user_id, total, status, created_at) VALUES ($user_id, $total, 'PENDING', NOW())");
     $order_id = mysqli_insert_id($conn);

     // Save Order Items & Update Stock
     foreach ($_SESSION['cart'] as $key => $qty) {
          $parts = explode("_", $key);
          $p_id = (int) $parts[0];
          $v_id = isset($parts[1]) ? (int) $parts[1] : 0;

          $p_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id=$p_id"));
          $final_price = $p_data['price'];

          if ($v_id > 0) {
               $v_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT extra_price FROM product_variants WHERE id=$v_id"));
               $final_price += $v_data['extra_price'];
          }

          mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) 
                            VALUES ($order_id, $p_id, $v_id, $qty, $final_price)");
          mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$p_id");
     }

     mysqli_commit($conn);

     // Clear cart
     $_SESSION['cart'] = [];

     // Redirect to success page
     header("Location: success.php?order=" . $order_id);
     exit;

} catch (Exception $e) {
     mysqli_rollback($conn);
     ?>
     <!DOCTYPE html>
     <html>

     <head>
          <title>Checkout Failed</title>
          <link rel="stylesheet" href="../assets/css/public.css">
     </head>

     <body>
          <?php include "../components/navbar.php"; ?>

          <main>
               <section class="hero" style="height: 60vh;">
                    <h1>ERROR</h1>
                    <p><?= $e->getMessage() ?></p>
                    <a href="../cart/index.php" class="btn-buy" style="margin-top: 20px;">Back to Cart</a>
               </section>
          </main>

          <?php include "../components/footer.php"; ?>
     </body>

     </html>
     <?php
}
?>