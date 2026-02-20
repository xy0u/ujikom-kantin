<?php
require "../core/database.php";
require "../core/helpers.php";
include "layout.php";

// Confirm order
if (isset($_GET['confirm'])) {
     $id = (int) $_GET['confirm'];
     mysqli_query($conn, "UPDATE orders SET status='SUCCESS' WHERE id=$id");
     header("Location: orders.php?success=1");
     exit;
}

// Cancel order
if (isset($_GET['cancel'])) {
     $id = (int) $_GET['cancel'];
     mysqli_query($conn, "UPDATE orders SET status='CANCELLED' WHERE id=$id");
     header("Location: orders.php?success=2");
     exit;
}

// Filter by status
$statusFilter = $_GET['status'] ?? '';
$whereClause = $statusFilter ? "WHERE o.status='$statusFilter'" : "";

$orders = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email 
                               FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               $whereClause 
                               ORDER BY o.id DESC");

// Get order statistics
$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='PENDING' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='SUCCESS' THEN 1 ELSE 0 END) as success,
    SUM(CASE WHEN status='CANCELLED' THEN 1 ELSE 0 END) as cancelled,
    SUM(CASE WHEN status='PENDING' THEN total ELSE 0 END) as pending_amount,
    SUM(CASE WHEN status='SUCCESS' THEN total ELSE 0 END) as success_amount
    FROM orders"));
?>

<h1>Riwayat Pesanan</h1>

<!-- Statistics -->
<div class="stats-grid" style="margin-bottom: 20px;">
     <div class="stat-card">
          <h3>TOTAL PESANAN</h3>
          <p><?= $stats['total'] ?? 0 ?></p>
     </div>
     <div class="stat-card">
          <h3>PENDING</h3>
          <p style="color: #eab308;"><?= $stats['pending'] ?? 0 ?></p>
          <small><?= format_rp($stats['pending_amount'] ?? 0) ?></small>
     </div>
     <div class="stat-card">
          <h3>SUKSES</h3>
          <p style="color: #22c55e;"><?= $stats['success'] ?? 0 ?></p>
          <small><?= format_rp($stats['success_amount'] ?? 0) ?></small>
     </div>
     <div class="stat-card">
          <h3>BATAL</h3>
          <p style="color: #ef4444;"><?= $stats['cancelled'] ?? 0 ?></p>
     </div>
</div>

<!-- Filter Buttons -->
<div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
     <a href="orders.php" class="btn-admin <?= !$statusFilter ? 'primary' : 'danger' ?>">Semua</a>
     <a href="?status=PENDING" class="btn-admin <?= $statusFilter == 'PENDING' ? 'primary' : 'danger' ?>">Pending</a>
     <a href="?status=SUCCESS" class="btn-admin <?= $statusFilter == 'SUCCESS' ? 'primary' : 'danger' ?>">Sukses</a>
     <a href="?status=CANCELLED" class="btn-admin <?= $statusFilter == 'CANCELLED' ? 'primary' : 'danger' ?>">Batal</a>
</div>

<?php if (isset($_GET['success'])): ?>
     <div class="alert success">
          <?php if ($_GET['success'] == 1): ?>
               Pesanan berhasil dikonfirmasi!
          <?php elseif ($_GET['success'] == 2): ?>
               Pesanan dibatalkan!
          <?php endif; ?>
     </div>
<?php endif; ?>

<div class="card">
     <table>
          <thead>
               <tr>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
               </tr>
          </thead>
          <tbody>
               <?php if (mysqli_num_rows($orders) > 0): ?>
                    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                         <tr>
                              <td><strong>#ORD-<?= $o['id'] ?></strong></td>
                              <td>
                                   <?= htmlspecialchars($o['customer_name']) ?>
                                   <br><small style="color: var(--text-muted);"><?= $o['email'] ?></small>
                              </td>
                              <td><strong><?= format_rp($o['total']) ?></strong></td>
                              <td>
                                   <span class="status-badge status-<?= strtolower($o['status']) ?>">
                                        <?= $o['status'] ?>
                                   </span>
                              </td>
                              <td>
                                   <small><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></small>
                              </td>
                              <td>
                                   <?php if ($o['status'] == 'PENDING'): ?>
                                        <a href="?confirm=<?= $o['id'] ?>" class="btn-admin primary small"
                                             onclick="return confirm('Konfirmasi pesanan ini?')">Konfirmasi</a>
                                        <a href="?cancel=<?= $o['id'] ?>" class="btn-admin danger small"
                                             onclick="return confirm('Batalkan pesanan ini?')">Batal</a>
                                   <?php elseif ($o['status'] == 'SUCCESS'): ?>
                                        <a href="../public/checkout/invoice.php?order=<?= $o['id'] ?>" class="btn-admin primary small"
                                             target="_blank">Invoice</a>
                                   <?php endif; ?>
                              </td>
                         </tr>
                    <?php endwhile; ?>
               <?php else: ?>
                    <tr>
                         <td colspan="6" style="text-align: center; color: var(--text-muted);">
                              Belum ada pesanan
                         </td>
                    </tr>
               <?php endif; ?>
          </tbody>
     </table>
</div>

<style>
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

     .status-cancelled {
          background: rgba(239, 68, 68, 0.2);
          color: #ef4444;
     }

     .btn-admin.small {
          padding: 4px 8px;
          font-size: 11px;
          margin: 2px;
     }

     .alert {
          padding: 15px 20px;
          border-radius: 8px;
          margin-bottom: 20px;
          font-weight: 500;
     }

     .alert.success {
          background: rgba(34, 197, 94, 0.1);
          color: #22c55e;
          border: 1px solid rgba(34, 197, 94, 0.2);
     }
</style>

</main>
</div>
</body>

</html>