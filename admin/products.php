<?php
require "../core/database.php";

$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
}

if (isset($_POST['save'])) {
     $id = (int) ($_POST['id'] ?? 0);
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $price = (int) $_POST['price'];
     $stock = (int) $_POST['stock'];
     $category_id = (int) $_POST['category_id'];

     $image = $edit['image'] ?? "";
     if (!empty($_FILES['image']['name'])) {
          $image = time() . "_" . $_FILES['image']['name'];
          move_uploaded_file($_FILES['image']['tmp_name'], "../public/uploads/" . $image);
     }

     if ($id > 0) {
          mysqli_query($conn, "UPDATE products SET name='$name', price=$price, stock=$stock, category_id=$category_id, image='$image' WHERE id=$id");
     } else {
          mysqli_query($conn, "INSERT INTO products (name,price,stock,category_id,image) VALUES ('$name',$price,$stock,$category_id,'$image')");
     }
     header("Location: products.php");
     exit;
}

if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     mysqli_query($conn, "DELETE FROM products WHERE id=$id");
     header("Location: products.php");
     exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC");

include "layout.php";
?>

<h1>Manajemen Produk</h1>

<div class="card">
     <h3 style="font-size: 14px; margin-bottom: 20px; color: var(--text-muted);">
          <?= $edit ? 'UBAH PRODUK' : 'TAMBAH PRODUK BARU' ?></h3>
     <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
          <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
               <input name="name" placeholder="Nama Produk" value="<?= $edit['name'] ?? '' ?>" required>
               <input type="number" name="price" placeholder="Harga" value="<?= $edit['price'] ?? '' ?>" required>
               <input type="number" name="stock" placeholder="Stok" value="<?= $edit['stock'] ?? '' ?>" required>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
               <select name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php mysqli_data_seek($categories, 0);
                    while ($c = mysqli_fetch_assoc($categories)): ?>
                         <option value="<?= $c['id'] ?>" <?= (isset($edit['category_id']) && $edit['category_id'] == $c['id']) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endwhile; ?>
               </select>
               <input type="file" name="image" style="border: 1px dashed var(--border); background: transparent;">
          </div>
          <button name="save" class="btn-admin primary"><?= $edit ? 'Perbarui Produk' : 'Simpan Produk' ?></button>
          <?php if ($edit): ?> <a href="products.php" class="btn-admin danger">Batal</a> <?php endif; ?>
     </form>
</div>

<div class="card">
     <table>
          <thead>
               <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
               </tr>
          </thead>
          <tbody>
               <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                         <td><img src="../public/uploads/<?= $p['image'] ?>" class="img-product"
                                   onerror="this.src='../public/assets/img/default.jpg'"></td>
                         <td><strong><?= $p['name'] ?></strong></td>
                         <td><?= $p['cat_name'] ?? '-' ?></td>
                         <td>Rp <?= number_format($p['price']) ?></td>
                         <td><?= $p['stock'] ?></td>
                         <td>
                              <a href="?edit=<?= $p['id'] ?>" class="btn-admin primary"
                                   style="font-size: 12px; padding: 6px 12px;">Edit</a>
                              <a href="javascript:void(0)" onclick="hapusData('?hapus=<?= $p['id'] ?>')"
                                   class="btn-admin danger" style="font-size: 12px; padding: 6px 12px;">Hapus</a>
                         </td>
                    </tr>
               <?php endwhile; ?>
          </tbody>
     </table>
</div>