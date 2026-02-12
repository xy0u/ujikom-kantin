<?php
session_start();
require "../../core/database.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$total = 0;
?>
<!DOCTYPE html>
<html>

<head>
     <title>Cart</title>
     <link rel="stylesheet" href="../assets/css/public.css">
</head>

<body>

     <header class="navbar">
          <div class="logo">KANTIN</div>
          <a href="../index.php">Back</a>
     </header>

     <div class="cart-container">

          <h2>Your Cart</h2>

          <?php if (empty($_SESSION['cart'])): ?>
               <p>Cart kosong.</p>
          <?php else: ?>

               <?php foreach ($_SESSION['cart'] as $key => $qty):

                    $parts = explode("_", $key);
                    $product_id = $parts[0];
                    $variant_id = $parts[1];

                    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id"));
                    $variant = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product_variants WHERE id=$variant_id"));

                    $variant_name = $variant ? $variant['name'] : "";

                    $subtotal = $product['price'] * $qty;
                    $total += $subtotal;
                    ?>

                    <div class="cart-item">
                         <div>
                              <strong><?= $product['name'] ?></strong><br>
                              <?= $qty ?> x Rp <?= number_format($product['price']) ?>
                         </div>

                         <div>
                              Rp <?= number_format($subtotal) ?><br>
                              <a href="remove.php?key=<?= $key ?>" class="btn">Remove</a>
                         </div>
                    </div>

               <?php endforeach; ?>

               <div class="checkout-box">
                    <h3>Total: Rp <?= number_format($total) ?></h3>
                    <a href="../checkout/index.php" class="btn">Proceed to Checkout</a>
               </div>

          <?php endif; ?>

          <strong>
               <?= $product['name'] ?>
          </strong>
          <?php if ($variant_name): ?>
               (
               <?= $variant_name ?>)
          <?php endif; ?>
          <?php if (empty($_SESSION['cart'])): ?>
               <div class="empty-state">
                    Your cart is empty.
               </div>
          <?php endif; ?>



     </div>

     <script src="../assets/js/public.js"></script>
</body>

</html>