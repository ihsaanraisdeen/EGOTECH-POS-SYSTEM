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
  <title>POS - EGOTECHWORLD</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav>
    <strong>EGOTECHWORLD POS</strong>
    <span><?php echo $_SESSION['user']['name']; ?></span>
    <a href="products.php">Manage Products</a>
    <a href="reports.php">Reports</a>
    <?php if ($_SESSION['user']['role'] === 'admin') { ?>
      <a href="users.php">Manage Users</a>
    <?php } ?>
    <button onclick="logout()" style="margin-left:auto;">Logout</button>
  </nav>

  <div class="container">
    <h1>New Sale</h1>
    <div id="lowStockAlert"></div>
    <div class="card">
      <input id="search" placeholder="Search product or scan barcode" autofocus style="width:100%;">
      <div id="searchResults" style="margin-top:8px;"></div>
    </div>
  <div id="searchResults"></div>

<div id="lowStockAlert" style="background:#fff3cd; padding:10px; margin-bottom:10px;"></div>

<h2>Cart</h2>  <table border="1" id="cartTable">
    <thead>
      <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr>
    </thead>
    <tbody id="cartBody"></tbody>
  </table>
  <h3>Total: Rs. <span id="cartTotal">0.00</span></h3>

  <select id="paymentMethod">
    <option value="cash">Cash</option>
    <option value="card">Card</option>
  </select>
  <button onclick="checkout()">Checkout</button>
  <p id="checkoutResult"></p>

  </div>

  <script>    let cart = [];
    function loadLowStock() {
  fetch('api/low_stock.php')
    .then(res => res.json())
    .then(items => {
      const el = document.getElementById('lowStockAlert');
      if (items.length === 0) {
        el.innerHTML = '';
        return;
      }
      el.innerHTML = '<strong>⚠ Low Stock:</strong> ' +
        items.map(i => `${i.name} (${i.stock_qty} left)`).join(', ');
    });
}

loadLowStock();
setInterval(loadLowStock, 5000);
    document.getElementById('search').addEventListener('input', function() {
      const q = this.value;
      if (q.length < 1) {
        document.getElementById('searchResults').innerHTML = '';
        return;
      }
      fetch('api/products_search.php?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(products => {
          document.getElementById('searchResults').innerHTML = products.map(p => `
            <div onclick="addToCart(${p.id}, '${p.name}', ${p.price}, ${p.stock_qty})" style="cursor:pointer; border:1px solid #ccc; padding:5px;">
              ${p.name} — Rs.${p.price} (Stock: ${p.stock_qty})
            </div>
          `).join('');
        });
    });

    function addToCart(id, name, price, stock) {
      const existing = cart.find(item => item.product_id === id);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push({ product_id: id, name, unit_price: price, quantity: 1, stock });
      }
      document.getElementById('search').value = '';
      document.getElementById('searchResults').innerHTML = '';
      renderCart();
    }

    function renderCart() {
      document.getElementById('cartBody').innerHTML = cart.map((item, i) => `
        <tr>
          <td>${item.name}</td>
          <td><input type="number" value="${item.quantity}" min="1" onchange="updateQty(${i}, this.value)" style="width:50px;"></td>
          <td>Rs.${item.unit_price}</td>
          <td>Rs.${(item.unit_price * item.quantity).toFixed(2)}</td>
          <td><button onclick="removeItem(${i})">X</button></td>
        </tr>
      `).join('');

      const total = cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);
      document.getElementById('cartTotal').textContent = total.toFixed(2);
    }

    function updateQty(index, qty) {
      cart[index].quantity = parseInt(qty);
      renderCart();
    }

    function removeItem(index) {
      cart.splice(index, 1);
      renderCart();
    }

    function checkout() {
      if (cart.length === 0) return alert('Cart is empty');

      const payment_method = document.getElementById('paymentMethod').value;

      fetch('api/sale_create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart, payment_method })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('checkoutResult').textContent = `Sale #${data.sale_id} completed! Total: Rs.${data.total}`;
          cart = [];
          renderCart();
        } else {
          alert('Error: ' + data.error);
        }
      });
    }

    function logout() {
      fetch('api/logout.php', { method: 'POST' })
        .then(() => window.location.href = 'login.php');
    }
  </script>
</body>
</html>