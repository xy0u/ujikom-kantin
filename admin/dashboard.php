<?php
require "../core/database.php";
include "layout.php";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$total_income = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total),0) as total FROM orders WHERE status='SUCCESS'"))['total'];

$chartData = [];
$query = mysqli_query($conn, "SELECT DATE(created_at) as date, SUM(total) as income FROM orders WHERE status='SUCCESS' GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7");
while ($row = mysqli_fetch_assoc($query)) {
     $chartData[] = $row;
}
$chartData = array_reverse($chartData);
?>

<h1>Ringkasan Statistik</h1>

<div class="stats-grid">
     <div class="stat-card">
          <h3>Total Produk</h3>
          <p><?= $total_products ?></p>
     </div>
     <div class="stat-card">
          <h3>Pesanan Masuk</h3>
          <p><?= $total_orders ?></p>
     </div>
     <div class="stat-card">
          <h3>Total Pendapatan</h3>
          <p>Rp <?= number_format($total_income) ?></p>
     </div>
</div>

<div class="card">
     <h3 style="margin-bottom:30px; font-size: 14px; color: var(--text-muted); text-transform: uppercase;">Grafik
          Pendapatan (7 Hari Terakhir)</h3>
     <canvas id="revenueChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
     const ctx = document.getElementById('revenueChart');
     new Chart(ctx, {
          type: 'line',
          data: {
               labels: <?= json_encode(array_column($chartData, 'date')) ?>,
               datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode(array_column($chartData, 'income')) ?>,
                    borderColor: '#ffffff',
                    backgroundColor: 'rgba(255, 255, 255, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff'
               }]
          },
          options: {
               responsive: true,
               plugins: { legend: { display: false } },
               scales: {
                    x: { grid: { display: false }, ticks: { color: "#4b5563" } },
                    y: { grid: { color: "#1f2937" }, ticks: { color: "#4b5563" } }
               }
          }
     });
</script>