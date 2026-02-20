<?php
if (session_status() === PHP_SESSION_NONE)
     session_start();

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
               <div class="brand">KANTIN ADMIN</div>
               <div class="admin-profile">
                    <small>Logged in as</small>
                    <p><strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></p>
                    <small style="color: #22c55e;"><?= $_SESSION['user_role'] ?? 'admin' ?></small>
               </div>
               <nav>
                    <a href="dashboard.php"
                         class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                    <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">Produk</a>
                    <a href="categories.php"
                         class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">Kategori</a>
                    <a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>">Transaksi</a>
               </nav>
               <a href="../auth/logout.php" class="logout-btn" onclick="return confirm('Yakin ingin keluar?')">Sign
                    Out</a>
          </aside>
          <main class="main-content">