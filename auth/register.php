<?php
require "../core/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

     $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

     if (mysqli_num_rows($check) > 0) {
          $error = "Email sudah terdaftar";
     } else {
          mysqli_query($conn, "
    INSERT INTO users(name,email,password,role)
    VALUES('$name','$email','$password','customer')
    ");

          header("Location: login.php");
          exit;
     }
}
?>
<!DOCTYPE html>
<html>

<head>
     <title>Register</title>
     <link rel="stylesheet" href="../public/assets/css/auth.css">
</head>

<body class="auth-body">

     <div class="auth-box">

          <h2>Register</h2>

          <?php if ($error): ?>
               <p class="auth-error"><?= $error ?></p>
          <?php endif; ?>

          <form method="POST">
               <input type="text" name="name" placeholder="Name" required>
               <input type="email" name="email" placeholder="Email" required>
               <input type="password" name="password" placeholder="Password" required>
               <button type="submit">Register</button>
          </form>

          <a href="login.php">Login</a>

     </div>

</body>

</html>