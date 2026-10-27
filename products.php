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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav>
    <strong>EGOTECHWORLD POS</strong>
    <a href="dashboard.php">Dashboard</a>
    <a href="index.php">Back to POS</a>
  </nav>

  <div class="container">
    <h1>Products</h1>

    <h2>Categories</h2>
    <div class="card">
      <input id="categoryName" placeholder="New category (e.g. Beverages)">
      <button onclick="addCategory()">Add Category</button>
      <p id="categoryError" style="color:#c0392b;"></p>
      <div id="categoryList"></div>
    </div>

    <h2>Add Product</h2>
    <div class="card">
      <input id="name" placeholder="Product name">
      <select id="category_id"><option value="">No category</option></select>      <input id="price" type="number" step="0.01" placeholder="Selling price">
      <input id="cost_price" type="number" step="0.01" placeholder="Cost price">
      <input id="stock_qty" type="number" placeholder="Initial stock quantity">
      <input id="reorder_level" type="number" placeholder="Reorder level" value="5">
      <input id="barcode" placeholder="Barcode (optional)">
      <br>
      <button onclick="addProduct()">Add Product</button>
    </div>

    <h2>Product List</h2>
    <div id="productList"></div>
  </div>

  <footer>EGOTECHWORLD POS v1.0 &nbsp;|&nbsp; © 2026 <strong>EGOTECHWORLD (PVT) LTD</strong></footer>

  <script>
    let categories = [];
    let editingId = null;

    function categoryOptions(selectedId) {
      return '<option value="">No category</option>' + categories.map(c =>
        `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.name}</option>`
      ).join('');
    }

    function loadCategories() {
      return fetch('api/categories_get.php')
        .then(res => res.json())
        .then(data => {
          categories = data;
          document.getElementById('category_id').innerHTML = categoryOptions('');
          document.getElementById('categoryList').innerHTML = data.length
            ? data.map(c => `
                <span class="cat-chip">${c.name} (${c.product_count})
                  <button onclick="deleteCategory(${c.id})">x</button>
                </span>`).join('')
            : '<p>No categories yet.</p>';
        });
    }

    function addCategory() {
      const name = document.getElementById('categoryName').value;
      fetch('api/categories_save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          document.getElementById('categoryError').textContent = data.error;
          return;
        }
        document.getElementById('categoryError').textContent = '';
        document.getElementById('categoryName').value = '';
        loadCategories().then(loadProducts);
      });
    }

    function deleteCategory(id) {
      if (!confirm('Delete this category?')) return;
      fetch('api/categories_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.error);
          return;
        }
        loadCategories();
      });
    }

    function loadProducts() {
      fetch('api/products_get.php')
        .then(res => res.json())
        .then(products => {
          document.getElementById('productList').innerHTML = products.map(p => {
            if (editingId === p.id) {
              return `
                <div class="product-row">
                  <div>
                    <input id="editName-${p.id}" value="${p.name}">
                    <select id="editCategory-${p.id}">${categoryOptions(p.category_id)}</select>
                    <input id="editPrice-${p.id}" type="number" step="0.01" value="${p.price}">
                    <input id="editCost-${p.id}" type="number" step="0.01" value="${p.cost_price}">
                    <input id="editStock-${p.id}" type="number" value="${p.stock_qty}">
                    <input id="editReorder-${p.id}" type="number" value="${p.reorder_level}">
                    <input id="editBarcode-${p.id}" value="${p.barcode || ''}">
                  </div>
                  <div class="actions">
                    <button onclick="saveEdit(${p.id})">Save</button>
                    <button onclick="cancelEdit()">Cancel</button>
                  </div>
                </div>
              `;
            }
            return `
              <div class="product-row">
                <div>
                  <strong>${p.name}</strong> (${p.category_name || 'Uncategorized'}) —
                  Price: Rs.${p.price} | Stock: ${p.stock_qty} | Barcode: ${p.barcode || '-'}
                </div>
                <div class="actions">
                  <button onclick="startEdit(${p.id})">Edit</button>
                  <button onclick="deleteProduct(${p.id})">Delete</button>
                </div>
              </div>
            `;
          }).join('');
        });
    }

    function startEdit(id) {
      editingId = id;
      loadProducts();
    }

    function cancelEdit() {
      editingId = null;
      loadProducts();
    }

    function saveEdit(id) {
      const payload = {
        id: id,
        name: document.getElementById(`editName-${id}`).value,
        category_id: document.getElementById(`editCategory-${id}`).value,
        price: document.getElementById(`editPrice-${id}`).value,
        cost_price: document.getElementById(`editCost-${id}`).value,
        stock_qty: document.getElementById(`editStock-${id}`).value,
        reorder_level: document.getElementById(`editReorder-${id}`).value,
        barcode: document.getElementById(`editBarcode-${id}`).value
      };

      fetch('api/products_update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(() => {
        editingId = null;
        loadCategories().then(loadProducts);
      });
    }

    function deleteProduct(id) {
      if (!confirm('Delete this product?')) return;

      fetch('api/products_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.error);
          return;
        }
        loadCategories().then(loadProducts);
      });
    }

    function addProduct() {
      const payload = {
        name: document.getElementById('name').value,
        category_id: document.getElementById('category_id').value,
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
        document.getElementById('category_id').value = '';
        document.getElementById('price').value = '';
        document.getElementById('cost_price').value = '';
        document.getElementById('stock_qty').value = '';
        document.getElementById('barcode').value = '';
        loadCategories().then(loadProducts);
      });
    }

    loadCategories().then(loadProducts);

    // Refresh the list every 5 seconds, but NOT while you are editing a row
    setInterval(() => {
      if (editingId === null) loadProducts();
    }, 5000);
  </script>
</body>
</html>