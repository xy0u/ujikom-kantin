<?php
session_start();
require '../../core/database.php';
require '../../core/helpers.php';
requireLogin();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart))
     redirect('/public/cart/index.php');

$total = getCartTotal();
$cartCount = getCartCount();
$flash = getFlash();

// Get user info
$user = mysqli_fetch_assoc(mysqli_prepare_exec($conn, "SELECT * FROM users WHERE id = ?", 'i', [$_SESSION['user_id']]));

// Helper function
function mysqli_prepare_exec($conn, $sql, $types, $params)
{
     $stmt = mysqli_prepare($conn, $sql);
     mysqli_stmt_bind_param($stmt, $types, ...$params);
     mysqli_stmt_execute($stmt);
     return mysqli_stmt_get_result($stmt);
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=" . (int) $_SESSION['user_id']));

// Handle checkout submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $notes = sanitize($_POST['notes'] ?? '');

     // Create order
     $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_price, notes, status) VALUES (?, ?, ?, 'pending')");
     mysqli_stmt_bind_param($stmt, 'ids', $_SESSION['user_id'], $total, $notes);
     mysqli_stmt_execute($stmt);
     $order_id = mysqli_insert_id($conn);

     if ($order_id) {
          // Insert order items
          foreach ($cart as $product_id => $item) {
               $stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
               mysqli_stmt_bind_param($stmt2, 'iiid', $order_id, $product_id, $item['qty'], $item['price']);
               mysqli_stmt_execute($stmt2);

               // Reduce stock
               mysqli_query($conn, "UPDATE products SET stock = stock - {$item['qty']}, sold = sold + {$item['qty']} WHERE id=$product_id AND stock >= {$item['qty']}");
          }

          clearCart();
          flash('success', "Pesanan #" . str_pad($order_id, 4, '0', STR_PAD_LEFT) . " berhasil dibuat! Menunggu konfirmasi.");
          redirect("/public/orders/detail.php?id=$order_id");
     } else {
          flash('error', 'Gagal membuat pesanan. Coba lagi.');
     }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Checkout — Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="/public/assets/css/public.css">
</head>

<body>
     <?php include '../../components/navbar.php'; ?>

     <main class="section">
          <div class="container container--narrow">
               <div class="page-header">
                    <h1 class="page-title">Checkout</h1>
                    <p class="page-subtitle">Selesaikan pesanan Anda</p>
               </div>

               <!-- Checkout Steps -->
               <div class="checkout-steps">
                    <div class="checkout-step checkout-step--done">
                         <div class="checkout-step__num">✓</div>
                         <span>Keranjang</span>
                    </div>
                    <div class="checkout-step checkout-step--active">
                         <div class="checkout-step__num">2</div>
                         <span>Checkout</span>
                    </div>
                    <div class="checkout-step">
                         <div class="checkout-step__num">3</div>
                         <span>Selesai</span>
                    </div>
               </div>

               <form method="POST" class="checkout-layout" id="checkoutForm">
                    <?= csrfField() ?>

                    <!-- Left: Details -->
                    <div class="checkout-main">
                         <!-- Customer Info -->
                         <div class="checkout-section">
                              <h3 class="checkout-section-title">Informasi Pelanggan</h3>
                              <div class="checkout-info-grid">
                                   <div class="checkout-info-item">
                                        <span class="checkout-info-label">Nama</span>
                                        <span class="checkout-info-value"><?= e($user['name']) ?></span>
                                   </div>
                                   <div class="checkout-info-item">
                                        <span class="checkout-info-label">Email</span>
                                        <span class="checkout-info-value"><?= e($user['email']) ?></span>
                                   </div>
                                   <?php if ($user['phone']): ?>
                                        <div class="checkout-info-item">
                                             <span class="checkout-info-label">Telepon</span>
                                             <span class="checkout-info-value"><?= e($user['phone']) ?></span>
                                        </div>
                                   <?php endif; ?>
                              </div>
                         </div>

                         <!-- Order Items -->
                         <div class="checkout-section">
                              <h3 class="checkout-section-title">Item Pesanan</h3>
                              <div class="checkout-items">
                                   <?php foreach ($cart as $id => $item): ?>
                                        <div class="checkout-item">
                                             <div class="checkout-item__name"><?= e($item['name']) ?></div>
                                             <div class="checkout-item__detail">
                                                  <?= formatRupiah($item['price']) ?> × <?= $item['qty'] ?>
                                             </div>
                                             <div class="checkout-item__subtotal font-mono">
                                                  <?= formatRupiah($item['price'] * $item['qty']) ?>
                                             </div>
                                        </div>
                                   <?php endforeach; ?>
                              </div>
                         </div>

                         <!-- Notes -->
                         <div class="checkout-section">
                              <h3 class="checkout-section-title">Catatan (Opsional)</h3>
                              <textarea name="notes" class="textarea" rows="3"
                                   placeholder="cth: Tanpa sambal, extra nasi, dll..."><?= e($_POST['notes'] ?? '') ?></textarea>
                         </div>
                    </div>

                    <!-- Right: Summary -->
                    <div class="checkout-sidebar">
                         <div class="cart-summary">
                              <div class="cart-summary__inner">
                                   <h3 class="cart-summary__title">Total Pembayaran</h3>

                                   <div class="cart-summary__rows">
                                        <?php foreach ($cart as $item): ?>
                                             <div class="cart-summary__row cart-summary__row--sm">
                                                  <span><?= e($item['name']) ?> ×<?= $item['qty'] ?></span>
                                                  <span
                                                       class="font-mono"><?= formatRupiah($item['price'] * $item['qty']) ?></span>
                                             </div>
                                        <?php endforeach; ?>
                                        <div class="cart-summary__row">
                                             <span>Biaya Layanan</span>
                                             <span class="text-muted font-mono">Gratis</span>
                                        </div>
                                   </div>

                                   <div class="cart-summary__total">
                                        <span>Total</span>
                                        <span class="font-mono"><?= formatRupiah($total) ?></span>
                                   </div>

                                   <button type="submit" class="btn btn--primary btn--full btn--lg" id="submitBtn">
                                        <span class="btn-text">Buat Pesanan</span>
                                        <span class="btn-loader" hidden>
                                             <svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                  stroke="currentColor" stroke-width="2">
                                                  <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                             </svg>
                                        </span>
                                   </button>

                                   <a href="/public/cart/index.php" class="btn btn--ghost btn--full"
                                        style="margin-top:0.5rem">
                                        ← Kembali ke Keranjang
                                   </a>
                              </div>
                         </div>
                    </div>
               </form>
          </div>
     </main>

     <?php include '../../components/footer.php'; ?>
     <script>
          document.getElementById('checkoutForm')?.addEventListener('submit', function () {
               const btn = document.getElementById('submitBtn');
               btn.disabled = true;
               btn.querySelector('.btn-text').hidden = true;
               btn.querySelector('.btn-loader').hidden = false;
          });
     </script>
</body>

</html>