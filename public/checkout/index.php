<?php
session_start();
require "../../core/database.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
     header("Location: ../index.php");
     exit;
}

$total = 0;
?>
<!DOCTYPE html>
<html>

<head>
     <title>Checkout</title>
     <link rel="stylesheet" href="../assets/css/public.css">
</head>

<body>

     <div class="cart-container">

          <h2>Checkout Summary</h2>

          <?php foreach ($_SESSION['cart'] as $id => $qty):

               $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
               if (!$product)
                    continue;

               $subtotal = $product['price'] * $qty;
               $total += $subtotal;
               ?>

               <div class="cart-item">
                    <div><?= $product['name'] ?> (<?= $qty ?>)</div>
                    <div>Rp <?= number_format($subtotal) ?></div>
               </div>

          <?php endforeach; ?>

          <div class="checkout-box">
               <h3>Total Payment</h3>
               <h2>Rp <?= number_format($total) ?></h2>
               <a href="process.php" onclick="showLoading()" class="btn">
                    Pay with Xendit
               </a>
          </div>

     </div>

     <script src="../assets/js/public.js"></script>
</body>

</html>