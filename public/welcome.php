<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
     <title>Welcome - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .welcome-wrapper {
               height: 100vh;
               display: flex;
               justify-content: center;
               align-items: center;
               background: #000;
          }

          .welcome-text {
               font-size: clamp(40px, 10vw, 120px);
               font-weight: 800;
               letter-spacing: 20px;
               color: white;
               text-align: center;
               opacity: 0;
               filter: blur(10px);
               animation: focusText 2s forwards;
          }

          @keyframes focusText {
               to {
                    opacity: 1;
                    filter: blur(0);
                    letter-spacing: 5px;
               }
          }
     </style>
</head>

<body class="welcome-body">
     <div class="welcome-wrapper">
          <div class="welcome-text">WELCOME</div>
     </div>

     <script>
          setTimeout(() => {
               document.body.style.opacity = '0';
               document.body.style.transition = '1s';
               setTimeout(() => {
                    window.location.href = "index.php";
               }, 1000);
          }, 2500);
     </script>
</body>

</html>