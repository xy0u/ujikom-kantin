<?php
require "../core/database.php";
include "layout.php";

// PROSES SIMPAN/UPDATE
if (isset($_POST['save'])) {
     $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
     $name = $_POST['name'];

     if ($id > 0) {
          $stmt = mysqli_prepare($conn, "UPDATE categories SET name=? WHERE id=?");
          mysqli_stmt_bind_param($stmt, "si", $name, $id);
     } else {
          $stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
          mysqli_stmt_bind_param($stmt, "s", $name);
     }
     mysqli_stmt_execute($stmt);
     header("Location: categories.php");
     exit;
}

// PROSES HAPUS
if (isset($_GET['hapus'])) {
     $id = (int) $_GET['hapus'];
     $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id=?");
     mysqli_stmt_bind_param($stmt, "i", $id);
     mysqli_stmt_execute($stmt);
     header("Location: categories.php");
     exit;
}

$edit = null;
if (isset($_GET['edit'])) {
     $id = (int) $_GET['edit'];
     $result = mysqli_query($conn, "SELECT * FROM categories WHERE id=$id");
     $edit = mysqli_fetch_assoc($result);
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>

<div class="header-section">
     <h1>Categories Management</h1>
</div>

<div class="card">
     <form method="POST" class="form-inline">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
          <input name="name" class="form-control" placeholder="New Category Name" value="<?= $edit['name'] ?? '' ?>"
               required>
          <button name="save" class="btn primary"><?= $edit ? 'Update Category' : 'Add Category' ?></button>
          <?php if ($edit): ?> <a href="categories.php" class="btn">Cancel</a> <?php endif; ?>
     </form>
</div>

<div class="card">
     <table class="styled-table">
          <thead>
               <tr>
                    <th>Category Name</th>
                    <th width="200">Action</th>
               </tr>
          </thead>
          <tbody>
               <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                         <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                         <td>
                              <a href="?edit=<?= $c['id'] ?>" class="btn-sm edit">Edit</a>
                              <a href="?hapus=<?= $c['id'] ?>" onclick="return confirm('Are you sure?')"
                                   class="btn-sm danger">Delete</a>
                         </td>
                    </tr>
               <?php endwhile; ?>
          </tbody>
     </table>
</div>