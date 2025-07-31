<?php require_once 'config.php'; require '_auth.php'; ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Task Tracker</title>
<link rel="stylesheet" href="styles.css"></head>
<body>
<header>
  <h2>Task Tracker</h2>
  <div>
    <small class="muted">Hello, <?=htmlspecialchars($_SESSION['username'])?></small>
    <a href="add_task.php">+ New Task</a>
    <a href="logout.php">Logout</a>
  </div>
</header>
<div class="container">
  <div class="card">
    <form class="filters" method="get">
      <input class="input" style="max-width:220px" name="q" placeholder="Search subject/task..." value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
      <select name="progress" class="input" style="max-width:200px">
        <option value="">All progress</option>
        <?php
          $opts = ['Not started','In progress','Under review','Completed'];
          $sel = $_GET['progress'] ?? '';
          foreach ($opts as $o) {
            $s = $sel === $o ? 'selected' : '';
            echo "<option $s>".htmlspecialchars($o)."</option>";
          }
        ?>
      </select>
      <button class="btn" type="submit">Apply</button>
      <a class="btn" href="dashboard.php">Reset</a>
    </form>

    <?php
      $uid = $_SESSION['user_id'];
      $q = '%' . ($mysqli->real_escape_string($_GET['q'] ?? '')) . '%';
      $prog = $_GET['progress'] ?? '';

      if ($prog) {
        $stmt = $mysqli->prepare("SELECT id,subject,task,progress,assigned,notes,deadline FROM tasks WHERE user_id=? AND (subject LIKE ? OR task LIKE ?) AND progress=? ORDER BY COALESCE(deadline,'9999-12-31') ASC, created_at DESC");
        $stmt->bind_param('isss', $uid, $q, $q, $prog);
      } else {
        $stmt = $mysqli->prepare("SELECT id,subject,task,progress,assigned,notes,deadline FROM tasks WHERE user_id=? AND (subject LIKE ? OR task LIKE ?) ORDER BY COALESCE(deadline,'9999-12-31') ASC, created_at DESC");
        $stmt->bind_param('iss', $uid, $q, $q);
      }
      $stmt->execute();
      $res = $stmt->get_result();

      function badge($p) {
        $cls = 'muted';
        if ($p==='Completed') $cls='ok';
        elseif ($p==='In progress') $cls='info';
        elseif ($p==='Under review') $cls='warn';
        return "<span class='badge $cls'>".htmlspecialchars($p)."</span>";
      }
    ?>

    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Subject</th>
            <th>Task</th>
            <th>Progress</th>
            <th>Assigned</th>
            <th>Notes</th>
            <th>Deadline</th>
            <th style="width:130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?=htmlspecialchars($row['subject'])?></td>
              <td><?=htmlspecialchars($row['task'])?></td>
              <td><?=badge($row['progress'])?></td>
              <td><?=htmlspecialchars($row['assigned'] ?? '')?></td>
              <td><small><?=nl2br(htmlspecialchars($row['notes'] ?? ''))?></small></td>
              <td><?=htmlspecialchars($row['deadline'] ?? '')?></td>
              <td>
                <a class="btn" href="edit_task.php?id=<?=$row['id']?>">Edit</a>
                <a class="btn danger" onclick="return confirm('Delete this task?')" href="delete_task.php?id=<?=$row['id']?>">Delete</a>
              </td>
            </tr>
          <?php endwhile; $stmt->close(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body></html>
