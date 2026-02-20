<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

$cartCount = getCartCount();

$faqs = [
     [
          'q' => 'Bagaimana cara memesan?',
          'a' => 'Pilih menu yang diinginkan, klik "Add to Cart", lalu lanjutkan ke checkout. Setelah itu pilih metode pembayaran dan selesaikan pembayaran.'
     ],
     [
          'q' => 'Apakah bisa pesan untuk diantar?',
          'a' => 'Saat ini layanan kami hanya untuk take away (ambil di kantin). Namun kami sedang mengembangkan fitur antar untuk ke depannya.'
     ],
     [
          'q' => 'Metode pembayaran apa saja yang tersedia?',
          'a' => 'Kami menerima pembayaran melalui transfer bank, e-wallet (OVO, GoPay, DANA), dan tunai saat pengambilan.'
     ],
     [
          'q' => 'Bagaimana jika pesanan saya tidak sesuai?',
          'a' => 'Silakan hubungi admin kantin melalui halaman Contact atau langsung datang ke kantin untuk komplain.'
     ],
     [
          'q' => 'Apakah bisa membatalkan pesanan?',
          'a' => 'Pembatalan pesanan hanya dapat dilakukan sebelum status pesanan berubah menjadi "SUCCESS". Hubungi admin untuk bantuan.'
     ],
     [
          'q' => 'Berapa lama waktu persiapan pesanan?',
          'a' => 'Rata-rata waktu persiapan adalah 10-15 menit tergantung keramaian. Status pesanan akan selalu diupdate.'
     ],
     [
          'q' => 'Apakah ada minimal pemesanan?',
          'a' => 'Tidak ada minimal pemesanan. Anda bisa memesan berapapun, mulai dari 1 item.'
     ],
     [
          'q' => 'Bagaimana jika makanan habis?',
          'a' => 'Menu yang habis akan ditandai "Sold Out". Anda bisa menambahkan ke wishlist untuk notifikasi ketersediaan.'
     ]
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>FAQ - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .faq-hero {
               height: 40vh;
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .faq-hero h1 {
               font-size: 5rem;
               font-weight: 900;
               letter-spacing: -3px;
          }

          .faq-container {
               max-width: 800px;
               margin: 0 auto;
               padding: 80px 20px;
          }

          .faq-item {
               background: var(--surface);
               border: 3px solid var(--fg);
               margin-bottom: 20px;
               transition: all 0.3s;
          }

          .faq-question {
               padding: 25px;
               cursor: pointer;
               display: flex;
               justify-content: space-between;
               align-items: center;
               font-weight: 700;
               font-size: 1.1rem;
          }

          .faq-question:hover {
               background: rgba(255, 255, 255, 0.05);
          }

          .faq-icon {
               font-size: 1.5rem;
               transition: transform 0.3s;
          }

          .faq-answer {
               max-height: 0;
               overflow: hidden;
               transition: max-height 0.5s ease;
               background: var(--bg);
               border-top: 1px solid transparent;
          }

          .faq-item.active .faq-answer {
               max-height: 200px;
               border-top-color: var(--border);
          }

          .faq-item.active .faq-icon {
               transform: rotate(45deg);
          }

          .faq-answer p {
               padding: 25px;
               color: var(--muted);
               line-height: 1.8;
          }

          .faq-more {
               text-align: center;
               margin-top: 50px;
               padding: 40px;
               background: var(--surface);
               border: 3px solid var(--fg);
          }

          .faq-more h3 {
               font-size: 1.5rem;
               margin-bottom: 20px;
          }

          @media (max-width: 768px) {
               .faq-hero h1 {
                    font-size: 3rem;
               }
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
               <a href="about.php">About</a>
               <a href="faq.php" class="active">FAQ</a>
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="faq-hero">
               <h1>FAQ</h1>
               <p>Frequently Asked Questions</p>
          </section>

          <!-- FAQ Content -->
          <div class="faq-container">
               <?php foreach ($faqs as $index => $faq): ?>
                    <div class="faq-item">
                         <div class="faq-question" onclick="toggleFaq(this)">
                              <span>
                                   <?= $faq['q'] ?>
                              </span>
                              <span class="faq-icon">+</span>
                         </div>
                         <div class="faq-answer">
                              <p>
                                   <?= $faq['a'] ?>
                              </p>
                         </div>
                    </div>
               <?php endforeach; ?>

               <!-- Still have questions -->
               <div class="faq-more">
                    <h3>Masih ada pertanyaan?</h3>
                    <p style="margin-bottom: 20px;">Hubungi kami melalui halaman contact</p>
                    <a href="contact.php" class="btn-buy">Contact Us</a>
               </div>
          </div>
     </main>

     <!-- Footer -->
     <footer>
          <div class="footer-brand">
               KANTIN
               <p>&copy;
                    <?= date('Y') ?> — All rights reserved
               </p>
          </div>
          <div class="footer-links">
               <a href="index.php#menu">Menu</a>
               <a href="about.php">About</a>
               <a href="faq.php">FAQ</a>
          </div>
     </footer>

     <script>
          function toggleFaq(element) {
               const faqItem = element.parentElement;
               faqItem.classList.toggle('active');
          }
     </script>
     <script src="assets/js/public.js"></script>
</body>

</html>