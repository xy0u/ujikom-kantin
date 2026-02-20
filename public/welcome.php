<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Welcome - Kantin Digital</title>
     <link rel="stylesheet" href="public/assets/css/public.css">
     <link rel="stylesheet" href="public/assets/css/animations.css">
     <style>
          .welcome-body {
               margin: 0;
               padding: 0;
               background: #000;
               overflow: hidden;
          }

          .welcome-wrapper {
               height: 100vh;
               display: flex;
               justify-content: center;
               align-items: center;
               position: relative;
          }

          .welcome-text {
               font-size: clamp(40px, 15vw, 150px);
               font-weight: 900;
               letter-spacing: 30px;
               color: white;
               text-align: center;
               opacity: 0;
               filter: blur(20px);
               animation: focusText 3s forwards cubic-bezier(0.16, 1, 0.3, 1);
               text-transform: uppercase;
               position: relative;
               z-index: 2;
          }

          @keyframes focusText {
               0% {
                    opacity: 0;
                    filter: blur(20px);
                    letter-spacing: 50px;
               }

               50% {
                    opacity: 1;
                    filter: blur(0);
                    letter-spacing: 20px;
               }

               100% {
                    opacity: 1;
                    filter: blur(0);
                    letter-spacing: 10px;
               }
          }

          .welcome-sub {
               position: absolute;
               bottom: 100px;
               left: 50%;
               transform: translateX(-50%);
               color: rgba(255, 255, 255, 0.5);
               font-size: 12px;
               letter-spacing: 5px;
               text-transform: uppercase;
               animation: fadeIn 2s 1s forwards;
               opacity: 0;
          }

          @keyframes fadeIn {
               to {
                    opacity: 1;
               }
          }

          .welcome-bg {
               position: absolute;
               top: 0;
               left: 0;
               width: 100%;
               height: 100%;
               background: repeating-linear-gradient(45deg,
                         transparent,
                         transparent 20px,
                         rgba(255, 255, 255, 0.02) 20px,
                         rgba(255, 255, 255, 0.02) 40px);
               pointer-events: none;
          }

          .loading-bar {
               position: absolute;
               bottom: 200px;
               left: 50%;
               transform: translateX(-50%);
               width: 200px;
               height: 2px;
               background: rgba(255, 255, 255, 0.1);
               overflow: hidden;
          }

          .loading-progress {
               width: 0%;
               height: 100%;
               background: white;
               animation: load 2.5s forwards cubic-bezier(0.16, 1, 0.3, 1);
          }

          @keyframes load {
               to {
                    width: 100%;
               }
          }
     </style>
</head>

<body class="welcome-body">
     <div class="welcome-wrapper">
          <div class="welcome-bg"></div>
          <div class="welcome-text">WELCOME</div>
          <div class="welcome-sub">to Kantin Digital</div>
          <div class="loading-bar">
               <div class="loading-progress"></div>
          </div>
     </div>

     <script>
          // Redirect setelah animasi selesai
          setTimeout(() => {
               document.body.style.opacity = '0';
               document.body.style.transition = 'opacity 0.8s ease';

               setTimeout(() => {
                    // Cek apakah user sudah login
                    <?php if (isset($_SESSION['user_id'])): ?>
                         window.location.href = "public/index.php";
                    <?php else: ?>
                         window.location.href = "auth/login.php";
                    <?php endif; ?>
               }, 800);
          }, 3000);
     </script>
</body>

</html>