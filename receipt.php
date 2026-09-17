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
  <title>Receipt</title>
  <style>
    body {
      font-family: 'Courier New', monospace;
      max-width: 320px;
      margin: 20px auto;
      font-size: 13px;
      color: #000;
    }
    .center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 8px 0; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 2px 0; }
    .right { text-align: right; }
    .no-print { margin-top: 20px; text-align: center; }
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body>
  <div id="receiptContent">Loading...</div>

  <div class="no-print">
    <button onclick="window.print()">🖨 Print Receipt</button>
    <button onclick="window.location.href='index.php'">New Sale</button>
  </div>

  <script>
    const params = new URLSearchParams(window.location.search);
    const saleId = params.get('sale_id');

    fetch('api/receipt_get.php?sale_id=' + saleId)
      .then(res => res.json())
      .then(data => {
        const sale = data.sale;
        const items = data.items;

        let itemsHtml = items.map(i => `
  <tr>
    <td>${i.product_name}<br><small>${i.quantity} x Rs.${parseFloat(i.unit_price).toFixed(2)}</small></td>
    <td class="right">Rs.${parseFloat(i.subtotal).toFixed(2)}</td>
  </tr>
`).join('');

        const receiptNo = 'POS-' + String(sale.id).padStart(6, '0');

let paymentRows = `
  <tr>
    <td>Payment</td>
    <td class="right">${sale.payment_method.toUpperCase()}</td>
  </tr>
`;

if (sale.payment_method === 'cash' && sale.paid_amount !== null) {
  const change = parseFloat(sale.paid_amount) - parseFloat(sale.total_amount);
  paymentRows += `
    <tr>
      <td>Cash Received</td>
      <td class="right">Rs.${parseFloat(sale.paid_amount).toFixed(2)}</td>
    </tr>
   <tr>
  <td><strong>Balance</strong></td>
  <td class="right"><strong>Rs.${change.toFixed(2)}</strong></td>
</tr>
  `;
}

document.getElementById('receiptContent').innerHTML = `
  <div class="center">
    <strong>ISHEEMS FOOD CITY</strong><br>
    Main Street, Madawakkulam, Andigama<br>
    Tel: 078-4775289<br>
    Point of Sale Receipt
  </div>
  <div class="line"></div>
  Receipt No: ${receiptNo}<br>
  Date: ${sale.created_at}<br>
  Cashier: ${sale.cashier_name}<br>
  <div class="line"></div>
  <table>${itemsHtml}</table>
  <div class="line"></div>
  <table>
  ${sale.discount_amount > 0 ? `
    <tr>
      <td>Discount</td>
      <td class="right">- Rs.${parseFloat(sale.discount_amount).toFixed(2)}</td>
    </tr>
  ` : ''}
  <tr>
    <td><strong>TOTAL</strong></td>
    <td class="right"><strong>Rs.${parseFloat(sale.total_amount).toFixed(2)}</strong></td>
  </tr>
  ${paymentRows}
</table>
  <div class="line"></div>
  <div class="center">Thank you for shopping with us!<br>Goods once sold are not returnable.</div>
`;
      });
  </script>
</body>
</html>