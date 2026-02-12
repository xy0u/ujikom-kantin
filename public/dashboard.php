<?php
session_start();
require "../core/database.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

$user_id = $_SESSION['user_id'];

$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as total FROM orders WHERE user_id=$user_id
"))['total'];

$total_success = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as total FROM orders 
WHERE user_id=$user_id AND status='SUCCESS'
"))['total'];

$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT IFNULL(SUM(total),0) as total 
FROM orders 
WHERE user_id=$user_id AND status='SUCCESS'
"))['total'];
?>
<!DOCTYPE html>
<html>

<head>
     <title>Dashboard</title>
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>

     <section class="products">
          <div class="card">
               <h3>Total Orders</h3>
               <p>
                    <?= $total_orders ?>
               </p>
          </div>

          <div class="card">
               <h3>Successful Orders</h3>
               <p>
                    <?= $total_success ?>
               </p>
          </div>

          <div class="card">
               <h3>Total Spent</h3>
               <p>Rp
                    <?= number_format($total_spent) ?>
               </p>
          </div>
     </section>

</body>

</html>