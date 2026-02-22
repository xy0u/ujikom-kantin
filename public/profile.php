<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';
requireLogin();

$user_id = (int) $_SESSION['user_id'];
$flash = getFlash();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $action = $_POST['action'] ?? '';

     if ($action === 'update_profile') {
          $name = sanitize($_POST['name'] ?? '');
          $phone = sanitize($_POST['phone'] ?? '');

          if (!$name) {
               $errors[] = 'Nama wajib diisi.';
          } else {
               $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, phone=? WHERE id=?");
               mysqli_stmt_bind_param($stmt, 'ssi', $name, $phone, $user_id);
               if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['name'] = $name;
                    flash('success', 'Profil berhasil diperbarui.');
               } else {
                    flash('error', 'Gagal menyimpan perubahan.');
               }
               redirect('/public/profile.php');
          }
     } elseif ($action === 'change_password') {
          $current = $_POST['current_password'] ?? '';
          $new_pw = $_POST['new_password'] ?? '';
          $confirm = $_POST['confirm_password'] ?? '';

          $user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

          if (!password_verify($current, $user_row['password'])) {
               $errors[] = 'Password saat ini salah.';
          } elseif (strlen($new_pw) < 8) {
               $errors[] = 'Password baru minimal 8 karakter.';
          } elseif ($new_pw !== $confirm) {
               $errors[] = 'Konfirmasi password tidak cocok.';
          } else {
               $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
               $stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
               mysqli_stmt_bind_param($stmt, 'si', $hashed, $user_id);
               if (mysqli_stmt_execute($stmt)) {
                    flash('success', 'Password berhasil diubah.');
               } else {
                    flash('error', 'Gagal mengubah password.');
               }
               redirect('/public/profile.php');
          }
     }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE user_id=$user_id"))['c'] ?? 0;
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) as s FROM orders WHERE user_id=$user_id AND status='completed'"))['s'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Profil Saya — Kantin Digital</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>
     <?php include '../components/navbar.php'; ?>

     <main class="section">
          <div class="container container--narrow">
               <div class="page-header">
                    <h1 class="page-title">Profil Saya</h1>
               </div>

               <?php if ($flash): ?>
                    <div class="alert alert--<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
               <?php endif; ?>

               <?php if (!empty($errors)): ?>
                    <div class="alert alert--error">
                         <?php foreach ($errors as $err): ?>
                              <div><?= e($err) ?></div><?php endforeach; ?>
                    </div>
               <?php endif; ?>

               <!-- Profile Header Card -->
               <div class="profile-header-card">
                    <div class="profile-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                    <div class="profile-info">
                         <h2 class="profile-name"><?= e($user['name']) ?></h2>
                         <div class="profile-email"><?= e($user['email']) ?></div>
                         <div
                              class="profile-role badge badge--<?= $user['role'] === 'admin' ? 'warning' : 'default' ?>">
                              <?= ucfirst($user['role']) ?>
                         </div>
                    </div>
                    <div class="profile-stats">
                         <div class="profile-stat">
                              <div class="profile-stat__value font-mono"><?= number_format($order_count) ?></div>
                              <div class="profile-stat__label">Pesanan</div>
                         </div>
                         <div class="profile-stat">
                              <div class="profile-stat__value font-mono"><?= formatRupiah($total_spent) ?></div>
                              <div class="profile-stat__label">Total Belanja</div>
                         </div>
                         <div class="profile-stat">
                              <div class="profile-stat__value font-mono">
                                   <?= formatDate($user['created_at'] ?? date('Y-m-d')) ?></div>
                              <div class="profile-stat__label">Bergabung</div>
                         </div>
                    </div>
               </div>

               <!-- Tab sections -->
               <div class="profile-tabs">
                    <button class="profile-tab active" onclick="switchTab('info', this)">Informasi Akun</button>
                    <button class="profile-tab" onclick="switchTab('password', this)">Ubah Password</button>
               </div>

               <!-- Info Tab -->
               <div class="profile-section" id="tab-info">
                    <div class="card">
                         <div class="card__header">
                              <h3 class="card__title">Informasi Akun</h3>
                         </div>
                         <div class="card__body">
                              <form method="POST">
                                   <input type="hidden" name="action" value="update_profile">
                                   <?= csrfField() ?>

                                   <div class="form-group">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="name" class="input" value="<?= e($user['name']) ?>"
                                             required>
                                   </div>

                                   <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="input" value="<?= e($user['email']) ?>" disabled>
                                        <div class="form-help">Email tidak bisa diubah</div>
                                   </div>

                                   <div class="form-group">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="tel" name="phone" class="input"
                                             value="<?= e($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                                   </div>

                                   <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
                              </form>
                         </div>
                    </div>
               </div>

               <!-- Password Tab -->
               <div class="profile-section" id="tab-password" style="display:none">
                    <div class="card">
                         <div class="card__header">
                              <h3 class="card__title">Ubah Password</h3>
                         </div>
                         <div class="card__body">
                              <form method="POST">
                                   <input type="hidden" name="action" value="change_password">
                                   <?= csrfField() ?>

                                   <div class="form-group">
                                        <label class="form-label">Password Saat Ini</label>
                                        <input type="password" name="current_password" class="input" required
                                             autocomplete="current-password">
                                   </div>

                                   <div class="form-group">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" name="new_password" class="input" required
                                             autocomplete="new-password" minlength="8">
                                   </div>

                                   <div class="form-group">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="confirm_password" class="input" required
                                             autocomplete="new-password">
                                   </div>

                                   <button type="submit" class="btn btn--primary">Ubah Password</button>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </main>

     <?php include '../components/footer.php'; ?>
     <script>
          function switchTab(tab, btn) {
               document.querySelectorAll('.profile-section').forEach(s => s.style.display = 'none');
               document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
               document.getElementById('tab-' + tab).style.display = 'block';
               btn.classList.add('active');
          }
     </script>
</body>

</html>