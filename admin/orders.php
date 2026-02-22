<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';
requireAdmin();

$flash = getFlash();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
     $order_id = (int) $_POST['order_id'];
     $status = sanitize($_POST['status']);
     $allowed = ['pending', 'processing', 'ready', 'completed', 'cancelled'];
     if (in_array($status, $allowed)) {
          mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
          flash('success', "Status pesanan #" . str_pad($order_id, 4, '0', STR_PAD_LEFT) . " berhasil diubah.");
     }
     redirect('/admin/orders.php');
}

// Detail view
$detail_order = null;
$detail_items = [];
if (isset($_GET['detail'])) {
     $did = (int) $_GET['detail'];
     $detail_order = mysqli_fetch_assoc(mysqli_query(
          $conn,
          "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
         FROM orders o LEFT JOIN users u ON o.user_id = u.id
         WHERE o.id = $did"
     ));
     if ($detail_order) {
          $items_q = mysqli_query(
               $conn,
               "SELECT oi.*, p.name as product_name, p.image
             FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = $did"
          );
          while ($row = mysqli_fetch_assoc($items_q))
               $detail_items[] = $row;
     }
}

// Filters
$filter_status = sanitize($_GET['status'] ?? '');
$filter_search = sanitize($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 15;

$where = [];
if ($filter_status)
     $where[] = "o.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
if ($filter_search)
     $where[] = "(u.name LIKE '%" . mysqli_real_escape_string($conn, $filter_search) . "%' OR o.id LIKE '%" . mysqli_real_escape_string($conn, $filter_search) . "%')";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$count = mysqli_fetch_assoc(mysqli_query(
     $conn,
     "SELECT COUNT(*) as c FROM orders o LEFT JOIN users u ON o.user_id = u.id $where_sql"
))['c'] ?? 0;

$pag = paginate($count, $per_page, $page, '/admin/orders.php');
$orders_q = mysqli_query(
     $conn,
     "SELECT o.*, u.name as user_name
     FROM orders o LEFT JOIN users u ON o.user_id = u.id
     $where_sql
     ORDER BY o.created_at DESC
     LIMIT {$per_page} OFFSET {$pag['offset']}"
);
$orders = [];
while ($row = mysqli_fetch_assoc($orders_q))
     $orders[] = $row;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Pesanan — Admin Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="/public/assets/css/admin.css">
</head>

<body class="admin-body">

          <?php include 'layout.php'; ?>

     <main class="admin-main" id="adminMain">
          <header class="admin-topbar">
               <div class="admin-topbar-left">
                    <button class="admin-sidebar-toggle" id="sidebarToggle">
                         <span></span><span></span><span></span>
                    </button>
                    <nav class="admin-breadcrumb">
                         <span class="breadcrumb-item">Admin</span>
                         <span class="breadcrumb-sep">/</span>
                         <span class="breadcrumb-item breadcrumb-item--active">Pesanan</span>
                    </nav>
               </div>
               <div class="admin-topbar-right">
                    <a href="/auth/logout.php" class="btn btn--ghost btn--sm">Logout</a>
               </div>
          </header>

          <div class="admin-content">
               <div class="admin-page-header">
                    <h1 class="admin-page-title">Manajemen Pesanan</h1>
                    <p class="admin-page-subtitle"><?= number_format($count) ?> total pesanan</p>
               </div>

                      <?php if ($flash): ?>
                    <div class="alert alert--<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
                      <?php endif; ?>

               <!-- Filters -->
               <div class="admin-card">
                    <div class="admin-card__body">
                         <form method="GET" class="filter-form">
                              <div class="filter-form-row">
                                   <div class="input-icon-wrap">
                                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2">
                                             <circle cx="11" cy="11" r="8" />
                                             <path d="m21 21-4.35-4.35" />
                                        </svg>
                                        <input type="text" name="q" class="input input--sm"
                                             placeholder="Cari nama / ID..." value="<?= e($filter_search) ?>">
                                   </div>
                                   <select name="status" class="select select--sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>
                                             Menunggu</option>
                                        <option value="processing" <?= $filter_status === 'processing' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="ready" <?= $filter_status === 'ready' ? 'selected' : '' ?>>Siap
                                        </option>
                                        <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>
                                             Selesai</option>
                                        <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>
                                             Dibatalkan</option>
                                   </select>
                                   <button type="submit" class="btn btn--primary btn--sm">Filter</button>
                                   <a href="/admin/orders.php" class="btn btn--ghost btn--sm">Reset</a>
                              </div>
                         </form>
                    </div>
               </div>

               <!-- Table -->
               <div class="admin-card">
                    <div class="admin-card__body">
                         <div class="table-wrap">
                              <table class="table">
                                   <thead>
                                        <tr>
                                             <th>ID Pesanan</th>
                                             <th>Pelanggan</th>
                                             <th>Total</th>
                                             <th>Status</th>
                                             <th>Waktu</th>
                                             <th>Aksi</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                                    <?php if (empty($orders)): ?>
                                             <tr>
                                                  <td colspan="6" class="table-empty">Tidak ada pesanan</td>
                                             </tr>
                                                    <?php else: ?>
                                                              <?php foreach ($orders as $o): ?>
                                                  <tr>
                                                       <td class="font-mono">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                                       <td><?= e($o['user_name'] ?? 'Guest') ?></td>
                                                       <td class="font-mono"><?= formatRupiah($o['total_price']) ?></td>
                                                       <td><?= orderStatusBadge($o['status']) ?></td>
                                                       <td class="text-sm text-muted"><?= formatDateTime($o['created_at']) ?></td>
                                                       <td>
                                                            <div class="table-actions">
                                                                 <a href="?detail=<?= $o['id'] ?>"
                                                                      class="btn btn--ghost btn--xs">Detail</a>
                                                                 <form method="POST" style="display:inline">
                                                                      <input type="hidden" name="order_id"
                                                                           value="<?= $o['id'] ?>">
                                                                      <input type="hidden" name="update_status" value="1">
                                                                      <select name="status" class="select select--xs"
                                                                           onchange="this.form.submit()">
                                                                                                      <?php foreach (['pending', 'processing', 'ready', 'completed', 'cancelled'] as $s): ?>
                                                                                <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                                                                                      <?php endforeach; ?>
                                                                      </select>
                                                                 </form>
                                                            </div>
                                                       </td>
                                                  </tr>
                                                              <?php endforeach; ?>
                                                    <?php endif; ?>
                                   </tbody>
                              </table>
                         </div>

                         <!-- Pagination -->
                                  <?php if ($pag['total_pages'] > 1): ?>
                              <div class="pagination">
                                                  <?php if ($pag['has_prev']): ?>
                                        <a href="?page=<?= $page - 1 ?>&status=<?= e($filter_status) ?>&q=<?= e($filter_search) ?>"
                                             class="pagination-btn">← Prev</a>
                                                  <?php endif; ?>
                                   <span class="pagination-info">Hal <?= $page ?> / <?= $pag['total_pages'] ?></span>
                                                  <?php if ($pag['has_next']): ?>
                                        <a href="?page=<?= $page + 1 ?>&status=<?= e($filter_status) ?>&q=<?= e($filter_search) ?>"
                                             class="pagination-btn">Next →</a>
                                                  <?php endif; ?>
                              </div>
                                  <?php endif; ?>
                    </div>
               </div>
          </div>
     </main>

     <!-- Order Detail Modal -->
          <?php if ($detail_order): ?>
          <div class="modal-overlay active" id="detailModal">
               <div class="modal modal--lg">
                    <div class="modal-header">
                         <h3 class="modal-title">
                              Detail Pesanan #<?= str_pad($detail_order['id'], 4, '0', STR_PAD_LEFT) ?>
                         </h3>
                         <a href="/admin/orders.php" class="modal-close">&times;</a>
                    </div>
                    <div class="modal-body">
                         <div class="order-detail-grid">
                              <div>
                                   <h4 class="detail-section-title">Informasi Pelanggan</h4>
                                   <dl class="detail-list">
                                        <dt>Nama</dt>
                                        <dd><?= e($detail_order['user_name']) ?></dd>
                                        <dt>Email</dt>
                                        <dd><?= e($detail_order['user_email']) ?></dd>
                                                        <?php if ($detail_order['user_phone']): ?>
                                             <dt>Telepon</dt>
                                             <dd><?= e($detail_order['user_phone']) ?></dd>
                                                        <?php endif; ?>
                                   </dl>
                              </div>
                              <div>
                                   <h4 class="detail-section-title">Informasi Pesanan</h4>
                                   <dl class="detail-list">
                                        <dt>Status</dt>
                                        <dd><?= orderStatusBadge($detail_order['status']) ?></dd>
                                        <dt>Waktu</dt>
                                        <dd><?= formatDateTime($detail_order['created_at']) ?></dd>
                                        <dt>Total</dt>
                                        <dd class="font-mono"><?= formatRupiah($detail_order['total_price']) ?></dd>
                                   </dl>
                              </div>
                         </div>

                         <h4 class="detail-section-title" style="margin-top:1.5rem">Item Pesanan</h4>
                         <table class="table">
                              <thead>
                                   <tr>
                                        <th>Produk</th>
                                        <th>Harga Satuan</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                   </tr>
                              </thead>
                              <tbody>
                                                  <?php foreach ($detail_items as $item): ?>
                                        <tr>
                                             <td><?= e($item['product_name']) ?></td>
                                             <td class="font-mono"><?= formatRupiah($item['price']) ?></td>
                                             <td><?= $item['quantity'] ?></td>
                                             <td class="font-mono"><?= formatRupiah($item['price'] * $item['quantity']) ?></td>
                                        </tr>
                                                  <?php endforeach; ?>
                                   <tr class="table-total">
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td class="font-mono">
                                             <strong><?= formatRupiah($detail_order['total_price']) ?></strong>
                                        </td>
                                   </tr>
                              </tbody>
                         </table>

                                      <?php if ($detail_order['notes']): ?>
                              <div style="margin-top:1rem">
                                   <h4 class="detail-section-title">Catatan</h4>
                                   <p class="text-muted"><?= e($detail_order['notes']) ?></p>
                              </div>
                                      <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                         <a href="/admin/orders.php" class="btn btn--ghost">Tutup</a>
                         <a href="/public/orders/invoice.php?id=<?= $detail_order['id'] ?>" target="_blank"
                              class="btn btn--primary">
                              Cetak Invoice
                         </a>
                    </div>
               </div>
          </div>
          <?php endif; ?>

          <?php include 'layout-footer.php'; ?>
     <script src="/public/assets/js/admin.js"></script>
</body>

</html>