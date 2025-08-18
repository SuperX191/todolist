<?php
session_start();
include 'db.php';

// ตรวจสอบห้อง
$pin = $_GET['pin'];
$stmt = $pdo->prepare("SELECT id FROM rooms WHERE pin_code = ?");
$stmt->execute([$pin]);
$room = $stmt->fetch();

if (!$room) {
    die("Room not found");
}

$room_id = $room['id'];

// ตรวจสอบว่ามีเกมที่กำลังเล่นอยู่หรือไม่
$stmt = $pdo->prepare("SELECT id FROM game_rooms WHERE room_id = ? AND status != 'finished'");
$stmt->execute([$room_id]);
$existing_game = $stmt->fetch();

if ($existing_game) {
    // เข้าร่วมเกมที่มีอยู่
    header("Location: odd_task_lobby.php?pin=".$pin);
    exit;
}

// สร้างเกมใหม่
$stmt = $pdo->prepare("INSERT INTO game_rooms (room_id, pin_code) VALUES (?, ?)");
$stmt->execute([$room_id, $pin]);
$game_id = $pdo->lastInsertId();

// เพิ่มผู้เล่นคนแรก
$stmt = $pdo->prepare("INSERT INTO game_players (game_id, user_id) VALUES (?, ?)");
$stmt->execute([$game_id, $_SESSION['user_id']]);

header("Location: odd_task_lobby.php?pin=".$pin);
?>