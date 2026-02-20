<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

$user_id = $_SESSION['user_id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC");

include "../components/navbar.php";
?>

<section class="cart-container">
     <h1 style="margin-bottom: 40px; letter-spacing: -1px;">Pesanan Saya</h1>

     <?php if (mysqli_num_rows($orders) == 0): ?>
          <div class="card" style="text-align: center; padding: 60px;">
               <p style="color: var(--text-muted);">Kamu belum pernah melakukan pemesanan.</p>
               <a href="index.php" class="btn" style="width: auto; display: inline-block; margin-top: 20px;">Pesan
                    Sekarang</a>
          </div>
     <?php else: ?>
          <?php while ($o = mysqli_fetch_assoc($orders)): ?>
               <div class="cart-item">
                    <div>
                         <h3 style="font-size: 18px;">Order #<?= $o['id'] ?></h3>
                         <p style="color: var(--text-muted); font-size: 14px;"><?= date('d M Y', strtotime($o['created_at'])) ?>
                         </p>
                    </div>
                    <div style="text-align: right;">
                         <p style="font-weight: 700; margin-bottom: 5px;"><?= format_rp($o['total']) ?></p>
                         <span class="stock-badge <?= get_status_badge($o['status']) ?>">
                              <?= $o['status'] ?>
                         </span>
                         <div style="margin-top: 15px;">
                              <a href="checkout/invoice.php?id=<?= $o['id'] ?>" class="btn"
                                   style="padding: 8px 20px; font-size: 12px;">Detail</a>
                         </div>
                    </div>
               </div>
          <?php endwhile; ?>
     <?php endif; ?>
</section>

<?php include "../components/footer.php"; ?>
<script src="assets/js/public.js"></script>