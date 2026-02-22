<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';

if (isLoggedIn())
     redirect('/public/index.php');

$error = '';
$values = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
          $error = 'Invalid request. Coba lagi.';
     } else {
          $name = sanitize($_POST['name'] ?? '');
          $email = sanitize($_POST['email'] ?? '');
          $phone = sanitize($_POST['phone'] ?? '');
          $password = $_POST['password'] ?? '';
          $confirm = $_POST['confirm_password'] ?? '';
          $values = compact('name', 'email', 'phone');

          if (!$name || !$email || !$password) {
               $error = 'Nama, email, dan password wajib diisi.';
          } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
               $error = 'Format email tidak valid.';
          } elseif (strlen($password) < 8) {
               $error = 'Password minimal 8 karakter.';
          } elseif ($password !== $confirm) {
               $error = 'Konfirmasi password tidak cocok.';
          } else {
               // Check duplicate
               $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
               mysqli_stmt_bind_param($stmt, 's', $email);
               mysqli_stmt_execute($stmt);
               mysqli_stmt_store_result($stmt);

               if (mysqli_stmt_num_rows($stmt) > 0) {
                    $error = 'Email sudah terdaftar. Coba login.';
               } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt2 = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
                    mysqli_stmt_bind_param($stmt2, 'ssss', $name, $email, $phone, $hashed);

                    if (mysqli_stmt_execute($stmt2)) {
                         flash('success', 'Akun berhasil dibuat! Silakan login.');
                         redirect('/auth/login.php');
                    } else {
                         $error = 'Gagal membuat akun. Coba lagi.';
                    }
               }
          }
     }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Daftar — Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="/public/assets/css/auth.css">
</head>

