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
     <title>Syarat & Ketentuan - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .terms-hero {
               height: 30vh;
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .terms-hero h1 {
               font-size: 4rem;
               font-weight: 900;
               letter-spacing: -2px;
          }

          .terms-container {
               max-width: 800px;
               margin: 0 auto;
               padding: 60px 20px;
          }

          .terms-section {
               margin-bottom: 50px;
          }

          .terms-section h2 {
               font-size: 2rem;
               margin-bottom: 20px;
               text-transform: uppercase;
               letter-spacing: -1px;
          }

          .terms-section h3 {
               font-size: 1.3rem;
               margin: 25px 0 15px;
          }

          .terms-section p {
               margin-bottom: 15px;
               color: var(--muted);
               line-height: 1.8;
          }

          .terms-section ul,
          .terms-section ol {
               margin: 15px 0 15px 30px;
               color: var(--muted);
          }

          .terms-section li {
               margin-bottom: 10px;
          }

          .terms-box {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 30px;
               margin-top: 40px;
          }

          .last-updated {
               margin-top: 30px;
               text-align: right;
               color: var(--muted);
               font-style: italic;
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
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="terms-hero">
               <h1>TERMS & CONDITIONS</h1>
               <p>Syarat dan Ketentuan Penggunaan</p>
          </section>

          <!-- Terms Content -->
          <div class="terms-container">
               <div class="terms-section">
                    <h2>1. Penerimaan Syarat</h2>
                    <p>Dengan mengakses dan menggunakan layanan Kantin Digital, Anda menyetujui untuk terikat oleh
                         syarat dan ketentuan ini. Jika Anda tidak setuju dengan bagian manapun dari syarat ini, Anda
                         tidak diperbolehkan menggunakan layanan kami.</p>
               </div>

               <div class="terms-section">
                    <h2>2. Akun Pengguna</h2>
                    <h3>2.1 Pendaftaran</h3>
                    <p>Untuk menggunakan fitur tertentu, Anda harus mendaftar dan membuat akun. Anda bertanggung jawab
                         untuk menjaga kerahasiaan informasi akun Anda.</p>

                    <h3>2.2 Keamanan</h3>
                    <p>Anda bertanggung jawab atas semua aktivitas yang terjadi dalam akun Anda. Segera beri tahu kami
                         jika terjadi pelanggaran keamanan.</p>
               </div>

               <div class="terms-section">
                    <h2>3. Pemesanan dan Pembayaran</h2>
                    <h3>3.1 Proses Pemesanan</h3>
                    <p>Setiap pemesanan yang dilakukan melalui platform kami merupakan penawaran untuk membeli produk.
                         Kami berhak menerima atau menolak pemesanan tersebut.</p>

                    <h3>3.2 Harga</h3>
                    <p>Harga produk tercantum dalam Rupiah dan sudah termasuk pajak yang berlaku. Harga dapat berubah
                         sewaktu-waktu tanpa pemberitahuan sebelumnya.</p>

                    <h3>3.3 Pembayaran</h3>
                    <p>Pembayaran harus dilakukan sesuai dengan metode yang tersedia. Pesanan akan diproses setelah
                         pembayaran berhasil dikonfirmasi.</p>
               </div>

               <div class="terms-section">
                    <h2>4. Pengiriman dan Pengambilan</h2>
                    <p>Produk yang dipesan dapat diambil langsung di kantin pada jam operasional. Waktu tunggu rata-rata
                         adalah 10-15 menit setelah pesanan dikonfirmasi.</p>
               </div>

               <div class="terms-section">
                    <h2>5. Pembatalan dan Pengembalian</h2>
                    <h3>5.1 Pembatalan oleh Pelanggan</h3>
                    <p>Pembatalan pesanan hanya dapat dilakukan sebelum status pesanan berubah menjadi "SUCCESS".</p>

                    <h3>5.2 Pembatalan oleh Kantin</h3>
                    <p>Kami berhak membatalkan pesanan jika produk tidak tersedia atau terjadi kesalahan harga.</p>
               </div>

               <div class="terms-section">
                    <h2>6. Privasi</h2>
                    <p>Penggunaan data pribadi Anda diatur dalam Kebijakan Privasi kami. Dengan menggunakan layanan
                         kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan tersebut.
                    </p>
               </div>

               <div class="terms-section">
                    <h2>7. Perubahan Syarat</h2>
                    <p>Kami berhak mengubah syarat dan ketentuan ini setiap saat. Perubahan akan berlaku segera setelah
                         dipublikasikan di platform.</p>
               </div>

               <div class="terms-box">
                    <p style="font-weight: 700; margin-bottom: 10px;">Dengan menggunakan Kantin Digital, Anda menyatakan
                         telah membaca, memahami, dan menyetujui semua syarat dan ketentuan yang berlaku.</p>
               </div>

               <div class="last-updated">
                    Terakhir diperbarui: 21 Februari 2026
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
               <a href="terms.php">Terms</a>
               <a href="privacy.php">Privacy</a>
               <a href="contact.php">Contact</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
</body>

</html>