<?php
include 'nav.php';
session_start();
include 'db.php';
$pin = $_GET['pin'];
$stmt = $pdo->prepare("SELECT id FROM rooms WHERE pin_code = ?");
$stmt->execute([$pin]);
$room = $stmt->fetch();
if (!$room) { echo "Room not found"; exit; }

$room_id = $room['id'];
$_SESSION['room_id'] = $room_id;

// Get current user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แชท - Room <?= htmlspecialchars($pin) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Reenie+Beanie&display=swap" rel="stylesheet">
    <style>
        :root {
            --yellow-note: #fefabc;
            --blue-note: #c6e2ff;
            --pink-note: #ffd1dc;
            --green-note: #e2f8d1;
            --paper-bg: #f5f5f5;
            --ink-color: #333;
            --shadow-color: rgba(0,0,0,0.2);
        }
        
        * {
            margin-top:20px;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--paper-bg);
            background-image: 
                linear-gradient(90deg, transparent 79px, #abced4 79px, #abced4 81px, transparent 81px),
                linear-gradient(#eee .1em, transparent .1em);
            background-size: 100% 1.2em;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink-color);
        }
        
        .container {
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        
        /* Notebook Section */
        .notebook {
            width: 100%;
            max-width: 800px;
            position: relative;
            background: white;
            box-shadow: 0 10px 30px var(--shadow-color);
            border-radius: 5px;
            overflow: hidden;
            margin-top:100px;
        }
        
        /* Spiral binding effect */
        .notebook::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 30px;
            background: 
                linear-gradient(135deg, #888 25%, transparent 25%) -50px 0,
                linear-gradient(225deg, #888 25%, transparent 25%) -50px 0,
                linear-gradient(315deg, #888 25%, transparent 25%),
                linear-gradient(45deg, #888 25%, transparent 25%);
            background-size: 20px 20px;
            background-color: #666;
            border-right: 1px solid #555;
        }
        
        .notebook-header {
            padding: 20px 20px 20px 50px;
            background: var(--yellow-note);
            border-bottom: 1px dashed #d4b943;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notebook-title {
            font-family: 'Reenie Beanie', cursive;
            font-size: 32px;
            color: #8a6d00;
            text-shadow: 1px 1px 0px rgba(255,255,255,0.5);
        }
        
        .room-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .room-pin {
            background: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            box-shadow: 2px 2px 0 var(--shadow-color);
            transform: rotate(2deg);
            border: 1px solid #d4b943;
        }
        
        .online-status {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
        }
        
        .online-dot {
            width: 10px;
            height: 10px;
            background: #4CAF50;
            border-radius: 50%;
            margin-right: 5px;
            box-shadow: 0 0 5px #4CAF50;
        }
        
        .messages-container {
            padding: 20px 20px 20px 50px;
            height: 60vh;
            overflow-y: auto;
            background: white;
            background-image: linear-gradient(#eee .1em, transparent .1em);
            background-size: 100% 1.2em;
            position: relative;
        }
        
        /* Message as sticky notes */
        .message {
            margin-bottom: 20px;
            position: relative;
            min-height: 100px;
            width: 80%;
            padding: 15px;
            margin: 10px 10px 30px;
            box-shadow: 3px 3px 5px var(--shadow-color);
            transform: rotate(-1deg);
            transition: all 0.3s ease;

            font-size: 14px;
            line-height: 1.5;
        }
        
        .message:hover {
            transform: rotate(0deg) scale(1.05);
            box-shadow: 5px 5px 10px var(--shadow-color);
        }
        
        .message.own {
            margin-left: auto;
            transform: rotate(1deg);
            background: var(--blue-note);
            border: 1px solid #a8c9ff;
        }
        
        .message.own:hover {
            transform: rotate(2deg) scale(1.05);
        }
        
        .message.other {
            background: var(--yellow-note);
            border: 1px solid #d4b943;
        }
        
        .message.admin {
            background: var(--green-note);
            border: 1px solid #a8d18d;
            width: 90%;
            margin: 20px auto;
            text-align: center;
            transform: rotate(0deg);
        }
        
        /* Pinned message special style */
        .message.pinned {
            background: var(--pink-note);
            border: 1px solid #ffb6c1;
            width: 90%;
            margin: 30px auto;
            transform: rotate(0deg);
            border-left: 5px solid #ff6b81;
        }
        
        .message-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            border-top: 1px dashed rgba(0,0,0,0.1);
            padding-top: 5px;
        }
        
        .message-sender {
            font-weight: bold;
            color: #333;
        }
        
        /* Pin button for messages */
        .pin-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: transparent;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 12px;
        }
        
        .pin-btn:hover {
            color: #ff6b81;
        }
        
        .input-container {
            padding: 20px 20px 20px 50px;
            background: var(--yellow-note);
            border-top: 1px dashed #d4b943;
        }
        
        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border-radius: 5px;
            padding: 5px 15px;
            box-shadow: 2px 2px 5px var(--shadow-color);
            border: 1px solid #d4b943;
        }
        
        .message-input {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--ink-color);
            padding: 12px 0;
            font-size: 16px;
            outline: none;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .message-input::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .send-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: #4CAF50;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 0 var(--shadow-color);
        }
        
        .send-btn:hover {
            transform: scale(1.1);
            background: #45a049;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 4px;
        }
        
        /* Animations */
        @keyframes appear {
            from {
                opacity: 0;
                transform: translateY(20px) rotate(-5deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotate(-1deg);
            }
        }
        
        /* Day divider */
        .day-divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #666;
            font-size: 14px;
            font-style: italic;
        }
        
        .day-divider::before,
        .day-divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
        }
        
        .day-divider::before {
            left: 0;
        }
        
        .day-divider::after {
            right: 0;
        }
        
        /* Notebook corner folded effect */
        .message::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 20px 20px 0;
            border-color: transparent rgba(0,0,0,0.1) transparent transparent;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0;
            }
            
            .notebook {
                border-radius: 0;
                min-height: 100vh;
            }
            
            .message {
                width: 90%;
            }
            
            .notebook::before {
                width: 20px;
            }
            
            .messages-container,
            .notebook-header,
            .input-container {
                padding-left: 30px;
            }
        }
        
        /* New center widget - Collaborative doodle pad */
        .doodle-widget {
            background: white;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 3px 10px var(--shadow-color);
            text-align: center;
            border-left: 5px solid #6c5ce7;
            transform: rotate(0.5deg);
        }
        
        .doodle-title {
            font-family: 'Reenie Beanie', cursive;
            font-size: 24px;
            margin-bottom: 10px;
            color: #6c5ce7;
        }
        
        .doodle-canvas {
            width: 100%;
            height: 150px;
            background: #f9f9f9;
            border: 1px dashed #ccc;
            margin-bottom: 10px;
            cursor: crosshair;
        }
        
        .doodle-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .doodle-btn {
            padding: 5px 10px;
            background: #6c5ce7;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .color-picker {
            width: 30px;
            height: 30px;
            border: 2px solid white;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 5px var(--shadow-color);
        }
        exit-btn {
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            text-decoration: none;
            margin-left: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 2px 2px 0 var(--shadow-color);
        }

        .exit-btn:hover {
            color: #ff5252;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="notebook">
            <!-- In the notebook-header div -->
            <div class="notebook-header">
                <div class="notebook-title">Group Notes</div>
                <div class="room-info">
                    <div class="room-pin">PIN: <?= htmlspecialchars($pin) ?></div>
                    <div class="online-status">
                        <span class="online-dot"></span> <?= htmlspecialchars($current_user['username']) ?>
                    </div>
                    <a href="odd_task.php?pin=<?= $pin ?>" class="game-btn" style="background:#6c5ce7; color:white; padding:5px 15px; border-radius:20px; margin-left:10px; text-decoration:none;">
                        Play The Odd Task
                    </a>
                    <a href="leave_room.php?pin=<?= $pin ?>" class="exit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="messages-container" id="messages">
                <!-- Messages will be loaded here -->
            </div>
            
            <!-- Collaborative doodle widget
            <div class="doodle-widget">
                <div class="doodle-title">Collaborative Doodle Pad</div>
                <canvas class="doodle-canvas" id="doodleCanvas"></canvas>
                <div class="doodle-controls">
                    <input type="color" class="color-picker" id="doodleColor" value="#000000">
                    <button class="doodle-btn" id="clearDoodle">Clear</button>
                    <button class="doodle-btn" id="saveDoodle">Save</button>
                </div>
            </div> -->
            
            <div class="input-container">
                <form method="POST" action="send_chat.php?pin=<?= $pin ?>" id="chatForm">
                    <div class="input-wrapper">
                        <input type="text" name="message" class="message-input" placeholder="Write your note..." id="messageInput" required>
                        <button type="submit" class="send-btn">✓</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load chat messages
        function loadMessages() {
            fetch('chat_display.php?pin=<?= $pin ?>')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('messages').innerHTML = html;
                    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                });
        }
        
        // Send message
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (message) {
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(() => {
                    messageInput.value = '';
                    loadMessages();
                });
            }
        });
        
        // Load messages initially and refresh every 3 seconds
        loadMessages();
        setInterval(loadMessages, 3000);
        
        // Doodle pad functionality
        const canvas = document.getElementById('doodleCanvas');
        const ctx = canvas.getContext('2d');
        const colorPicker = document.getElementById('doodleColor');
        const clearBtn = document.getElementById('clearDoodle');
        const saveBtn = document.getElementById('saveDoodle');
        
        // Set canvas size
        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        
        window.addEventListener('load', resizeCanvas);
        window.addEventListener('resize', resizeCanvas);
        
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        
        // Drawing functions
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('touchstart', startDrawing);
        
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('touchmove', draw);
        
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        
        function startDrawing(e) {
            isDrawing = true;
            [lastX, lastY] = getPosition(e);
        }
        
        function draw(e) {
            if (!isDrawing) return;
            
            ctx.strokeStyle = colorPicker.value;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.lineWidth = 3;
            
            const [x, y] = getPosition(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();
            
            [lastX, lastY] = [x, y];
        }
        
        function stopDrawing() {
            isDrawing = false;
        }
        
        function getPosition(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;
            
            if (e.type.includes('touch')) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            
            return [x, y];
        }
        
        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
        
        saveBtn.addEventListener('click', () => {
            const dataUrl = canvas.toDataURL();
            // Here you would send the doodle to the server to share with others
            alert('Doodle saved! (This would be shared with the group in a real implementation)');
        });
    </script>
</body>
</html>