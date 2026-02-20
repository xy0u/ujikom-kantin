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

          <?php foreach ($_SESSION['cart'] as $key => $qty):

               /* ===== FIX CART KEY ===== */
               $parts = explode("_", $key);

               $product_id = (int) $parts[0];
               $variant_id = isset($parts[1]) ? (int) $parts[1] : 0;

               $product = mysqli_fetch_assoc(
                    mysqli_query(
                         $conn,
                         "SELECT * FROM products WHERE id=$product_id"
                    )
               );

               if (!$product)
                    continue;

               $price = (int) $product['price'];
               $variant_name = "";

               if ($variant_id > 0) {

                    $variant = mysqli_fetch_assoc(
                         mysqli_query(
                              $conn,
                              "SELECT * FROM product_variants WHERE id=$variant_id"
                         )
                    );

                    if ($variant) {
                         $price += (int) $variant['price_modifier'];
                         $variant_name = $variant['name'];
                    }
               }

               $subtotal = $price * $qty;
               $total += $subtotal;
               ?>

               <div class="cart-item">
                    <div>
                         <strong><?= $product['name'] ?></strong>
                                          <?php if ($variant_name): ?>
                              (<?= $variant_name ?>)
                                          <?php endif; ?>
                         <br>
                                          <?= $qty ?> x Rp <?= number_format($price) ?>
                    </div>

                    <div>
                         Rp <?= number_format($subtotal) ?>
                    </div>
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