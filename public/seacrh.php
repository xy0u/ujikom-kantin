<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if (!empty($keyword)) {
     $products = mysqli_query($conn, "SELECT p.*, c.name as category_name 
                                     FROM products p 
                                     LEFT JOIN categories c ON p.category_id = c.id 
                                     WHERE p.name LIKE '%$keyword%' 
                                     OR c.name LIKE '%$keyword%'
                                     ORDER BY p.id DESC");
} else {
     $products = mysqli_query($conn, "SELECT p.*, c.name as category_name 
                                     FROM products p 
                                     LEFT JOIN categories c ON p.category_id = c.id 
                                     ORDER BY p.id DESC LIMIT 10");
}

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Search - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .search-header {
               padding: 150px 5% 50px;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .search-header h1 {
               font-size: 4rem;
               font-weight: 900;
               margin-bottom: 20px;
          }

          .search-box {
               max-width: 600px;
               margin: 0 auto;
               display: flex;
               gap: 10px;
          }

          .search-box input {
               flex: 1;
               padding: 15px 20px;
               border: 3px solid var(--fg);
               background: var(--bg);
               font-size: 1rem;
               outline: none;
          }

          .search-box button {
               padding: 15px 30px;
               border: 3px solid var(--fg);
               background: var(--fg);
               color: var(--bg);
               font-weight: 700;
               cursor: pointer;
               transition: all 0.3s ease;
          }

          .search-box button:hover {
               background: var(--bg);
               color: var(--fg);
          }

          .search-stats {
               text-align: center;
               margin: 30px 0;
               font-size: 0.9rem;
               color: var(--muted);
          }

          .no-results {
               grid-column: 1 / -1;
               padding: 100px 40px;
               text-align: center;
          }

          .no-results h3 {
               font-size: 2rem;
               margin-bottom: 20px;
          }

          .suggestions {
               margin-top: 30px;
               color: var(--muted);
          }

          .suggestions a {
               color: var(--fg);
               text-decoration: underline;
          }

          @media (max-width: 768px) {
               .search-header h1 {
                    font-size: 3rem;
               }

               .search-box {
                    flex-direction: column;
               }
          }
     </style>
</head>

<body>
     <!-- Header -->
     <header>
          <div class="logo">KANTIN</div>
          <nav>
               <a href="index.php">Home</a>
               <a href="index.php#menu">Menu</a>
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="search.php" class="active">Search</a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Search Header -->
          <section class="search-header">
               <h1>SEARCH</h1>
               <form class="search-box" method="GET" action="search.php">
                    <input type="text" name="q" placeholder="Cari menu..." value="<?= htmlspecialchars($keyword) ?>">
                    <button type="submit">🔍</button>
               </form>
          </section>

          <!-- Search Stats -->
          <?php if (!empty($keyword)): ?>
               <div class="search-stats">
                    <?php
                    $total = mysqli_num_rows($products);
                    echo "Ditemukan <strong>$total</strong> hasil untuk <strong>'$keyword'</strong>";
                    ?>
               </div>
          <?php endif; ?>

          <!-- Product Grid -->
          <section class="products" id="menu">
               <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($products)): ?>
                         <div class="card">
                              <img src="uploads/<?= $p['image'] ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                                   loading="lazy" onerror="this.src='assets/img/default.jpg'">
                              <div class="card-content">
                                   <small>
                                        <?= htmlspecialchars($p['category_name'] ?? 'FOOD') ?>
                                   </small>
                                   <h3>
                                        <?= htmlspecialchars($p['name']) ?>
                                   </h3>
                                   <div class="price">
                                        <?= format_rp($p['price']) ?>
                                   </div>
                                   <?php if ($p['stock'] > 0): ?>
                                        <button class="btn-buy addCart" data-id="<?= $p['id'] ?>">
                                             Add to Cart +
                                        </button>
                                   <?php else: ?>
                                        <span class="sold-out">Sold Out</span>
                                   <?php endif; ?>
                              </div>
                         </div>
                    <?php endwhile; ?>
               <?php else: ?>
                    <div class="no-results">
                         <h3>🔍 NO RESULTS FOUND</h3>
                         <p>Maaf, tidak ada menu yang cocok dengan pencarian Anda.</p>

                         <?php if (!empty($keyword)): ?>
                              <div class="suggestions">
                                   <p>Saran:</p>
                                   <ul style="list-style: none; margin-top: 10px;">
                                        <li>• Periksa kembali ejaan kata kunci</li>
                                        <li>• Gunakan kata kunci yang lebih umum</li>
                                        <li>• Coba cari dengan kata kunci lain</li>
                                   </ul>
                                   <p style="margin-top: 20px;">
                                        <a href="index.php#menu">Lihat semua menu →</a>
                                   </p>
                              </div>
                         <?php endif; ?>
                    </div>
               <?php endif; ?>
          </section>
     </main>

     <!-- Footer -->
     <footer>
          <div class="footer-brand">
               KANTIN
               <p>&copy;
                    <?= date('Y') ?> — All rights reserved
               </p>
          </div>
          <div class="footer-links">
               <a href="index.php#menu">Menu</a>
               <a href="cart/index.php">Cart</a>
               <a href="search.php">Search</a>
          </div>
     </footer>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
     <script src="assets/js/public.js?v=<?= time() ?>"></script>
     <script src="assets/js/cart.js?v=<?= time() ?>"></script>
</body>

</html>