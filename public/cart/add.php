<?php
session_start();

$id = (int) $_POST['id'];
$qty = (int) $_POST['qty'];

if (!isset($_SESSION['cart'])) {
     $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
     $_SESSION['cart'][$id] += $qty;
} else {
     $_SESSION['cart'][$id] = $qty;
}

header("Location: index.php");
exit;
