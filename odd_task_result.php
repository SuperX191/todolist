<?php
session_start();
include 'db.php';

$pin = $_GET['pin'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลเกม
$stmt = $pdo->prepare("SELECT gr.id as game_id, gr.odd_player_id, gr.category_index
                       FROM game_rooms gr
                       JOIN rooms r ON gr.room_id = r.id
                       WHERE r.pin_code = ? AND gr.status = 'finished'");
$stmt->execute([$pin]);
$game = $stmt->fetch();

if (!$game) {
    die("Game results not found");
}

// ดึงข้อมูลผู้เล่น
$stmt = $pdo->prepare("SELECT u.username, gp.user_id, gp.role, gp.voted_for
                       FROM game_players gp
                       JOIN users u ON gp.user_id = u.id
                       WHERE gp.game_id = ?");
$stmt->execute([$game['game_id']]);
$players = $stmt->fetchAll();

// หาข้อมูลผู้เล่นที่เป็น Odd One Out
$odd_players = array_filter($players, function($p) {
    return $p['role'] === 'odd';
});

$odd_player = reset($odd_players);

// ตรวจสอบว่าผู้เล่นปัจจุบันโหวตถูกหรือไม่
$filtered = array_filter($players, function($p) use ($user_id) {
    return $p['user_id'] == $user_id;
});
$current_player = reset($filtered);

$did_vote_correct = $current_player['voted_for'] == $odd_player['user_id'];

// นับจำนวนโหวตที่ถูกต้อง
$correct_votes = count(array_filter($players, function($p) use ($odd_player) {
    return $p['voted_for'] == $odd_player['user_id'];
}));

$total_votes = count($players) - 1; // ลบ 1 เพราะ Odd One Out ไม่โหวต
?>

<!DOCTYPE html>
<html>
<head>
    <title>Results - PIN <?= $pin ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;700&family=Charmonman:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --light: #f8f9fa;
            --dark: #343a40;
            --yellow: #fdcb6e;
            --pink: #fd79a8;
            --blue: #74b9ff;
            --green: #00b894;
            --orange: #e17055;
            --red: #d63031;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Mali', cursive;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
        }

        .header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 0 rgba(255,255,255,0.5);
        }

        .header p {
            color: var(--dark);
            font-size: 1.2rem;
        }

        .result-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            margin-bottom: 30px;
            text-align: center;
        }

        .result-title {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .odd-player {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--pink);
            margin: 20px 0;
            padding: 15px;
            background: rgba(253, 121, 168, 0.1);
            border-radius: 10px;
            border-left: 5px solid var(--pink);
        }

        .vote-result {
            font-size: 1.3rem;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .correct {
            background: rgba(0, 184, 148, 0.1);
            color: var(--green);
            border-left: 5px solid var(--green);
        }

        .incorrect {
            background: rgba(214, 48, 49, 0.1);
            color: var(--red);
            border-left: 5px solid var(--red);
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
        }

        .stat-box {
            padding: 15px;
            border-radius: 10px;
            width: 45%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-correct {
            background: linear-gradient(45deg, rgba(0, 184, 148, 0.1), rgba(0, 184, 148, 0.2));
            border-top: 5px solid var(--green);
        }

        .stat-total {
            background: linear-gradient(45deg, rgba(108, 92, 231, 0.1), rgba(108, 92, 231, 0.2));
            border-top: 5px solid var(--primary);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--dark);
        }

        .btn-back {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4);
            text-decoration: none;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.6);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .stats {
                flex-direction: column;
                align-items: center;
            }
            
            .stat-box {
                width: 100%;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Odd Task Game</h1>
        <p>ผลลัพธ์เกม - PIN <?= $pin ?></p>
    </div>
    
    <div class="result-container">
        <?php if ($current_player['role'] === 'odd'): ?>
            <h2 class="result-title">คุณคือ Odd One Out!</h2>
            <div class="odd-player"><?= htmlspecialchars($current_player['username']) ?></div>
            <div class="vote-result">
                <p>มีผู้เล่น <?= $correct_votes ?> คนจาก <?= $total_votes ?> คน ทายถูกว่าคุณคือ Odd One Out!</p>
            </div>
        <?php else: ?>
            <h2 class="result-title">ผลการโหวต</h2>
            <div class="odd-player">Odd One Out คือ: <?= htmlspecialchars($odd_player['username']) ?></div>
            <div class="vote-result <?= $did_vote_correct ? 'correct' : 'incorrect' ?>">
                <p>คุณทาย<?= $did_vote_correct ? 'ถูกต้อง' : 'ผิด' ?>!</p>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-box stat-correct">
                <div class="stat-value"><?= $correct_votes ?></div>
                <div class="stat-label">ผู้เล่นที่ทายถูก</div>
            </div>
            <div class="stat-box stat-total">
                <div class="stat-value"><?= $total_votes ?></div>
                <div class="stat-label">ผู้เล่นทั้งหมด</div>
            </div>
        </div>
    </div>
    
    <a href="board.php?pin=<?= $pin ?>" class="btn-back">กลับไปที่ห้อง</a>
</body>
</html>