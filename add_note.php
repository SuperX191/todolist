<?php
session_start();
include 'db.php';

if (!isset($_SESSION['room_id']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$room_id = $_SESSION['room_id'];
$user_id = $_SESSION['user_id'];
$content = $_POST['content'];
$color = $_POST['color'];
$pattern = $_POST['pattern'];

// Set random initial position
$pos_x = rand(0, 800);
$pos_y = rand(0, 400);

$stmt = $pdo->prepare("INSERT INTO notes (room_id, user_id, content, color, pattern, pos_x, pos_y) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$room_id, $user_id, $content, $color, $pattern, $pos_x, $pos_y]);

header("Location: note.php?pin=" . $_GET['pin']);
exit;
?>