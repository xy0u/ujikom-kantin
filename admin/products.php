<?php
$title = "Manajemen Produk";
require "../core/database.php";
require "../core/helpers.php";
include "layout.php";

$success = '';
$error = '';

// TAMBAH / UPDATE PRODUK
if (isset($_POST['save'])) {
     $id = (int) ($_POST['id'] ?? 0);
     $name = mysqli_real_escape_string($conn, trim($_POST['name']));
     $price = (int) str_replace(['.', ','], '', $_POST['price']);
     $stock = (int) $_POST['stock'];
     $cat_id = (int) ($_POST['category_id'] ?? 0);
     $status = in_array($_POST['status'], ['available', 'soldout', 'coming']) ? $_POST['status'] : 'available';

     // Handle image upload
     $image_sql = '';
     if (!empty($_FILES['image']['name'])) {
          $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
          $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
          if (in_array($ext, $allowed) && $_FILES['image']['size'] < 2 * 1024 * 1024) {
               $filename = time() . '_' . uniqid() . '.' . $ext;
               $dest = '../public/uploads/' . $filename;
               if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_sql = ", image='$filename'";
               }
          } else {
               $_SESSION['error'] = "File gambar tidak valid atau terlalu besar (maks 2MB).";
               header("Location: products.php");
               exit;
          }
     }

     if (empty($name) || $price <= 0) {
          $_SESSION['error'] = "Nama produk dan harga wajib diisi.";
          header("Location: products.php");
          exit;
     }

     if ($id > 0) {
          mysqli_query(
               $conn,
               "UPDATE products SET
                name='$name', price=$price, stock=$stock,
                category_id=$cat_id, status='$status'$image_sql
             WHERE id=$id"
          );
          $_SESSION['success'] = "Produk \"$name\" berhasil diupdate!";
     } else {
          mysqli_query(
               $conn,
               "INSERT INTO products (name, price, stock, category_id, status)
             VALUES ('$name', $price, $stock, $cat_id, '$status')"
          );
          if ($image_sql) {
               $new_id = mysqli_insert_id($conn);
               mysqli_query($conn, "UPDATE products SET image='" . ltrim(explode("'", $image_sql)[1], ',image=') . "' WHERE id=$new_id");
          }
          $_SESSION['success'] = "Produk \"$name\" berhasil ditambahkan!";
     }

     header("Location: products.php");
     exit;
}

// HAPUS PRODUK
if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM products WHERE id=$id"));
     if ($p) {
          mysqli_query($conn, "DELETE FROM products WHERE id=$id");
          $_SESSION['success'] = "Produk \"{$p['name']}\" berhasil dihapus.";
     }
     header("Location: products.php");
     exit;
}

// GET DATA
$edit = null;
if (isset($_GET['edit'])) {
     $edit_id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$edit_id"));
}

// Search & Filter
$search = $_GET['search'] ?? '';
$filter_cat = (int) ($_GET['cat'] ?? 0);
$filter_status = $_GET['status'] ?? '';

$where = [];
if ($search)
     $where[] = "p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
if ($filter_cat > 0)
     $where[] = "p.category_id=$filter_cat";
if ($filter_status)
     $where[] = "p.status='" . mysqli_real_escape_string($conn, $filter_status) . "'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Pagination
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products p $where_sql"))['t'];
$total_pages = max(1, ceil($total / $limit));

$products = mysqli_query(
     $conn,
     "SELECT p.*, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     $where_sql
     ORDER BY p.id DESC
     LIMIT $offset, $limit"
);

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<!-- Alerts -->
<?php if (isset($_SESSION['success'])): ?>
     <div class="alert success">
          <?= htmlspecialchars($_SESSION['success']);
          unset($_SESSION['success']); ?>
          <button class="close-alert" onclick="this.parentElement.remove()">×</button>
     </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
     <div class="alert error">
          <?= htmlspecialchars($_SESSION['error']);
          unset($_SESSION['error']); ?>
          <button class="close-alert" onclick="this.parentElement.remove()">×</button>
     </div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
     <h1>Manajemen Produk</h1>
     <div class="page-header-actions">
          <button class="btn-primary" onclick="toggleForm()">+ Tambah Produk</button>
     </div>
