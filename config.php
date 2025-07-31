<?php
// Adjust to your XAMPP/MySQL credentials
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'task_tracker;';

$mysqli = new mysqli('127.0.0.1', 'root', '', 'task_tracker;');
if ($mysqli->connect_errno) {
    die('DB connection failed: ' . $mysqli->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
