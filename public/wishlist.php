<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

// Inisialisasi wishlist
if (!isset($_SESSION['wishlist'])) {
     $_SESSION['wishlist'] = [];
}

// Tambah ke wishlist
if (isset($_POST['add'])) {
     $id = (int) $_POST['id'];
     if (!in_array($id, $_SESSION['wishlist'])) {
          $_SESSION['wishlist'][] = $id;
     }
     header("Location: wishlist.php");
     exit;
}

// Hapus dari wishlist
if (isset($_GET['remove'])) {
     $id = (int) $_GET['remove'];
     $key = array_search($id, $_SESSION['wishlist']);
     if ($key !== false) {
          unset($_SESSION['wishlist'][$key]);
          $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
     }
     header("Location: wishlist.php");
     exit;
}

// Clear wishlist
if (isset($_GET['clear'])) {
     $_SESSION['wishlist'] = [];
     header("Location: wishlist.php");
     exit;
}

// Ambil produk wishlist
$products = [];
if (!empty($_SESSION['wishlist'])) {
     $ids = implode(',', $_SESSION['wishlist']);
     $products = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
}

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Wishlist - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .wishlist-header {
               padding: 150px 5% 50px;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .wishlist-header h1 {
               font-size: 4rem;
               font-weight: 900;
               margin-bottom: 10px;
          }

          .wishlist-actions {
               max-width: 1200px;
               margin: 30px auto;
               padding: 0 20px;
               display: flex;
               justify-content: space-between;
               align-items: center;
          }

          .wishlist-count {
               font-size: 0.9rem;
               color: var(--muted);
          }

          .clear-wishlist {
               color: #ef4444;
               text-decoration: none;
               border-bottom: 1px solid transparent;
               transition: all 0.3s;
          }

          .clear-wishlist:hover {
               border-bottom-color: #ef4444;
          }

          .empty-wishlist {
               grid-column: 1 / -1;
               padding: 100px 40px;
               text-align: center;
          }

          .empty-wishlist h3 {
               font-size: 2rem;
               margin-bottom: 20px;
          }

          .empty-wishlist p {
               color: var(--muted);
               margin-bottom: 30px;
          }

          .wishlist-badge {
               position: absolute;
               top: 20px;
               right: 20px;
               background: var(--fg);
               color: var(--bg);
               padding: 5px 10px;
               font-size: 0.7rem;
               font-weight: 600;
               z-index: 10;
          }

          .card {
               position: relative;
          }

          .remove-wishlist {
               position: absolute;
               top: 20px;
               left: 20px;
               background: #ef4444;
               color: white;
               width: 30px;
               height: 30px;
               display: flex;
               align-items: center;
               justify-content: center;
               text-decoration: none;
               font-weight: 800;
               z-index: 20;
               border: 2px solid var(--fg);
               transition: all 0.3s;
          }

          .remove-wishlist:hover {
               background: #dc2626;
               transform: scale(1.1);
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
               <a href="cart/index.php">Cart (<?= $cartCount ?>)</a>
               <a href="wishlist.php" class="active">Wishlist (<?= count($_SESSION['wishlist']) ?>)</a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Wishlist Header -->
          <section class="wishlist-header">
               <h1>WISHLIST</h1>
               <p>Your favorite items</p>
          </section>

          <!-- Wishlist Actions -->
          <?php if (!empty($_SESSION['wishlist'])): ?>
               <div class="wishlist-actions">
                    <span class="wishlist-count"><?= count($_SESSION['wishlist']) ?> items in wishlist</span>
                    <a href="?clear=1" class="clear-wishlist" onclick="return confirm('Clear all wishlist?')">Clear
                         Wishlist</a>
               </div>
          <?php endif; ?>

          <!-- Product Grid -->
          <section class="products">
               <?php if (!empty($_SESSION['wishlist']) && mysqli_num_rows($products) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($products)): ?>
                         <div class="card">
                              <span class="wishlist-badge">❤️ WISHLIST</span>
                              <a href="?remove=<?= $p['id'] ?>" class="remove-wishlist"
                                   onclick="return confirm('Remove from wishlist?')">✕</a>
                              <img src="uploads/<?= $p['image'] ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                                   loading="lazy" onerror="this.src='assets/img/default.jpg'">
                              <div class="card-content">
                                   <small>WISHLIST ITEM</small>
                                   <h3><?= htmlspecialchars($p['name']) ?></h3>
                                   <div class="price"><?= format_rp($p['price']) ?></div>
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
                    <div class="empty-wishlist">
                         <h3>❤️ EMPTY WISHLIST</h3>
                         <p>You haven't added any items to your wishlist yet.</p>
                         <div class="suggestions">
                              <p style="margin-top: 20px;">
                                   <a href="index.php#menu" class="btn-buy">Browse Menu</a>
                              </p>
                         </div>
                    </div>
               <?php endif; ?>
          </section>
     </main>

     <!-- Footer -->
     <footer>
          <div class="footer-brand">
               KANTIN
               <p>&copy; <?= date('Y') ?> — All rights reserved</p>
          </div>
          <div class="footer-links">
               <a href="index.php#menu">Menu</a>
               <a href="cart/index.php">Cart</a>
               <a href="wishlist.php">Wishlist</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
     <script src="assets/js/cart.js"></script>
</body>

</html>