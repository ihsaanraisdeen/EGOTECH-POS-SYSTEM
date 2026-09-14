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
</head>
<body>
  <h1>User Management</h1>
  <a href="index.php">Back to POS</a>

  <h2>Add User</h2>
  <input id="name" placeholder="Full name"><br><br>
  <input id="username" placeholder="Username"><br><br>
  <input id="password" type="password" placeholder="Password"><br><br>
  <select id="role">
    <option value="cashier">Cashier</option>
    <option value="admin">Admin</option>
  </select><br><br>
  <button onclick="addUser()">Add User</button>
  <p id="error" style="color:red;"></p>

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