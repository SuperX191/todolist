<?php
session_start();
include 'db.php';

$pin = $_GET['pin'] ?? '';

// Remove user from room (if you have a participants table)
// $stmt = $pdo->prepare("DELETE FROM room_participants WHERE user_id = ? AND room_id = (SELECT id FROM rooms WHERE pin_code = ?)");
// $stmt->execute([$_SESSION['user_id'], $pin]);

// Or just clear the room_id from session
unset($_SESSION['room_id']);

// Redirect to home page or rooms list
header("Location: index.php");
exit;
?>