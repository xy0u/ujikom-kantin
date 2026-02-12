<?php
if (session_status() === PHP_SESSION_NONE) {
     session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
     header("Location: ../auth/login.php");
     exit;
}
?>
<!DOCTYPE html>
<html>

<head>
     <title>Admin Panel</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../public/assets/css/admin.css">
</head>

<body>

     <div class="admin-container">

          <aside class="sidebar">
               <div>
                    <h2>KANTIN</h2>
                    <div class="admin-name"><?= $_SESSION['user_name'] ?></div>

                    <nav>
                         <a href="dashboard.php">Dashboard</a>
                         <a href="products.php">Products</a>
                         <a href="categories.php">Categories</a>
                         <a href="orders.php">Orders</a>
                    </nav>
               </div>

               <a href="../auth/logout.php" class="logout-btn">Logout</a>
          </aside>

          <main class="main-content">