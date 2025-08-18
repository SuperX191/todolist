<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['create'])) {
    $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO rooms (pin_code) VALUES (?)");
    $stmt->execute([$pin]);
    $room_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO user_rooms (user_id, room_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $room_id]);
    header("Location: board.php?pin=$pin");
    exit;
}

if (isset($_POST['join'])) {
    $pin = $_POST['pin'];
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE pin_code = ?");
    $stmt->execute([$pin]);
    if ($room = $stmt->fetch()) {
        $pdo->prepare("INSERT IGNORE INTO user_rooms (user_id, room_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $room['id']]);
        header("Location: board.php?pin=$pin");
        exit;
    } else {
        $error = "ไม่พบห้องที่ระบุ";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้อง - Post-it Board</title>
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
            max-width: 800px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            animation: fadeIn 0.5s ease;
        }

        .post-it-note {
            width: 350px;
            min-height: 250px;
            padding: 25px;
            border-radius: 2px;
            box-shadow: 
                0 10px 30px rgba(0,0,0,0.2), 
                0 5px 10px rgba(0,0,0,0.1),
                inset 0 -10px 20px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
            transform: rotate(-3deg);
            transition: all 0.3s ease;
            animation: float 10s ease-in-out infinite;
        }

        .post-it-note:nth-child(even) {
            transform: rotate(2deg);
            animation-delay: 0.5s;
        }

        .post-it-note:hover {
            transform: rotate(0deg) scale(1.05);
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

        .post-it-content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .post-it-title {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: var(--dark);
            text-shadow: 1px 1px 0 rgba(255,255,255,0.5);
        }

        .post-it-form {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background: rgba(255,255,255,0.8);
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
            font-family: 'Charmonman', cursive;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.3);
            outline: none;
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            margin-top: auto;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }

        .error-message {
            color: #ff4757;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            transform: rotate(0.5deg);
            background: rgba(255,255,255,0.7);
            padding: 8px;
            border-radius: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            font-size: 18px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }

        /* Floating Background Post-its */
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
            background: var(--yellow);
            top: 10%;
            left: 10%;
            transform: rotate(-5deg);
            animation-delay: 0s;
        }

        .post-it-2 {
            background: var(--pink);
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
        .yellow{
            background: var(--yellow);
        }
        .pink{
            background: var(--pink);
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(-5deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .post-it-container {
                flex-direction: column;
                align-items: center;
            }
            
            .post-it-note {
                width: 100%;
                max-width: 350px;
                transform: rotate(-1deg) !important;
            }
            
            .post-it-note:nth-child(even) {
                transform: rotate(1deg) !important;
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

    <div class="welcome-message">
        สวัสดี, <?= htmlspecialchars($_SESSION['username'] ?? 'ผู้ใช้') ?>! ✨
    </div>

    <div class="post-it-container">
        <?php if(isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <!-- Post-it 1: สร้างห้องใหม่ -->
        <div class="post-it-note yellow">
            <div class="post-it-content">
                <h3 class="post-it-title">สร้างห้องใหม่</h3>
                <form method="POST" class="post-it-form">
                    <p style="margin-bottom: 20px; text-align: center;">เริ่มกระดานโน้ตใหม่สำหรับทีมของคุณ</p>
                    <button type="submit" name="create" class="btn btn-primary">สร้างห้อง</button>
                </form>
            </div>
        </div>

        <!-- Post-it 2: เข้าร่วมห้อง -->
        <div class="post-it-note pink">
            <div class="post-it-content">
                <h3 class="post-it-title">เข้าร่วมห้อง</h3>
                <form method="POST" class="post-it-form">
                    <div class="form-group">
                        <input type="text" name="pin" class="form-control" placeholder="กรอกรหัส PIN 6 หลัก" maxlength="6" pattern="\d{6}" required>
                    </div>
                    <button type="submit" name="join" class="btn btn-primary">เข้าร่วมห้อง</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>