<?php require_once 'config.php'; require_once '_flash.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $email === '' || $pass === '') {
        $err = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } elseif ($pass !== $confirm) {
        $err = 'Passwords do not match.';
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $err = 'Username or email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO users(username,email,password_hash) VALUES (?,?,?)");
            $ins->bind_param('sss', $username, $email, $hash);
            if ($ins->execute()) {
                flash('ok', 'Registration successful. Please login.');
                header('Location: login.php'); exit;
            } else {
                $err = 'Registration failed. Try again.';
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register - Task Tracker</title>
<link rel="stylesheet" href="styles.css">
</head><body>
<header><h2>Task Tracker</h2><div><a href="login.php">Login</a></div></header>
<div class="container">
  <div class="card" style="max-width:520px;margin:auto">
    <h3>Create account</h3>
    <?php if ($err): ?><div class="flash"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="row cols-1">
        <label>Username
          <input class="input" name="username" required>
        </label>
        <label>Email
          <input class="input" type="email" name="email" required>
        </label>
        <label>Password
          <input class="input" type="password" name="password" minlength="6" required>
        </label>
        <label>Confirm Password
          <input class="input" type="password" name="confirm" minlength="6" required>
        </label>
      </div>
      <div style="margin-top:12px">
        <button class="btn primary" type="submit">Register</button>
        <a class="btn" href="login.php">Back to login</a>
      </div>
    </form>
  </div>
</div>
</body></html>