<body class="auth-body">

     <div class="auth-bg">
          <div class="auth-bg-grid"></div>
          <div class="auth-bg-glow auth-bg-glow--1"></div>
          <div class="auth-bg-glow auth-bg-glow--2"></div>
     </div>

     <div class="auth-container">
          <div class="auth-panel auth-panel--left">
               <div class="auth-panel-content">
                    <a href="/public/index.php" class="auth-logo"><span>[</span>KD<span>]</span></a>
                    <h1 class="auth-panel-title">MULAI<br>PERJALANAN</h1>
                    <p class="auth-panel-desc">Bergabung dengan ribuan pengguna yang sudah menikmati kemudahan Kantin
                         Digital.</p>

                    <div class="auth-panel-features">
                         <div class="auth-feature">
                              <div class="auth-feature-icon">🎯</div>
                              <div>
                                   <div class="auth-feature-title">Gratis Selamanya</div>
                                   <div class="auth-feature-desc">Tanpa biaya berlangganan</div>
                              </div>
                         </div>
                         <div class="auth-feature">
                              <div class="auth-feature-icon">📦</div>
                              <div>
                                   <div class="auth-feature-title">Lacak Pesanan</div>
                                   <div class="auth-feature-desc">Status realtime pesananmu</div>
                              </div>
                         </div>
                         <div class="auth-feature">
                              <div class="auth-feature-icon">💾</div>
                              <div>
                                   <div class="auth-feature-title">Riwayat Lengkap</div>
                                   <div class="auth-feature-desc">Semua pesanan tersimpan</div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>

          <div class="auth-panel auth-panel--right">
               <div class="auth-form-wrap">
                    <a href="/public/index.php" class="auth-logo-mobile">[KD]</a>

                    <div class="auth-form-header">
                         <h2 class="auth-form-title">Buat Akun</h2>
                         <p class="auth-form-subtitle">Daftar sekarang — gratis dan mudah</p>
                    </div>

                    <?php if ($error): ?>
                         <div class="auth-alert auth-alert--error">
                              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                   stroke-width="2">
                                   <circle cx="12" cy="12" r="10" />
                                   <line x1="12" y1="8" x2="12" y2="12" />
                                   <line x1="12" y1="16" x2="12.01" y2="16" />
                              </svg>
                              <?= e($error) ?>
                         </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form" id="registerForm" novalidate>
                         <?= csrfField() ?>

                         <div class="form-group">
                              <label for="name" class="form-label">Nama Lengkap</label>
                              <div class="input-icon-wrap">
                                   <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                   </svg>
                                   <input type="text" name="name" id="name" class="input" placeholder="Nama lengkap"
                                        value="<?= e($values['name'] ?? '') ?>" required>
                              </div>
                         </div>

                         <div class="form-group">
                              <label for="email" class="form-label">Email</label>
                              <div class="input-icon-wrap">
                                   <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path
                                             d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                   </svg>
                                   <input type="email" name="email" id="email" class="input"
                                        placeholder="nama@email.com" value="<?= e($values['email'] ?? '') ?>" required>
                              </div>
                         </div>

                         <div class="form-group">
                              <label for="phone" class="form-label">Nomor Telepon <span
                                        class="form-label-optional">(opsional)</span></label>
                              <div class="input-icon-wrap">
                                   <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path
                                             d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.6 19.79 19.79 0 0 1 1.61 5a2 2 0 0 1 1.99-2H6.6a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10.1a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17.18v-.26z" />
                                   </svg>
                                   <input type="tel" name="phone" id="phone" class="input" placeholder="08xxxxxxxxxx"
                                        value="<?= e($values['phone'] ?? '') ?>">
                              </div>
                         </div>

                         <div class="form-group">
                              <label for="password" class="form-label">Password</label>
                              <div class="input-icon-wrap input-password-wrap">
                                   <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                   </svg>
                                   <input type="password" name="password" id="password" class="input"
                                        placeholder="Min. 8 karakter" required autocomplete="new-password">
                                   <button type="button" class="input-toggle-pw" id="togglePw"
                                        aria-label="Tampilkan password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2">
                                             <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                             <circle cx="12" cy="12" r="3" />
                                        </svg>
                                   </button>
                              </div>
                              <!-- Password strength -->
                              <div class="pw-strength" id="pwStrength">
                                   <div class="pw-strength-bar">
                                        <div class="pw-strength-fill" id="pwStrengthFill"></div>
                                   </div>
                                   <span class="pw-strength-label" id="pwStrengthLabel"></span>
                              </div>
                         </div>

                         <div class="form-group">
                              <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                              <div class="input-icon-wrap">
                                   <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                   </svg>
                                   <input type="password" name="confirm_password" id="confirm_password" class="input"
                                        placeholder="Ulangi password" required autocomplete="new-password">
                              </div>
                              <div class="form-feedback" id="confirmFeedback"></div>
                         </div>

                         <div class="form-check">
                              <input type="checkbox" name="agree" id="agree" class="form-check-input" required>
                              <label for="agree" class="form-check-label">
                                   Saya setuju dengan <a href="/public/terms.php" target="_blank">Syarat & Ketentuan</a>
                                   dan <a href="/public/privacy.php" target="_blank">Kebijakan Privasi</a>
                              </label>
                         </div>

                         <button type="submit" class="btn btn--primary btn--full btn--lg" id="submitBtn">
                              <span class="btn-text">Buat Akun</span>
                              <span class="btn-loader" hidden>
                                   <svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                   </svg>
                              </span>
                         </button>
                    </form>

                    <p class="auth-switch">
                         Sudah punya akun?
                         <a href="/auth/login.php" class="auth-switch-link">Login sekarang</a>
                    </p>

                    <a href="/public/index.php" class="auth-back-link">
                         <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2">
                              <polyline points="15 18 9 12 15 6" />
                         </svg>
                         Kembali ke Beranda
                    </a>
               </div>
          </div>
     </div>

     <script>
          // Password toggle
          document.getElementById('togglePw')?.addEventListener('click', function () {
               const pw = document.getElementById('password');
               pw.type = pw.type === 'password' ? 'text' : 'password';
          });

          // Password strength
          document.getElementById('password')?.addEventListener('input', function () {
               const pw = this.value;
               const fill = document.getElementById('pwStrengthFill');
               const label = document.getElementById('pwStrengthLabel');
               let score = 0;
               if (pw.length >= 8) score++;
               if (/[A-Z]/.test(pw)) score++;
               if (/[0-9]/.test(pw)) score++;
               if (/[^A-Za-z0-9]/.test(pw)) score++;

               const levels = [
                    { label: '', color: '', width: '0%' },
                    { label: 'Lemah', color: '#ff2b2b', width: '25%' },
                    { label: 'Cukup', color: '#ff8800', width: '50%' },
                    { label: 'Kuat', color: '#f0ff00', width: '75%' },
                    { label: 'Sangat Kuat', color: '#00ff88', width: '100%' },
               ];
               const lvl = levels[score] ?? levels[0];
               fill.style.width = lvl.width;
               fill.style.background = lvl.color;
               label.textContent = lvl.label;
               label.style.color = lvl.color;
          });

          // Confirm match
          document.getElementById('confirm_password')?.addEventListener('input', function () {
               const pw = document.getElementById('password').value;
               const fb = document.getElementById('confirmFeedback');
               if (this.value === '') { fb.textContent = ''; return; }
               if (this.value === pw) {
                    fb.textContent = '✓ Password cocok';
                    fb.style.color = '#00ff88';
               } else {
                    fb.textContent = '✗ Password tidak cocok';
                    fb.style.color = '#ff2b2b';
               }
          });

          // Loading state
          document.getElementById('registerForm')?.addEventListener('submit', function (e) {
               const agree = document.getElementById('agree');
               if (!agree.checked) { e.preventDefault(); alert('Anda harus menyetujui syarat & ketentuan.'); return; }
               const btn = document.getElementById('submitBtn');
               btn.disabled = true;
               btn.querySelector('.btn-text').hidden = true;
               btn.querySelector('.btn-loader').hidden = false;
          });
     </script>
</body>

</html>