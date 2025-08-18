<?php
include 'db.php';
session_start();

$pin = $_GET['pin'];

// ตรวจสอบห้อง
$stmt = $pdo->prepare("SELECT id, created_by FROM rooms WHERE pin_code = ?");
$stmt->execute([$pin]);
$room = $stmt->fetch();

// ตรวจสอบว่าผู้ใช้เป็นผู้สร้างห้องหรือไม่
if ($_SESSION['user_id'] !== $room['created_by']) {
    echo json_encode(['error' => 'เฉพาะผู้สร้างห้องเท่านั้นที่สามารถเริ่มเกมใหม่ได้']);
    exit;
}

$room_id = $room['id'];

// รีเซ็ตสถานะผู้เล่น
$stmt = $pdo->prepare("UPDATE room_players SET 
    role = NULL, 
    tasks = NULL, 
    voted_for = NULL, 
    is_alive = 1 
    WHERE room_id = ?");
$stmt->execute([$room_id]);

// รีเซ็ตสถานะห้อง
$stmt = $pdo->prepare("UPDATE rooms SET status = 'waiting' WHERE id = ?");
$stmt->execute([$room_id]);

echo json_encode(['success' => true]);