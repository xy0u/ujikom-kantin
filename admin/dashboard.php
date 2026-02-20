<?php
require "../core/database.php";
require "../core/helpers.php";
include "layout.php";

// Ambil data statistik
$countProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$countCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$countOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$countCustomers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];

$totalIncome = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total),0) as total FROM orders WHERE status='SUCCESS'"))['total'];

// Pesanan terbaru
$recentOrders = mysqli_query($conn, "SELECT o.*, u.name as customer_name 
                                     FROM orders o 
                                     JOIN users u ON o.user_id = u.id 
                                     ORDER BY o.id DESC LIMIT 5");

// Produk stok menipis (stok < 5)
$lowStock = mysqli_query($conn, "SELECT * FROM products WHERE stock < 5 AND stock > 0 ORDER BY stock ASC LIMIT 5");
?>

<h1>Dashboard Overview</h1>

<div class="stats-grid">
     <div class="stat-card">
          <h3>TOTAL PRODUK</h3>
          <p><?= $countProducts ?></p>
          <small><?= $countCategories ?> Kategori</small>
     </div>
     <div class="stat-card">
          <h3>PESANAN</h3>
          <p><?= $countOrders ?></p>
          <small>Total transaksi</small>
     </div>
     <div class="stat-card">
          <h3>PENDAPATAN</h3>
          <p><?= format_rp($totalIncome ?? 0) ?></p>
          <small>Dari pesanan sukses</small>
     </div>
     <div class="stat-card">
          <h3>PELANGGAN</h3>
          <p><?= $countCustomers ?></p>
          <small>User terdaftar</small>
     </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
     <!-- Recent Orders -->
     <div class="card">
          <h3>Pesanan Terbaru</h3>
          <?php if (mysqli_num_rows($recentOrders) > 0): ?>
               <table style="margin-top: 15px;">
                    <thead>
                         <tr>
                              <th>Invoice</th>
                              <th>Pelanggan</th>
                              <th>Total</th>
                              <th>Status</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php while ($order = mysqli_fetch_assoc($recentOrders)): ?>
                              <tr>
                                   <td>#<?= $order['id'] ?></td>
                                   <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                   <td><?= format_rp($order['total']) ?></td>
                                   <td>
                                        <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                             <?= $order['status'] ?>
                                        </span>
                                   </td>
                              </tr>
                         <?php endwhile; ?>
                    </tbody>
               </table>
          <?php else: ?>
               <p style="color: var(--text-muted); margin-top: 15px;">Belum ada pesanan</p>
          <?php endif; ?>
          <a href="orders.php" style="display: inline-block; margin-top: 15px; color: var(--accent);">Lihat semua →</a>
     </div>

     <!-- Low Stock Alert -->
     <div class="card">
          <h3>Stok Menipis</h3>
          <?php if (mysqli_num_rows($lowStock) > 0): ?>
               <table style="margin-top: 15px;">
                    <thead>
                         <tr>
                              <th>Produk</th>
                              <th>Stok</th>
                              <th>Aksi</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php while ($product = mysqli_fetch_assoc($lowStock)): ?>
                              <tr>
                                   <td><?= htmlspecialchars($product['name']) ?></td>
                                   <td style="color: #ef4444; font-weight: 700;"><?= $product['stock'] ?></td>
                                   <td>
                                        <a href="products.php?edit=<?= $product['id'] ?>" class="btn-admin primary small">Tambah
                                             Stok</a>
                                   </td>
                              </tr>
                         <?php endwhile; ?>
                    </tbody>
               </table>
          <?php else: ?>
               <p style="color: #22c55e; margin-top: 15px;">✓ Semua stok aman</p>
          <?php endif; ?>
     </div>
</div>

<div class="card" style="margin-top: 20px;">
     <h3>Aktivitas Terbaru</h3>
     <p style="color: var(--text-muted); margin-top: 10px;">
          Selamat datang di panel admin. Gunakan sidebar untuk mengelola kantin digital.
     </p>
     <p style="color: var(--text-muted); margin-top: 5px; font-size: 13px;">
          Terakhir login: <?= date('d M Y H:i:s') ?>
     </p>
</div>

<style>
     .btn-admin.small {
          padding: 4px 8px;
          font-size: 11px;
     }

     .status-badge {
          padding: 4px 8px;
          border-radius: 4px;
          font-size: 11px;
          font-weight: 600;
          text-transform: uppercase;
     }

     .status-success {
          background: rgba(34, 197, 94, 0.2);
          color: #22c55e;
     }

     .status-pending {
          background: rgba(234, 179, 8, 0.2);
          color: #eab308;
     }

     .status-failed {
          background: rgba(239, 68, 68, 0.2);
          color: #ef4444;
     }
</style>

</main>
</div>
</body>

</html>