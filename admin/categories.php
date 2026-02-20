<?php
require "../core/database.php";
include "layout.php";

// Handle form submission
if (isset($_POST['save'])) {
     $id = (int) ($_POST['id'] ?? 0);
     $name = mysqli_real_escape_string($conn, $_POST['name']);

     if (!empty($name)) {
          if ($id > 0) {
               $stmt = mysqli_prepare($conn, "UPDATE categories SET name=? WHERE id=?");
               mysqli_stmt_bind_param($stmt, "si", $name, $id);
          } else {
               $stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
               mysqli_stmt_bind_param($stmt, "s", $name);
          }

          if (mysqli_stmt_execute($stmt)) {
               header("Location: categories.php?success=1");
          } else {
               header("Location: categories.php?error=1");
          }
          exit;
     }
}

// Handle delete
if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];

     // Check if category has products
     $check = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE category_id=$id");
     $result = mysqli_fetch_assoc($check);

     if ($result['total'] == 0) {
          mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
          header("Location: categories.php?success=2");
     } else {
          header("Location: categories.php?error=2");
     }
     exit;
}

// Get edit data
$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id=$id"));
}

// Get all categories
$categories = mysqli_query($conn, "SELECT c.*, 
                                   (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
                                   FROM categories c 
                                   ORDER BY c.id DESC");
?>

<h1>Kategori Menu</h1>

<?php if (isset($_GET['success'])): ?>
     <div class="alert success">
          <?php if ($_GET['success'] == 1): ?>
               Kategori berhasil disimpan!
          <?php elseif ($_GET['success'] == 2): ?>
               Kategori berhasil dihapus!
          <?php endif; ?>
     </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
     <div class="alert error">
          <?php if ($_GET['error'] == 2): ?>
               Tidak dapat menghapus kategori yang masih memiliki produk!
          <?php else: ?>
               Gagal menyimpan kategori!
          <?php endif; ?>
     </div>
<?php endif; ?>

<div class="card">
     <h3><?= $edit ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h3>
     <form method="POST" style="display: flex; gap: 10px; margin-top: 15px;">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
          <input type="text" name="name" placeholder="Nama Kategori"
               value="<?= htmlspecialchars($edit['name'] ?? '') ?>" required style="flex: 1;">
          <button name="save" class="btn-admin primary"><?= $edit ? 'Update' : 'Tambah' ?></button>
          <?php if ($edit): ?>
               <a href="categories.php" class="btn-admin danger">Batal</a>
          <?php endif; ?>
     </form>
</div>

<div class="card">
     <table>
          <thead>
               <tr>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th width="200">Aksi</th>
               </tr>
          </thead>
          <tbody>
               <?php if (mysqli_num_rows($categories) > 0): ?>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                         <tr>
                              <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                              <td><?= $c['product_count'] ?> produk</td>
                              <td>
                                   <a href="?edit=<?= $c['id'] ?>" class="btn-admin primary small">Edit</a>
                                   <?php if ($c['product_count'] == 0): ?>
                                        <a href="?hapus=<?= $c['id'] ?>"
                                             onclick="return confirm('Hapus kategori <?= htmlspecialchars($c['name']) ?>?')"
                                             class="btn-admin danger small">Hapus</a>
                                   <?php else: ?>
                                        <span class="btn-admin danger small disabled"
                                             title="Tidak dapat menghapus kategori yang memiliki produk">Hapus</span>
                                   <?php endif; ?>
                              </td>
                         </tr>
                    <?php endwhile; ?>
               <?php else: ?>
                    <tr>
                         <td colspan="3" style="text-align: center; color: var(--text-muted);">
                              Belum ada kategori. Tambahkan kategori baru.
                         </td>
                    </tr>
               <?php endif; ?>
          </tbody>
     </table>
</div>

<style>
     .btn-admin.small {
          padding: 5px 10px;
          font-size: 12px;
          margin-right: 5px;
     }

     .btn-admin.disabled {
          opacity: 0.5;
          cursor: not-allowed;
          pointer-events: none;
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

     .alert.error {
          background: rgba(239, 68, 68, 0.1);
          color: #ef4444;
          border: 1px solid rgba(239, 68, 68, 0.2);
     }
</style>

</main>
</div>
</body>

</html>