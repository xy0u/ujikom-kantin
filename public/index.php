<?php
session_start();
require "../core/database.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

/* TOP SELLING */
$top = mysqli_query($conn, "
SELECT p.name, p.image, SUM(oi.quantity) as sold
FROM order_items oi
JOIN products p ON oi.product_id=p.id
GROUP BY p.id
ORDER BY sold DESC
LIMIT 3
");
?>
<!DOCTYPE html>
<html>

<head>
     <title>Kantin Modern</title>
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>

     <!-- NAVBAR -->
     <header class="navbar">
          <div class="logo">KANTIN</div>

          <div class="hamburger">
               <span></span>
               <span></span>
               <span></span>
          </div>

          <nav>
               <a href="#home">Home</a>
               <a href="#products">Menu</a>
               <a href="orders.php">Orders</a>
               <a href="cart/index.php">
                    Cart (<span id="cartCount">
                         <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
                    </span>)
               </a>
               <a href="../auth/logout.php">Logout</a>
          </nav>
     </header>

     <!-- HERO -->
     <section class="hero" id="home">
          <div>
               <h1>Eat Different.</h1>
               <p>Modern Taste Experience</p>
               <h2 id="counter" style="margin-top:20px;font-size:40px;">0+</h2>
               <a href="#products" class="btn" style="margin-top:30px;width:auto;">
                    Explore Menu
               </a>
          </div>
     </section>

     <!-- TOP SELLING -->
     <section style="padding:120px 80px;">
          <h2 style="margin-bottom:40px;">Top Selling</h2>

          <div class="products">
               <?php while ($t = mysqli_fetch_assoc($top)): ?>
                    <div class="card">
                         <img src="uploads/<?= $t['image'] ?: 'default.jpg' ?>">
                         <div class="card-body">
                              <h3><?= $t['name'] ?></h3>
                              <p><?= $t['sold'] ?> Sold</p>
                         </div>
                    </div>
               <?php endwhile; ?>
          </div>
     </section>

     <!-- PRODUCT LIST -->
     <section class="products" id="products">

          <?php while ($p = mysqli_fetch_assoc($products)): ?>

               <?php
               $class = "";
               if ($p['is_coming_soon'] == 1)
                    $class = "coming-soon";
               ?>

               <div class="card <?= $class ?>">

                    <!-- STOCK BADGE -->
                    <?php if ($p['is_coming_soon'] == 1): ?>
                         <div class="stock-badge coming">Coming Soon</div>
                    <?php elseif ($p['stock'] > 0): ?>
                         <div class="stock-badge available">Available</div>
                    <?php else: ?>
                         <div class="stock-badge out">Out of Stock</div>
                    <?php endif; ?>

                    <img src="uploads/<?= $p['image'] ?: 'default.jpg' ?>">

                    <div class="card-body">
                         <h3><?= $p['name'] ?></h3>
                         <div class="price">Rp <?= number_format($p['price']) ?></div>

                         <?php
                         $variants = mysqli_query(
                              $conn,
                              "SELECT * FROM product_variants WHERE product_id=" . $p['id']
                         );
                         ?>

                         <?php if ($p['is_coming_soon'] == 0 && $p['stock'] > 0): ?>

                              <!-- VARIANT -->
                              <select class="variant-select" data-id="<?= $p['id'] ?>">
                                   <?php while ($v = mysqli_fetch_assoc($variants)): ?>
                                        <option value="<?= $v['id'] ?>">
                                             <?= $v['name'] ?>
                                             <?php if ($v['price_modifier'] > 0): ?>
                                                  (+Rp <?= number_format($v['price_modifier']) ?>)
                                             <?php endif; ?>
                                        </option>
                                   <?php endwhile; ?>
                              </select>

                              <!-- QTY -->
                              <input type="number" value="1" min="1" max="<?= $p['stock'] ?>" class="qty"
                                   data-id="<?= $p['id'] ?>">

                              <!-- ADD TO CART -->
                              <button class="btn addCart" data-id="<?= $p['id'] ?>">
                                   Add to Cart
                              </button>

                              <!-- WISHLIST -->
                              <button class="btn wishlist" data-id="<?= $p['id'] ?>"
                                   style="margin-top:8px;background:#222;color:white;">
                                   ♡ Wishlist
                              </button>

                         <?php endif; ?>
                    </div>
               </div>

          <?php endwhile; ?>

     </section>

     <script src="assets/js/public.js"></script>
</body>

</html>