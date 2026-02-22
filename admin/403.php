<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Akses Ditolak - Admin</title>
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">
     <style>
          * {
               margin: 0;
               padding: 0;
               box-sizing: border-box;
          }

          body {
               font-family: 'Inter', sans-serif;
               background: #0a0a0a;
               color: #fff;
               height: 100vh;
               display: flex;
               align-items: center;
               justify-content: center;
          }

          .error-container {
               text-align: center;
               padding: 40px;
          }

          .error-code {
               font-size: 120px;
               font-weight: 900;
               color: #ff4444;
               text-shadow: 5px 5px 0 rgba(255, 68, 68, 0.3);
               line-height: 1;
               margin-bottom: 20px;
               animation: glitch 5s infinite;
          }

          @keyframes glitch {

               0%,
               100% {
                    transform: translate(0);
               }

               92% {
                    transform: translate(0);
               }

               93% {
                    transform: translate(-3px, 1px);
               }

               94% {
                    transform: translate(3px, -1px);
               }
          }

          .error-title {
               font-size: 32px;
               margin-bottom: 20px;
               text-transform: uppercase;
          }

          .error-message {
               color: #888;
               margin-bottom: 30px;
               max-width: 500px;
          }

          .user-info {
               background: #1a1a1a;
               border: 1px solid #333;
               padding: 20px;
               margin: 20px 0;
               border-radius: 0;
          }

          .btn-home {
               display: inline-block;
               padding: 15px 40px;
               background: #fff;
               color: #000;
               text-decoration: none;
               font-weight: 600;
               text-transform: uppercase;
               letter-spacing: 2px;
               border: 2px solid #fff;
               margin: 0 10px;
               transition: all 0.3s;
          }

          .btn-home:hover {
               background: transparent;
               color: #fff;
          }

          .btn-logout {
               display: inline-block;
               padding: 15px 40px;
               background: transparent;
               color: #ff4444;
               text-decoration: none;
               font-weight: 600;
               text-transform: uppercase;
               letter-spacing: 2px;
               border: 2px solid #ff4444;
               transition: all 0.3s;
          }

          .btn-logout:hover {
               background: #ff4444;
               color: #000;
          }
     </style>
</head>

<body>
     <div class="error-container">
          <div class="error-code">403</div>
          <h1 class="error-title">ACCESS DENIED</h1>
          <p class="error-message">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>

          <?php if (isset($_SESSION['user_id'])): ?>
               <div class="user-info">
                    <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Unknown') ?></strong></p>
                    <p>Role: <span style="color: #ffaa44;"><?= $_SESSION['user_role'] ?? 'customer' ?></span></p>
                    <p style="color: #ff4444; margin-top: 10px;">⚠️ Halaman ini hanya untuk ADMIN!</p>
               </div>
               <a href="../public/index.php" class="btn-home">KEMBALI KE BERANDA</a>
               <a href="../auth/logout.php" class="btn-logout">LOGOUT</a>
          <?php else: ?>
               <div class="user-info">
                    <p>Anda belum login</p>
               </div>
               <a href="../auth/login.php" class="btn-home">LOGIN ADMIN</a>
          <?php endif; ?>
     </div>
</body>

</html>