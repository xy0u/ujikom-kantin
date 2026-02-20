<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Tentang Kami - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .about-hero {
               height: 60vh;
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
               position: relative;
               overflow: hidden;
          }

          .about-hero::before {
               content: 'KANTIN';
               position: absolute;
               font-size: 20vw;
               font-weight: 900;
               color: rgba(0, 0, 0, 0.03);
               white-space: nowrap;
               transform: rotate(-5deg);
          }

          .about-hero h1 {
               font-size: 6rem;
               font-weight: 900;
               letter-spacing: -5px;
               z-index: 2;
               position: relative;
          }

          .about-hero p {
               font-size: 1.2rem;
               color: var(--muted);
               max-width: 600px;
               margin-top: 20px;
               z-index: 2;
               position: relative;
          }

          .about-section {
               max-width: 1200px;
               margin: 0 auto;
               padding: 80px 20px;
          }

          .about-grid {
               display: grid;
               grid-template-columns: 1fr 1fr;
               gap: 60px;
               align-items: center;
          }

          .about-content h2 {
               font-size: 2.5rem;
               font-weight: 800;
               margin-bottom: 30px;
               text-transform: uppercase;
               letter-spacing: -1px;
          }

          .about-content p {
               margin-bottom: 20px;
               line-height: 1.8;
               color: var(--muted);
          }

          .about-stats {
               display: grid;
               grid-template-columns: repeat(3, 1fr);
               gap: 20px;
               margin-top: 40px;
          }

          .stat {
               text-align: center;
               padding: 30px;
               background: var(--surface);
               border: 3px solid var(--fg);
          }

          .stat .number {
               font-size: 3rem;
               font-weight: 900;
               line-height: 1;
          }

          .stat .label {
               color: var(--muted);
               text-transform: uppercase;
               font-size: 0.8rem;
               letter-spacing: 1px;
               margin-top: 10px;
          }

          .values-grid {
               display: grid;
               grid-template-columns: repeat(3, 1fr);
               gap: 30px;
               margin-top: 60px;
          }

          .value-card {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 40px 30px;
               text-align: center;
               transition: transform 0.3s;
          }

          .value-card:hover {
               transform: translateY(-10px);
          }

          .value-icon {
               font-size: 3rem;
               margin-bottom: 20px;
          }

          .value-card h3 {
               font-size: 1.5rem;
               margin-bottom: 15px;
          }

          .value-card p {
               color: var(--muted);
               font-size: 0.9rem;
          }

          .team-section {
               margin-top: 100px;
          }

          .team-section h2 {
               font-size: 3rem;
               text-align: center;
               margin-bottom: 60px;
          }

          .team-grid {
               display: grid;
               grid-template-columns: repeat(4, 1fr);
               gap: 30px;
          }

          .team-member {
               text-align: center;
          }

          .member-photo {
               width: 100%;
               aspect-ratio: 1;
               background: var(--surface);
               border: 3px solid var(--fg);
               margin-bottom: 20px;
               display: flex;
               align-items: center;
               justify-content: center;
               font-size: 3rem;
               font-weight: 800;
          }

          .member-name {
               font-size: 1.2rem;
               font-weight: 700;
               margin-bottom: 5px;
          }

          .member-role {
               color: var(--muted);
               font-size: 0.8rem;
               text-transform: uppercase;
               letter-spacing: 1px;
          }

          @media (max-width: 768px) {
               .about-hero h1 {
                    font-size: 3rem;
                    letter-spacing: -2px;
               }

               .about-grid {
                    grid-template-columns: 1fr;
                    gap: 40px;
               }

               .values-grid {
                    grid-template-columns: 1fr;
               }

               .team-grid {
                    grid-template-columns: repeat(2, 1fr);
               }
          }

          @media (max-width: 480px) {
               .team-grid {
                    grid-template-columns: 1fr;
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
               <a href="about.php" class="active">About</a>
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="wishlist.php">Wishlist (
                    <?= isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0 ?>)
               </a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="about-hero">
               <h1>ABOUT US</h1>
               <p>Lebih dari sekadar kantin, kami menghadirkan pengalaman digital dalam setiap pesanan</p>
          </section>

          <!-- About Content -->
          <div class="about-section">
               <div class="about-grid">
                    <div class="about-content">
                         <h2>Cerita Kami</h2>
                         <p>Kantin Digital lahir dari kebutuhan akan sistem pemesanan makanan yang modern, cepat, dan
                              efisien di lingkungan sekolah. Kami percaya bahwa teknologi dapat mengubah cara orang
                              menikmati makanan.</p>
                         <p>Dimulai dari proyek UJIKOM sederhana, kini Kantin Digital telah berkembang menjadi platform
                              yang melayani ratusan siswa setiap harinya. Dengan desain brutalist yang khas dan
                              pengalaman pengguna yang mulus, kami ingin membuktikan bahwa aplikasi sekolah juga bisa
                              terlihat keren!</p>
                         <p>Visi kami adalah menjadi kantin digital nomor satu di Indonesia, dimulai dari
                              sekolah-sekolah.</p>

                         <div class="about-stats">
                              <div class="stat">
                                   <div class="number">500+</div>
                                   <div class="label">Pelanggan</div>
                              </div>
                              <div class="stat">
                                   <div class="number">50+</div>
                                   <div class="label">Menu</div>
                              </div>
                              <div class="stat">
                                   <div class="number">1000+</div>
                                   <div class="label">Transaksi</div>
                              </div>
                         </div>
                    </div>
                    <div style="background: var(--surface); border: 3px solid var(--fg); padding: 40px;">
                         <h3 style="font-size: 2rem; margin-bottom: 20px;">"Makan enak, bayar gampang"</h3>
                         <p style="color: var(--muted); font-style: italic;">- Rifat Dwi Purnama Sopian, Founder</p>
                         <div style="margin-top: 30px;">
                              <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                   <span style="font-weight: 800;">📍</span>
                                   <span>SMK Negeri 1 Ciamis</span>
                              </div>
                              <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                   <span style="font-weight: 800;">📅</span>
                                   <span>Berdiri 2026</span>
                              </div>
                              <div style="display: flex; gap: 10px;">
                                   <span style="font-weight: 800;">⭐</span>
                                   <span>Rating 4.9/5 dari pelanggan</span>
                              </div>
                         </div>
                    </div>
               </div>

               <!-- Values -->
               <div class="values-grid">
                    <div class="value-card">
                         <div class="value-icon">⚡</div>
                         <h3>Cepat</h3>
                         <p>Proses pemesanan hanya dalam hitungan detik</p>
                    </div>
                    <div class="value-card">
                         <div class="value-icon">🎨</div>
                         <h3>Brutalist Design</h3>
                         <p>Tampilan unik yang membedakan dari yang lain</p>
                    </div>
                    <div class="value-card">
                         <div class="value-icon">🔒</div>
                         <h3>Aman</h3>
                         <p>Data dan transaksi terjamin keamanannya</p>
                    </div>
               </div>

               <!-- Team -->
               <div class="team-section">
                    <h2>OUR TEAM</h2>
                    <div class="team-grid">
                         <div class="team-member">
                              <div class="member-photo">👨‍💻</div>
                              <div class="member-name">Rifat Dwi Purnama Sopian</div>
                              <div class="member-role">Founder & Developer</div>
                         </div>
                         <div class="team-member">
                              <div class="member-photo">👩‍🎨</div>
                              <div class="member-name">Tim Creative</div>
                              <div class="member-role">UI/UX Designer</div>
                         </div>
                         <div class="team-member">
                              <div class="member-photo">👨‍🍳</div>
                              <div class="member-name">Tim Dapur</div>
                              <div class="member-role">Food & Beverage</div>
                         </div>
                         <div class="team-member">
                              <div class="member-photo">👨‍💼</div>
                              <div class="member-name">Tim Support</div>
                              <div class="member-role">Customer Service</div>
                         </div>
                    </div>
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
               <p style="font-size: 0.6rem; margin-top: 5px;">Developed by Rifat Dwi Purnama Sopian</p>
          </div>
          <div class="footer-links">
               <a href="index.php#menu">Menu</a>
               <a href="about.php">About</a>
               <a href="cart/index.php">Cart</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
</body>

</html>