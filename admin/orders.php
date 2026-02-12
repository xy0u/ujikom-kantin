<?php
require "../core/database.php";
include "layout.php";

/* FILTER */
$statusFilter = $_GET['status'] ?? '';

$where = "";
if ($statusFilter) {
     $where = "WHERE status='$statusFilter'";
}

/* GET ORDERS */
$orders = mysqli_query($conn, "
SELECT o.*, u.name 
FROM orders o 
JOIN users u ON o.user_id=u.id
$where
ORDER BY o.id DESC
");

/* CONFIRM */
if (isset($_GET['confirm'])) {
     $id = (int) $_GET['confirm'];
     mysqli_query($conn, "UPDATE orders SET status='SUCCESS' WHERE id=$id");
     header("Location: orders.php");
     exit;
}
?>

<h1>Orders</h1>

<div class="card">
<a href="orders.php" class="btn">All</a>
<a href="?status=PENDING" class="btn">Pending</a>
<a href="?status=SUCCESS" class="btn">Success</a>
</div>

<div class="card">
<table>
<tr>
<th>ID</th>
<th>User</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while ($o = mysqli_fetch_assoc($orders)): ?>
     <tr>
     <td>#<?= $o['id'] ?></td>
     <td><?= $o['name'] ?></td>
     <td>Rp <?= number_format($o['total']) ?></td>
     <td>
     <span class="status <?= strtolower($o['status']) ?>">
     <?= $o['status'] ?>
     </span>
     </td>
     <td>
     <?php if ($o['status'] == "PENDING"): ?>
          <a href="?confirm=<?= $o['id'] ?>" class="btn">Confirm</a>
     <?php endif; ?>
     <a href="?detail=<?= $o['id'] ?>" class="btn">Detail</a>
     </td>
     </tr>

     <?php
     if (isset($_GET['detail']) && $_GET['detail'] == $o['id']):
          $items = mysqli_query($conn, "
SELECT oi.*, p.name 
FROM order_items oi 
JOIN products p ON oi.product_id=p.id
WHERE order_id=" . $o['id']);
          ?>
          <tr>
          <td colspan="5">
          <?php while ($i = mysqli_fetch_assoc($items)): ?>
               - <?= $i['name'] ?> (<?= $i['quantity'] ?>) <br>
          <?php endwhile; ?>
          </td>
          </tr>
     <?php endif; ?>

<?php endwhile; ?>

</table>
</div>

</main>
</div>
</body>
</html>
