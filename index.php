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
</head>
<body>
  <h1>Welcome, <?php echo $_SESSION['user']['name']; ?> (<?php echo $_SESSION['user']['role']; ?>)</h1>
  <button onclick="logout()">Logout</button>

  <script>
    function logout() {
      fetch('api/logout.php', { method: 'POST' })
        .then(() => window.location.href = 'login.php');
    }
  </script>
</body>
</html>