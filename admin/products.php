<?php
require "../core/database.php";
require "../core/helpers.php";

// Logic Update/Save
if (isset($_POST['save'])) {
     $id = (int) ($_POST['id'] ?? 0);
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $price = (int) $_POST['price'];
     $stock = (int) $_POST['stock'];
     $category_id = (int) $_POST['category_id'];
     $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'available');

     // Handle Image
     $image_name = $_POST['old_image'] ?? "";
     if (!empty($_FILES['image']['name'])) {
          $image_name = time() . "_" . $_FILES['image']['name'];
          $target_dir = "../public/uploads/";
          if (!is_dir($target_dir)) {
               mkdir($target_dir, 0777, true);
          }
          move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name);
     }

     if ($id > 0) {
          $query = "UPDATE products SET 
                  name='$name', 
                  price=$price, 
                  stock=$stock, 
                  category_id=$category_id, 
                  status='$status',
                  image='$image_name' 
                  WHERE id=$id";
     } else {
          $query = "INSERT INTO products (name, price, stock, category_id, status, image) 
                  VALUES ('$name', $price, $stock, $category_id, '$status', '$image_name')";
     }

     if (mysqli_query($conn, $query)) {
          header("Location: products.php?success=1");
     } else {
          header("Location: products.php?error=1");
     }
     exit;
}

if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     mysqli_query($conn, "DELETE FROM products WHERE id=$id");
     header("Location: products.php");
     exit;
}

include "layout.php";

$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name 
                                 FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.id DESC");
?>

<h1>Manajemen Produk</h1>

<?php if (isset($_GET['success'])): ?>
     <div class="alert success">Produk berhasil disimpan!</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
     <div class="alert error">Gagal menyimpan produk!</div>
<?php endif; ?>

<div class="card">
     <h3><?= $edit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
     <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
          <input type="hidden" name="old_image" value="<?= $edit['image'] ?? '' ?>">

          <div class="form-grid">
               <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" value="<?= $edit['name'] ?? '' ?>" required>
               </div>

               <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" required>
                         <option value="">Pilih Kategori</option>
                         <?php mysqli_data_seek($categories, 0);
                         while ($c = mysqli_fetch_assoc($categories)): ?>
                              <option value="<?= $c['id'] ?>" <?= (isset($edit['category_id']) && $edit['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                   <?= htmlspecialchars($c['name']) ?>
                              </option>
                         <?php endwhile; ?>
                    </select>
               </div>

               <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" value="<?= $edit['price'] ?? '' ?>" required>
               </div>

               <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stock" value="<?= $edit['stock'] ?? '' ?>" required>
               </div>

               <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                         <option value="available" <?= (isset($edit['status']) && $edit['status'] == 'available') ? 'selected' : '' ?>>Available</option>
                         <option value="soldout" <?= (isset($edit['status']) && $edit['status'] == 'soldout') ? 'selected' : '' ?>>Sold Out</option>
                         <option value="coming" <?= (isset($edit['status']) && $edit['status'] == 'coming') ? 'selected' : '' ?>>Coming Soon</option>
                    </select>
               </div>

               <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="image">
                    <?php if (!empty($edit['image'])): ?>
                         <small>Current: <?= $edit['image'] ?></small>
                    <?php endif; ?>
               </div>
          </div>

          <div class="form-actions">
               <button type="submit" name="save" class="btn-admin primary">
                    <?= $edit ? 'Update Produk' : 'Tambah Produk' ?>
               </button>
               <?php if ($edit): ?>
                    <a href="products.php" class="btn-admin danger">Batal</a>
               <?php endif; ?>
          </div>
     </form>
</div>

<div class="card">
     <table>
          <thead>
               <tr>
                    <th>Gambar</th>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
               </tr>
          </thead>
          <tbody>
               <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                         <td>
                              <img src="../public/uploads/<?= $p['image'] ?>" class="img-product"
                                   onerror="this.src='../public/assets/img/default.jpg'">
                         </td>
                         <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                         <td><?= $p['cat_name'] ?></td>
                         <td><?= format_rp($p['price']) ?></td>
                         <td><?= $p['stock'] ?></td>
                         <td>
                              <span class="status-badge status-<?= $p['status'] ?>">
                                   <?= $p['status'] ?>
                              </span>
                         </td>
                         <td>
                              <a href="?edit=<?= $p['id'] ?>" class="btn-admin primary small">Edit</a>
                              <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Hapus produk ini?')"
                                   class="btn-admin danger small">Hapus</a>
                         </td>
                    </tr>
               <?php endwhile; ?>
          </tbody>
     </table>
</div>

<style>
     .form-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 20px;
          margin-bottom: 20px;
     }

     .form-group {
          display: flex;
          flex-direction: column;
          gap: 5px;
     }

     .form-group label {
          font-size: 12px;
          font-weight: 600;
          color: var(--text-muted);
          text-transform: uppercase;
          letter-spacing: 1px;
     }

     .form-actions {
          display: flex;
          gap: 10px;
          margin-top: 20px;
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

     .status-badge {
          padding: 4px 8px;
          border-radius: 4px;
          font-size: 11px;
          font-weight: 600;
          text-transform: uppercase;
     }

     .status-available {
          background: rgba(34, 197, 94, 0.2);
          color: #22c55e;
     }

     .status-soldout {
          background: rgba(239, 68, 68, 0.2);
          color: #ef4444;
     }

     .status-coming {
          background: rgba(234, 179, 8, 0.2);
          color: #eab308;
     }

     .btn-admin.small {
          padding: 4px 8px;
          font-size: 12px;
     }
</style>

</main>
</div>
</body>

</html>