<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';
requireAdmin();

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'] ?? 0;
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='user'"))['c'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'] ?? 0;
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) as s FROM orders WHERE status='completed'"))['s'] ?? 0;

$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'] ?? 0;

// Recent orders
$recent_orders_q = mysqli_query($conn, "
    SELECT o.*, u.name as user_name
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 8
");
$recent_orders = [];
while ($row = mysqli_fetch_assoc($recent_orders_q))
     $recent_orders[] = $row;

// Low stock products
$low_stock_q = mysqli_query($conn, "SELECT * FROM products WHERE stock > 0 AND stock <= 5 ORDER BY stock ASC LIMIT 5");
$low_stock = [];
while ($row = mysqli_fetch_assoc($low_stock_q))
     $low_stock[] = $row;

// Monthly revenue (last 6 months)
$monthly_q = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%b') as month, COALESCE(SUM(total_price),0) as revenue
    FROM orders
    WHERE status='completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY created_at ASC
");
$monthly = [];
while ($row = mysqli_fetch_assoc($monthly_q))
     $monthly[] = $row;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Dashboard — Admin Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="/public/assets/css/admin.css">
</head>

<body class="admin-body">

     <?php include 'layout.php'; ?>

     <main class="admin-main" id="adminMain">
          <!-- Topbar -->
          <header class="admin-topbar">
               <div class="admin-topbar-left">
                    <button class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                         <span></span><span></span><span></span>
                    </button>
                    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
                         <span class="breadcrumb-item">Admin</span>
                         <span class="breadcrumb-sep">/</span>
                         <span class="breadcrumb-item breadcrumb-item--active">Dashboard</span>
                    </nav>
               </div>
               <div class="admin-topbar-right">
                    <div class="admin-topbar-user">
                         <div class="admin-topbar-avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)) ?>
                         </div>
                         <span class="admin-topbar-name"><?= e($_SESSION['name'] ?? 'Admin') ?></span>
                    </div>
                    <a href="/auth/logout.php" class="btn btn--ghost btn--sm">Logout</a>
               </div>
          </header>

          <!-- Content -->
          <div class="admin-content">
               <div class="admin-page-header">
                    <h1 class="admin-page-title">Dashboard</h1>
                    <p class="admin-page-subtitle">Overview sistem Kantin Digital</p>
               </div>

               <!-- Stats Grid -->
               <div class="stats-grid">
                    <div class="stat-card stat-card--yellow">
                         <div class="stat-card__icon">
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="2">
                                   <line x1="12" y1="1" x2="12" y2="23" />
                                   <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                              </svg>
                         </div>
                         <div class="stat-card__body">
                              <div class="stat-card__label">Total Pendapatan</div>
                              <div class="stat-card__value"><?= formatRupiah($total_revenue) ?></div>
                              <div class="stat-card__sub">Dari order selesai</div>
                         </div>
                    </div>

                    <div class="stat-card stat-card--green">
                         <div class="stat-card__icon">
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="2">
                                   <path d="M9 11l3 3L22 4" />
                                   <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                              </svg>
                         </div>
                         <div class="stat-card__body">
                              <div class="stat-card__label">Total Pesanan</div>
                              <div class="stat-card__value"><?= number_format($total_orders) ?></div>
                              <div class="stat-card__sub">
                                   <?php if ($pending_orders > 0): ?>
                                        <span class="badge badge--warning"><?= $pending_orders ?> menunggu</span>
                                   <?php else: ?>
                                        Semua diproses
                                   <?php endif; ?>
                              </div>
                         </div>
                    </div>

                    <div class="stat-card stat-card--blue">
                         <div class="stat-card__icon">
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="2">
                                   <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                   <circle cx="9" cy="7" r="4" />
                                   <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                   <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                              </svg>
                         </div>
                         <div class="stat-card__body">
                              <div class="stat-card__label">Total Pengguna</div>
                              <div class="stat-card__value"><?= number_format($total_users) ?></div>
                              <div class="stat-card__sub">Akun terdaftar</div>
                         </div>
                    </div>

                    <div class="stat-card stat-card--red">
                         <div class="stat-card__icon">
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="2">
                                   <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                   <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                              </svg>
                         </div>
                         <div class="stat-card__body">
                              <div class="stat-card__label">Total Produk</div>
                              <div class="stat-card__value"><?= number_format($total_products) ?></div>
                              <div class="stat-card__sub">Produk aktif</div>
                         </div>
                    </div>
               </div>

               <!-- Content Columns -->
               <div class="admin-cols">
                    <!-- Recent Orders -->
                    <div class="admin-card admin-card--wide">
                         <div class="admin-card__header">
                              <h2 class="admin-card__title">Pesanan Terbaru</h2>
                              <a href="/admin/orders.php" class="btn btn--ghost btn--sm">Lihat Semua</a>
                         </div>
                         <div class="admin-card__body">
                              <div class="table-wrap">
                                   <table class="table">
                                        <thead>
                                             <tr>
                                                  <th>ID</th>
                                                  <th>Pelanggan</th>
                                                  <th>Total</th>
                                                  <th>Status</th>
                                                  <th>Waktu</th>
                                                  <th>Aksi</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             <?php if (empty($recent_orders)): ?>
                                                  <tr>
                                                       <td colspan="6" class="table-empty">Belum ada pesanan</td>
                                                  </tr>
                                             <?php else: ?>
                                                  <?php foreach ($recent_orders as $order): ?>
                                                       <tr>
                                                            <td class="font-mono">
                                                                 #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                                            <td><?= e($order['user_name'] ?? 'Guest') ?></td>
                                                            <td class="font-mono"><?= formatRupiah($order['total_price']) ?></td>
                                                            <td><?= orderStatusBadge($order['status']) ?></td>
                                                            <td class="text-muted text-sm"><?= timeAgo($order['created_at']) ?>
                                                            </td>
                                                            <td>
                                                                 <a href="/admin/orders.php?detail=<?= $order['id'] ?>"
                                                                      class="btn btn--ghost btn--xs">Detail</a>
                                                            </td>
                                                       </tr>
                                                  <?php endforeach; ?>
                                             <?php endif; ?>
                                        </tbody>
                                   </table>
                              </div>
                         </div>
                    </div>

                    <!-- Low Stock -->
                    <div class="admin-card">
                         <div class="admin-card__header">
                              <h2 class="admin-card__title">Stok Hampir Habis</h2>
                              <a href="/admin/products.php" class="btn btn--ghost btn--sm">Kelola</a>
                         </div>
                         <div class="admin-card__body">
                              <?php if (empty($low_stock)): ?>
                                   <div class="empty-state empty-state--sm">
                                        <p>Semua stok aman ✓</p>
                                   </div>
                              <?php else: ?>
                                   <ul class="low-stock-list">
                                        <?php foreach ($low_stock as $p): ?>
                                             <li class="low-stock-item">
                                                  <div class="low-stock-info">
                                                       <span class="low-stock-name"><?= e($p['name']) ?></span>
                                                       <span class="low-stock-stock badge badge--danger">Sisa
                                                            <?= $p['stock'] ?></span>
                                                  </div>
                                                  <div class="low-stock-bar">
                                                       <div class="low-stock-fill" style="width: <?= ($p['stock'] / 5) * 100 ?>%">
                                                       </div>
                                                  </div>
                                             </li>
                                        <?php endforeach; ?>
                                   </ul>
                              <?php endif; ?>
                         </div>
                    </div>
               </div>
          </div>
     </main>

     <?php include 'layout-footer.php'; ?>

     <script src="/public/assets/js/admin.js"></script>
</body>

</html>