</div>

<!-- Form Tambah/Edit Produk -->
<div id="productForm" style="display:<?= $edit ? 'block' : 'none' ?>; margin-bottom: 24px;">
     <div class="card">
          <h2 class="card-title"><?= $edit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h2>
          <form method="POST" enctype="multipart/form-data" class="product-form">
               <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

               <div class="form-row">
                    <div class="form-group">
                         <label>Nama Produk <span class="required">*</span></label>
                         <input type="text" name="name" value="<?= htmlspecialchars($edit['name'] ?? '') ?>"
                              placeholder="Nama produk" required>
                    </div>
                    <div class="form-group">
                         <label>Harga (Rp) <span class="required">*</span></label>
                         <input type="number" name="price" value="<?= $edit['price'] ?? '' ?>" placeholder="0" min="0"
                              required>
                    </div>
               </div>

               <div class="form-row">
                    <div class="form-group">
                         <label>Stok <span class="required">*</span></label>
                         <input type="number" name="stock" value="<?= $edit['stock'] ?? '' ?>" placeholder="0" min="0"
                              required>
                    </div>
                    <div class="form-group">
                         <label>Kategori</label>
                         <select name="category_id">
                              <option value="">-- Pilih Kategori --</option>
                              <?php
                              mysqli_data_seek($categories, 0);
                              while ($cat = mysqli_fetch_assoc($categories)):
                                   ?>
                                   <option value="<?= $cat['id'] ?>" <?= ($edit['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                   </option>
                              <?php endwhile; ?>
                         </select>
                    </div>
               </div>

               <div class="form-row">
                    <div class="form-group">
                         <label>Status</label>
                         <select name="status">
                              <option value="available" <?= ($edit['status'] ?? '') === 'available' ? 'selected' : '' ?>>
                                   Available</option>
                              <option value="soldout" <?= ($edit['status'] ?? '') === 'soldout' ? 'selected' : '' ?>>Sold
                                   Out</option>
                              <option value="coming" <?= ($edit['status'] ?? '') === 'coming' ? 'selected' : '' ?>>Coming
                                   Soon</option>
                         </select>
                    </div>
                    <div class="form-group">
                         <label>Gambar Produk</label>
                         <input type="file" name="image" accept="image/*">
                         <?php if (!empty($edit['image'])): ?>
                              <small class="form-help">Gambar saat ini: <?= htmlspecialchars($edit['image']) ?></small>
                         <?php endif; ?>
                         <small class="form-help">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                    </div>
               </div>

               <div class="form-actions">
                    <button type="submit" name="save" class="btn-primary">
                         <?= $edit ? '💾 Update Produk' : '+ Simpan Produk' ?>
                    </button>
                    <button type="button" class="btn-secondary" onclick="cancelForm()">Batal</button>
               </div>
          </form>
     </div>
</div>

<!-- Filter & Search -->
<div class="card" style="margin-bottom:24px;">
     <form method="GET" class="filter-form">
          <div class="filter-row">
               <div class="filter-group" style="flex:2;">
                    <label>Cari Produk</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                         placeholder="Nama produk...">
               </div>
               <div class="filter-group">
                    <label>Kategori</label>
                    <select name="cat">
                         <option value="">Semua Kategori</option>
                         <?php
                         mysqli_data_seek($categories, 0);
                         while ($cat = mysqli_fetch_assoc($categories)):
                              ?>
                              <option value="<?= $cat['id'] ?>" <?= $filter_cat == $cat['id'] ? 'selected' : '' ?>>
                                   <?= htmlspecialchars($cat['name']) ?>
                              </option>
                         <?php endwhile; ?>
                    </select>
               </div>
               <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                         <option value="">Semua Status</option>
                         <option value="available" <?= $filter_status === 'available' ? 'selected' : '' ?>>Available
                         </option>
                         <option value="soldout" <?= $filter_status === 'soldout' ? 'selected' : '' ?>>Sold Out</option>
                         <option value="coming" <?= $filter_status === 'coming' ? 'selected' : '' ?>>Coming Soon</option>
                    </select>
               </div>
               <div class="filter-actions">
                    <button type="submit" class="btn-primary btn-sm">Filter</button>
                    <a href="products.php" class="btn-secondary btn-sm">Reset</a>
               </div>
          </div>
     </form>
</div>

<!-- Products Table -->
<div class="card">
     <div class="card-header">
          <h2>Daftar Produk
               <span style="font-size:0.65rem; color:var(--fg-4); margin-left:8px;">(<?= $total ?> produk)</span>
          </h2>
          <?php if ($search || $filter_cat || $filter_status): ?>
               <span class="badge badge-info">Filter aktif</span>
          <?php endif; ?>
     </div>

     <div class="table-responsive">
          <table class="admin-table">
               <thead>
                    <tr>
                         <th>#</th>
                         <th>Produk</th>
                         <th>Kategori</th>
                         <th>Harga</th>
                         <th>Stok</th>
                         <th>Status</th>
                         <th>Aksi</th>
                    </tr>
               </thead>
               <tbody>
                    <?php if (mysqli_num_rows($products) > 0): ?>
                         <?php while ($p = mysqli_fetch_assoc($products)): ?>
                              <tr>
                                   <td class="col-id">#<?= $p['id'] ?></td>
                                   <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                             <div class="product-image">
                                                  <img src="../public/uploads/<?= htmlspecialchars($p['image'] ?: 'default.jpg') ?>"
                                                       alt="<?= htmlspecialchars($p['name']) ?>"
                                                       onerror="this.src='../public/assets/img/default.jpg'">
                                             </div>
                                             <span class="col-name"><?= htmlspecialchars($p['name']) ?></span>
                                        </div>
                                   </td>
                                   <td>
                                        <span style="font-family:var(--font-mono); font-size:0.75rem; color:var(--fg-3);">
                                             <?= htmlspecialchars($p['category_name'] ?? '—') ?>
                                        </span>
                                   </td>
                                   <td style="font-family:var(--font-mono); font-size:0.85rem;">
                                        <?= format_rp($p['price']) ?>
                                   </td>
                                   <td>
                                        <span
                                             class="stock-badge <?= $p['stock'] == 0 ? 'zero' : ($p['stock'] < 5 ? 'low' : '') ?>">
                                             <?= $p['stock'] ?>
                                        </span>
                                   </td>
                                   <td>
                                        <span
                                             class="badge badge-<?= $p['status'] === 'available' ? 'available' : ($p['status'] === 'soldout' ? 'soldout' : 'info') ?>">
                                             <?= ucfirst($p['status']) ?>
                                        </span>
                                   </td>
                                   <td>
                                        <div class="action-buttons">
                                             <a href="?edit=<?= $p['id'] ?>" class="btn-edit btn-sm" title="Edit">✏️ Edit</a>
                                             <a href="?hapus=<?= $p['id'] ?>" class="btn-delete btn-sm"
                                                  onclick="return confirm('Hapus produk \'<?= htmlspecialchars(addslashes($p['name'])) ?>\'?')"
                                                  title="Hapus">🗑️ Hapus</a>
                                        </div>
                                   </td>
                              </tr>
                         <?php endwhile; ?>
                    <?php else: ?>
                         <tr>
                              <td colspan="7" style="text-align:center; padding:48px; color:var(--fg-4);">
                                   <div
                                        style="font-family:var(--font-mono); font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase;">
                                        Tidak ada produk ditemukan
                                   </div>
                              </td>
                         </tr>
                    <?php endif; ?>
               </tbody>
          </table>
     </div>

     <!-- Pagination -->
     <?php if ($total_pages > 1): ?>
          <div class="pagination">
               <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&cat=<?= $filter_cat ?>&status=<?= urlencode($filter_status) ?>"
                         class="page-link <?= $i == $page ? 'active' : '' ?>">
                         <?= $i ?>
                    </a>
               <?php endfor; ?>
          </div>
     <?php endif; ?>
</div>

<script>
     function toggleForm() {
          const f = document.getElementById('productForm');
          f.style.display = f.style.display === 'none' ? 'block' : 'none';
          if (f.style.display === 'block') {
               f.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
     }

     function cancelForm() {
          const f = document.getElementById('productForm');
          f.style.display = 'none';
          window.location.href = 'products.php';
     }
</script>

<?php include "layout-footer.php"; ?>