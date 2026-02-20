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

// Cek order milik user yang login
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id"));

if (!$order) {
     die("Order tidak ditemukan");
}

// Ambil items
$items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");

// Untuk download file
if (isset($_GET['download'])) {
     header("Content-Type: application/octet-stream");
     header("Content-Disposition: attachment; filename=Invoice_Order_" . $order_id . ".txt");

     echo "========================================\n";
     echo "           KANTIN DIGITAL               \n";
     echo "========================================\n\n";
     echo "Invoice #: ORD-" . $order['id'] . "\n";
     echo "Tanggal: " . date('d/m/Y H:i', strtotime($order['created_at'])) . "\n";
     echo "Status: " . $order['status'] . "\n";
     echo "----------------------------------------\n\n";

     $total = 0;
     while ($i = mysqli_fetch_assoc($items)) {
          $subtotal = $i['price'] * $i['quantity'];
          $total += $subtotal;

          echo $i['name'] . " x" . $i['quantity'] . "\n";
          echo "Harga: " . format_rp($i['price']) . "\n";
          echo "Subtotal: " . format_rp($subtotal) . "\n\n";
     }

     echo "----------------------------------------\n";
     echo "TOTAL: " . format_rp($total) . "\n";
     echo "========================================\n";
     echo "Terima kasih telah berbelanja!\n";
     echo "========================================\n";
     exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Invoice #<?= $order_id ?> - Kantin Digital</title>
     <link rel="stylesheet" href="../assets/css/public.css">
     <style>
          .invoice-container {
               max-width: 800px;
               margin: 0 auto;
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 40px;
          }

          .invoice-header {
               text-align: center;
               margin-bottom: 40px;
               padding-bottom: 20px;
               border-bottom: 2px solid var(--border);
          }

          .invoice-header h1 {
               font-size: 2.5rem;
               letter-spacing: 5px;
               margin-bottom: 10px;
          }

          .invoice-details {
               display: flex;
               justify-content: space-between;
               margin-bottom: 30px;
               padding: 20px;
               background: var(--bg);
               border: 1px solid var(--border);
          }

          .invoice-table {
               width: 100%;
               border-collapse: collapse;
               margin: 30px 0;
          }

          .invoice-table th {
               text-align: left;
               padding: 10px;
               border-bottom: 2px solid var(--fg);
               text-transform: uppercase;
               font-size: 0.8rem;
          }

          .invoice-table td {
               padding: 10px;
               border-bottom: 1px solid var(--border);
          }

          .invoice-total {
               text-align: right;
               margin-top: 30px;
               padding-top: 20px;
               border-top: 2px solid var(--fg);
          }

          .invoice-total h2 {
               font-size: 2rem;
          }

          .invoice-actions {
               display: flex;
               gap: 20px;
               margin-top: 40px;
               justify-content: center;
          }

          @media print {
               .no-print {
                    display: none;
               }

               .invoice-container {
                    border: none;
                    padding: 0;
               }
          }
     </style>
</head>

<body>
     <?php include "../components/navbar.php"; ?>

     <main>
          <section class="hero" style="height: 30vh; min-height: 200px;">
               <h1>INVOICE</h1>
               <p>Order #<?= $order_id ?></p>
          </section>

          <div style="padding: 0 5% 100px;">
               <div class="invoice-container">
                    <div class="invoice-header">
                         <h1>KANTIN</h1>
                         <p>Digital Experience</p>
                    </div>

                    <div class="invoice-details">
                         <div>
                              <strong>Invoice #:</strong> ORD-<?= $order['id'] ?><br>
                              <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                         </div>
                         <div>
                              <strong>Status:</strong>
                              <span style="color: <?= $order['status'] == 'SUCCESS' ? '#22c55e' : '#eab308' ?>;">
                                   <?= $order['status'] ?>
                              </span><br>
                              <strong>Pelanggan:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?>
                         </div>
                    </div>

                    <table class="invoice-table">
                         <thead>
                              <tr>
                                   <th>Produk</th>
                                   <th>Harga</th>
                                   <th>Qty</th>
                                   <th>Subtotal</th>
                              </tr>
                         </thead>
                         <tbody>
                              <?php
                              $total = 0;
                              mysqli_data_seek($items, 0);
                              while ($item = mysqli_fetch_assoc($items)):
                                   $subtotal = $item['price'] * $item['quantity'];
                                   $total += $subtotal;
                                   ?>
                                   <tr>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= format_rp($item['price']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= format_rp($subtotal) ?></td>
                                   </tr>
                              <?php endwhile; ?>
                         </tbody>
                    </table>

                    <div class="invoice-total">
                         <h2>Total: <?= format_rp($total) ?></h2>
                    </div>

                    <div class="invoice-actions no-print">
                         <button onclick="window.print()" class="btn-buy">Print Invoice</button>
                         <a href="?order=<?= $order_id ?>&download=1" class="btn-buy">Download</a>
                         <a href="../index.php" class="btn-buy"
                              style="background: transparent; color: var(--fg);">Kembali</a>
                    </div>

                    <div style="text-align: center; margin-top: 40px; font-size: 0.8rem; color: var(--muted);">
                         Terima kasih telah berbelanja di Kantin Digital
                    </div>
               </div>
          </div>
     </main>

     <?php include "../components/footer.php"; ?>
</body>

</html>