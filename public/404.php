<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>404 - Halaman Tidak Ditemukan</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .error-404 {
               height: 100vh;
               display: flex;
               align-items: center;
               justify-content: center;
               text-align: center;
               background: var(--bg);
               position: relative;
               overflow: hidden;
          }

          .error-404::before {
               content: '404';
               position: absolute;
               top: 50%;
               left: 50%;
               transform: translate(-50%, -50%);
               font-size: 30vw;
               font-weight: 900;
               color: rgba(0, 0, 0, 0.03);
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
               color: var(--fg);
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

          .error-actions {
               display: flex;
               gap: 20px;
               justify-content: center;
          }

          @media (max-width: 768px) {
               .error-content h1 {
                    font-size: 5rem;
                    letter-spacing: -5px;
               }

               .error-content h2 {
                    font-size: 1.2rem;
               }

               .error-actions {
                    flex-direction: column;
                    gap: 10px;
               }
          }
     </style>
</head>

<body>
     <?php include "components/navbar.php"; ?>

     <div class="error-404">
          <div class="error-content">
               <h1>404</h1>
               <h2>PAGE NOT FOUND</h2>
               <p>Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.</p>
               <div class="error-actions">
                    <a href="index.php" class="btn-buy">KEMBALI KE BERANDA</a>
                    <a href="javascript:history.back()" class="btn-buy"
                         style="background: transparent; color: var(--fg);">KEMBALI</a>
               </div>
          </div>
     </div>

     <?php include "components/footer.php"; ?>
</body>

</html>