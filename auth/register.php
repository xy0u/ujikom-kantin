<?php
session_start();
require "../core/database.php";

if (isset($_SESSION['user_id'])) {
     header("Location: " . ($_SESSION['user_role'] == 'admin' ? "../admin/dashboard.php" : "../public/index.php"));
     exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = $_POST['password'];
     $confirm_password = $_POST['confirm_password'] ?? '';

     // Validasi
     if (empty($name) || empty($email) || empty($password)) {
          $error = "Semua field harus diisi!";
     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $error = "Format email tidak valid!";
     } elseif (strlen($password) < 6) {
          $error = "Password minimal 6 karakter!";
     } elseif ($password !== $confirm_password) {
          $error = "Password dan konfirmasi password tidak cocok!";
     } else {
          // Cek email sudah terdaftar
          $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

          if (mysqli_num_rows($check) > 0) {
               $error = "Email sudah terdaftar";
          } else {
               $hashed_password = password_hash($password, PASSWORD_DEFAULT);
               $query = "INSERT INTO users (name, email, password, role, created_at) 
                      VALUES ('$name', '$email', '$hashed_password', 'customer', NOW())";

               if (mysqli_query($conn, $query)) {
                    $success = "Berhasil daftar! Silakan login.";
                    // Redirect ke login setelah 2 detik
                    header("refresh:2;url=login.php");
               } else {
                    $error = "Gagal mendaftar: " . mysqli_error($conn);
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
     <title>Register - Kantin Digital</title>
     <link rel="stylesheet" href="../public/assets/css/auth.css?v=<?= time(); ?>">
</head>

<body class="auth-body">
     <div class="auth-box">
          <h2>Daftar Akun</h2>
          <p>Buat akun barumu untuk akses penuh layanan kami</p>

          <?php if ($error): ?>
               <div class="auth-error"><?= $error ?></div>
          <?php endif; ?>

          <?php if ($success): ?>
               <div class="auth-success">
                    <?= $success ?>
               </div>
          <?php endif; ?>

          <form method="POST">
               <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" required
                         value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
               </div>
               <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="nama@email.com" required
                         value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
               </div>
               <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
               </div>
               <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password" required>
               </div>
               <button type="submit" class="btn-auth">Buat Akun Sekarang</button>
          </form>

          <a href="login.php">Sudah punya akun? <span>Masuk</span></a>
     </div>

     <style>
          .auth-success {
               background: rgba(34, 197, 94, 0.1);
               border: 1px solid rgba(34, 197, 94, 0.2);
               color: #22c55e;
               padding: 12px;
               border-radius: 10px;
               margin-bottom: 20px;
               font-size: 13px;
               text-align: center;
          }
     </style>
</body>

</html>