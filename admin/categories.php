<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';
requireAdmin();

$flash = getFlash();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $action = $_POST['action'] ?? '';

     if ($action === 'add') {
          $name = sanitize($_POST['name'] ?? '');
          $desc = sanitize($_POST['description'] ?? '');
          if ($name) {
               $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?, ?)");
               mysqli_stmt_bind_param($stmt, 'ss', $name, $desc);
               if (mysqli_stmt_execute($stmt)) {
                    flash('success', "Kategori \"$name\" berhasil ditambahkan.");
               } else {
                    flash('error', 'Gagal menambahkan kategori.');
               }
          }
     } elseif ($action === 'edit') {
          $id = (int) $_POST['id'];
          $name = sanitize($_POST['name'] ?? '');
          $desc = sanitize($_POST['description'] ?? '');
          if ($id && $name) {
               $stmt = mysqli_prepare($conn, "UPDATE categories SET name=?, description=? WHERE id=?");
               mysqli_stmt_bind_param($stmt, 'ssi', $name, $desc, $id);
               mysqli_stmt_execute($stmt) ? flash('success', 'Kategori berhasil diperbarui.') : flash('error', 'Gagal memperbarui.');
          }
     } elseif ($action === 'delete') {
          $id = (int) $_POST['id'];
          // Check if category has products
          $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE category_id=$id"))['c'] ?? 0;
          if ($count > 0) {
               flash('error', "Tidak bisa hapus — kategori masih memiliki $count produk.");
          } else {
               mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
               flash('success', 'Kategori berhasil dihapus.');
          }
     }
     redirect('/admin/categories.php');
}

// Get categories with product count
$categories_q = mysqli_query($conn, "
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
");
$categories = [];
while ($row = mysqli_fetch_assoc($categories_q))
     $categories[] = $row;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Kategori — Admin Kantin Digital</title>
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
                         <span class="breadcrumb-item breadcrumb-item--active">Kategori</span>
                    </nav>
               </div>
               <div class="admin-topbar-right">
                    <a href="/auth/logout.php" class="btn btn--ghost btn--sm">Logout</a>
               </div>
          </header>

          <div class="admin-content">
               <div class="admin-page-header">
                    <h1 class="admin-page-title">Kategori Produk</h1>
                    <button class="btn btn--primary" onclick="openModal('addModal')">
                         <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2">
                              <line x1="12" y1="5" x2="12" y2="19" />
                              <line x1="5" y1="12" x2="19" y2="12" />
                         </svg>
                         Tambah Kategori
                    </button>
               </div>

               <?php if ($flash): ?>
                    <div class="alert alert--<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
               <?php endif; ?>

               <div class="admin-card">
                    <div class="admin-card__body">
                         <div class="table-wrap">
                              <table class="table">
                                   <thead>
                                        <tr>
                                             <th>#</th>
                                             <th>Nama Kategori</th>
                                             <th>Deskripsi</th>
                                             <th>Jumlah Produk</th>
                                             <th>Aksi</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        <?php if (empty($categories)): ?>
                                             <tr>
                                                  <td colspan="5" class="table-empty">Belum ada kategori</td>
                                             </tr>
                                        <?php else: ?>
                                             <?php foreach ($categories as $i => $cat): ?>
                                                  <tr>
                                                       <td class="text-muted text-sm"><?= $i + 1 ?></td>
                                                       <td><strong><?= e($cat['name']) ?></strong></td>
                                                       <td class="text-muted"><?= e($cat['description'] ?: '—') ?></td>
                                                       <td>
                                                            <span class="badge badge--default"><?= $cat['product_count'] ?>
                                                                 produk</span>
                                                       </td>
                                                       <td>
                                                            <div class="table-actions">
                                                                 <button class="btn btn--ghost btn--xs"
                                                                      onclick='openEditModal(<?= json_encode($cat) ?>)'>
                                                                      Edit
                                                                 </button>
                                                                 <?php if ($cat['product_count'] == 0): ?>
                                                                      <form method="POST"
                                                                           onsubmit="return confirm('Hapus kategori ini?')">
                                                                           <input type="hidden" name="action" value="delete">
                                                                           <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                                                           <button type="submit"
                                                                                class="btn btn--danger btn--xs">Hapus</button>
                                                                      </form>
                                                                 <?php endif; ?>
                                                            </div>
                                                       </td>
                                                  </tr>
                                             <?php endforeach; ?>
                                        <?php endif; ?>
                                   </tbody>
                              </table>
                         </div>
                    </div>
               </div>
          </div>
     </main>

     <!-- Add Modal -->
     <div class="modal-overlay" id="addModal">
          <div class="modal">
               <div class="modal-header">
                    <h3 class="modal-title">Tambah Kategori</h3>
                    <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
               </div>
               <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                         <div class="form-group">
                              <label class="form-label">Nama Kategori *</label>
                              <input type="text" name="name" class="input" placeholder="cth: Makanan Berat" required>
                         </div>
                         <div class="form-group">
                              <label class="form-label">Deskripsi</label>
                              <textarea name="description" class="textarea" rows="3"
                                   placeholder="Deskripsi singkat..."></textarea>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn--ghost" onclick="closeModal('addModal')">Batal</button>
                         <button type="submit" class="btn btn--primary">Simpan</button>
                    </div>
               </form>
          </div>
     </div>

     <!-- Edit Modal -->
     <div class="modal-overlay" id="editModal">
          <div class="modal">
               <div class="modal-header">
                    <h3 class="modal-title">Edit Kategori</h3>
                    <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
               </div>
               <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body">
                         <div class="form-group">
                              <label class="form-label">Nama Kategori *</label>
                              <input type="text" name="name" id="editName" class="input" required>
                         </div>
                         <div class="form-group">
                              <label class="form-label">Deskripsi</label>
                              <textarea name="description" id="editDesc" class="textarea" rows="3"></textarea>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn--ghost" onclick="closeModal('editModal')">Batal</button>
                         <button type="submit" class="btn btn--primary">Update</button>
                    </div>
               </form>
          </div>
     </div>

     <?php include 'layout-footer.php'; ?>
     <script src="/public/assets/js/admin.js"></script>
     <script>
          function openModal(id) {
               document.getElementById(id).classList.add('active');
          }
          function closeModal(id) {
               document.getElementById(id).classList.remove('active');
          }
          function openEditModal(cat) {
               document.getElementById('editId').value = cat.id;
               document.getElementById('editName').value = cat.name;
               document.getElementById('editDesc').value = cat.description || '';
               openModal('editModal');
          }
     </script>
</body>

</html>