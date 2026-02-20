<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>403 - Akses Ditolak</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .error-403 {
               height: 100vh;
               display: flex;
               align-items: center;
               justify-content: center;
               text-align: center;
               background: var(--bg);
               position: relative;
               overflow: hidden;
          }

          .error-403::before {
               content: '403';
               position: absolute;
               top: 50%;
               left: 50%;
               transform: translate(-50%, -50%);
               font-size: 30vw;
               font-weight: 900;
               color: rgba(239, 68, 68, 0.03);
               z-index: 1;
               pointer-events: none;
               white-space: nowrap;
          }

          .error-content {
               position: relative;
               z-index: 2;
               max-width: 600px;
               padding: 40px;
          }

          .error-content h1 {
               font-size: 8rem;
               font-weight: 900;
               line-height: 1;
               letter-spacing: -10px;
               margin-bottom: 20px;
               color: #ef4444;
          }

          .error-content h2 {
               font-size: 2rem;
               text-transform: uppercase;
               letter-spacing: 5px;
               margin-bottom: 20px;
          }

          .error-content p {
               color: var(--muted);
               margin-bottom: 40px;
               font-size: 1.1rem;
          }

          .login-required {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 30px;
               margin-top: 30px;
          }

          .login-required h3 {
               font-size: 1.5rem;
               margin-bottom: 20px;
          }

          .btn-group {
               display: flex;
               gap: 15px;
               justify-content: center;
          }

          @media (max-width: 768px) {
               .error-content h1 {
                    font-size: 5rem;
               }

               .btn-group {
                    flex-direction: column;
               }
          }
     </style>
</head>

<body>
     <div class="error-403">
          <div class="error-content">
               <h1>403</h1>
               <h2>ACCESS DENIED</h2>
               <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>

               <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="login-required">
                         <h3>🔒 LOGIN REQUIRED</h3>
                         <p>Silakan login terlebih dahulu untuk mengakses halaman ini.</p>
                         <div class="btn-group">
                              <a href="../auth/login.php" class="btn-buy">LOGIN</a>
                              <a href="../auth/register.php" class="btn-buy"
                                   style="background: transparent; color: var(--fg);">REGISTER</a>
                         </div>
                    </div>
               <?php else: ?>
                    <a href="index.php" class="btn-buy">KEMBALI KE BERANDA</a>
               <?php endif; ?>
          </div>
     </div>
</body>

</html>