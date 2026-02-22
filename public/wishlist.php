<?php
session_start();
require '../../core/database.php';
require '../../core/helpers.php';

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     header('Content-Type: application/json');
     if (!isLoggedIn()) {
          echo json_encode(['success' => false, 'message' => 'Harus login']);
          exit;
     }

     $data = json_decode(file_get_contents('php://input'), true);
     $action = $data['action'] ?? '';
     $id = (int) ($data['id'] ?? 0);

     if ($action === 'toggle' && $id) {
          if (!isset($_SESSION['wishlist']))
               $_SESSION['wishlist'] = [];
          $added = false;
          if (in_array($id, $_SESSION['wishlist'])) {
               $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$id]));
          } else {
               $_SESSION['wishlist'][] = $id;
               $added = true;
          }
          echo json_encode(['success' => true, 'added' => $added]);
          exit;
     }

     echo json_encode(['success' => false]);
     exit;
}

requireLogin();

$wishlist_ids = $_SESSION['wishlist'] ?? [];
$products = [];

if (!empty($wishlist_ids)) {
     $ids_str = implode(',', array_map('intval', $wishlist_ids));
     $products_q = mysqli_query(
          $conn,
          "SELECT p.*, c.name as category_name
         FROM products p LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.id IN ($ids_str)"
     );
     while ($row = mysqli_fetch_assoc($products_q))
          $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Wishlist — Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="/public/assets/css/public.css">
</head>

<body>
     <?php include '../../components/navbar.php'; ?>
     <div class="toast-container" id="toastContainer"></div>

     <main class="section">
          <div class="container">
               <div class="page-header">
                    <h1 class="page-title">Wishlist</h1>
                    <p class="page-subtitle"><?= count($products) ?> produk tersimpan</p>
               </div>

               <?php if (empty($products)): ?>
                    <div class="empty-state">
                         <div class="empty-state-icon">
                              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="1.5">
                                   <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                              </svg>
                         </div>
                         <h3 class="empty-state-title">Wishlist kosong</h3>
                         <p class="empty-state-desc">Tambahkan produk favorit ke wishlist Anda.</p>
                         <a href="/public/index.php" class="btn btn--primary">Lihat Menu</a>
                    </div>
               <?php else: ?>
                    <div class="products-grid">
                         <?php foreach ($products as $p): ?>
                              <?php $imgSrc = $p['image'] ? '/public/assets/uploads/' . $p['image'] : '/public/assets/img/placeholder.png'; ?>
                              <article class="product-card" data-id="<?= $p['id'] ?>">
                                   <div class="product-card__img-wrap">
                                        <a href="/public/product.php?id=<?= $p['id'] ?>">
                                             <img src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>" class="product-card__img"
                                                  loading="lazy">
                                        </a>
                                        <button class="product-card__wishlist active"
                                             onclick="removeFromWishlist(<?= $p['id'] ?>, this)" aria-label="Hapus dari wishlist">
                                             <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                                                  stroke="currentColor" stroke-width="2">
                                                  <path
                                                       d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                             </svg>
                                        </button>
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

     <?php include '../../components/footer.php'; ?>
     <script>
          async function removeFromWishlist(id, btn) {
               const res = await fetch('/public/wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle', id })
               });
               const data = await res.json();
               if (data.success && !data.added) {
                    const card = btn.closest('.product-card');
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                    showToast('Dihapus dari wishlist', 'success');
               }
          }

          async function addToCart(id, name, price) {
               const res = await fetch('/public/cart/action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add', id, name, price, qty: 1 })
               });
               const data = await res.json();
               if (data.success) showToast(`${name} ditambahkan!`, 'success');
          }

          function showToast(msg, type) {
               const c = document.getElementById('toastContainer');
               const t = document.createElement('div');
               t.className = `toast toast--${type}`;
               t.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()">&times;</button>`;
               c.appendChild(t);
               setTimeout(() => t.classList.add('show'), 10);
               setTimeout(() => t.remove(), 4000);
          }
     </script>
</body>

</html>