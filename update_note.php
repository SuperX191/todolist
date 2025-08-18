<?php
include 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) exit;

$updates = [];
$params = [];

if (isset($data['pos_x']) && isset($data['pos_y'])) {
    $updates[] = "pos_x = ?";
    $updates[] = "pos_y = ?";
    $params[] = $data['pos_x'];
    $params[] = $data['pos_y'];
}

if (isset($data['content'])) {
    $updates[] = "content = ?";
    $params[] = $data['content'];
}

$params[] = $data['id'];

if ($updates) {
    $sql = "UPDATE notes SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'no_changes']);
}
?>