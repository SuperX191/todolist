<?php
session_start();
include 'db.php';
$room_id = $_SESSION['room_id'];
$user_id = $_SESSION['user_id'];
$msg = $_POST['message'];

$pdo->prepare("INSERT INTO messages (room_id, user_id, message) VALUES (?, ?, ?)")->execute([$room_id, $user_id, $msg]);
header("Location: board.php?pin=" . $_GET['pin']);
