<?php
session_start();
include 'db.php';

$pin = $_GET['pin'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลเกมและผู้เล่น
$stmt = $pdo->prepare("SELECT gr.id as game_id, gr.category_index, gp.role
                       FROM game_rooms gr
                       JOIN game_players gp ON gr.id = gp.game_id
                       JOIN rooms r ON gr.room_id = r.id
                       WHERE r.pin_code = ? AND gp.user_id = ? AND gr.status = 'playing'");
$stmt->execute([$pin, $user_id]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found or not started");
}

$categories = [
    ['citizen' => "งานบ้าน", 'odd' => "งานออฟฟิศ"],
    ['citizen' => "การบ้าน", 'odd' => "กิจกรรมพิเศษ"],
    ['citizen' => "ของกิน", 'odd' => "เครื่องใช้"]
];

$category = $game['role'] === 'odd' 
    ? $categories[$game['category_index']]['odd']
    : $categories[$game['category_index']]['citizen'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Play Game - PIN <?= $pin ?></title>
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

        .post-it-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            width: 100%;
            max-width: 1200px;
            margin-bottom: 30px;
        }

        .post-it {
            width: 300px;
            height: 300px;
            padding: 25px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .post-it:hover {
            transform: scale(1.05) rotate(-1deg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .post-it-1 {
            background: linear-gradient(45deg, var(--yellow), #ffeaa7);
            transform: rotate(-5deg);
        }

        .post-it-2 {
            background: linear-gradient(45deg, var(--pink), #ffb8d1);
            transform: rotate(2deg);
        }

        .post-it-3 {
            background: linear-gradient(45deg, var(--blue), #a5d8ff);
            transform: rotate(-3deg);
        }

        .post-it:after {
            content: '';
            position: absolute;
            bottom: -15px;
            right: 10px;
            width: 95%;
            height: 30px;
            background: rgba(0,0,0,0.1);
            transform: skewX(5deg) rotate(3deg);
            filter: blur(7px);
            z-index: -1;
        }

        .post-it-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--dark);
            text-align: center;
            border-bottom: 2px dashed rgba(0,0,0,0.1);
            padding-bottom: 10px;
        }

        .post-it-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .category-display {
            background: rgba(255,255,255,0.7);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }

        .category-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
            font-family: 'Charmonman', cursive;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            background: rgba(255,255,255,0.9);
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
            font-family: 'Charmonman', cursive;
        }

        .form-control:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.3);
            outline: none;
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
            align-self: center;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.6);
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
            .post-it-grid {
                flex-direction: column;
                align-items: center;
            }
            
            .post-it {
                width: 100%;
                max-width: 300px;
                transform: rotate(0deg) !important;
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
        <p>เขียนคำตอบของคุณ - PIN <?= $pin ?></p>
    </div>
    
    <div class="post-it-grid">
        <div class="post-it post-it-1">
            <div class="post-it-title">งานที่ 1</div>
            <div class="post-it-content">
                <div class="category-display">
                    <div class="category-name"><?= $category ?></div>
                </div>
                <form id="taskForm">
                    <div class="form-group">
                        <input type="text" name="task1" id="task1" class="form-control" placeholder="เขียนงานแรกของคุณ" required>
                    </div>
            </div>
        </div>
        
        <div class="post-it post-it-2">
            <div class="post-it-title">งานที่ 2</div>
            <div class="post-it-content">
                <div class="form-group">
                    <input type="text" name="task2" id="task2" class="form-control" placeholder="เขียนงานที่สองของคุณ" required>
                </div>
            </div>
        </div>
        
        <div class="post-it post-it-3">
            <div class="post-it-title">งานที่ 3</div>
            <div class="post-it-content">
                <div class="form-group">
                    <input type="text" name="task3" id="task3" class="form-control" placeholder="เขียนงานที่สามของคุณ" required>
                </div>
            </div>
        </div>
    </div>
    
    <button type="submit" form="taskForm" class="btn-submit">ส่งคำตอบ</button>
    <div id="statusMessage" class="status-message"></div>
    </form>

    <script>
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('odd_task_api.php?action=submit_tasks&pin=<?= $pin ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('statusMessage');
                if (data.success) {
                    statusDiv.textContent = "ส่งคำตอบสำเร็จ! รอผู้เล่นคนอื่น...";
                    statusDiv.style.display = 'block';
                    
                    // ตรวจสอบว่าทุกคนส่งคำตอบแล้ว
                    checkAllSubmitted();
                } else {
                    statusDiv.textContent = data.message || "เกิดข้อผิดพลาดในการส่งคำตอบ";
                    statusDiv.style.display = 'block';
                }
            });
        });

        function checkAllSubmitted() {
            fetch('odd_task_api.php?action=check_submitted&pin=<?= $pin ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.all_submitted) {
                        window.location.href = 'odd_task_vote.php?pin=<?= $pin ?>';
                    } else {
                        setTimeout(checkAllSubmitted, 3000);
                    }
                });
        }
    </script>
</body>
</html>