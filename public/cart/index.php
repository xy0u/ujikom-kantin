<?php
session_start();
require "../../core/database.php";
require "../../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$total = 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <title>Cart - Kantin Digital</title>
     <link rel="stylesheet" href="../assets/css/public.css">
</head>

<body>
     <?php include "../components/navbar.php"; ?>

     <main>
          <section class="hero" style="height: 40vh; min-height: 300px;">
               <h1>CART</h1>
               <p>Your selected items</p>
          </section>

          <div class="cart-container" style="padding: 0 5% 100px;">
               <div style="background: var(--surface); padding: 40px; border: 3px solid var(--fg);">
                    <?php if (empty($_SESSION['cart'])): ?>
                         <div class="empty-state">
                              <h3>Cart is Empty</h3>
                              <p>Add some items to your cart</p>
                              <a href="../index.php#menu" class="btn-buy" style="margin-top: 20px;">Browse Menu</a>
                         </div>
                    <?php else: ?>
                         <?php foreach ($_SESSION['cart'] as $key => $qty):
                              $parts = explode("_", $key);
                              $p_id = (int) $parts[0];
                              $v_id = isset($parts[1]) ? (int) $parts[1] : 0;

                              $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$p_id"));
                              if (!$product)
                                   continue;

                              $price = (int) $product['price'];
                              $variant_name = "";

                              if ($v_id > 0) {
                                   $variant = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product_variants WHERE id=$v_id"));
                                   if ($variant) {
                                        $price += (int) $variant['extra_price'];
                                        $variant_name = " (" . $variant['name'] . ")";
                                   }
                              }

                              $subtotal = $price * $qty;
                              $total += $subtotal;
                              ?>
                              <div
                                   style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 20px 0;">
                                   <div>
                                        <h3 style="text-transform: uppercase; font-weight: 800;">
                                             <?= htmlspecialchars($product['name']) ?>          <?= $variant_name ?></h3>
                                        <small><?= $qty ?> x <?= format_rp($price) ?></small>
                                   </div>
                                   <div style="text-align: right;">
                                        <div style="font-weight: 900; font-size: 1.2rem;"><?= format_rp($subtotal) ?></div>
                                        <a href="remove.php?key=<?= urlencode($key) ?>"
                                             style="color: #ef4444; font-size: 0.8rem; text-decoration: none; border-bottom: 1px solid transparent;"
                                             onclick="return confirm('Remove item from cart?')">Remove</a>
                                   </div>
                              </div>
                         <?php endforeach; ?>

                         <div style="margin-top: 40px; text-align: right;">
                              <small style="letter-spacing: 0.3em; color: var(--muted);">TOTAL</small>
                              <h2 style="font-size: 3rem; font-weight: 900; line-height: 1; margin: 10px 0;">
                                   <?= format_rp($total) ?></h2>
                              <a href="../checkout/index.php" class="btn-buy"
                                   style="display: inline-block; padding: 16px 48px;">Proceed to Checkout</a>
                         </div>
                    <?php endif; ?>
               </div>
          </div>
     </main>

     <?php include "../components/footer.php"; ?>
     <script src="../assets/js/public.js"></script>
</body>

</html>