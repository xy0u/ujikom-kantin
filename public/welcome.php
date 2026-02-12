<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
     <title>Welcome</title>
     <link rel="stylesheet" href="assets/css/public.css">
</head>

<body class="welcome-body">

     <div class="welcome-wrapper">
          <div class="welcome-text">WELCOME</div>
     </div>

     <script>
          setTimeout(() => {
               window.location.href = "index.php";
          }, 2000);
     </script>

</body>

</html>