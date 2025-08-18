<?php
session_start();
include 'db.php';

$action = $_GET['action'] ?? '';
$pin = $_GET['pin'] ?? '';

// ตรวจสอบห้อง
$stmt = $pdo->prepare("SELECT r.id, gr.id as game_id, gr.status
                       FROM rooms r
                       JOIN game_rooms gr ON r.id = gr.room_id
                       WHERE r.pin_code = ?");
$stmt->execute([$pin]);
$room = $stmt->fetch();

if (!$room) {
    die(json_encode(['error' => 'Room not found']));
}

$response = [];

switch ($action) {
    case 'check_status':
        $response = ['status' => $room['status']];
        break;

    case 'start_game':
        $category_index = rand(0, 2);

        $stmt = $pdo->prepare("SELECT user_id FROM game_players WHERE game_id = ? ORDER BY RAND() LIMIT 1");
        $stmt->execute([$room['game_id']]);
        $odd_player = $stmt->fetch();

        $stmt = $pdo->prepare("UPDATE game_rooms
                               SET status = 'playing',
                                   category_index = ?,
                                   odd_player_id = ?
                               WHERE id = ?");
        $stmt->execute([$category_index, $odd_player['user_id'], $room['game_id']]);

        $stmt = $pdo->prepare("UPDATE game_players
                               SET role = CASE WHEN user_id = ? THEN 'odd' ELSE 'citizen' END
                               WHERE game_id = ?");
        $stmt->execute([$odd_player['user_id'], $room['game_id']]);

        $response = ['success' => true];
        break;

    case 'submit_tasks':
        $tasks = json_encode([
            $_POST['task1'],
            $_POST['task2'],
            $_POST['task3']
        ]);

        $stmt = $pdo->prepare("UPDATE game_players
                               SET tasks = ?, has_submitted = TRUE
                               WHERE game_id = ? AND user_id = ?");
        $stmt->execute([$tasks, $room['game_id'], $_SESSION['user_id']]);

        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(has_submitted) as submitted
                               FROM game_players
                               WHERE game_id = ?");
        $stmt->execute([$room['game_id']]);
        $count = $stmt->fetch();

        if ($count['total'] == $count['submitted']) {
            $stmt = $pdo->prepare("UPDATE game_rooms SET status = 'voting' WHERE id = ?");
            $stmt->execute([$room['game_id']]);
        }

        $response = ['success' => true];
        break;

    case 'check_submitted':
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(has_submitted) as submitted
                               FROM game_players
                               WHERE game_id = ?");
        $stmt->execute([$room['game_id']]);
        $count = $stmt->fetch();

        $response = ['all_submitted' => $count['total'] == $count['submitted']];
        break;

    case 'submit_vote':
        $stmt = $pdo->prepare("UPDATE game_players
                               SET voted_for = ?, has_voted = TRUE
                               WHERE game_id = ? AND user_id = ?");
        $stmt->execute([$_POST['voted_player'], $room['game_id'], $_SESSION['user_id']]);

        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(has_voted) as voted
                               FROM game_players
                               WHERE game_id = ? AND role = 'citizen'");
        $stmt->execute([$room['game_id']]);
        $count = $stmt->fetch();

        if ($count['total'] == $count['voted']) {
            $stmt = $pdo->prepare("UPDATE game_rooms SET status = 'finished' WHERE id = ?");
            $stmt->execute([$room['game_id']]);
        }

        $response = ['success' => true];
        break;

    case 'check_voted':
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(has_voted) as voted
                               FROM game_players
                               WHERE game_id = ? AND role = 'citizen'");
        $stmt->execute([$room['game_id']]);
        $count = $stmt->fetch();

        $response = ['all_voted' => $count['total'] == $count['voted']];
        break;

    case 'player_ready':
        $stmt = $pdo->prepare("UPDATE game_players
                               SET is_ready = TRUE
                               WHERE game_id = ? AND user_id = ?");
        $stmt->execute([$room['game_id'], $_SESSION['user_id']]);

        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(is_ready) as ready
                               FROM game_players
                               WHERE game_id = ?");
        $stmt->execute([$room['game_id']]);
        $count = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT u.username, gp.is_ready
                               FROM game_players gp
                               JOIN users u ON gp.user_id = u.id
                               WHERE gp.game_id = ?");
        $stmt->execute([$room['game_id']]);
        $players = $stmt->fetchAll();

        $response = [
            'success' => true,
            'all_ready' => $count['total'] == $count['ready'],
            'players' => $players
        ];
        break;

    case 'check_players':
        $stmt = $pdo->prepare("SELECT u.username, gp.is_ready
                               FROM game_players gp
                               JOIN users u ON gp.user_id = u.id
                               WHERE gp.game_id = ?");
        $stmt->execute([$room['game_id']]);
        $players = $stmt->fetchAll();

        $total = count($players);
        $ready = count(array_filter($players, function($p) {
            return $p['is_ready'];
        }));


        $response = [
            'players' => $players,
            'all_ready' => $total == $ready
        ];
        break;

    default:
        $response = ['error' => 'Invalid action'];
}

header('Content-Type: application/json');
echo json_encode($response);