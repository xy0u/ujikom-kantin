<?php
session_start();
require "../../core/database.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
     header("Location: ../index.php");
     exit;
}
$total = 0;
?>
<?php include "../components/navbar.php"; ?>

<div class="hero" style="height: auto; padding-top: 150px; padding-bottom: 50px;">
     <h1 class="reveal">CHECKOUT</h1>
     <p>Konfirmasi pesanan anda sebelum pembayaran</p>
</div>

<div class="cart-container" style="padding: 0 5%; margin-bottom: 100px;">
     <div style="background: var(--surface); padding: 40px; border: 1px solid var(--fg);">
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
                    $v_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product_variants WHERE id=$v_id"));
                    if ($v_res) {
                         $price += (int) ($v_res['price_modifier'] ?? $v_res['extra_price']);
                         $variant_name = " (" . $v_res['name'] . ")";
                    }
               }
               $subtotal = $price * $qty;
               $total += $subtotal;
               ?>
               <div
                    style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding: 20px 0;">
                    <div>
                         <h3 style="text-transform: uppercase; font-weight: 800;"><?= $product['name'] ?><?= $variant_name ?>
                         </h3>
                         <small><?= $qty ?> UNIT x Rp <?= number_format($price) ?></small>
                    </div>
                    <div style="font-weight: 900;">Rp <?= number_format($subtotal) ?></div>
               </div>
          <?php endforeach; ?>

          <div style="margin-top: 40px; text-align: right;">
               <small style="letter-spacing: 0.3em; color: var(--muted);">TOTAL TAGIHAN</small>
               <h2 style="font-size: 3rem; font-weight: 900; line-height: 1; margin: 10px 0;">Rp
                    <?= number_format($total) ?></h2>
               <br>
               <a href="process.php" class="btn-buy"
                    style="display: inline-block; width: auto; padding: 20px 60px; font-size: 1rem;">BAYAR SEKARANG</a>
          </div>
     </div>
</div>

<?php include "../components/footer.php"; ?>