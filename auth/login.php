<?php
session_start();
require "../core/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = $_POST['password'];

     $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
     $user = mysqli_fetch_assoc($query);

     if ($user && password_verify($password, $user['password'])) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user_role'] = $user['role'];
          $_SESSION['user_name'] = $user['name'];

          if ($user['role'] == "admin") {
               header("Location: ../admin/dashboard.php");
          } else {
               header("Location: ../public/index.php");
          }
          exit;
     } else {
          $error = "Email atau password salah";
     }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Login - Kantin Digital</title>
     <link rel="stylesheet" href="../public/assets/css/auth.css?v=<?= time(); ?>">
</head>

<body class="auth-body">
     <div class="auth-box">
          <h2>Kantin Digital</h2>
          <p>Masuk untuk mulai memesan menu favoritmu</p>

          <?php if ($error): ?>
               <div class="auth-error"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST">
               <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" name="email" placeholder="contoh@email.com" required>
               </div>

               <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" placeholder="••••••••" required>
               </div>

               <button type="submit" class="btn-auth">Masuk Sekarang</button>
          </form>

          <a href="register.php">Belum punya akun? <span>Daftar Disini</span></a>
     </div>
     <script src="../public/assets/js/auth.js?v=<?= time(); ?>"></script>
</body>

</html>