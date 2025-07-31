<?php require_once 'config.php'; require '_auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $subject = trim($_POST['subject'] ?? '');
    $task    = trim($_POST['task'] ?? '');
    $progress= $_POST['progress'] ?? 'Not started';
    $assigned= trim($_POST['assigned'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    $deadline= $_POST['deadline'] ?: null;

    if ($subject==='') $errors[]='Subject is required.';
    if ($task==='')    $errors[]='Task is required.';

    if (!$errors) {
        $stmt = $mysqli->prepare("INSERT INTO tasks(user_id,subject,task,progress,assigned,notes,deadline) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('issssss', $_SESSION['user_id'], $subject, $task, $progress, $assigned, $notes, $deadline);
        if ($stmt->execute()) { header('Location: dashboard.php'); exit; }
        else $errors[] = 'Failed to save task.';
        $stmt->close();
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Task</title><link rel="stylesheet" href="styles.css"></head>
<body>
<header><h2>Task Tracker</h2><div><a href="dashboard.php">Back</a><a href="logout.php">Logout</a></div></header>
<div class="container"><div class="card">
  <h3>New Task</h3>
  <?php foreach($errors as $e): ?><div class="flash"><?=htmlspecialchars($e)?></div><?php endforeach; ?>
  <form method="post">
    <div class="row cols-2">
      <label>Subject<input class="input" name="subject" required></label>
      <label>Task<input class="input" name="task" required></label>
    </div>
    <div class="row cols-3" style="margin-top:8px">
      <label>Progress
        <select class="input" name="progress">
          <?php foreach(['Not started','In progress','Under review','Completed'] as $o): ?>
            <option><?=htmlspecialchars($o)?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Assigned to<input class="input" name="assigned" placeholder="Name(s)"></label>
      <label>Deadline<input class="input" type="date" name="deadline"></label>
    </div>
    <label style="margin-top:8px">Notes
      <textarea class="input" name="notes" rows="4" placeholder="Links, resources, reminders..."></textarea>
    </label>
    <div style="margin-top:12px">
      <button class="btn primary" type="submit">Save</button>
      <a class="btn" href="dashboard.php">Cancel</a>
    </div>
  </form>
</div></div>
</body></html>
