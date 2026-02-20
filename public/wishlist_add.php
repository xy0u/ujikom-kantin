<?php
session_start();

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
     if (!isset($_SESSION['wishlist'])) {
          $_SESSION['wishlist'] = [];
     }

     if (!in_array($id, $_SESSION['wishlist'])) {
          $_SESSION['wishlist'][] = $id;
     }

     echo count($_SESSION['wishlist']);
} else {
     echo "0";
}
exit;