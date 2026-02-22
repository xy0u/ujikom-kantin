<?php
session_start();
require '../../core/database.php';
require '../../core/helpers.php';
requireLogin();

$id      = (int)($_GET['id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

$order = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
     FROM orders o LEFT JOIN users u ON o.user_id = u.id
     WHERE o.id=$id AND (o.user_id=$user_id" . (isAdmin() ? " OR 1=1" : "") . ")"
));

if (!$order) die('Pesanan tidak ditemukan.');

$items_q = mysqli_query($conn,
    "SELECT oi.*, p.name as product_name
     FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id=$id"
);
$items = [];
while ($row = mysqli_fetch_assoc($items_q)) $items[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?> — Kantin Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/public.css">
    <style>
        body { background: #fff; color: #111; }
        .invoice { max-width: 680px; margin: 2rem auto; padding: 2rem; border: 2px solid #111; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #111; }
        .invoice-logo { font-family: 'Space Mono', monospace; font-size: 1.5rem; font-weight: 700; }
        .invoice-meta { text-align: right; }
        .invoice-number { font-family: 'Space Mono', monospace; font-size: 1.25rem; font-weight: 700; }
        .invoice-date { font-size: 0.85rem; color: #666; margin-top: 0.25rem; }
        .invoice-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        .invoice-party-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #666; margin-bottom: 0.5rem; font-family: 'Space Mono', monospace; }
        .invoice-party-name { font-weight: 600; }
        .invoice-party-detail { font-size: 0.85rem; color: #666; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        .invoice-table th { text-align: left; font-family: 'Space Mono', monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.75rem 0; border-bottom: 2px solid #111; }
        .invoice-table td { padding: 0.75rem 0; border-bottom: 1px solid #e5e5e5; vertical-align: top; }
        .invoice-table tfoot td { border-top: 2px solid #111; border-bottom: none; padding-top: 1rem; }
        .invoice-total-row td { font-weight: 700; font-family: 'Space Mono', monospace; font-size: 1.1rem; }
        .invoice-footer { text-align: center; font-family: 'Space Mono', monospace; font-size: 0.75rem; color: #999; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e5e5; }
        .invoice-status { display: inline-block; padding: 0.25rem 0.75rem; border: 2px solid #111; font-family: 'Space Mono', monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; }
        .no-print { margin-bottom: 1rem; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .invoice { border: none; margin: 0; padding: 1.5rem; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print" style="padding: 1rem; text-align: center; background: #f4f4f0; border-bottom: 2px solid #111;">
    <button onclick="window.print()" style="font-family:'Space Mono',monospace;padding:0.5rem 1.5rem;background:#111;color:#f0ff00;border:2px solid #111;cursor:pointer;font-weight:700;letter-spacing:0.05em;">
        🖨 Cetak Invoice
    </button>
    <a href="/public/orders/detail.php?id=<?= $order['id'] ?>" style="margin-left:1rem;font-family:'Space Mono',monospace;font-size:0.85rem;color:#111;">
        ← Kembali
    </a>
</div>

<div class="invoice">
    <!-- Header -->
    <div class="invoice-header">
        <div>
            <div class="invoice-logo">[KANTIN DIGITAL]</div>
            <div style="font-size:0.85rem;color:#666;margin-top:0.25rem">Sistem Kantin Digital</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-number">INVOICE</div>
            <div class="invoice-number">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></div>
            <div class="invoice-date"><?= formatDate($order['created_at']) ?></div>
            <div style="margin-top:0.5rem">
                <span class="invoice-status"><?= strtoupper($order['status']) ?></span>
            </div>
        </div>
    </div>

    <!-- Parties -->
    <div class="invoice-parties">
        <div>
            <div class="invoice-party-label">Dari</div>
            <div class="invoice-party-name">Kantin Digital</div>
            <div class="invoice-party-detail">Sistem Kantin Sekolah</div>
        </div>
        <div>
            <div class="invoice-party-label">Kepada</div>
            <div class="invoice-party-name"><?= e($order['user_name']) ?></div>
            <div class="invoice-party-detail"><?= e($order['user_email']) ?></div>
            <?php if ($order['user_phone']): ?>
            <div class="invoice-party-detail"><?= e($order['user_phone']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items Table -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align:right">Harga</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['product_name']) ?></td>
                <td style="text-align:right;font-family:'Space Mono',monospace;font-size:0.9rem"><?= formatRupiah($item['price']) ?></td>
                <td style="text-align:right"><?= $item['quantity'] ?></td>
                <td style="text-align:right;font-family:'Space Mono',monospace;font-size:0.9rem"><?= formatRupiah($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Biaya Layanan</td>
                <td style="text-align:right;font-family:'Space Mono',monospace">Gratis</td>
            </tr>
            <tr class="invoice-total-row">
                <td colspan="3">TOTAL</td>
                <td style="text-align:right"><?= formatRupiah($order['total_price']) ?></td>
            </tr>
        </tfoot>
    </table>

    <?php if ($order['notes']): ?>
    <div style="margin-bottom:2rem">
        <div class="invoice-party-label">Catatan</div>
        <p style="font-size:0.9rem;color:#444"><?= e($order['notes']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="invoice-footer">
        <p>TERIMA KASIH ATAS PESANAN ANDA</p>
        <p style="margin-top:0.25rem">Dicetak pada <?= date('d M Y H:i') ?></p>
    </div>
</div>

</body>
</html>