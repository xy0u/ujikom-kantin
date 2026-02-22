<?php
if (session_status() === PHP_SESSION_NONE)
     session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
     header("Location: ../auth/login.php");
     exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

// Pending orders count for badge
require_once "../core/database.php";
$pending_count = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='PENDING'");
if ($result)
     $pending_count = mysqli_fetch_assoc($result)['c'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> — Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link rel="stylesheet" href="../public/assets/css/admin.css">
     <meta name="theme-color" content="#08090a">
</head>

<body>

     <div class="admin-wrapper">

          <!-- ===== SIDEBAR ===== -->
          <aside class="admin-sidebar" id="adminSidebar">
               <!-- Brand -->
               <div class="sidebar-brand">
                    <div class="brand-logo">KANTIN</div>
                    <div class="brand-sub">Admin Panel</div>
                    <div class="brand-indicator">
                         <div class="brand-dot"></div>
                         <span class="brand-status">System Online</span>
                    </div>
               </div>

               <!-- User Info -->
               <div class="sidebar-user">
                    <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>">
                         <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                         <p class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></p>
                         <p class="user-role">Administrator</p>
                    </div>
               </div>

               <!-- Navigation -->
               <nav class="sidebar-nav">
                    <div class="nav-section-label">Menu Utama</div>

                    <a href="dashboard.php" class="nav-item <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                         <span class="nav-icon">📊</span>
                         <span class="nav-text">Dashboard</span>
                    </a>

                    <a href="products.php" class="nav-item <?= $current_page === 'products.php' ? 'active' : '' ?>">
                         <span class="nav-icon">📦</span>
                         <span class="nav-text">Produk</span>
                    </a>

                    <a href="categories.php" class="nav-item <?= $current_page === 'categories.php' ? 'active' : '' ?>">
                         <span class="nav-icon">📁</span>
                         <span class="nav-text">Kategori</span>
                    </a>

                    <a href="orders.php" class="nav-item <?= $current_page === 'orders.php' ? 'active' : '' ?>">
                         <span class="nav-icon">🛒</span>
                         <span class="nav-text">Pesanan</span>
                         <?php if ($pending_count > 0): ?>
                              <span class="nav-badge"><?= $pending_count ?></span>
                         <?php endif; ?>
                    </a>

                    <div class="nav-section-label">Sistem</div>

                    <a href="../public/index.php" class="nav-item" target="_blank">
                         <span class="nav-icon">🌐</span>
                         <span class="nav-text">Lihat Website</span>
                    </a>

                    <a href="../auth/logout.php" class="nav-item logout">
                         <span class="nav-icon">🚪</span>
                         <span class="nav-text">Logout</span>
                    </a>
               </nav>

               <!-- Sidebar Footer -->
               <div class="sidebar-footer">
                    <a href="../public/index.php" class="sidebar-footer-link">
                         <span>↗</span> <span>Buka Toko</span>
                    </a>
               </div>
          </aside>

          <!-- ===== MAIN CONTENT ===== -->
          <main class="admin-main">

               <!-- Header / Topbar -->
               <header class="admin-header">
                    <div class="header-left">
                         <button class="btn-ghost btn-sm" id="sidebarToggle" style="display:none;"
                              aria-label="Toggle sidebar">
                              ☰
                         </button>
                         <div class="header-breadcrumb">
                              <span>Admin</span>
                              <span class="header-breadcrumb-sep">/</span>
                              <span
                                   class="header-breadcrumb-current"><?= htmlspecialchars($title ?? 'Dashboard') ?></span>
                         </div>
                    </div>
                    <div class="header-right">
                         <span class="header-date"><?= date('d F Y') ?></span>
                         <?php if ($pending_count > 0): ?>
                              <a href="orders.php?status=PENDING" class="header-notif"
                                   title="<?= $pending_count ?> pesanan pending">
                                   🔔
                                   <span class="header-notif-badge"><?= $pending_count ?></span>
                              </a>
                         <?php endif; ?>
                    </div>
               </header>

               <!-- Content Area -->
               <div class="admin-content">
                    <?php
                    // NOTE: layout-footer.php closes these divs/tags
                    ?>