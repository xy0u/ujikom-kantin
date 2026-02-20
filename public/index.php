<?php
// FILE: public/index.php
// Ini adalah halaman utama setelah user melewati welcome.php

session_start();
require "../core/database.php";
require "../core/helpers.php";

// Ambil semua produk dari database
$products = mysqli_query($conn, "SELECT p.*, c.name as category_name 
                                 FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 WHERE p.status = 'available' OR p.status IS NULL
                                 ORDER BY p.id DESC");

// Hitung jumlah cart dan wishlist
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;

// Set flag untuk navbar (biar home jadi active)
$isHome = true;

// Include navbar
include "../components/navbar.php";
?>

<main>
     <!-- Hero Section -->
     <section class="hero">
          <h1 class="glitch">KANTIN</h1>
          <p class="reveal">The Digital Experience</p>
     </section>

     <!-- Marquee Band (running text) -->
     <div class="marquee-band">
          <div class="marquee-inner marquee">
               <?php
               $texts = ['FRESH DAILY', 'HANDCRAFTED', 'PREMIUM QUALITY', 'ORDER NOW', 'FAST DELIVERY'];
               for ($i = 0; $i < 3; $i++):
                    foreach ($texts as $text):
                         ?>
                         <span><?= $text ?></span>
                         <span>●</span>
                    <?php
                    endforeach;
               endfor;
               ?>
          </div>
     </div>

     <!-- Product Grid -->
     <section class="products" id="menu">
          <?php if (mysqli_num_rows($products) > 0): ?>
               <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <div class="card card-animate">
                         <img src="uploads/<?= $p['image'] ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                              loading="lazy" onerror="this.src='assets/img/default.jpg'">
                         <div class="card-content">
                              <small><?= htmlspecialchars($p['category_name'] ?? 'FOOD & BEVERAGE') ?></small>
                              <h3><?= htmlspecialchars($p['name']) ?></h3>
                              <div class="price"><?= format_rp($p['price']) ?></div>

                              <?php if ($p['stock'] > 0): ?>
                                   <!-- Tombol Add to Cart -->
                                   <button class="btn-buy addCart" data-id="<?= $p['id'] ?>">
                                        ADD TO CART →
                                   </button>

                                   <!-- Tombol Add to Wishlist -->
                                   <button class="btn-buy addWishlist" data-id="<?= $p['id'] ?>"
                                        style="background: transparent; color: var(--fg); margin-top: 10px;">
                                        ♥ WISHLIST
                                   </button>
                              <?php else: ?>
                                   <!-- Jika stok habis -->
                                   <span class="sold-out">SOLD OUT</span>
                              <?php endif; ?>
                         </div>
                    </div>
               <?php endwhile; ?>
          <?php else: ?>
               <!-- Jika tidak ada produk -->
               <div class="empty-state">
                    <h3>NO ITEMS AVAILABLE</h3>
                    <p>Menu belum tersedia saat ini. Silakan cek kembali nanti.</p>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                         <a href="../admin/products.php" class="btn-buy" style="margin-top: 20px;">TAMBAH PRODUK</a>
                    <?php endif; ?>
               </div>
          <?php endif; ?>
     </section>
</main>

<!-- Toast Notification (untuk notifikasi add to cart) -->
<div class="toast" id="toast">ADDED TO CART</div>

<!-- Footer -->
<?php include "../components/footer.php"; ?>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="assets/js/public.js?v=<?= time() ?>"></script>
<script src="assets/js/cart.js?v=<?= time() ?>"></script>

</body>

</html>