<?php
session_start();
require "../../core/database.php";

/* ===== CEK LOGIN ===== */
if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

/* ===== VALIDASI ORDER ID ===== */
if (!isset($_GET['order'])) {
     header("Location: ../index.php");
     exit;
}

$order_id = (int) $_GET['order'];

/* ===== AMBIL ORDER ===== */
$order = mysqli_fetch_assoc(
     mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE id=$order_id 
    AND user_id=" . $_SESSION['user_id'])
);

if (!$order) {
     header("Location: ../index.php");
     exit;
}

/* ===== AMBIL ITEMS ===== */
$items = mysqli_query($conn, "
SELECT oi.*, p.name 
FROM order_items oi
JOIN products p ON oi.product_id=p.id
WHERE oi.order_id=$order_id
");

/* ===== STATUS CLASS ===== */
$status_class = "status-pending";

if ($order['status'] === "SUCCESS") {
     $status_class = "status-success";
} elseif ($order['status'] === "FAILED") {
     $status_class = "status-failed";
}
?>
<!DOCTYPE html>
<html>

<head>
     <title>Order Detail</title>
     <link rel="stylesheet" href="../assets/css/public.css">
</head>

<body class="success-body">

     <div class="success-box">

          <h2>🧾 Order Detail</h2>

          <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>

          <p>
               <strong>Status:</strong>
               <span class="<?= $status_class ?>">
                    <?= $order['status'] ?>
               </span>
          </p>

          <hr style="margin:20px 0;">

          <?php
          $grand_total = 0;
          while ($i = mysqli_fetch_assoc($items)):
               $subtotal = $i['price'] * $i['quantity'];
               $grand_total += $subtotal;
               ?>
               <div style="margin-bottom:10px;">
                    <?= $i['name'] ?>
                    (<?= $i['quantity'] ?> x Rp <?= number_format($i['price']) ?>)
                    <br>
                    <small>Subtotal: Rp <?= number_format($subtotal) ?></small>
               </div>
          <?php endwhile; ?>

          <hr style="margin:20px 0;">

          <h3>Total: Rp <?= number_format($grand_total) ?></h3>

          <br>
          <a href="invoice.php?order=<?= $order['id'] ?>" class="btn">
               Download Invoice
          </a>
          <a href="../index.php" class="btn">Back to Menu</a>
     </div>
</body>

</html>