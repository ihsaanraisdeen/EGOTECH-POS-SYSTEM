<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard - EGOTECHWORLD POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>
  <nav>
    <strong>EGOTECHWORLD POS</strong>
    <span><?php echo $_SESSION['user']['name']; ?></span>
    <a href="dashboard.php">Dashboard</a>
    <a href="index.php">POS</a>
    <a href="products.php">Manage Products</a>
    <a href="reports.php">Reports</a>
    <?php if ($_SESSION['user']['role'] === 'admin') { ?>
      <a href="users.php">Manage Users</a>
    <?php } ?>
    <button onclick="logout()" style="margin-left:auto;">Logout</button>
  </nav>

  <div class="container">
    <h1>Dashboard</h1>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Today's Sales</div>
        <div class="stat-value">Rs. <span id="todayTotal">0.00</span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Transactions Today</div>
        <div class="stat-value" id="todayCount">0</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Items Sold Today</div>
        <div class="stat-value" id="itemsSold">0</div>
      </div>
      <div class="stat-card warn">
        <div class="stat-label">Low Stock Items</div>
        <div class="stat-value" id="lowStock">0</div>
      </div>
    </div>

    <h2>Sales - Last 7 Days</h2>
    <div class="card">
      <canvas id="trendChart" height="90"></canvas>
    </div>
  </div>

  <footer>EGOTECHWORLD POS v1.0 &nbsp;|&nbsp; © 2026 <strong>EGOTECHWORLD (PVT) LTD</strong></footer>

  <script>
    let chart = null;

    function loadStats() {
      fetch('api/dashboard_stats.php')
        .then(res => res.json())
        .then(d => {
          document.getElementById('todayTotal').textContent = d.today_total.toFixed(2);
          document.getElementById('todayCount').textContent = d.today_count;
          document.getElementById('itemsSold').textContent = d.items_sold;
          document.getElementById('lowStock').textContent = d.low_stock;

          const labels = d.trend.map(t => t.day.slice(5));
          const values = d.trend.map(t => t.total);

          if (chart) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = values;
            chart.update();
          } else {
            chart = new Chart(document.getElementById('trendChart'), {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  label: 'Sales (Rs.)',
                  data: values,
                  backgroundColor: '#0f9d78',
                  borderRadius: 4
                }]
              },
              options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
              }
            });
          }
        });
    }

    loadStats();
    setInterval(loadStats, 10000);

    function logout() {
      fetch('api/logout.php', { method: 'POST' })
        .then(() => window.location.href = 'login.php');
    }
  </script>
</body>
</html>