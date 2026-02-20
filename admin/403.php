<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>403 - Admin Access Denied</title>
     <link rel="stylesheet" href="../public/assets/css/admin.css">
     <style>
          body {
               background: var(--bg);
               display: flex;
               align-items: center;
               justify-content: center;
               height: 100vh;
               margin: 0;
               font-family: 'Inter', sans-serif;
          }

          .error-box {
               background: var(--card-bg);
               border: 3px solid var(--danger);
               padding: 50px;
               text-align: center;
               max-width: 500px;
               border-radius: var(--radius);
               animation: shake 0.5s ease;
          }

          @keyframes shake {

               0%,
               100% {
                    transform: translateX(0);
               }

               10%,
               30%,
               50%,
               70%,
               90% {
                    transform: translateX(-5px);
               }

               20%,
               40%,
               60%,
               80% {
                    transform: translateX(5px);
               }
          }

          .error-box h1 {
               font-size: 8rem;
               font-weight: 900;
               line-height: 1;
               color: var(--danger);
               margin-bottom: 20px;
               text-shadow: 5px 5px 0 rgba(239, 68, 68, 0.3);
          }

          .error-box h2 {
               font-size: 2rem;
               margin-bottom: 20px;
               text-transform: uppercase;
          }

          .error-box p {
               color: var(--text-muted);
               margin-bottom: 30px;
          }

          .error-box .btn-admin {
               padding: 12px 30px;
               font-size: 14px;
          }

          .user-info {
               background: var(--bg);
               padding: 15px;
               border-radius: 8px;
               margin: 20px 0;
               border: 1px solid var(--border);
          }
     </style>
</head>

<body>
     <div class="error-box">
          <h1>403</h1>
          <h2>ADMIN ACCESS DENIED</h2>

          <?php if (isset($_SESSION['user_id'])): ?>
               <div class="user-info">
                    <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Unknown') ?></strong></p>
                    <p>Role: <strong style="color: #eab308;"><?= $_SESSION['user_role'] ?? 'customer' ?></strong></p>
                    <p style="color: var(--danger); margin-top: 10px;">⚠️ Halaman ini hanya untuk ADMIN!</p>
               </div>
               <a href="../public/index.php" class="btn-admin primary">Kembali ke Beranda</a>
               <a href="../auth/logout.php" class="btn-admin danger" style="margin-left: 10px;">Logout</a>
          <?php else: ?>
               <p>Silakan login sebagai admin untuk mengakses halaman ini.</p>
               <a href="../auth/login.php" class="btn-admin primary">Login Admin</a>
          <?php endif; ?>
     </div>
</body>

</html>