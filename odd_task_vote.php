<?php
session_start();
include 'db.php';

$pin = $_GET['pin'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลเกมและผู้เล่น
$stmt = $pdo->prepare("SELECT gr.id as game_id
                       FROM game_rooms gr
                       JOIN rooms r ON gr.room_id = r.id
                       WHERE r.pin_code = ? AND gr.status = 'voting'");
$stmt->execute([$pin]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found or not in voting stage");
}

// ดึงข้อมูลผู้เล่นและ tasks
$stmt = $pdo->prepare("SELECT u.username, gp.tasks, gp.user_id
                       FROM game_players gp
                       JOIN users u ON gp.user_id = u.id
                       WHERE gp.game_id = ?");
$stmt->execute([$game['game_id']]);
$players = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vote - PIN <?= $pin ?></title>
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

        .instructions {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        .player-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            width: 100%;
            max-width: 1200px;
            margin-bottom: 30px;
        }

        .player-card {
            width: 300px;
            min-height: 300px;
            padding: 25px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .player-card:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .player-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary), 0 15px 40px rgba(0,0,0,0.3);
        }

        .player-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--primary);
            text-align: center;
            border-bottom: 2px dashed rgba(0,0,0,0.1);
            padding-bottom: 10px;
        }

        .player-tasks {
            flex-grow: 1;
        }

        .player-tasks ul {
            list-style-type: none;
        }

        .player-tasks li {
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            font-family: 'Charmonman', cursive;
            font-size: 1.1rem;
        }

        .player-tasks li:last-child {
            border-bottom: none;
        }

        .btn-submit {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4);
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.6);
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .status-message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            display: none;
            background: rgba(0, 184, 148, 0.2);
            color: var(--green);
            border-left: 4px solid var(--green);
        }

        @media (max-width: 768px) {
            .player-grid {
                flex-direction: column;
                align-items: center;
            }
            
            .player-card {
                width: 100%;
                max-width: 300px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Odd Task Game</h1>
        <p>โหวต Odd One Out - PIN <?= $pin ?></p>
    </div>
    
    <div class="instructions">
        <p>ดูงานทั้งหมดของผู้เล่นและเลือกคนที่คุณคิดว่าเป็น "Odd One Out" (คนที่มีหมวดหมู่ต่างจากคนอื่น)</p>
    </div>
    
    <div class="player-grid">
        <?php foreach ($players as $player): ?>
            <?php if ($player['user_id'] != $user_id): ?>
                <div class="player-card" data-user-id="<?= $player['user_id'] ?>">
                    <div class="player-name"><?= htmlspecialchars($player['username']) ?></div>
                    <div class="player-tasks">
                        <ul>
                            <?php foreach (json_decode($player['tasks']) as $task): ?>
                                <li><?= htmlspecialchars($task) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <form id="voteForm">
        <input type="hidden" name="voted_player" id="voted_player" required>
        <button type="submit" id="submitBtn" class="btn-submit" disabled>ส่งโหวต</button>
    </form>
    
    <div id="statusMessage" class="status-message"></div>

    <script>
        let selectedCard = null;
        
        // เลือกผู้เล่นโดยการคลิกที่การ์ด
        document.querySelectorAll('.player-card').forEach(card => {
            card.addEventListener('click', function() {
                // ยกเลิกการเลือกการ์ดเดิม
                if (selectedCard) {
                    selectedCard.classList.remove('selected');
                }
                
                // เลือกการ์ดใหม่
                this.classList.add('selected');
                selectedCard = this;
                
                // ตั้งค่าค่าที่เลือกในฟอร์ม
                document.getElementById('voted_player').value = this.dataset.userId;
                document.getElementById('submitBtn').disabled = false;
            });
        });
        
        document.getElementById('voteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('odd_task_api.php?action=submit_vote&pin=<?= $pin ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('statusMessage');
                if (data.success) {
                    statusDiv.textContent = "โหวตสำเร็จ! รอผู้เล่นคนอื่น...";
                    statusDiv.style.display = 'block';
                    
                    // ตรวจสอบว่าทุกคนโหวตแล้ว
                    checkAllVoted();
                } else {
                    statusDiv.textContent = data.message || "เกิดข้อผิดพลาดในการโหวต";
                    statusDiv.style.display = 'block';
                }
            });
        });

        function checkAllVoted() {
            fetch('odd_task_api.php?action=check_voted&pin=<?= $pin ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.all_voted) {
                        window.location.href = 'odd_task_result.php?pin=<?= $pin ?>';
                    } else {
                        setTimeout(checkAllVoted, 3000);
                    }
                });
        }
    </script>
</body>
</html>