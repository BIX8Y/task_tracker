<?php require_once 'config.php'; require '_auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $mysqli->prepare("SELECT id,subject,task,progress,assigned,notes,deadline FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$task) { header('Location: dashboard.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $subject = trim($_POST['subject'] ?? '');
    $t       = trim($_POST['task'] ?? '');
    $progress= $_POST['progress'] ?? 'Not started';
    $assigned= trim($_POST['assigned'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    $deadline= $_POST['deadline'] ?: null;

    if ($subject==='') $errors[]='Subject is required.';
    if ($t==='')       $errors[]='Task is required.';

    if (!$errors) {
        $upd = $mysqli->prepare("UPDATE tasks SET subject=?, task=?, progress=?, assigned=?, notes=?, deadline=? WHERE id=? AND user_id=?");
        $upd->bind_param('ssssssii', $subject, $t, $progress, $assigned, $notes, $deadline, $id, $_SESSION['user_id']);
        if ($upd->execute()) { header('Location: dashboard.php'); exit; }
        else $errors[]='Failed to update task.';
        $upd->close();
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Task</title><link rel="stylesheet" href="styles.css"></head>
<body>
<header><h2>Task Tracker</h2><div><a href="dashboard.php">Back</a><a href="logout.php">Logout</a></div></header>
<div class="container"><div class="card">
  <h3>Edit Task</h3>
  <?php foreach($errors as $e): ?><div class="flash"><?=htmlspecialchars($e)?></div><?php endforeach; ?>
  <form method="post">
    <div class="row cols-2">
      <label>Subject<input class="input" name="subject" value="<?=htmlspecialchars($task['subject'])?>" required></label>
      <label>Task<input class="input" name="task" value="<?=htmlspecialchars($task['task'])?>" required></label>
    </div>
    <div class="row cols-3" style="margin-top:8px">
      <label>Progress
        <select class="input" name="progress">
          <?php foreach(['Not started','In progress','Under review','Completed'] as $o): ?>
            <option <?=$task['progress']===$o?'selected':''?>><?=htmlspecialchars($o)?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Assigned to<input class="input" name="assigned" value="<?=htmlspecialchars($task['assigned'])?>"></label>
      <label>Deadline<input class="input" type="date" name="deadline" value="<?=htmlspecialchars($task['deadline'])?>"></label>
    </div>
    <label style="margin-top:8px">Notes
      <textarea class="input" name="notes" rows="4"><?=htmlspecialchars($task['notes'])?></textarea>
    </label>
    <div style="margin-top:12px">
      <button class="btn primary" type="submit">Update</button>
      <a class="btn" href="dashboard.php">Cancel</a>
    </div>
  </form>
</div></div>
</body></html>
