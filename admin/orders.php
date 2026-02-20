<?php
require "../core/database.php";
include "layout.php";

$statusFilter = $_GET['status'] ?? '';
$where = $statusFilter ? "WHERE status='$statusFilter'" : "";

if (isset($_GET['confirm'])) {
     $id = (int) $_GET['confirm'];
     mysqli_query($conn, "UPDATE orders SET status='SUCCESS' WHERE id=$id");
     header("Location: orders.php");
     exit;
}

$orders = mysqli_query($conn, "SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id=u.id $where ORDER BY o.id DESC");
?>

<h1>Transaksi</h1>

<div style="display: flex; gap: 10px; margin-bottom: 30px;">
     <a href="orders.php" class="btn-admin <?= !$statusFilter ? 'primary' : 'danger' ?>">Semua</a>
     <a href="?status=PENDING" class="btn-admin <?= $statusFilter == 'PENDING' ? 'primary' : 'danger' ?>">Menunggu</a>
     <a href="?status=SUCCESS" class="btn-admin <?= $statusFilter == 'SUCCESS' ? 'primary' : 'danger' ?>">Selesai</a>
</div>

<div class="card">
     <table>
          <thead>
               <tr>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Tindakan</th>
               </tr>
          </thead>
          <tbody>
                  <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                         <td>#ORD-<?= $o['id'] ?></td>
                         <td><?= $o['name'] ?></td>
                         <td><strong>Rp <?= number_format($o['total']) ?></strong></td>
                         <td>
                              <span
                                   style="font-size: 12px; padding: 4px 10px; border-radius: 20px; border: 1px solid <?= $o['status'] == 'SUCCESS' ? '#22c55e' : '#eab308' ?>; color: <?= $o['status'] == 'SUCCESS' ? '#22c55e' : '#eab308' ?>;">
                                              <?= $o['status'] ?>
                              </span>
                         </td>
                         <td>
                                        <?php if ($o['status'] == "PENDING"): ?>
                                   <a href="?confirm=<?= $o['id'] ?>" class="btn-admin primary"
                                        style="font-size: 12px; padding: 6px 12px;">Konfirmasi</a>
                                        <?php endif; ?>
                              <a href="?detail=<?= $o['id'] ?>" class="btn-admin danger"
                                   style="font-size: 12px; padding: 6px 12px;">Detail</a>
                         </td>
                    </tr>
                  <?php endwhile; ?>
          </tbody>
     </table>
</div>