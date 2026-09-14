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
  <title>Products - EGOTECHWORLD POS</title>
</head>
<body>
  <h1>Products</h1>
  <a href="index.php">Back to POS</a>

  <h2>Add Product</h2>
  <input id="name" placeholder="Product name"><br><br>
  <input id="price" type="number" step="0.01" placeholder="Selling price"><br><br>
  <input id="cost_price" type="number" step="0.01" placeholder="Cost price"><br><br>
  <input id="stock_qty" type="number" placeholder="Initial stock quantity"><br><br>
  <input id="reorder_level" type="number" placeholder="Reorder level" value="5"><br><br>
  <input id="barcode" placeholder="Barcode (optional)"><br><br>
  <button onclick="addProduct()">Add Product</button>

  <h2>Product List</h2>
  <div id="productList"></div>

  <script>
    function loadProducts() {
      fetch('api/products_get.php')
        .then(res => res.json())
        .then(products => {
          document.getElementById('productList').innerHTML = products.map(p => `
            <div>
              <strong>${p.name}</strong> —
              Price: Rs.${p.price} |
              Stock: ${p.stock_qty} |
              Barcode: ${p.barcode || '-'}
            </div>
          `).join('');
        });
    }

    function addProduct() {
      const payload = {
        name: document.getElementById('name').value,
        price: document.getElementById('price').value,
        cost_price: document.getElementById('cost_price').value,
        stock_qty: document.getElementById('stock_qty').value,
        reorder_level: document.getElementById('reorder_level').value,
        barcode: document.getElementById('barcode').value
      };

      fetch('api/products_save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(() => {
        document.getElementById('name').value = '';
        document.getElementById('price').value = '';
        document.getElementById('cost_price').value = '';
        document.getElementById('stock_qty').value = '';
        document.getElementById('barcode').value = '';
        loadProducts();
      });
    }

loadProducts();
setInterval(loadProducts, 5000);  </script>
</body>
</html>