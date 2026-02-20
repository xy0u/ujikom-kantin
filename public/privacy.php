<?php
session_start();
require "../core/helpers.php";

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Kebijakan Privasi - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .privacy-hero {
               height: 30vh;
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .privacy-hero h1 {
               font-size: 4rem;
               font-weight: 900;
               letter-spacing: -2px;
          }

          .privacy-container {
               max-width: 800px;
               margin: 0 auto;
               padding: 60px 20px;
          }

          .privacy-section {
               margin-bottom: 40px;
          }

          .privacy-section h2 {
               font-size: 2rem;
               margin-bottom: 20px;
               text-transform: uppercase;
          }

          .privacy-section h3 {
               font-size: 1.3rem;
               margin: 25px 0 15px;
          }

          .privacy-section p {
               margin-bottom: 15px;
               color: var(--muted);
               line-height: 1.8;
          }

          .privacy-section ul {
               margin: 15px 0 15px 30px;
               color: var(--muted);
          }

          .privacy-section li {
               margin-bottom: 10px;
          }

          .highlight-box {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 30px;
               margin: 30px 0;
          }
     </style>
</head>

<body>
     <!-- Header -->
     <header>
          <div class="logo">KANTIN</div>
          <nav>
               <a href="index.php">Home</a>
               <a href="index.php#menu">Menu</a>
               <a href="cart/index.php">Cart (<?= $cartCount ?>)</a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="privacy-hero">
               <h1>PRIVACY POLICY</h1>
               <p>Kebijakan Privasi Kantin Digital</p>
          </section>

          <!-- Privacy Content -->
          <div class="privacy-container">
               <div class="privacy-section">
                    <p style="font-size: 1.1rem; margin-bottom: 30px;">Kami di Kantin Digital menghargai privasi Anda.
                         Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi
                         informasi pribadi Anda.</p>
               </div>

               <div class="privacy-section">
                    <h2>1. Informasi yang Kami Kumpulkan</h2>
                    <h3>1.1 Informasi Pribadi</h3>
                    <p>Kami mengumpulkan informasi yang Anda berikan saat mendaftar, seperti:</p>
                    <ul>
                         <li>Nama lengkap</li>
                         <li>Alamat email</li>
                         <li>Nomor telepon</li>
                         <li>Alamat (jika diperlukan)</li>
                    </ul>

                    <h3>1.2 Informasi Transaksi</h3>
                    <p>Kami menyimpan riwayat pesanan dan pembayaran Anda untuk keperluan layanan dan administrasi.</p>
               </div>

               <div class="privacy-section">
                    <h2>2. Penggunaan Informasi</h2>
                    <p>Informasi yang kami kumpulkan digunakan untuk:</p>
                    <ul>
                         <li>Memproses dan mengelola pesanan Anda</li>
                         <li>Mengirim konfirmasi dan update pesanan</li>
                         <li>Meningkatkan layanan dan pengalaman pengguna</li>
                         <li>Mengirim informasi promosi (dengan persetujuan Anda)</li>
                         <li>Memenuhi kewajiban hukum</li>
                    </ul>
               </div>

               <div class="privacy-section">
                    <h2>3. Keamanan Data</h2>
                    <p>Kami menerapkan langkah-langkah keamanan yang sesuai untuk melindungi informasi pribadi Anda dari
                         akses tidak sah, perubahan, pengungkapan, atau penghancuran.</p>
               </div>

               <div class="privacy-section">
                    <h2>4. Pengungkapan kepada Pihak Ketiga</h2>
                    <p>Kami tidak menjual, menukar, atau mentransfer informasi pribadi Anda kepada pihak luar, kecuali:
                    </p>
                    <ul>
                         <li>Untuk memproses pembayaran (mitra payment gateway)</li>
                         <li>Untuk mematuhi kewajiban hukum</li>
                         <li>Dengan persetujuan Anda</li>
                    </ul>
               </div>

               <div class="privacy-section">
                    <h2>5. Cookie</h2>
                    <p>Kami menggunakan cookie untuk meningkatkan pengalaman Anda di situs kami. Anda dapat mengatur
                         browser untuk menolak cookie, namun beberapa fitur mungkin tidak berfungsi dengan baik.</p>
               </div>

               <div class="highlight-box">
                    <h3 style="margin-bottom: 15px;">🔒 Komitmen Kami</h3>
                    <p>Kami berkomitmen untuk menjaga kerahasiaan informasi pribadi Anda. Jika ada pertanyaan tentang
                         kebijakan privasi ini, silakan hubungi kami melalui halaman Contact.</p>
               </div>

               <div style="text-align: right; color: var(--muted);">
                    <p>Terakhir diperbarui: 21 Februari 2026</p>
               </div>
          </div>
     </main>

     <!-- Footer -->
     <footer>
          <div class="footer-brand">
               KANTIN
               <p>&copy; <?= date('Y') ?> — All rights reserved</p>
          </div>
          <div class="footer-links">
               <a href="terms.php">Terms</a>
               <a href="privacy.php">Privacy</a>
               <a href="contact.php">Contact</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
</body>

</html>