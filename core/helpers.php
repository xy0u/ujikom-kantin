<?php

// ── Session ──────────────────────────────────────────────────────────────────
function isLoggedIn(): bool
{
     return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
     if (!isLoggedIn()) {
          header('Location: /auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
          exit;
     }
}

function isAdmin(): bool
{
     return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin(): void
{
     if (!isAdmin()) {
          header('Location: /public/index.php');
          exit;
     }
}

// ── Formatting ────────────────────────────────────────────────────────────────
function formatRupiah(int|float $amount): string
{
     return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate(string $date): string
{
     return date('d M Y', strtotime($date));
}

function formatDateTime(string $date): string
{
     return date('d M Y, H:i', strtotime($date));
}

function timeAgo(string $date): string
{
     $now = new DateTime();
     $past = new DateTime($date);
     $diff = $now->diff($past);

     if ($diff->d === 0 && $diff->h === 0)
          return $diff->i . ' menit lalu';
     if ($diff->d === 0)
          return $diff->h . ' jam lalu';
     if ($diff->d < 7)
          return $diff->d . ' hari lalu';
     return formatDate($date);
}

// ── Security ──────────────────────────────────────────────────────────────────
function e(string $str): string
{
     return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function sanitize(string $str): string
{
     return trim(strip_tags($str));
}

function csrfToken(): string
{
     if (empty($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
     }
     return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool
{
     return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
     return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

// ── Cart ──────────────────────────────────────────────────────────────────────
function getCartCount(): int
{
     if (!isset($_SESSION['cart']))
          return 0;
     return array_sum(array_column($_SESSION['cart'], 'qty'));
}

function getCartTotal(): float
{
     if (!isset($_SESSION['cart']))
          return 0;
     $total = 0;
     foreach ($_SESSION['cart'] as $item) {
          $total += $item['price'] * $item['qty'];
     }
     return $total;
}

function addToCart(int $productId, int $qty, float $price, string $name, string $image = ''): void
{
     if (!isset($_SESSION['cart']))
          $_SESSION['cart'] = [];

     if (isset($_SESSION['cart'][$productId])) {
          $_SESSION['cart'][$productId]['qty'] += $qty;
     } else {
          $_SESSION['cart'][$productId] = [
               'id' => $productId,
               'name' => $name,
               'price' => $price,
               'qty' => $qty,
               'image' => $image,
          ];
     }
}

function removeFromCart(int $productId): void
{
     unset($_SESSION['cart'][$productId]);
}

function clearCart(): void
{
     $_SESSION['cart'] = [];
}

// ── Upload ────────────────────────────────────────────────────────────────────
function uploadImage(array $file, string $dest = 'uploads/'): string|false
{
     $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
     if (!in_array($file['type'], $allowed))
          return false;

     $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
     $filename = uniqid('img_', true) . '.' . $ext;
     $path = rtrim($dest, '/') . '/' . $filename;

     if (!is_dir($dest))
          mkdir($dest, 0755, true);
     if (!move_uploaded_file($file['tmp_name'], $path))
          return false;

     return $filename;
}

// ── Status Badges ─────────────────────────────────────────────────────────────
function orderStatusBadge(string $status): string
{
     $map = [
          'pending' => ['label' => 'Menunggu', 'class' => 'warning'],
          'processing' => ['label' => 'Diproses', 'class' => 'info'],
          'ready' => ['label' => 'Siap', 'class' => 'success'],
          'completed' => ['label' => 'Selesai', 'class' => 'success'],
          'cancelled' => ['label' => 'Dibatalkan', 'class' => 'danger'],
     ];
     $s = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'default'];
     return '<span class="badge badge--' . $s['class'] . '">' . $s['label'] . '</span>';
}

// ── Redirect with Flash ───────────────────────────────────────────────────────
function flash(string $type, string $message): void
{
     $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
     if (!isset($_SESSION['flash']))
          return null;
     $flash = $_SESSION['flash'];
     unset($_SESSION['flash']);
     return $flash;
}

function redirect(string $url): void
{
     header('Location: ' . $url);
     exit;
}

// ── Pagination ────────────────────────────────────────────────────────────────
function paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array
{
     $totalPages = (int) ceil($total / $perPage);
     $offset = ($currentPage - 1) * $perPage;

     return [
          'total' => $total,
          'per_page' => $perPage,
          'current' => $currentPage,
          'total_pages' => $totalPages,
          'offset' => $offset,
          'has_prev' => $currentPage > 1,
          'has_next' => $currentPage < $totalPages,
          'base_url' => $baseUrl,
     ];
}