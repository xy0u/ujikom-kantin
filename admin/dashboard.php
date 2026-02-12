<?php
require "../core/database.php";
include "layout.php";

/* TOTAL DATA */
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_income = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total),0) as total FROM orders WHERE status='SUCCESS'"))['total'];

/* CHART DATA (7 DATA TERBARU) */
$chartData = [];
$query = mysqli_query($conn, "
SELECT DATE(created_at) as date, SUM(total) as income 
FROM orders 
WHERE status='SUCCESS'
GROUP BY DATE(created_at)
ORDER BY DATE(created_at) DESC
LIMIT 7
");

while ($row = mysqli_fetch_assoc($query)) {
     $chartData[] = $row;
}

$chartData = array_reverse($chartData);
?>
<h1>Dashboard Overview</h1>

<div class="stats-grid">
     <div class="stat-card">
          <h3>Total Products</h3>
          <p><?= $total_products ?></p>
     </div>

     <div class="stat-card">
          <h3>Total Orders</h3>
          <p><?= $total_orders ?></p>
     </div>

     <div class="stat-card">
          <h3>Total Users</h3>
          <p><?= $total_users ?></p>
     </div>

     <div class="stat-card">
          <h3>Total Revenue</h3>
          <p>Rp <?= number_format($total_income) ?></p>
     </div>
</div>

<div class="card">
     <h3 style="margin-bottom:20px;">Revenue Chart</h3>
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
                    label: 'Revenue',
                    data: <?= json_encode(array_column($chartData, 'income')) ?>,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6,182,212,0.2)',
                    fill: true,
                    tension: 0.4
               }]
          },
          options: {
               responsive: true,
               plugins: {
                    legend: { labels: { color: "white" } }
               },
               scales: {
                    x: { ticks: { color: "white" } },
                    y: { ticks: { color: "white" } }
               }
          }
     });
</script>

</main>
</div>
</body>

</html>