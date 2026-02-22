<?php
session_start();
require "../../core/database.php";
require "../../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$order_id = (int) ($_GET['order'] ?? 0);
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$order_id AND user_id=" . $_SESSION['user_id']));

if (!$order) {
     header("Location: ../index.php");
     exit;
}

include "../../components/navbar.php";
?>

<main>
     <section class="hero" style="height: 300px;">
          <h1>SUKSES!</h1>
          <p>Order #<?= $order_id ?></p>
     </section>

     <div class="success-container">
          <div class="success-box">
               <span class="success-icon">✓</span>
               <h2>Pesanan Berhasil!</h2>
               <p>Terima kasih telah berbelanja di Kantin Digital</p>
               <p>Total: <?= format_rp($order['total']) ?></p>
               <a href="../index.php" class="btn-buy">KEMBALI KE MENU</a>
          </div>
     </div>
</main>

<?php include "../../components/footer.php"; ?>