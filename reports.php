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
</head>
<body>
  <h1>Sales Reports</h1>
  <a href="index.php">Back to POS</a>

  <br><br>
  <input type="date" id="reportDate">
  <button onclick="loadReport()">Load Report</button>

  <h2>Summary</h2>
  <p id="summary"></p>

  <h2>Best Sellers</h2>
  <div id="bestSellers"></div>

  <h2>Transactions</h2>
  <div id="salesList"></div>

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