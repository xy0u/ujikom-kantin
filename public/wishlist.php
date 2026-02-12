<?php
session_start();

$id = (int) $_POST['id'];

if (!isset($_SESSION['wishlist'])) {
     $_SESSION['wishlist'] = [];
}

if (!in_array($id, $_SESSION['wishlist'])) {
     $_SESSION['wishlist'][] = $id;
}
