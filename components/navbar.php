<?php
if (session_status() === PHP_SESSION_NONE)
     session_start();
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html>

<head>
     <meta charset="UTF-8">
     <title>Kantin Digital</title>
     <link rel="stylesheet" href="/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/public/assets/css/public.css">
</head>

<body>
     <header>
          <div class="logo">KANTIN</div>
          <nav>
               <a href="/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/public/index.php">Home</a>
               <a href="/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/public/cart/index.php">Cart (<?= $cartCount ?>)</a>
               <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/auth/logout.php">Logout</a>
               <?php else: ?>
                    <a href="/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/auth/login.php">Login</a>
               <?php endif; ?>
          </nav>
     </header>