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
  <title>Reports - EGOTECHWORLD POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav>
    <strong>EGOTECHWORLD POS</strong>
    <a href="index.php">Back to POS</a>
  </nav>

  <div class="container">
    <h1>Sales Reports</h1>

    <div class="card">
      <input type="date" id="reportDate">
      <button onclick="loadReport()">Load Report</button>
    </div>

    <h2>Summary</h2>
    <p id="summary" class="card"></p>

    <h2>Best Sellers</h2>
    <div id="bestSellers"></div>

    <h2>Transactions</h2>
    <div id="salesList"></div>
<footer>EGOTECHWORLD POS v1.0 &nbsp;|&nbsp; © 2026 <strong>EGOTECHWORLD (PVT) LTD</strong></footer>
  <script>
    document.getElementById('reportDate').valueAsDate = new Date();

    function loadReport() {
      const date = document.getElementById('reportDate').value;
      fetch('api/sales_report.php?date=' + date)
        .then(res => res.json())
        .then(data => {
          document.getElementById('summary').textContent =
            `Total Revenue: Rs. ${parseFloat(data.total_revenue).toFixed(2)} | Transactions: ${data.total_transactions}`;

          document.getElementById('bestSellers').innerHTML = data.best_sellers.map(b => `
            <div>${b.name} — Sold: ${b.total_qty} | Revenue: Rs.${parseFloat(b.total_revenue).toFixed(2)}</div>
          `).join('') || '<p>No sales yet.</p>';

          document.getElementById('salesList').innerHTML = data.sales.map(s => `
            <div>Sale #${s.id} — ${s.cashier_name} — Rs.${s.total_amount} (${s.payment_method}) — ${s.created_at}</div>
          `).join('') || '<p>No transactions for this date.</p>';
        });
    }

    loadReport();
  </script>
</body>
</html>