<?php
require "../core/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

     $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

     if (mysqli_num_rows($check) > 0) {
          $error = "Email sudah terdaftar";
     } else {
          // Menggunakan role 'customer' sesuai kodingan awalmu
          $query = "INSERT INTO users(name,email,password,role) VALUES('$name','$email','$password','customer')";
          if (mysqli_query($conn, $query)) {
               $success = "Berhasil daftar! Silakan login.";
          } else {
               $error = "Gagal mendaftar";
          }
     }
}
?>
<!DOCTYPE html>
<html lang="en">

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
               <div class="auth-error"
                    style="background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2);">
                    <?= $success ?>
               </div>
          <?php endif; ?>

          <form method="POST">
               <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" required>
               </div>
               <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="nama@email.com" required>
               </div>
               <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required>
               </div>
               <button type="submit" class="btn-auth">Buat Akun Sekarang</button>
          </form>

          <a href="login.php">Sudah punya akun? <span>Masuk</span></a>
     </div>
     <script src="../public/assets/js/auth.js?v=<?= time(); ?>"></script>
</body>

</html>