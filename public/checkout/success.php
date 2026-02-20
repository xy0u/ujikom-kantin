<?php
session_start();
require "../../core/database.php";
require "../../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$order_id = (int) ($_GET['order'] ?? 0);
$user_id = $_SESSION['user_id'];

$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id"));

if (!$order) {
     header("Location: ../index.php");
     exit;
}

// Get order items
$items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <title>Order Success - Kantin Digital</title>
     <link rel="stylesheet" href="../assets/css/public.css">
</head>

<body>
     <?php include "../components/navbar.php"; ?>

     <main>
          <section class="hero" style="height: 50vh;">
               <h1>SUCCESS!</h1>
               <p>Your order has been placed</p>
          </section>

          <div style="padding: 0 5% 100px;">
               <div
                    style="background: var(--surface); border: 3px solid var(--fg); padding: 40px; max-width: 600px; margin: 0 auto;">
                    <div style="text-align: center; margin-bottom: 30px;">
                         <span style="font-size: 4rem;">🎉</span>
                         <h2 style="margin-top: 20px;">Order #<?= $order['id'] ?></h2>
                         <p style="color: var(--muted);"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>

                    <hr style="margin: 20px 0; border-color: var(--border);">

                    <div style="display: grid; gap: 15px;">
                         <?php while ($item = mysqli_fetch_assoc($items)): ?>
                              <div style="display: flex; justify-content: space-between;">
                                   <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                                   <span><?= format_rp($item['price'] * $item['quantity']) ?></span>
                              </div>
                         <?php endwhile; ?>
                    </div>

                    <hr style="margin: 20px 0; border-color: var(--border);">

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                         <span style="font-weight: 800;">TOTAL</span>
                         <h3 style="font-size: 2rem;"><?= format_rp($order['total']) ?></h3>
                    </div>

                    <div style="display: flex; gap: 20px; margin-top: 40px;">
                         <a href="invoice.php?order=<?= $order_id ?>" class="btn-buy"
                              style="flex: 1; text-align: center;">Download Invoice</a>
                         <a href="../index.php" class="btn-buy"
                              style="flex: 1; text-align: center; background: transparent; color: var(--fg);">Continue
                              Shopping</a>
                    </div>
               </div>
          </div>
     </main>

     <?php include "../components/footer.php"; ?>
</body>

</html>