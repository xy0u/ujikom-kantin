<?php
session_start();
require "../core/database.php";

if (isset($_SESSION['user_id'])) {
     header("Location: " . ($_SESSION['user_role'] == 'admin' ? "../admin/dashboard.php" : "../public/index.php"));
     exit;
}

if (isset($_POST['login'])) {
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = $_POST['password'];

     $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

     if (mysqli_num_rows($query) > 0) {
          $user = mysqli_fetch_assoc($query);
          if (password_verify($password, $user['password'])) {
               $_SESSION['user_id'] = $user['id'];
               $_SESSION['user_name'] = $user['name'];
               $_SESSION['user_role'] = $user['role'];

               header("Location: " . ($user['role'] == 'admin' ? "../admin/dashboard.php" : "../public/index.php"));
               exit;
          } else {
               $error = "Password yang Anda masukkan salah.";
          }
     } else {
          $error = "Akun dengan email tersebut tidak ditemukan.";
     }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Login - Kantin Digital</title>
     <link rel="stylesheet" href="../public/assets/css/auth.css?v=<?= time(); ?>">
</head>

<body class="auth-body">

     <div class="auth-box">
          <h2>Welcome Back</h2>
          <p>Silakan masuk ke akun Kantin Anda</p>

          <?php if (isset($error)): ?>
               <div class="auth-error">
                    <?= $error ?>
               </div>
          <?php endif; ?>

          <form method="POST">
               <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="nama@email.com" required
                         value="<?= $_POST['email'] ?? '' ?>">
               </div>

               <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
               </div>

               <button type="submit" name="login" class="btn-auth">
                    Sign In
               </button>
          </form>

          <a href="register.php">Belum punya akun? <span>Daftar Sekarang</span></a>
     </div>

</body>

</html>