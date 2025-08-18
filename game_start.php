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
    echo json_encode(['error' => 'เฉพาะผู้สร้างห้องเท่านั้นที่สามารถเริ่มเกมได้']);
    exit;
}

$room_id = $room['id'];

// สุ่มบทบาทผู้เล่น
$stmt = $pdo->prepare("SELECT user_id FROM room_players WHERE room_id = ?");
$stmt->execute([$room_id]);
$players = $stmt->fetchAll(PDO::FETCH_COLUMN);

// สุ่มเลือก 1 คนเป็น odd
shuffle($players);
$odd_player = $players[0];

// อัพเดทบทบาทผู้เล่น
$stmt = $pdo->prepare("UPDATE room_players SET role = 'odd' WHERE user_id = ? AND room_id = ?");
$stmt->execute([$odd_player, $room_id]);

// ที่เหลือเป็นพลเมือง
$stmt = $pdo->prepare("UPDATE room_players SET role = 'citizen' WHERE user_id != ? AND room_id = ?");
$stmt->execute([$odd_player, $room_id]);

// สุ่มหมวดหมู่
$category_index = rand(0, 4); // มี 5 หมวดหมู่

// อัพเดทสถานะห้อง
$stmt = $pdo->prepare("UPDATE rooms SET status = 'playing', category_index = ? WHERE id = ?");
$stmt->execute([$category_index, $room_id]);

echo json_encode(['success' => true]);