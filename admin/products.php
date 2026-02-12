<?php
require "../core/database.php";
include "layout.php";

/* SAVE */
if (isset($_POST['save'])) {

     $id = (int) ($_POST['id'] ?? 0);
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $price = (int) $_POST['price'];
     $stock = (int) $_POST['stock'];
     $category_id = (int) $_POST['category_id'];

     $image = "";

     if (!empty($_FILES['image']['name'])) {
          $image = time() . "_" . $_FILES['image']['name'];
          move_uploaded_file($_FILES['image']['tmp_name'], "../public/uploads/" . $image);
     }

     if ($id > 0) {
          if ($image) {
               mysqli_query($conn, "UPDATE products 
                SET name='$name', price=$price, stock=$stock, category_id=$category_id, image='$image' 
                WHERE id=$id");
          } else {
               mysqli_query($conn, "UPDATE products 
                SET name='$name', price=$price, stock=$stock, category_id=$category_id 
                WHERE id=$id");
          }
     } else {
          mysqli_query($conn, "INSERT INTO products (name,price,stock,category_id,image) 
            VALUES ('$name',$price,$stock,$category_id,'$image')");
     }

     header("Location: products.php");
     exit;
}

/* DELETE */
if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     mysqli_query($conn, "DELETE FROM products WHERE id=$id");
     header("Location: products.php");
     exit;
}

/* EDIT */
$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
}

/* GET CATEGORIES */
$categories = mysqli_query($conn, "SELECT * FROM categories");

/* GET PRODUCTS */
$products = mysqli_query($conn, "
SELECT p.*, c.name as category_name 
FROM products p 
LEFT JOIN categories c ON p.category_id=c.id 
ORDER BY p.id DESC
");
?>

<h1>Product Management</h1>

<div class="card">
     <form method="POST" enctype="multipart/form-data" class="form-inline">

          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

          <input name="name" placeholder="Product Name" value="<?= $edit['name'] ?? '' ?>" required>

          <input type="number" name="price" placeholder="Price" value="<?= $edit['price'] ?? '' ?>" required>

          <input type="number" name="stock" placeholder="Stock" value="<?= $edit['stock'] ?? '' ?>" required>

          <select name="category_id" required>
               <option value="">Select Category</option>
               <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $c['id'] ?>" <?= isset($edit['category_id']) && $edit['category_id'] == $c['id'] ? 'selected' : '' ?>>
                         <?= $c['name'] ?>
                    </option>
               <?php endwhile; ?>
          </select>

          <input type="file" name="image">

          <button name="save" class="btn">
               <?= $edit ? 'Update' : 'Add' ?>
          </button>

     </form>
</div>

<div class="card">
     <table>
          <tr>
               <th>Image</th>
               <th>Name</th>
               <th>Category</th>
               <th>Price</th>
               <th>Stock</th>
               <th>Action</th>
          </tr>

          <?php while ($p = mysqli_fetch_assoc($products)): ?>
               <tr>
                    <td>
                         <?php if ($p['image']): ?>
                              <img src="../public/uploads/<?= $p['image'] ?>" width="60">
                         <?php endif; ?>
                    </td>
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['category_name'] ?? '-' ?></td>
                    <td>Rp <?= number_format($p['price']) ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td>
                         <a href="?edit=<?= $p['id'] ?>" class="btn">Edit</a>
                         <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Delete?')" class="btn danger">Delete</a>
                    </td>
               </tr>
          <?php endwhile; ?>
     </table>
</div>

</main>
</div>
</body>

</html>