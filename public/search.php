<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';

$q = sanitize($_GET['q'] ?? '');
$products = [];
$cartCount = getCartCount();
$wishlist = $_SESSION['wishlist'] ?? [];

if ($q) {
     $escaped = mysqli_real_escape_string($conn, $q);
     $products_q = mysqli_query($conn, "
        SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE '%$escaped%' OR p.description LIKE '%$escaped%'
        ORDER BY p.name ASC
        LIMIT 50
    ");
     while ($row = mysqli_fetch_assoc($products_q))
          $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title><?= $q ? "Cari: $q — " : 'Cari Produk — ' ?>Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>
     <?php include '../components/navbar.php'; ?>
     <div class="toast-container" id="toastContainer"></div>

     <main class="section">
          <div class="container">
               <div class="page-header">
                    <h1 class="page-title">
                         <?= $q ? 'Hasil Pencarian: <span class="text-accent">' . e($q) . '</span>' : 'Cari Produk' ?>
                    </h1>
                    <?php if ($q): ?>
                         <p class="page-subtitle"><?= count($products) ?> produk ditemukan</p>
                    <?php endif; ?>
               </div>

               <!-- Search Form -->
               <div class="search-hero">
                    <form method="GET" class="search-form">
                         <div class="search-input-wrap">
                              <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                   stroke="currentColor" stroke-width="2">
                                   <circle cx="11" cy="11" r="8" />
                                   <path d="m21 21-4.35-4.35" />
                              </svg>
                              <input type="text" name="q" class="search-input" placeholder="Cari makanan, minuman..."
                                   value="<?= e($q) ?>" autofocus>
                              <?php if ($q): ?>
                                   <a href="/public/search.php" class="search-clear">×</a>
                              <?php endif; ?>
                         </div>
                         <button type="submit" class="btn btn--primary btn--lg">Cari</button>
                    </form>
               </div>

               <?php if ($q && empty($products)): ?>
                    <div class="empty-state">
                         <div class="empty-state-icon">
                              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="1.5">
                                   <circle cx="11" cy="11" r="8" />
                                   <path d="m21 21-4.35-4.35" />
                                   <line x1="8" y1="11" x2="14" y2="11" />
                              </svg>
                         </div>
                         <h3 class="empty-state-title">Produk tidak ditemukan</h3>
                         <p class="empty-state-desc">Coba kata kunci yang berbeda atau telusuri semua menu.</p>
                         <a href="/public/index.php" class="btn btn--primary">Lihat Semua Menu</a>
                    </div>
               <?php elseif (!empty($products)): ?>
                    <div class="products-grid">
                         <?php foreach ($products as $p): ?>
                              <?php $imgSrc = $p['image'] ? '/public/assets/uploads/' . $p['image'] : '/public/assets/img/placeholder.png'; ?>
                              <article class="product-card">
                                   <div class="product-card__img-wrap">
                                        <a href="/public/product.php?id=<?= $p['id'] ?>">
                                             <img src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>" class="product-card__img"
                                                  loading="lazy">
                                        </a>
                                        <?php if (isLoggedIn()): ?>
                                             <button
                                                  class="product-card__wishlist <?= in_array($p['id'], $wishlist) ? 'active' : '' ?>"
                                                  onclick="toggleWishlist(<?= $p['id'] ?>, this)">
                                                  <svg width="18" height="18" viewBox="0 0 24 24"
                                                       fill="<?= in_array($p['id'], $wishlist) ? 'currentColor' : 'none' ?>"
                                                       stroke="currentColor" stroke-width="2">
                                                       <path
                                                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                                  </svg>
                                             </button>
                                        <?php endif; ?>
                                   </div>
                                   <div class="product-card__body">
                                        <div class="product-card__category"><?= e($p['category_name'] ?? '') ?></div>
                                        <h3 class="product-card__name"><a
                                                  href="/public/product.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a></h3>
                                        <div class="product-card__footer">
                                             <div class="product-card__price"><?= formatRupiah($p['price']) ?></div>
                                             <?php if ($p['stock'] > 0): ?>
                                                  <button class="btn btn--primary btn--sm"
                                                       onclick="addToCart(<?= $p['id'] ?>, '<?= e(addslashes($p['name'])) ?>', <?= $p['price'] ?>)">+
                                                       Keranjang</button>
                                             <?php else: ?>
                                                  <span class="badge badge--danger">Habis</span>
                                             <?php endif; ?>
                                        </div>
                                   </div>
                              </article>
                         <?php endforeach; ?>
                    </div>
               <?php endif; ?>
          </div>
     </main>

     <?php include '../components/footer.php'; ?>
     <script src="assets/js/public.js"></script>
</body>

</html>