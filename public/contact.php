<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

$cartCount = getCartCount();
$success = '';
$error = '';

// Kirim pesan kontak
if (isset($_POST['send_message'])) {
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $message = mysqli_real_escape_string($conn, $_POST['message']);

     // Simpan ke database (buat table contacts jika perlu)
     // Untuk sementara kita simpan di session
     $_SESSION['contact_message'] = "Pesan dari $name ($email): $message";
     $success = "Pesan Anda telah terkirim! Kami akan segera merespon.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Kontak - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .contact-hero {
               height: 40vh;
               display: flex;
               flex-direction: column;
               justify-content: center;
               align-items: center;
               text-align: center;
               background: var(--surface);
               border-bottom: 3px solid var(--fg);
          }

          .contact-hero h1 {
               font-size: 5rem;
               font-weight: 900;
               letter-spacing: -3px;
          }

          .contact-container {
               max-width: 1200px;
               margin: 0 auto;
               padding: 80px 20px;
          }

          .contact-grid {
               display: grid;
               grid-template-columns: 1fr 1fr;
               gap: 60px;
          }

          .contact-info {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 40px;
          }

          .contact-info h2 {
               font-size: 2rem;
               margin-bottom: 30px;
               text-transform: uppercase;
          }

          .info-item {
               display: flex;
               gap: 20px;
               margin-bottom: 30px;
          }

          .info-icon {
               font-size: 1.5rem;
               width: 50px;
               height: 50px;
               background: var(--fg);
               color: var(--bg);
               display: flex;
               align-items: center;
               justify-content: center;
               border: 2px solid var(--fg);
          }

          .info-content h3 {
               font-size: 1.1rem;
               margin-bottom: 5px;
          }

          .info-content p {
               color: var(--muted);
          }

          .contact-form {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 40px;
          }

          .contact-form h2 {
               font-size: 2rem;
               margin-bottom: 30px;
               text-transform: uppercase;
          }

          .form-group {
               margin-bottom: 20px;
          }

          .form-group label {
               display: block;
               margin-bottom: 5px;
               color: var(--muted);
               text-transform: uppercase;
               font-size: 0.7rem;
               letter-spacing: 1px;
          }

          .form-group input,
          .form-group textarea {
               width: 100%;
               padding: 12px;
               background: var(--bg);
               border: 2px solid var(--border);
               color: var(--fg);
               font-size: 1rem;
          }

          .form-group input:focus,
          .form-group textarea:focus {
               border-color: var(--fg);
               outline: none;
          }

          .form-group textarea {
               height: 150px;
               resize: vertical;
          }

          .alert {
               padding: 15px;
               margin-bottom: 20px;
               border: 2px solid;
          }

          .alert.success {
               background: rgba(34, 197, 94, 0.1);
               border-color: #22c55e;
               color: #22c55e;
          }

          .map-container {
               margin-top: 60px;
               border: 3px solid var(--fg);
               height: 400px;
               background: var(--surface);
               display: flex;
               align-items: center;
               justify-content: center;
          }

          .map-placeholder {
               text-align: center;
               color: var(--muted);
          }

          .map-placeholder span {
               font-size: 3rem;
               display: block;
               margin-bottom: 20px;
          }

          @media (max-width: 768px) {
               .contact-hero h1 {
                    font-size: 3rem;
               }

               .contact-grid {
                    grid-template-columns: 1fr;
                    gap: 30px;
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
               <a href="contact.php" class="active">Contact</a>
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <!-- Hero -->
          <section class="contact-hero">
               <h1>CONTACT</h1>
               <p>Hubungi kami untuk pertanyaan dan kerjasama</p>
          </section>

          <!-- Contact Content -->
          <div class="contact-container">
               <!-- Alert -->
               <?php if ($success): ?>
                    <div class="alert success" style="margin-bottom: 30px;">
                         <?= $success ?>
                    </div>
               <?php endif; ?>

               <div class="contact-grid">
                    <!-- Contact Info -->
                    <div class="contact-info">
                         <h2>Get in Touch</h2>

                         <div class="info-item">
                              <div class="info-icon">📍</div>
                              <div class="info-content">
                                   <h3>Alamat</h3>
                                   <p>SMK Negeri 1 Ciamis<br>Jl. Jenderal Sudirman No. 123<br>Ciamis, Jawa Barat 46211
                                   </p>
                              </div>
                         </div>

                         <div class="info-item">
                              <div class="info-icon">📞</div>
                              <div class="info-content">
                                   <h3>Telepon</h3>
                                   <p>(0265) 771234<br>+62 812-3456-7890</p>
                              </div>
                         </div>

                         <div class="info-item">
                              <div class="info-icon">✉️</div>
                              <div class="info-content">
                                   <h3>Email</h3>
                                   <p>info@kantindigital.com<br>support@kantindigital.com</p>
                              </div>
                         </div>

                         <div class="info-item">
                              <div class="info-icon">⏰</div>
                              <div class="info-content">
                                   <h3>Jam Operasional</h3>
                                   <p>Senin - Jumat: 07:00 - 16:00<br>Sabtu: 07:00 - 12:00</p>
                              </div>
                         </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form">
                         <h2>Send Message</h2>
                         <form method="POST">
                              <div class="form-group">
                                   <label>Nama Lengkap</label>
                                   <input type="text" name="name" required
                                        value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
                              </div>
                              <div class="form-group">
                                   <label>Email</label>
                                   <input type="email" name="email" required>
                              </div>
                              <div class="form-group">
                                   <label>Pesan</label>
                                   <textarea name="message" required placeholder="Tulis pesan Anda..."></textarea>
                              </div>
                              <button type="submit" name="send_message" class="btn-buy">Kirim Pesan</button>
                         </form>
                    </div>
               </div>

               <!-- Map -->
               <div class="map-container">
                    <div class="map-placeholder">
                         <span>🗺️</span>
                         <p>Google Maps akan ditampilkan di sini</p>
                         <p style="font-size: 0.8rem; margin-top: 10px;">SMK Negeri 1 Ciamis</p>
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
               <a href="contact.php">Contact</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
</body>

</html>