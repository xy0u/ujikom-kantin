<?php
require "../core/database.php";
include "layout.php";

if (isset($_POST['save'])) {
     $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
     $name = mysqli_real_escape_string($conn, $_POST['name']);

     if ($id > 0) {
          mysqli_query($conn, "UPDATE categories SET name='$name' WHERE id=$id");
     } else {
          mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
     }

     header("Location: categories.php");
     exit;
}

if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
     header("Location: categories.php");
     exit;
}

$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id=$id"));
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>

<h1>Categories</h1>

<div class="card">
     <form method="POST" class="form-inline">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
          <input name="name" placeholder="Category Name" value="<?= $edit['name'] ?? '' ?>" required>
          <button name="save" class="btn"><?= $edit ? 'Update' : 'Add' ?></button>
     </form>
</div>

<div class="card">
     <table>
          <tr>
               <th>Name</th>
               <th>Action</th>
          </tr>

          <?php while ($c = mysqli_fetch_assoc($categories)): ?>
               <tr>
                    <td><?= $c['name'] ?></td>
                    <td>
                         <a href="?edit=<?= $c['id'] ?>" class="btn">Edit</a>
                         <a href="?hapus=<?= $c['id'] ?>" onclick="return confirm('Delete this category?')"
                              class="btn danger">Delete</a>
                    </td>
               </tr>
          <?php endwhile; ?>
     </table>
</div>

</main>
</div>
</body>

</html>