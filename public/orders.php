<?php
session_start();
require "../core/database.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

$user_id = $_SESSION['user_id'];

$orders = mysqli_query($conn, "
SELECT * FROM orders 
WHERE user_id=$user_id
ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html>

<head>
     <title>My Orders</title>
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>

     <header class="navbar">
          <div class="logo">KANTIN</div>
          <nav>
               <a href="index.php">Menu</a>
               <a href="cart/index.php">Cart</a>
               <a href="../auth/logout.php">Logout</a>
          </nav>
     </header>

     <section class="products">

          <h2>My Orders</h2>

          <?php if (mysqli_num_rows($orders) == 0): ?>
               <p>Belum ada order.</p>
          <?php else: ?>

               <?php while ($o = mysqli_fetch_assoc($orders)):

                    $status_class = "status-pending";
                    if ($o['status'] == "SUCCESS")
                         $status_class = "status-success";
                    if ($o['status'] == "FAILED")
                         $status_class = "status-failed";
                    ?>

                    <div class="card">
                         <div class="card-body">
                              <p><strong>Order #
                                        <?= $o['id'] ?>
                                   </strong></p>
                              <p>Total: Rp
                                   <?= number_format($o['total']) ?>
                              </p>
                              <p>Status: <span class="<?= $status_class ?>">
                                        <?= $o['status'] ?>
                                   </span></p>

                              <a href="checkout/success.php?order=<?= $o['id'] ?>" class="btn">
                                   Detail
                              </a>

                              <?php if ($o['status'] == "FAILED"): ?>
                                   <a href="checkout/retry.php?order=<?= $o['id'] ?>" class="btn">
                                        Pay Again
                                   </a>
                              <?php endif; ?>

                         </div>
                    </div>

               <?php endwhile; ?>

          <?php endif; ?>

     </section>
</body>

</html>