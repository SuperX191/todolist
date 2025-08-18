<?php
session_start();
include 'db.php';

$pin = $_GET['pin'];
$user_id = $_SESSION['user_id'];

// ตรวจสอบห้องและเกม
$stmt = $pdo->prepare("SELECT r.id, gr.id as game_id, gr.status 
                       FROM rooms r 
                       JOIN game_rooms gr ON r.id = gr.room_id
                       WHERE r.pin_code = ? AND gr.status != 'finished'");
$stmt->execute([$pin]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found");
}

// ตรวจสอบว่าผู้เล่นอยู่ในเกมหรือไม่
$stmt = $pdo->prepare("SELECT id FROM game_players WHERE game_id = ? AND user_id = ?");
$stmt->execute([$game['game_id'], $user_id]);
$player = $stmt->fetch();

if (!$player) {
    // เพิ่มผู้เล่นใหม่
    $stmt = $pdo->prepare("INSERT INTO game_players (game_id, user_id) VALUES (?, ?)");
    $stmt->execute([$game['game_id'], $user_id]);
}

// ดึงข้อมูลผู้เล่นทั้งหมด
$stmt = $pdo->prepare("SELECT u.username, gp.user_id 
                       FROM game_players gp
                       JOIN users u ON gp.user_id = u.id
                       WHERE gp.game_id = ?");
$stmt->execute([$game['game_id']]);
$players = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game Lobby - PIN <?= $pin ?></title>
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Mali', cursive;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
        }

        /* Post-it Note Style */
        .post-it-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            transform: rotate(-2deg);
            animation: float 8s ease-in-out infinite;
        }

        .post-it-note {
            background: linear-gradient(45deg, #fdcb6e, #ffeaa7);
            padding: 30px;
            border-radius: 3px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2), 
                        0 5px 10px rgba(0,0,0,0.1),
                        inset 0 -10px 20px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }

        .post-it-note:after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: 5px;
            width: 98%;
            height: 20px;
            background: rgba(0,0,0,0.1);
            transform: skewX(5deg) rotate(3deg);
            filter: blur(5px);
            z-index: -1;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
            transform: rotate(1deg);
        }

        .logo h1 {
            color: var(--dark);
            font-size: 28px;
            margin-bottom: 5px;
            text-shadow: 1px 1px 0 rgba(255,255,255,0.5);
        }

        .logo p {
            color: #666;
            font-size: 14px;
            transform: rotate(-1deg);
        }

        .player-list {
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .player-item {
            background: rgba(255,255,255,0.7);
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transform: rotate(0.5deg);
        }

        .player-item.ready {
            background: rgba(0, 184, 148, 0.2);
            border-left: 4px solid var(--green);
        }

        .ready-status {
            font-size: 12px;
            color: var(--green);
            font-weight: bold;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            transform: rotate(-1deg);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: rotate(-1deg) translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }

        .btn-primary:disabled {
            background: #ccc;
            transform: rotate(-1deg);
            cursor: not-allowed;
        }

        .status-message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            display: none;
        }

        .status-success {
            background: rgba(0, 184, 148, 0.2);
            color: var(--green);
            border-left: 4px solid var(--green);
        }

        .status-info {
            background: rgba(116, 185, 255, 0.2);
            color: var(--blue);
            border-left: 4px solid var(--blue);
        }

        .status-error {
            background: rgba(253, 121, 168, 0.2);
            color: var(--pink);
            border-left: 4px solid var(--pink);
        }

        /* Floating Post-its Background */
        .bg-post-it {
            position: absolute;
            width: 150px;
            height: 150px;
            padding: 15px;
            border-radius: 3px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            opacity: 0.7;
            z-index: -1;
            animation: float 6s ease-in-out infinite;
        }

        .post-it-1 {
            background: var(--pink);
            top: 10%;
            left: 10%;
            transform: rotate(-5deg);
            animation-delay: 0s;
        }

        .post-it-2 {
            background: var(--blue);
            bottom: 15%;
            right: 10%;
            transform: rotate(5deg);
            animation-delay: 1s;
        }

        .post-it-3 {
            background: var(--green);
            top: 60%;
            left: 5%;
            transform: rotate(-3deg);
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(-5deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @media (max-width: 480px) {
            .post-it-container {
                transform: rotate(-1deg);
            }
            
            .bg-post-it {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Post-its -->
    <div class="bg-post-it post-it-1"></div>
    <div class="bg-post-it post-it-2"></div>
    <div class="bg-post-it post-it-3"></div>

    <!-- Main Lobby Post-it -->
    <div class="post-it-container">
        <div class="post-it-note">
            <div class="logo">
                <h1>Odd Task Game</h1>
                <p>ห้องรอเล่นเกม - PIN <?= $pin ?></p>
            </div>
            
            <div class="player-list">
                <h3 style="transform: rotate(0.5deg); margin-bottom: 10px;">ผู้เล่นในห้อง:</h3>
                <ul id="playerList">
                    <?php foreach ($players as $player): ?>
                        <li class="player-item"><?= htmlspecialchars($player['username']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <button id="readyBtn" class="btn btn-primary">ฉันพร้อมแล้ว!</button>
            <div id="statusMessage" class="status-message"></div>
        </div>
    </div>

    <script>
        let isReady = false;

        // ฟังก์ชันแสดงข้อความสถานะ
        function showStatusMessage(message, type = 'info') {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.textContent = message;
            statusDiv.className = `status-message status-${type}`;
            statusDiv.style.display = 'block';
            
            // ซ่อนข้อความหลังจาก 3 วินาที
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }

        // ฟังก์ชันส่งสถานะ "พร้อม"
        function sendReadyStatus() {
            showStatusMessage('กำลังส่งสถานะ...', 'info');
            
            fetch('odd_task_api.php?action=player_ready&pin=<?= $pin ?>', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    isReady = true;
                    document.getElementById('readyBtn').textContent = 'ส่งแล้ว ✓';
                    document.getElementById('readyBtn').disabled = true;
                    
                    showStatusMessage('ส่งสถานะสำเร็จ! กำลังรอผู้เล่นคนอื่น...', 'success');
                    
                    // ถ้าทุกคนพร้อมแล้ว ให้เริ่มเกมทันที
                    if (data.all_ready) {
                        showStatusMessage('ทุกคนพร้อมแล้ว! เริ่มเกมทันที!', 'success');
                        setTimeout(() => {
                            startGame();
                        }, 1500);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // ฟังก์ชันตรวจสอบสถานะผู้เล่น
        function checkPlayersStatus() {
            fetch('odd_task_api.php?action=check_players&pin=<?= $pin ?>')
                .then(response => response.json())
                .then(data => {
                    // อัปเดตรายชื่อผู้เล่น
                    const playerList = document.getElementById('playerList');
                    playerList.innerHTML = '';
                    
                    data.players.forEach(player => {
                        const li = document.createElement('li');
                        li.className = 'player-item';
                        if (player.is_ready) {
                            li.classList.add('ready');
                        }
                        
                        li.innerHTML = `
                            <span>${player.username}</span>
                            <span class="ready-status">${player.is_ready ? '✓ พร้อม' : 'รออยู่...'}</span>
                        `;
                        playerList.appendChild(li);
                    });
                    
                    // ถ้าทุกคนพร้อมแล้ว ให้เริ่มเกมทันที
                    if (data.all_ready) {
                        showStatusMessage('ทุกคนพร้อมแล้ว! เริ่มเกมทันที!', 'success');
                        setTimeout(() => {
                            startGame();
                        }, 1500);
                    }
                });
        }

        // ฟังก์ชันเริ่มเกม
        function startGame() {
            if (window.gameStarting) return;
            window.gameStarting = true;
            
            fetch('odd_task_api.php?action=start_game&pin=<?= $pin ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'odd_task_play.php?pin=<?= $pin ?>';
                    }
                })
                .catch(error => {
                    console.error('Start game error:', error);
                    window.gameStarting = false;
                });
        }

        // ฟังก์ชันตรวจสอบสถานะเกม
        function checkGameStatus() {
            fetch('odd_task_api.php?action=check_status&pin=<?= $pin ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'playing') {
                        window.location.href = 'odd_task_play.php?pin=<?= $pin ?>';
                    }
                });
        }

        // Event Listeners
        document.getElementById('readyBtn').addEventListener('click', function() {
            if (!isReady) {
                sendReadyStatus();
            }
        });

        // ตรวจสอบสถานะผู้เล่นทุกๆ 2 วินาที
        setInterval(checkPlayersStatus, 2000);
        
        // ตรวจสอบสถานะเกมทุกๆ 3 วินาที
        setInterval(checkGameStatus, 3000);
        
        // เรียกครั้งแรกทันที
        checkPlayersStatus();
    </script>
</body>
</html>