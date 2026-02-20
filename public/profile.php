<?php
session_start();
require "../core/database.php";
require "../core/helpers.php";

if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Update profile
if (isset($_POST['update_profile'])) {
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);

     $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $user_id");
     if (mysqli_num_rows($check) > 0) {
          $error = "Email sudah digunakan!";
     } else {
          mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE id=$user_id");
          $_SESSION['user_name'] = $name;
          $success = "Profil berhasil diperbarui!";
     }
}

// Change password
if (isset($_POST['change_password'])) {
     $old = $_POST['old_password'];
     $new = $_POST['new_password'];
     $confirm = $_POST['confirm_password'];

     $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id"));

     if (!password_verify($old, $user['password'])) {
          $error = "Password lama salah!";
     } elseif (strlen($new) < 6) {
          $error = "Password baru minimal 6 karakter!";
     } elseif ($new !== $confirm) {
          $error = "Konfirmasi password tidak cocok!";
     } else {
          $hashed = password_hash($new, PASSWORD_DEFAULT);
          mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$user_id");
          $success = "Password berhasil diubah!";
     }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
$orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id=$user_id"))['total'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total),0) as total FROM orders WHERE user_id=$user_id AND status='SUCCESS'"))['total'];
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Profile - Kantin Digital</title>
     <link rel="stylesheet" href="assets/css/public.css">
     <style>
          .profile-container {
               max-width: 1200px;
               margin: 0 auto;
               padding: 120px 20px 60px;
          }

          .profile-header {
               display: flex;
               align-items: center;
               gap: 40px;
               margin-bottom: 40px;
               padding: 40px;
               background: var(--surface);
               border: 3px solid var(--fg);
          }

          .profile-avatar {
               width: 120px;
               height: 120px;
               background: var(--fg);
               color: var(--bg);
               display: flex;
               align-items: center;
               justify-content: center;
               font-size: 3rem;
               font-weight: 800;
               border: 3px solid var(--fg);
          }

          .profile-info h1 {
               font-size: 2.5rem;
               margin-bottom: 10px;
          }

          .profile-stats {
               display: grid;
               grid-template-columns: repeat(3, 1fr);
               gap: 20px;
               margin-bottom: 40px;
          }

          .stat-item {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 20px;
               text-align: center;
          }

          .stat-item .number {
               font-size: 2rem;
               font-weight: 800;
               margin-bottom: 5px;
          }

          .stat-item .label {
               color: var(--muted);
               text-transform: uppercase;
               font-size: 0.8rem;
               letter-spacing: 1px;
          }

          .profile-grid {
               display: grid;
               grid-template-columns: 1fr 1fr;
               gap: 30px;
          }

          .profile-card {
               background: var(--surface);
               border: 3px solid var(--fg);
               padding: 30px;
          }

          .profile-card h2 {
               font-size: 1.5rem;
               margin-bottom: 30px;
               text-transform: uppercase;
               letter-spacing: 2px;
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

          .form-group input {
               width: 100%;
               padding: 12px;
               background: var(--bg);
               border: 2px solid var(--border);
               color: var(--fg);
               font-size: 1rem;
          }

          .form-group input:focus {
               border-color: var(--fg);
               outline: none;
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

          .alert.error {
               background: rgba(239, 68, 68, 0.1);
               border-color: #ef4444;
               color: #ef4444;
          }

          @media (max-width: 768px) {
               .profile-header {
                    flex-direction: column;
                    text-align: center;
               }

               .profile-stats {
                    grid-template-columns: 1fr;
               }

               .profile-grid {
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
               <a href="orders.php">Orders</a>
               <a href="cart/index.php">Cart (
                    <?= $cartCount ?>)
               </a>
               <a href="profile.php" class="active">Profile</a>
               <a href="../auth/logout.php">Exit</a>
          </nav>
     </header>

     <main>
          <div class="profile-container">
               <!-- Profile Header -->
               <div class="profile-header">
                    <div class="profile-avatar">
                         <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <div class="profile-info">
                         <h1>
                              <?= htmlspecialchars($user['name']) ?>
                         </h1>
                         <p>
                              <?= htmlspecialchars($user['email']) ?>
                         </p>
                         <p style="color: var(--muted); margin-top: 10px;">Member since
                              <?= date('d F Y', strtotime($user['created_at'])) ?>
                         </p>
                    </div>
               </div>

               <!-- Stats -->
               <div class="profile-stats">
                    <div class="stat-item">
                         <div class="number">
                              <?= $orders_count ?>
                         </div>
                         <div class="label">Total Orders</div>
                    </div>
                    <div class="stat-item">
                         <div class="number">
                              <?= format_rp($total_spent) ?>
                         </div>
                         <div class="label">Total Spent</div>
                    </div>
                    <div class="stat-item">
                         <div class="number">
                              <?= $cartCount ?>
                         </div>
                         <div class="label">In Cart</div>
                    </div>
               </div>

               <!-- Alerts -->
               <?php if ($success): ?>
                    <div class="alert success">
                         <?= $success ?>
                    </div>
               <?php endif; ?>
               <?php if ($error): ?>
                    <div class="alert error">
                         <?= $error ?>
                    </div>
               <?php endif; ?>

               <!-- Profile Forms -->
               <div class="profile-grid">
                    <!-- Edit Profile -->
                    <div class="profile-card">
                         <h2>Edit Profile</h2>
                         <form method="POST">
                              <div class="form-group">
                                   <label>Nama Lengkap</label>
                                   <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                                        required>
                              </div>
                              <div class="form-group">
                                   <label>Email</label>
                                   <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                        required>
                              </div>
                              <button type="submit" name="update_profile" class="btn-buy">Update Profile</button>
                         </form>
                    </div>

                    <!-- Change Password -->
                    <div class="profile-card">
                         <h2>Change Password</h2>
                         <form method="POST">
                              <div class="form-group">
                                   <label>Password Lama</label>
                                   <input type="password" name="old_password" required>
                              </div>
                              <div class="form-group">
                                   <label>Password Baru</label>
                                   <input type="password" name="new_password" required>
                              </div>
                              <div class="form-group">
                                   <label>Konfirmasi Password</label>
                                   <input type="password" name="confirm_password" required>
                              </div>
                              <button type="submit" name="change_password" class="btn-buy">Change Password</button>
                         </form>
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
          </div>
          <div class="footer-links">
               <a href="index.php#menu">Menu</a>
               <a href="orders.php">Orders</a>
               <a href="profile.php">Profile</a>
          </div>
     </footer>

     <script src="assets/js/public.js"></script>
</body>

</html>