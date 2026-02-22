<?php
session_start();
require '../../core/database.php';
require '../../core/helpers.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data)
     $data = $_POST;

$action = $data['action'] ?? '';

switch ($action) {
     case 'add':
          $id = (int) ($data['id'] ?? 0);
          $qty = max(1, (int) ($data['qty'] ?? 1));
          $name = sanitize($data['name'] ?? '');
          $price = (float) ($data['price'] ?? 0);

          if (!$id || !$name || $price <= 0) {
               echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
               exit;
          }

          // Verify product exists and has stock
          $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id AND stock >= $qty"));
          if (!$product) {
               echo json_encode(['success' => false, 'message' => 'Produk tidak tersedia atau stok tidak mencukupi']);
               exit;
          }

          addToCart($id, $qty, $product['price'], $product['name'], $product['image'] ?? '');
          echo json_encode([
               'success' => true,
               'cart_count' => getCartCount(),
               'cart_total' => getCartTotal(),
          ]);
          break;

     case 'update':
          $id = (int) ($data['id'] ?? 0);
          $qty = (int) ($data['qty'] ?? 1);

          if ($qty <= 0) {
               removeFromCart($id);
          } elseif (isset($_SESSION['cart'][$id])) {
               $_SESSION['cart'][$id]['qty'] = $qty;
          }

          echo json_encode([
               'success' => true,
               'cart_count' => getCartCount(),
               'cart_total' => getCartTotal(),
          ]);
          break;

     case 'remove':
          $id = (int) ($data['id'] ?? 0);
          removeFromCart($id);
          echo json_encode([
               'success' => true,
               'cart_count' => getCartCount(),
               'cart_total' => getCartTotal(),
          ]);
          break;

     case 'clear':
          clearCart();
          echo json_encode(['success' => true, 'cart_count' => 0, 'cart_total' => 0]);
          break;

     case 'get':
          echo json_encode([
               'success' => true,
               'cart' => $_SESSION['cart'] ?? [],
               'cart_count' => getCartCount(),
               'cart_total' => getCartTotal(),
          ]);
          break;

     default:
          echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
}