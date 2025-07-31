<?php require_once 'config.php'; require '_auth.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $mysqli->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();
header('Location: dashboard.php'); exit;
