<?php require_once 'config.php'; require_once '_flash.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['user'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($username_or_email === '' || $pass === '') {
        $err = 'Enter your username/email and password.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, password_hash, username FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param('ss', $username_or_email, $username_or_email);
        $stmt->execute();
        $stmt->bind_result($uid, $hash, $uname);
        if ($stmt->fetch() && password_verify($pass, $hash)) {
            $_SESSION['user_id'] = $uid;
            $_SESSION['username'] = $uname;
            header('Location: dashboard.php'); exit;
        } else {
            $err = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
$ok = flash('ok');
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Task Tracker</title>
<link rel="stylesheet" href="styles.css"></head>
<body>
<header><h2>Task Tracker</h2><div><a href="register.php">Register</a></div></header>
<div class="container">
  <div class="card" style="max-width:420px;margin:auto">
    <h3>Welcome back</h3>
    <?php if ($ok): ?><div class="flash"><?=htmlspecialchars($ok)?></div><?php endif; ?>
    <?php if ($err): ?><div class="flash"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <label>Username or Email
        <input class="input" name="user" required>
      </label>
      <label style="margin-top:8px">Password
        <input class="input" type="password" name="password" required>
      </label>
      <div style="margin-top:12px">
        <button class="btn primary" type="submit">Login</button>
      </div>
    </form>
  </div>
</div>
</body></html>
