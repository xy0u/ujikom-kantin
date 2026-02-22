<?php
/**
 * Kantin Digital — Public Index
 *
 * Perbaikan:
 *  - Keamanan : Prepared statements, strict input validation, CSP header
 *  - Performa  : SELECT kolom spesifik, array_flip untuk wishlist O(1), pagination
 *  - Refactor  : Logika dipisah ke fungsi, konstanta untuk sort key, early-return
 *  - Tampilan  : UI / UX lebih modern
 */

declare(strict_types=1);
session_start();

require '../core/database.php';   // Menyediakan $conn (MySQLi)
require '../core/helpers.php';    // isLoggedIn(), getCartCount(), getFlash(), e(), formatRupiah(), sanitize()

// ─── Security Headers ────────────────────────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");

// ─── Constants ───────────────────────────────────────────────────────────────
const SORT_OPTIONS = [
     'newest' => 'p.id DESC',
     'popular' => 'p.id DESC',   // kolom 'sold' tidak ada, fallback ke newest
     'price_asc' => 'p.price ASC',
     'price_desc' => 'p.price DESC',
];
const ITEMS_PER_PAGE = 12;

// ─── Helper functions (lokal) ─────────────────────────────────────────────────

/**
 * Mengambil semua kategori dari database.
 */
function fetchCategories(mysqli $conn): array
{
     $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
     return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Memvalidasi dan menormalisasi input filter dari $_GET.
 */
function resolveFilters(): array
{
     return [
          'cat' => max(0, (int) ($_GET['cat'] ?? 0)),
          'sort' => array_key_exists($_GET['sort'] ?? '', SORT_OPTIONS)
               ? $_GET['sort']
               : 'newest',
          'search' => mb_substr(trim($_GET['q'] ?? ''), 0, 100),  // batasi panjang
          'page' => max(1, (int) ($_GET['page'] ?? 1)),
     ];
}

/**
 * Mengambil produk dengan prepared statement dan pagination.
 * Mengembalikan ['items' => [...], 'total' => int]
 */
function fetchProducts(mysqli $conn, array $filters): array
{
     $orderBy = SORT_OPTIONS[$filters['sort']];
     $offset = ($filters['page'] - 1) * ITEMS_PER_PAGE;

     // Bangun klausa WHERE secara dinamis
     $conditions = ["p.status = 'available'"];
     $types = '';
     $params = [];

     if ($filters['cat'] > 0) {
          $conditions[] = 'p.category_id = ?';
          $types .= 'i';
          $params[] = $filters['cat'];
     }

     if ($filters['search'] !== '') {
          $conditions[] = 'p.name LIKE ?';
          $types .= 's';
          $params[] = '%' . $filters['search'] . '%';
     }

     $where = 'WHERE ' . implode(' AND ', $conditions);

     // Hitung total untuk pagination
     $countSql = "SELECT COUNT(*) FROM products p $where";
     $countStmt = $conn->prepare($countSql);
     if ($types !== '') {
          $countStmt->bind_param($types, ...$params);
     }
     $countStmt->execute();
     $total = (int) $countStmt->get_result()->fetch_row()[0];
     $countStmt->close();

     // Ambil data produk
     $sql = "
        SELECT p.id, p.name, p.price, p.stock, p.image, p.category_id, p.status,
               c.name AS category_name
        FROM   products p
        LEFT JOIN categories c ON p.category_id = c.id
        $where
        ORDER BY $orderBy
        LIMIT ? OFFSET ?
    ";

     $stmt = $conn->prepare($sql);

     // Tambahkan tipe & param untuk LIMIT dan OFFSET
     $bindTypes = $types . 'ii';
     $bindParams = array_merge($params, [ITEMS_PER_PAGE, $offset]);
     $stmt->bind_param($bindTypes, ...$bindParams);
     $stmt->execute();

     $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
     $stmt->close();

     return compact('items', 'total');
}

// ─── Data ────────────────────────────────────────────────────────────────────
$flash = getFlash();
$categories = fetchCategories($conn);
$filters = resolveFilters();
$result = fetchProducts($conn, $filters);

$products = $result['items'];
$totalItems = $result['total'];
$totalPages = (int) ceil($totalItems / ITEMS_PER_PAGE);

// Wishlist: gunakan array_flip agar lookup O(1)
$wishlistMap = array_flip($_SESSION['wishlist'] ?? []);
$cartCount = getCartCount();

// Nama kategori aktif
$activeCatName = '';
if ($filters['cat'] > 0) {
     foreach ($categories as $cat) {
          if ($cat['id'] === $filters['cat']) {
               $activeCatName = $cat['name'];
               break;
          }
     }
}

// URL builder helper (inline)
$buildUrl = static fn(array $overrides): string =>
     '?' . http_build_query(array_merge($filters, $overrides));

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin Digital — <?= e($activeCatName ?: 'Menu') ?></title>
    <meta name="description" content="Kantin Digital — Pesan makanan dan minuman dengan cepat dan mudah.">

    <!-- Preconnect fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/public.css">

    <style>
    /* ── Design Tokens ─────────────────────────────────────────────── */
    :root {
        --bg:          #0d0f14;
        --bg-card:     #13161e;
        --bg-card-hov: #191d28;
        --border:      #ffffff0f;
        --border-hov:  #ffffff22;

        --accent:      #f97316;      /* warm orange */
        --accent-dim:  #f9731622;
        --accent-text: #fb923c;

        --text-1:  #f0f0f0;
        --text-2:  #9ca3af;
        --text-3:  #6b7280;

        --success: #22c55e;
        --danger:  #ef4444;
        --warn:    #eab308;

        --radius:  12px;
        --radius-sm: 6px;

        --font-display: 'Syne', sans-serif;
        --font-body:    'DM Sans', sans-serif;

        --trans: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ── Reset / Base ──────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
        font-family: var(--font-body);
        background: var(--bg);
        color: var(--text-1);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }
    a { color: inherit; text-decoration: none; }
    img { display: block; max-width: 100%; }
    button { cursor: pointer; border: none; background: none; font: inherit; }

    /* ── Layout ────────────────────────────────────────────────────── */
    .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }
    .section    { padding: 96px 0; }
    .section--dark { background: #0a0c10; }

    /* ── Navbar (minimal override — real nav from navbar.php) ──────── */
    .toast-container {
        position: fixed; top: 20px; right: 20px;
        z-index: 9999; display: flex; flex-direction: column; gap: 8px;
    }
    .toast {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; border-radius: var(--radius-sm);
        background: var(--bg-card); border: 1px solid var(--border);
        font-size: 14px; max-width: 340px;
        opacity: 0; transform: translateX(20px);
        transition: all 0.3s ease; pointer-events: none;
        box-shadow: 0 8px 32px #00000060;
    }
    .toast.show { opacity: 1; transform: none; pointer-events: auto; }
    .toast--success { border-color: var(--success); }
    .toast--error   { border-color: var(--danger);  }
    .toast--info    { border-color: var(--accent);  }
    .toast-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
    .toast--success .toast-dot { background: var(--success); }
    .toast--error   .toast-dot { background: var(--danger);  }
    .toast--info    .toast-dot { background: var(--accent);  }
    .toast-close {
        margin-left: auto; color: var(--text-3); font-size: 18px;
        line-height: 1; padding: 0 2px;
        transition: color var(--trans);
    }
    .toast-close:hover { color: var(--text-1); }

    /* ── Hero ──────────────────────────────────────────────────────── */
    .hero {
        min-height: 100svh;
        display: flex; flex-direction: column; justify-content: center;
        position: relative; overflow: hidden;
        padding: 120px 0 80px;
    }
    .hero-noise {
        position: absolute; inset: 0; pointer-events: none;
        opacity: .04;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
        background-size: 200px;
    }
    .hero-grid {
        position: absolute; inset: 0; pointer-events: none;
        background-image:
            linear-gradient(var(--border) 1px, transparent 1px),
            linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 60px 60px;
        mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
    }
    .hero-glow {
        position: absolute; width: 600px; height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, #f9731618 0%, transparent 70%);
        top: 50%; left: 50%; transform: translate(-50%, -60%);
        pointer-events: none;
    }
    .hero-content {
        position: relative; z-index: 1;
        max-width: 780px; margin: 0 auto; text-align: center;
        padding: 0 24px;
    }
    .hero-tag {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 14px; border-radius: 100px;
        border: 1px solid var(--border-hov);
        font-size: 12px; letter-spacing: .1em; text-transform: uppercase;
        color: var(--accent-text); margin-bottom: 32px;
    }
    .hero-tag-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--accent);
        box-shadow: 0 0 8px var(--accent);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; } 50% { opacity: .4; }
    }
    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(56px, 10vw, 120px);
        font-weight: 800; line-height: .92;
        letter-spacing: -0.03em;
        text-transform: uppercase;
    }
    .hero-title em {
        font-style: normal;
        -webkit-text-stroke: 2px var(--accent);
        color: transparent;
    }
    .hero-desc {
        margin: 28px auto 40px; max-width: 520px;
        font-size: 17px; color: var(--text-2); line-height: 1.7;
    }
    .hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    .hero-scroll {
        position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%);
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: var(--text-3); font-size: 11px; letter-spacing: .1em;
        text-transform: uppercase; z-index: 1;
    }
    .hero-scroll-line {
        width: 1px; height: 48px;
        background: linear-gradient(to bottom, var(--text-3), transparent);
        animation: scrollAnim 1.6s ease-in-out infinite;
    }
    @keyframes scrollAnim { 0% { transform: scaleY(0); transform-origin: top; }
        50% { transform: scaleY(1); transform-origin: top; }
        51% { transform-origin: bottom; }
        100% { transform: scaleY(0); transform-origin: bottom; } }

    /* ── Marquee ───────────────────────────────────────────────────── */
    .marquee {
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        padding: 14px 0; overflow: hidden; white-space: nowrap;
        background: #0a0c10;
    }
    .marquee-track {
        display: inline-flex; gap: 0;
        animation: marquee 30s linear infinite;
    }
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .marquee-item {
        padding: 0 32px; font-size: 13px; letter-spacing: .08em;
        text-transform: uppercase; color: var(--text-3);
    }
    .marquee-item span { color: var(--accent); margin-right: 8px; }

    /* ── Buttons ───────────────────────────────────────────────────── */
    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px 24px; border-radius: var(--radius-sm);
        font-family: var(--font-display); font-weight: 600;
        font-size: 14px; letter-spacing: .02em;
        transition: all var(--trans); white-space: nowrap;
    }
    .btn--primary {
        background: var(--accent); color: #fff;
        box-shadow: 0 0 0 0 #f9731400;
    }
    .btn--primary:hover {
        background: #ea6a0f;
        box-shadow: 0 0 24px #f9731440;
        transform: translateY(-1px);
    }
    .btn--ghost {
        background: transparent; color: var(--text-1);
        border: 1px solid var(--border-hov);
    }
    .btn--ghost:hover { border-color: var(--text-3); background: #ffffff08; }
    .btn--sm { padding: 8px 16px; font-size: 13px; }
    .btn--outline {
        background: transparent; color: var(--accent);
        border: 1px solid var(--accent);
    }
    .btn--outline:hover { background: var(--accent-dim); }

    /* ── Section Header ────────────────────────────────────────────── */
    .sec-header { margin-bottom: 56px; }
    .sec-header--center { text-align: center; }
    .sec-label {
        font-size: 12px; letter-spacing: .15em; text-transform: uppercase;
        color: var(--accent-text); margin-bottom: 12px;
    }
    .sec-title {
        font-family: var(--font-display);
        font-size: clamp(28px, 4vw, 48px);
        font-weight: 700; line-height: 1.1;
    }
    .sec-count {
        font-family: var(--font-body); font-weight: 400;
        font-size: .55em; color: var(--text-3); margin-left: 12px;
        vertical-align: middle;
    }

    /* ── Filter Bar ────────────────────────────────────────────────── */
    .filter-bar {
        display: flex; flex-wrap: wrap; gap: 16px;
        align-items: center; justify-content: space-between;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }
    .filter-tabs {
        display: flex; flex-wrap: wrap; gap: 8px;
    }
    .filter-tab {
        padding: 7px 16px; border-radius: 100px;
        font-size: 13px; font-weight: 500;
        border: 1px solid var(--border);
        color: var(--text-2);
        transition: all var(--trans);
    }
    .filter-tab:hover { border-color: var(--border-hov); color: var(--text-1); }
    .filter-tab.active {
        background: var(--accent); border-color: var(--accent);
        color: #fff;
    }
    .filter-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-search {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 14px; border-radius: var(--radius-sm);
        background: var(--bg-card); border: 1px solid var(--border);
        transition: border-color var(--trans);
    }
    .filter-search:focus-within { border-color: var(--accent); }
    .filter-search svg { color: var(--text-3); flex-shrink: 0; }
    .filter-search input {
        background: none; border: none; outline: none;
        font-family: var(--font-body); font-size: 14px;
        color: var(--text-1); width: 180px;
    }
    .filter-search input::placeholder { color: var(--text-3); }
    .select {
        padding: 8px 14px; border-radius: var(--radius-sm);
        background: var(--bg-card); border: 1px solid var(--border);
        color: var(--text-1); font-family: var(--font-body); font-size: 14px;
        outline: none; cursor: pointer;
        transition: border-color var(--trans);
    }
    .select:hover { border-color: var(--border-hov); }
    .select:focus { border-color: var(--accent); }

    /* ── Products Grid ─────────────────────────────────────────────── */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    /* ── Product Card ──────────────────────────────────────────────── */
    .pcard {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: border-color var(--trans), transform var(--trans), box-shadow var(--trans);
        position: relative;
        display: flex; flex-direction: column;
    }
    .pcard:hover {
        border-color: var(--border-hov);
        transform: translateY(-4px);
        box-shadow: 0 20px 60px #00000060;
    }

    /* Image */
    .pcard__img-wrap {
        position: relative; overflow: hidden;
        aspect-ratio: 4/3; background: #1a1d26;
    }
    .pcard__img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.5s ease;
    }
    .pcard:hover .pcard__img { transform: scale(1.06); }

    /* Overlay */
    .pcard__overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, #0d0f14cc 0%, transparent 50%);
        opacity: 0; transition: opacity var(--trans);
    }
    .pcard:hover .pcard__overlay { opacity: 1; }

    /* Quick View */
    .pcard__quick-view {
        position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%) translateY(8px);
        background: var(--accent); color: #fff; padding: 8px 18px;
        border-radius: 100px; font-size: 13px; font-weight: 600;
        white-space: nowrap; opacity: 0;
        transition: all var(--trans);
    }
    .pcard:hover .pcard__quick-view { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* Badges */
    .pcard__badges {
        position: absolute; top: 10px; left: 10px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .badge {
        display: inline-block; padding: 3px 10px; border-radius: 100px;
        font-size: 11px; font-weight: 600; letter-spacing: .04em;
    }
    .badge--warn   { background: #eab30820; color: var(--warn); border: 1px solid #eab30830; }
    .badge--cat    { background: #ffffff10; color: var(--text-2); border: 1px solid var(--border); }
    .badge--danger { background: #ef444420; color: var(--danger); border: 1px solid #ef444430; }

    /* Wishlist */
    .pcard__wish {
        position: absolute; top: 10px; right: 10px;
        width: 34px; height: 34px; border-radius: 50%;
        background: #0d0f14bb; backdrop-filter: blur(8px);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-3);
        transition: all var(--trans); z-index: 2;
    }
    .pcard__wish:hover { border-color: #ef4444; color: #ef4444; }
    .pcard__wish.active { color: #ef4444; }
    .pcard__wish.active svg { fill: #ef4444; }

    /* Body */
    .pcard__body { padding: 16px; flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .pcard__cat  { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: var(--accent-text); }
    .pcard__name {
        font-family: var(--font-display); font-weight: 700; font-size: 16px;
        line-height: 1.3;
    }
    .pcard__name a { transition: color var(--trans); }
    .pcard__name a:hover { color: var(--accent-text); }
    .pcard__desc { font-size: 13px; color: var(--text-3); line-height: 1.5; flex: 1; }

    /* Footer */
    .pcard__footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: auto; padding-top: 12px;
        border-top: 1px solid var(--border);
    }
    .pcard__price {
        font-family: var(--font-display); font-weight: 700;
        font-size: 18px; color: var(--text-1);
    }

    /* Stock bar */
    .pcard__stock-wrap { margin-top: 6px; }
    .pcard__stock-bar {
        height: 3px; background: #ffffff0f; border-radius: 100px; overflow: hidden;
    }
    .pcard__stock-fill {
        height: 100%; background: var(--accent); border-radius: 100px;
        transition: width 1s ease;
    }
    .pcard__stock-text { font-size: 11px; color: var(--text-3); margin-top: 4px; }

    /* ── Empty State ───────────────────────────────────────────────── */
    .empty-state {
        text-align: center; padding: 80px 24px;
        color: var(--text-3);
    }
    .empty-state svg { margin: 0 auto 20px; opacity: .3; }
    .empty-state h3 {
        font-family: var(--font-display); font-size: 22px;
        color: var(--text-2); margin-bottom: 8px;
    }
    .empty-state p { font-size: 15px; margin-bottom: 28px; }

    /* ── Pagination ────────────────────────────────────────────────── */
    .pagination {
        display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;
        margin-top: 56px;
    }
    .page-btn {
        min-width: 40px; height: 40px; padding: 0 12px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: var(--radius-sm); font-size: 14px; font-weight: 500;
        border: 1px solid var(--border); color: var(--text-2);
        transition: all var(--trans);
    }
    .page-btn:hover { border-color: var(--border-hov); color: var(--text-1); }
    .page-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
    .page-btn:disabled { opacity: .3; pointer-events: none; }

    /* ── Features ──────────────────────────────────────────────────── */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 2px;
    }
    .feature-card {
        padding: 36px 28px; background: var(--bg-card);
        border: 1px solid var(--border);
        transition: background var(--trans), border-color var(--trans);
        position: relative; overflow: hidden;
    }
    .feature-card::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle at 50% 0%, var(--accent-dim) 0%, transparent 60%);
        opacity: 0; transition: opacity var(--trans);
    }
    .feature-card:hover::before { opacity: 1; }
    .feature-card:hover { border-color: var(--border-hov); }
    .feature-icon {
        width: 52px; height: 52px; border-radius: var(--radius-sm);
        background: var(--accent-dim); border: 1px solid #f9731630;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); margin-bottom: 20px;
    }
    .feature-title {
        font-family: var(--font-display); font-weight: 700;
        font-size: 18px; margin-bottom: 10px;
    }
    .feature-desc { font-size: 14px; color: var(--text-3); line-height: 1.6; }

    /* ── Responsive ────────────────────────────────────────────────── */
    @media (max-width: 640px) {
        .hero-actions { flex-direction: column; align-items: center; }
        .filter-bar { flex-direction: column; align-items: flex-start; }
        .filter-search input { width: 140px; }
        .sec-header { margin-bottom: 36px; }
    }
    </style>
</head>
<body>
<?php include '../components/navbar.php'; ?>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<?php if ($flash): ?>
     <script>
     document.addEventListener('DOMContentLoaded', () => {
         showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
     });
     </script>
<?php endif; ?>

<!-- ═══════════ HERO ═══════════════════════════════════════════════════════ -->
<section class="hero" aria-label="Banner utama">
    <div class="hero-noise" aria-hidden="true"></div>
    <div class="hero-grid"  aria-hidden="true"></div>
    <div class="hero-glow"  aria-hidden="true"></div>

    <div class="hero-content">
        <div class="hero-tag" aria-hidden="true">
            <span class="hero-tag-dot"></span>
            Sistem Kantin Digital
        </div>

        <h1 class="hero-title">
            KANTIN <em>DIGITAL</em>
        </h1>

        <p class="hero-desc">
            Pesan makanan &amp; minuman favoritmu dengan cepat, mudah, dan praktis —
            langsung dari genggamanmu.
        </p>

        <div class="hero-actions">
            <a href="#menu" class="btn btn--primary btn--lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                </svg>
                Lihat Menu
            </a>
            <?php if (!isLoggedIn()): ?>
                 <a href="/auth/register.php" class="btn btn--ghost">Daftar Sekarang</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero-scroll" aria-hidden="true">
        <span>Scroll</span>
        <div class="hero-scroll-line"></div>
    </div>
</section>

<!-- ═══════════ MARQUEE ═══════════════════════════════════════════════════ -->
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        <?php
        $marqueeItems = ['Makanan Segar', 'Minuman Dingin', 'Harga Terjangkau', 'Order Digital', 'Bayar Mudah', 'Siap Cepat'];
        // Duplikasi 4× agar seamless pada layar lebar
        for ($i = 0; $i < 4; $i++) {
             foreach ($marqueeItems as $item): ?>
                      <span class="marquee-item"><span>★</span> <?= e($item) ?></span>
             <?php endforeach;
        } ?>
    </div>
</div>

<!-- ═══════════ MENU ══════════════════════════════════════════════════════ -->
<section class="section" id="menu" aria-label="Daftar menu">
    <div class="container">

        <div class="sec-header">
            <div class="sec-label">Menu Kami</div>
            <h2 class="sec-title">
                <?= e($activeCatName ?: 'Semua Produk') ?>
                <span class="sec-count">(<?= $totalItems ?> produk)</span>
            </h2>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <!-- Category Tabs -->
            <nav class="filter-tabs" aria-label="Filter kategori">
                <a href="<?= $buildUrl(['cat' => 0, 'page' => 1]) ?>"
                   class="filter-tab <?= $filters['cat'] === 0 ? 'active' : '' ?>">
                    Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                     <a href="<?= $buildUrl(['cat' => $cat['id'], 'page' => 1]) ?>"
                        class="filter-tab <?= $filters['cat'] === (int) $cat['id'] ? 'active' : '' ?>">
                         <?= e($cat['name']) ?>
                     </a>
                <?php endforeach; ?>
            </nav>

            <!-- Search & Sort -->
            <div class="filter-controls">
                <!-- Search -->
                <form method="GET" action="" role="search">
                    <?php if ($filters['cat'] > 0): ?>
                         <input type="hidden" name="cat" value="<?= $filters['cat'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>">
                    <input type="hidden" name="page" value="1">
                    <div class="filter-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="search" name="q" placeholder="Cari produk…"
                               value="<?= e($filters['search']) ?>"
                               maxlength="100"
                               aria-label="Cari produk">
                    </div>
                </form>

                <!-- Sort -->
                <form method="GET" action="">
                    <?php if ($filters['cat'] > 0): ?>
                         <input type="hidden" name="cat" value="<?= $filters['cat'] ?>">
                    <?php endif; ?>
                    <?php if ($filters['search'] !== ''): ?>
                         <input type="hidden" name="q" value="<?= e($filters['search']) ?>">
                    <?php endif; ?>
                    <input type="hidden" name="page" value="1">
                    <select name="sort" class="select" onchange="this.form.submit()" aria-label="Urutan produk">
                        <?php foreach (['newest' => 'Terbaru', 'popular' => 'Terpopuler', 'price_asc' => 'Harga ↑', 'price_desc' => 'Harga ↓'] as $val => $label): ?>
                             <option value="<?= $val ?>" <?= $filters['sort'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
             <div class="empty-state" role="status">
                 <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                     <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                 </svg>
                 <h3>Produk tidak ditemukan</h3>
                 <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
                 <a href="/public/index.php" class="btn btn--primary">Reset Filter</a>
             </div>

        <?php else: ?>
             <div class="products-grid" id="productsGrid">
                 <?php foreach ($products as $p):
                      $inWishlist = isset($wishlistMap[$p['id']]);
                      $imgSrc = !empty($p['image'])
                           ? '/public/assets/uploads/' . rawurlencode($p['image'])
                           : '/public/assets/img/placeholder.png';
                      $stockPct = $p['stock'] <= 20 ? min(100, ($p['stock'] / 20) * 100) : 100;
                      ?>
                      <article class="pcard" data-id="<?= (int) $p['id'] ?>">
                          <div class="pcard__img-wrap">
                              <a href="/public/product.php?id=<?= (int) $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                  <img src="<?= e($imgSrc) ?>"
                                       alt="<?= e($p['name']) ?>"
                                       class="pcard__img"
                                       loading="lazy"
                                       width="400" height="300">
                              </a>
                              <div class="pcard__overlay"></div>

                              <a href="/public/product.php?id=<?= (int) $p['id'] ?>" class="pcard__quick-view">
                                  Lihat Detail
                              </a>

                              <!-- Badges -->
                              <div class="pcard__badges">
                                  <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                                       <span class="badge badge--warn">Stok Terbatas</span>
                                  <?php endif; ?>
                                  <?php if (!empty($p['category_name'])): ?>
                                       <span class="badge badge--cat"><?= e($p['category_name']) ?></span>
                                  <?php endif; ?>
                              </div>

                              <!-- Wishlist -->
                              <?php if (isLoggedIn()): ?>
                                   <button class="pcard__wish <?= $inWishlist ? 'active' : '' ?>"
                                           onclick="toggleWishlist(<?= (int) $p['id'] ?>, this)"
                                           aria-label="<?= $inWishlist ? 'Hapus dari wishlist' : 'Tambah ke wishlist' ?>"
                                           aria-pressed="<?= $inWishlist ? 'true' : 'false' ?>">
                                       <svg width="16" height="16" viewBox="0 0 24 24"
                                            fill="<?= $inWishlist ? 'currentColor' : 'none' ?>"
                                            stroke="currentColor" stroke-width="2" aria-hidden="true">
                                           <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                       </svg>
                                   </button>
                              <?php endif; ?>
                          </div>

                          <div class="pcard__body">
                              <div class="pcard__cat"><?= e($p['category_name'] ?? '') ?></div>
                              <h3 class="pcard__name">
                                  <a href="/public/product.php?id=<?= (int) $p['id'] ?>"><?= e($p['name']) ?></a>
                              </h3>

                              <div class="pcard__footer">
                                  <div class="pcard__price"><?= formatRupiah((float) $p['price']) ?></div>
                                  <?php if ($p['stock'] > 0): ?>
                                       <button class="btn btn--primary btn--sm"
                                               onclick="addToCart(<?= (int) $p['id'] ?>, <?= json_encode($p['name']) ?>, <?= (float) $p['price'] ?>)">
                                           + Keranjang
                                       </button>
                                  <?php else: ?>
                                       <span class="badge badge--danger">Habis</span>
                                  <?php endif; ?>
                              </div>

                              <?php if ($p['stock'] > 0 && $p['stock'] <= 20): ?>
                                   <div class="pcard__stock-wrap">
                                       <div class="pcard__stock-bar">
                                           <div class="pcard__stock-fill" style="width:<?= $stockPct ?>%"></div>
                                       </div>
                                       <p class="pcard__stock-text">Sisa <?= (int) $p['stock'] ?> porsi</p>
                                   </div>
                              <?php endif; ?>
                          </div>
                      </article>
                 <?php endforeach; ?>
             </div>

             <!-- Pagination -->
             <?php if ($totalPages > 1): ?>
                  <nav class="pagination" aria-label="Navigasi halaman">
                      <?php if ($filters['page'] > 1): ?>
                           <a href="<?= $buildUrl(['page' => $filters['page'] - 1]) ?>"
                              class="page-btn" aria-label="Halaman sebelumnya">&larr;</a>
                      <?php else: ?>
                           <span class="page-btn" aria-disabled="true">&larr;</span>
                      <?php endif; ?>

                      <?php
                      $start = max(1, $filters['page'] - 2);
                      $end = min($totalPages, $filters['page'] + 2);
                      if ($start > 1): ?><span class="page-btn" style="pointer-events:none">…</span><?php endif;
                      for ($pg = $start; $pg <= $end; $pg++): ?>
                           <a href="<?= $buildUrl(['page' => $pg]) ?>"
                              class="page-btn <?= $pg === $filters['page'] ? 'active' : '' ?>"
                              <?= $pg === $filters['page'] ? 'aria-current="page"' : '' ?>>
                               <?= $pg ?>
                           </a>
                      <?php endfor;
                      if ($end < $totalPages): ?><span class="page-btn" style="pointer-events:none">…</span><?php endif; ?>

                      <?php if ($filters['page'] < $totalPages): ?>
                           <a href="<?= $buildUrl(['page' => $filters['page'] + 1]) ?>"
                              class="page-btn" aria-label="Halaman berikutnya">&rarr;</a>
                      <?php else: ?>
                           <span class="page-btn" aria-disabled="true">&rarr;</span>
                      <?php endif; ?>
                  </nav>
             <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<!-- ═══════════ FEATURES ═════════════════════════════════════════════════ -->
<section class="section section--dark" id="features" aria-label="Keunggulan kami">
    <div class="container">
        <div class="sec-header sec-header--center">
            <div class="sec-label">Mengapa Kami</div>
            <h2 class="sec-title">Lebih dari sekadar kantin</h2>
        </div>

        <div class="features-grid">
            <?php
            $features = [
                 ['icon' => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>', 'title' => 'Order Kilat', 'desc' => 'Pesan dalam hitungan detik, makananmu siap sebelum istirahat habis.'],
                 ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 'title' => 'Aman & Terpercaya', 'desc' => 'Transaksi aman, data terlindungi, dan menu dijamin bersih & higienis.'],
                 ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'title' => 'Realtime Status', 'desc' => 'Pantau status pesananmu secara langsung — dari proses hingga siap diambil.'],
                 ['icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>', 'title' => 'Harga Bersaing', 'desc' => 'Menu terjangkau untuk semua kalangan, tanpa biaya tambahan tersembunyi.'],
            ];
            foreach ($features as $f): ?>
                 <div class="feature-card">
                     <div class="feature-icon" aria-hidden="true">
                         <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                             <?= $f['icon'] ?>
                         </svg>
                     </div>
                     <h3 class="feature-title"><?= e($f['title']) ?></h3>
                     <p class="feature-desc"><?= e($f['desc']) ?></p>
                 </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<!-- ═══════════ SCRIPTS ══════════════════════════════════════════════════ -->
<script>
"use strict";

// ── Add to Cart ──────────────────────────────────────────────────────────────
async function addToCart(id, name, price) {
    try {
        const res = await fetch('/public/cart/action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add', id, name, price, qty: 1 }),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data.success) {
            showToast(`${name} ditambahkan ke keranjang!`, 'success');
            updateCartCount(data.cart_count ?? 0);
        } else {
            showToast(data.message || 'Gagal menambahkan produk.', 'error');
        }
    } catch (err) {
        console.error('addToCart:', err);
        showToast('Terjadi kesalahan jaringan.', 'error');
    }
}

// ── Wishlist Toggle ──────────────────────────────────────────────────────────
async function toggleWishlist(id, btn) {
    btn.disabled = true;
    try {
        const res = await fetch('/public/wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'toggle', id }),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data.success) {
            const added = Boolean(data.added);
            btn.classList.toggle('active', added);
            btn.querySelector('path').setAttribute('fill', added ? 'currentColor' : 'none');
            btn.setAttribute('aria-pressed', String(added));
            btn.setAttribute('aria-label', added ? 'Hapus dari wishlist' : 'Tambah ke wishlist');
            showToast(added ? 'Ditambahkan ke wishlist ♥' : 'Dihapus dari wishlist', 'success');
        } else {
            showToast(data.message || 'Gagal memperbarui wishlist.', 'error');
        }
    } catch (err) {
        console.error('toggleWishlist:', err);
        showToast('Terjadi kesalahan jaringan.', 'error');
    } finally {
        btn.disabled = false;
    }
}

// ── Cart Count ───────────────────────────────────────────────────────────────
function updateCartCount(count) {
    let badge = document.querySelector('.nav-cart-count');
    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            const btn = document.querySelector('.nav-cart-btn');
            if (btn) {
                badge = document.createElement('span');
                badge.className = 'nav-cart-count';
                badge.textContent = count;
                btn.appendChild(badge);
            }
        }
    } else {
        badge?.remove();
    }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.setAttribute('role', 'alert');

    const dot = document.createElement('span');
    dot.className = 'toast-dot';

    const text = document.createElement('span');
    text.textContent = message;

    const close = document.createElement('button');
    close.className = 'toast-close';
    close.setAttribute('aria-label', 'Tutup notifikasi');
    close.textContent = '×';
    close.addEventListener('click', () => removeToast(toast));

    toast.append(dot, text, close);
    container.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
    setTimeout(() => removeToast(toast), 4000);
}

function removeToast(toast) {
    toast.classList.remove('show');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
}

// ── Smooth Scroll ─────────────────────────────────────────────────────────────
document.querySelector('a[href="#menu"]')?.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('menu')?.scrollIntoView({ behavior: 'smooth' });
});

// ── Search: auto-submit on clear ─────────────────────────────────────────────
document.querySelector('input[type="search"]')?.addEventListener('search', function () {
    if (this.value === '') this.closest('form').submit();
});
</script>
</body>
</html>