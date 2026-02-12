<?php
session_start();
require "../core/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = $_POST['password'];

     $user = mysqli_fetch_assoc(
          mysqli_query($conn, "SELECT * FROM users WHERE email='$email'")
     );

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
<html>

<head>
     <title>Login</title>
     <link rel="stylesheet" href="../public/assets/css/auth.css">
</head>

<body class="auth-body">

     <div class="auth-box">

          <h2>Login</h2>

          <?php if ($error): ?>
               <p class="auth-error"><?= $error ?></p>
          <?php endif; ?>

          <form method="POST">
               <input type="email" name="email" placeholder="Email" required>
               <input type="password" name="password" placeholder="Password" required>
               <button type="submit">Login</button>
          </form>

          <a href="register.php">Register</a>

     </div>

</body>

</html>