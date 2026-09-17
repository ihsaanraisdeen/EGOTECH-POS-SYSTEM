<!DOCTYPE html>
<html>
<head>
  <title>Login - EGOTECHWORLD POS</title>
<head>
  <title>Login - EGOTECHWORLD POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container" style="max-width:380px; margin-top:80px;">
    <h1>EGOTECHWORLD POS</h1>
    <div class="card">
      <input type="text" id="username" placeholder="Username" style="width:100%;"><br><br>
      <input type="password" id="password" placeholder="Password" style="width:100%;"><br><br>
      <form id="loginForm" onsubmit="return false;">
        <button type="submit" style="width:100%;">Login</button>
      </form>
      <p id="error" style="color:#c0392b; margin-top:10px;"></p>
    </div>
  </div>
<footer>EGOTECHWORLD POS v1.0 &nbsp;|&nbsp; © 2026 <strong>EGOTECHWORLD (PVT) LTD</strong></footer>
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