<?php
if (session_status() === PHP_SESSION_NONE) {
     session_start();
}

// Proteksi Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
     header("Location: ../auth/login.php");
     exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Admin Panel - Kantin Digital</title>
     <link rel="stylesheet" href="../public/assets/css/admin.css?v=<?= time(); ?>">
</head>

<body>

     <div class="admin-container">
          <aside class="sidebar">
               <div class="brand">ADMIN PANEL</div>

               <div class="admin-profile">
                    <small>Logged in as</small>
                    <p><strong><?= $_SESSION['user_name'] ?></strong></p>
               </div>

               <nav>
                    <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Overview</a>
                    <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">Inventory</a>
                    <a href="categories.php"
                         class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">Categories</a>
                    <a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>">Transactions</a>
               </nav>

               <a href="../auth/logout.php" class="logout-btn" onclick="return confirm('Yakin ingin keluar?')">Sign
                    Out</a>
          </aside>

          <main class="main-content">