<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Users - EGOTECHWORLD POS</title>
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
    <h1>User Management</h1>

    <h2>Add User</h2>
    <div class="card">
      <input id="name" placeholder="Full name">
      <input id="username" placeholder="Username">
      <input id="password" type="password" placeholder="Password">
      <select id="role">
        <option value="cashier">Cashier</option>
        <option value="admin">Admin</option>
      </select>
      <br>
      <button onclick="addUser()">Add User</button>
      <p id="error" style="color:#c0392b;"></p>
    </div>

    <h2>Users List</h2>
    <div id="usersList"></div>

  <script>
    function loadUsers() {
      fetch('api/users_get.php')
        .then(res => res.json())
        .then(users => {
          document.getElementById('usersList').innerHTML = users.map(u => `
            <div>${u.name} (${u.username}) — ${u.role}</div>
          `).join('');
        });
    }

    function addUser() {
      const payload = {
        name: document.getElementById('name').value,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value
      };

      fetch('api/users_save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('name').value = '';
          document.getElementById('username').value = '';
          document.getElementById('password').value = '';
          document.getElementById('error').textContent = '';
          loadUsers();
        } else {
          document.getElementById('error').textContent = data.error;
        }
      });
    }

    loadUsers();
  </script>
</body>
</html>