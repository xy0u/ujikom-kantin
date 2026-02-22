<?php
session_start();
require '../../core/database.php';
require '../../core/helpers.php';

$flash     = getFlash();
$cart      = $_SESSION['cart'] ?? [];
$cartTotal = getCartTotal();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang — Kantin Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/public.css">
</head>
<body>
<?php include '../../components/navbar.php'; ?>
<div class="toast-container" id="toastContainer"></div>

<main class="section">
    <div class="container container--narrow">
        <div class="page-header">
            <h1 class="page-title">Keranjang Belanja</h1>
            <p class="page-subtitle"><?= $cartCount ?> item dalam keranjang</p>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert--<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
            </div>
            <h3 class="empty-state-title">Keranjang kosong</h3>
            <p class="empty-state-desc">Tambahkan produk ke keranjang terlebih dahulu.</p>
            <a href="/public/index.php" class="btn btn--primary">Lihat Menu</a>
        </div>
        <?php else: ?>

        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items" id="cartItems">
                <?php foreach ($cart as $id => $item): ?>
                <div class="cart-item" id="cart-item-<?= $id ?>">
                    <div class="cart-item__img-wrap">
                        <?php if ($item['image']): ?>
                        <img src="/public/assets/uploads/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="cart-item__img">
                        <?php else: ?>
                        <div class="cart-item__img-placeholder">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="cart-item__info">
                        <div class="cart-item__name"><?= e($item['name']) ?></div>
                        <div class="cart-item__price"><?= formatRupiah($item['price']) ?></div>
                    </div>

                    <div class="cart-item__qty">
                        <button class="qty-btn" onclick="updateQty(<?= $id ?>, -1)">−</button>
                        <span class="qty-value" id="qty-<?= $id ?>"><?= $item['qty'] ?></span>
                        <button class="qty-btn" onclick="updateQty(<?= $id ?>, 1)">+</button>
                    </div>

                    <div class="cart-item__subtotal font-mono" id="subtotal-<?= $id ?>">
                        <?= formatRupiah($item['price'] * $item['qty']) ?>
                    </div>

                    <button class="cart-item__remove" onclick="removeItem(<?= $id ?>)" aria-label="Hapus">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="cart-summary__inner">
                    <h3 class="cart-summary__title">Ringkasan Pesanan</h3>

                    <div class="cart-summary__rows">
                        <div class="cart-summary__row">
                            <span>Subtotal (<?= $cartCount ?> item)</span>
                            <span id="totalDisplay" class="font-mono"><?= formatRupiah($cartTotal) ?></span>
                        </div>
                        <div class="cart-summary__row">
                            <span>Biaya Layanan</span>
                            <span class="font-mono text-muted">Gratis</span>
                        </div>
                    </div>

                    <div class="cart-summary__total">
                        <span>Total</span>
                        <span id="grandTotalDisplay" class="font-mono"><?= formatRupiah($cartTotal) ?></span>
                    </div>

                    <?php if (isLoggedIn()): ?>
                    <a href="/public/checkout/index.php" class="btn btn--primary btn--full btn--lg">
                        Lanjut ke Checkout
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                    <?php else: ?>
                    <a href="/auth/login.php?redirect=/public/checkout/index.php" class="btn btn--primary btn--full btn--lg">
                        Login untuk Checkout
                    </a>
                    <?php endif; ?>

                    <a href="/public/index.php" class="btn btn--ghost btn--full" style="margin-top:0.5rem">
                        ← Lanjut Belanja
                    </a>

                    <button class="btn btn--danger btn--full" style="margin-top:0.5rem" onclick="clearCart()">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</main>

<?php include '../../components/footer.php'; ?>

<script>
const PRICE_MAP = <?= json_encode(array_map(fn($i) => $i['price'], $cart)) ?>;

function formatRupiah(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

async function updateQty(id, delta) {
    const qtyEl = document.getElementById('qty-' + id);
    const newQty = Math.max(1, (parseInt(qtyEl.textContent) || 1) + delta);

    const res = await fetch('/public/cart/action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update', id, qty: newQty })
    });
    const data = await res.json();
    if (data.success) {
        qtyEl.textContent = newQty;
        const price = PRICE_MAP[id] || 0;
        document.getElementById('subtotal-' + id).textContent = formatRupiah(price * newQty);
        updateTotals(data.cart_total, data.cart_count);
    }
}

async function removeItem(id) {
    if (!confirm('Hapus produk dari keranjang?')) return;
    const res = await fetch('/public/cart/action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', id })
    });
    const data = await res.json();
    if (data.success) {
        const el = document.getElementById('cart-item-' + id);
        el.style.opacity = '0';
        el.style.height  = el.offsetHeight + 'px';
        setTimeout(() => {
            el.style.height = '0';
            el.style.margin = '0';
            el.style.padding = '0';
            setTimeout(() => el.remove(), 300);
        }, 200);
        updateTotals(data.cart_total, data.cart_count);
        if (data.cart_count === 0) setTimeout(() => location.reload(), 600);
    }
}

async function clearCart() {
    if (!confirm('Kosongkan seluruh keranjang?')) return;
    const res = await fetch('/public/cart/action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'clear' })
    });
    const data = await res.json();
    if (data.success) location.reload();
}

function updateTotals(total, count) {
    const totalEl = document.getElementById('totalDisplay');
    const grandEl = document.getElementById('grandTotalDisplay');
    const formatted = formatRupiah(total);
    if (totalEl) totalEl.textContent = formatted;
    if (grandEl) grandEl.textContent = formatted;

    const badge = document.querySelector('.nav-cart-count');
    if (badge) badge.textContent = count;
}

function showToast(msg, type) {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast toast--${type}`;
    t.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()">&times;</button>`;
    c.appendChild(t);
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => t.remove(), 4000);
}
</script>
</body>
</html>