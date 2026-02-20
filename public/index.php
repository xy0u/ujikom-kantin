<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="theme-color" content="#ffffff">
     <title>KANTIN &mdash; The Digital Experience</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link rel="stylesheet" href="assets/css/public.css?v=<?= time() ?>">
</head>

<body>

     <!-- 18-Column Grid Overlay -->
     <div class="grid-overlay">
          <?php for ($i = 0; $i < 18; $i++)
               echo '<div></div>'; ?>
     </div>

     <!-- Header -->
     <header>
          <div class="logo">KANTIN</div>
          <nav>
               <a href="#menu">Menu</a>
               <a href="cart/index.php">Cart (<?= $cartCount ?>)</a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="hero">
               <h1>KANTIN</h1>
               <p>The Digital Experience</p>
          </section>

          <!-- Marquee -->
          <div class="marquee-band">
               <div class="marquee-inner">
                    <span>Fresh Daily</span>
                    <span>&bull;</span>
                    <span>Handcrafted</span>
                    <span>&bull;</span>
                    <span>Premium Quality</span>
                    <span>&bull;</span>
                    <span>Order Now</span>
                    <span>&bull;</span>
                    <span>Fresh Daily</span>
                    <span>&bull;</span>
                    <span>Handcrafted</span>
                    <span>&bull;</span>
                    <span>Premium Quality</span>
                    <span>&bull;</span>
                    <span>Order Now</span>
                    <span>&bull;</span>
               </div>
          </div>

          <!-- Product Grid -->
          <section class="products" id="menu">
               <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($products)): ?>
                         <div class="card">
                              <img src="uploads/<?= $p['image'] ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                                   loading="lazy">
                              <div class="card-content">
                                   <small>Edition / 2026</small>
                                   <h3><?= htmlspecialchars($p['name']) ?></h3>
                                   <div class="price"><?= format_rp($p['price']) ?></div>
                                   <?php if ($p['stock'] > 0): ?>
                                        <button class="btn-buy addCart" data-id="<?= $p['id'] ?>">
                                             Add to Cart +
                                        </button>
                                   <?php else: ?>
                                        <span class="price"
                                             style="opacity:0.4; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase;">
                                             Sold Out
                                        </span>
                                   <?php endif; ?>
                              </div>
                         </div>
                    <?php endwhile; ?>
               <?php else: ?>
                    <div class="empty-state">
                         <h3>No Items</h3>
                         <p>Menu belum tersedia saat ini</p>
                    </div>
               <?php endif; ?>
          </section>
     </main>

     <!-- Footer -->
     <footer>
          <div class="footer-brand">
               KANTIN
               <p>&copy; <?= date('Y') ?> &mdash; All rights reserved</p>
          </div>
          <div class="footer-links">
               <a href="#menu">Menu</a>
               <a href="cart/index.php">Cart</a>
               <a href="../auth/logout.php">Exit</a>
          </div>
     </footer>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
     <script src="assets/js/public.js?v=<?= time() ?>"></script>
</body>

</html>