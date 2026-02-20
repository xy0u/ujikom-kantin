<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>KANTIN DIGITAL</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <link rel="stylesheet" href="assets/css/animations.css">
</head>

<body>

     <header class="navbar">
          <div class="logo">KANTIN</div>
          <nav class="nav-links">
               <a href="index.php">Home</a>
               <a href="#menu">Menu</a>
               <a href="orders.php">Pesanan</a>
               <a href="cart/index.php">Keranjang
                    (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a>
               <a href="../auth/logout.php" style="color: #ef4444;">Keluar</a>
          </nav>
     </header>