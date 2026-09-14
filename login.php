<!DOCTYPE html>
<html>
<head>
  <title>Login - EGOTECHWORLD POS</title>
</head>
<body>
  <h1>POS System Login</h1>
  <form id="loginForm">
    <input type="text" id="username" placeholder="Username"><br><br>
    <input type="password" id="password" placeholder="Password"><br><br>
    <button type="submit">Login</button>
  </form>
  <p id="error" style="color:red;"></p>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault(); // stop the form from reloading the page
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;

      fetch('api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.href = 'index.php';
        } else {
          document.getElementById('error').textContent = data.error;
        }
      });
    });
  </script>
</body>
</html>