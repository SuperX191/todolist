<?php
session_start();
require 'conn.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// API Endpoint สำหรับการจัดการ Post-it
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $response = ['status' => 'error', 'message' => 'Invalid action'];
    
    try {
        switch ($action) {
            case 'load':
                $stmt = $conn->prepare("SELECT id, content, color, pattern, pos_x, pos_y, z_index FROM postits WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $postits = $result->fetch_all(MYSQLI_ASSOC);
                $response = ['status' => 'success', 'postits' => $postits];
                break;
                
            case 'save':
                $content = $_POST['content'] ?? '';
                $color = $_POST['color'] ?? 'yellow';
                $pattern = $_POST['pattern'] ?? '';
                $pos_x = (int)($_POST['pos_x'] ?? 100);
                $pos_y = (int)($_POST['pos_y'] ?? 100);
                $z_index = (int)($_POST['z_index'] ?? 1);
                
                $stmt = $conn->prepare("INSERT INTO postits (user_id, content, color, pattern, pos_x, pos_y, z_index) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssiii", $user_id, $content, $color, $pattern, $pos_x, $pos_y, $z_index);
                $stmt->execute();
                $id = $stmt->insert_id;
                $stmt->close();
                
                $response = ['status' => 'success', 'id' => $id];
                break;
                
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $content = $_POST['content'] ?? '';
                $pos_x = (int)($_POST['pos_x'] ?? 0);
                $pos_y = (int)($_POST['pos_y'] ?? 0);
                $z_index = (int)($_POST['z_index'] ?? 1);
                
                $stmt = $conn->prepare("UPDATE postits SET content = ?, pos_x = ?, pos_y = ?, z_index = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("siiiii", $content, $pos_x, $pos_y, $z_index, $id, $user_id);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                $response = ['status' => $affected > 0 ? 'success' : 'no_change'];
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                
                $stmt = $conn->prepare("DELETE FROM postits WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $id, $user_id);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                $response = ['status' => $affected > 0 ? 'success' : 'not_found'];
                break;
        }
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }
    
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-it Todo Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
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
        
        body {
            font-family: 'Mali', cursive, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding-top: 70px;
        }

        .board {
            width: 100vw;
            height: calc(100vh - 70px);
            position: relative;
            background: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
            background-size: 50px 50px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
            font-family: 'Mali', cursive;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.6);
        }

        .toolbar {
            position: fixed;
            top: 90px;
            left: 20px;
            z-index: 1500;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-100%);
            opacity: 0;
        }

        .toolbar.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toolbar.expanded {
            width: auto;
            height: auto;
            padding: 15px;
        }

        .add-btn-floating {
            position: fixed;
            top: 100px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(238,90,82,0.4);
            z-index: 1600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-btn-floating:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 25px rgba(238,90,82,0.5);
        }

        .add-btn-floating.hidden {
            transform: translateX(-100px);
            opacity: 0;
            pointer-events: none;
        }

        .toolbar-toggle {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 50%;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.8);
        }

        .toolbar-toggle:hover {
            background: rgba(102,126,234,0.2);
        }

        .close-icon {
            width: 20px;
            height: 20px;
            position: relative;
            transition: all 0.3s ease;
        }

        .close-icon::before,
        .close-icon::after {
            content: '';
            position: absolute;
            background: #666;
            border-radius: 2px;
            width: 20px;
            height: 2px;
            top: 50%;
            left: 0;
            transform-origin: center;
        }

        .close-icon::before {
            transform: translateY(-50%) rotate(45deg);
        }

        .close-icon::after {
            transform: translateY(-50%) rotate(-45deg);
        }

        .toolbar-content {
            display: flex;
            gap: 15px;
            align-items: center;
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
            pointer-events: none;
            padding-right: 50px;
        }

        .toolbar.show .toolbar-content {
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }

        .add-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(238,90,82,0.3);
            white-space: nowrap;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238,90,82,0.4);
        }

        .color-picker {
            display: flex;
            gap: 8px;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .color-option:hover {
            transform: scale(1.1);
            border-color: rgba(0,0,0,0.3);
        }

        .color-option.selected {
            border-color: #333;
            transform: scale(1.1);
        }

        .pattern-picker {
            display: flex;
            gap: 8px;
        }

        .pattern-option {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .pattern-option:hover,
        .pattern-option.selected {
            border-color: #333;
            transform: scale(1.1);
        }

        .post-it {
            position: absolute;
            width: 200px;
            min-height: 200px;
            padding: 15px;
            border-radius: 5px;
            cursor: move;
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.1),
                0 1px 3px rgba(0,0,0,0.08);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            transform: rotate(-2deg);
            font-family: 'Mali', cursive, 'Comic Sans MS', sans-serif;
            user-select: none;
        }

        .post-it:nth-child(2n) {
            transform: rotate(1deg);
        }

        .post-it:nth-child(3n) {
            transform: rotate(-1deg);
        }

        .post-it:hover {
            transform: rotate(0deg) scale(1.05);
            box-shadow: 
                0 8px 25px rgba(0,0,0,0.15),
                0 3px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .post-it.dragging {
            transform: rotate(0deg) scale(1.05);
            z-index: 999;
            box-shadow: 
                0 15px 35px rgba(0,0,0,0.2),
                0 5px 15px rgba(0,0,0,0.1);
            transition: none;
            will-change: transform, left, top;
        }

        .post-it textarea {
            width: 100%;
            height: 100%;
            min-height: 150px;
            background: transparent;
            border: none;
            resize: none;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
        }

        .post-it textarea::placeholder {
            color: rgba(0,0,0,0.4);
        }

        .delete-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 25px;
            height: 25px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(255,71,87,0.3);
            transition: all 0.3s ease;
        }

        .post-it:hover .delete-btn {
            display: flex;
        }

        .delete-btn:hover {
            background: #ff3742;
            transform: scale(1.1);
        }

        /* สีต่างๆ ของ Post-it */
        .yellow { background: linear-gradient(135deg, #ffeaa7, #fdcb6e); }
        .pink { background: linear-gradient(135deg, #fd79a8, #e84393); }
        .blue { background: linear-gradient(135deg, #74b9ff, #0984e3); }
        .green { background: linear-gradient(135deg, #55efc4, #00b894); }
        .orange { background: linear-gradient(135deg, #fab1a0, #e17055); }
        .purple { background: linear-gradient(135deg, #a29bfe, #6c5ce7); }

        /* ลวดลายต่างๆ */
        .dots {
            background-image: radial-gradient(circle, rgba(0,0,0,0.1) 2px, transparent 2px);
            background-size: 20px 20px;
        }

        .lines {
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 18px,
                rgba(0,0,0,0.1) 18px,
                rgba(0,0,0,0.1) 20px
            );
        }

        .grid {
            background-image: 
                linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .stats {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            font-size: 14px;
            color: #666;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) rotate(-10deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotate(-2deg);
            }
        }

        .post-it.new {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <button class="add-btn-floating" id="addBtnFloating" onclick="showToolbar()">+</button>

    <div class="board" id="board">
        <div class="toolbar" id="toolbar">
            <div class="toolbar-toggle" onclick="hideToolbar()">
                <div class="close-icon" onclick="hideToolbar()"></div>
            </div>
            <div class="toolbar-content">
                <button class="add-btn" onclick="addPostIt()">เพิ่ม Post-it</button>
                
                <div class="color-picker">
                    <div class="color-option yellow selected" data-color="yellow" style="background: linear-gradient(135deg, #ffeaa7, #fdcb6e);"></div>
                    <div class="color-option pink" data-color="pink" style="background: linear-gradient(135deg, #fd79a8, #e84393);"></div>
                    <div class="color-option blue" data-color="blue" style="background: linear-gradient(135deg, #74b9ff, #0984e3);"></div>
                    <div class="color-option green" data-color="green" style="background: linear-gradient(135deg, #55efc4, #00b894);"></div>
                    <div class="color-option orange" data-color="orange" style="background: linear-gradient(135deg, #fab1a0, #e17055);"></div>
                    <div class="color-option purple" data-color="purple" style="background: linear-gradient(135deg, #a29bfe, #6c5ce7);"></div>
                </div>

                <div class="pattern-picker">
                    <div class="pattern-option" data-pattern="" style="background: #fff;"></div>
                    <div class="pattern-option" data-pattern="dots" style="background: #fff; background-image: radial-gradient(circle, #333 2px, transparent 2px); background-size: 15px 15px;"></div>
                    <div class="pattern-option" data-pattern="lines" style="background: #fff; background-image: repeating-linear-gradient(0deg, transparent, transparent 8px, #333 8px, #333 10px);"></div>
                    <div class="pattern-option" data-pattern="grid" style="background: #fff; background-image: linear-gradient(#333 1px, transparent 1px), linear-gradient(90deg, #333 1px, transparent 1px); background-size: 15px 15px;"></div>
                </div>
            </div>
        </div>

        <div class="stats">
            <div>Post-it ทั้งหมด: <span id="totalCount">0</span></div>
        </div>
    </div>

    <script>
        // ตัวแปร global
        let selectedColor = 'yellow';
        let selectedPattern = '';
        let postItCount = 0;
        let dragElement = null;
        let dragOffset = { x: 0, y: 0 };
        let toolbarVisible = false;
        let clickCount = 0;
        let clickTimer = null;

        // โหลด Post-it จากฐานข้อมูลเมื่อหน้าเว็บโหลดเสร็จ
        window.addEventListener('load', () => {
            loadPostIts();
            document.getElementById('board').addEventListener('click', handleBoardClick);
        });

        // ฟังก์ชันโหลด Post-it จากเซิร์ฟเวอร์
        async function loadPostIts() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=load'
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    data.postits.forEach(postit => {
                        createPostItElement(postit);
                    });
                    updateStats();
                    
                    // ถ้าไม่มี Post-it ให้สร้างตัวอย่าง
                    if (data.postits.length === 0) {
                        setTimeout(() => {
                            addPostIt();
                            const firstPostIt = document.querySelector('.post-it textarea');
                            if (firstPostIt) {
                                firstPostIt.value = 'ยินดีต้อนรับสู่ Post-it Board!\nลองดับเบิลคลิกที่พื้นหลังเพื่อสร้างโน๊ตใหม่ 📝\nหรือคลิกปุ่ม + เพื่อเปิดเครื่องมือ';
                                updatePostItInDatabase(firstPostIt.closest('.post-it'));
                            }
                        }, 500);
                    }
                }
            } catch (error) {
                console.error('Error loading postits:', error);
            }
        }

        // สร้าง Post-it จากข้อมูลที่ได้จากฐานข้อมูล
        function createPostItElement(data) {
            const board = document.getElementById('board');
            const postIt = document.createElement('div');
            postIt.className = `post-it ${data.color} ${data.pattern || ''}`;
            postIt.dataset.id = data.id;
            
            postIt.style.left = `${data.pos_x}px`;
            postIt.style.top = `${data.pos_y}px`;
            postIt.style.zIndex = data.z_index;
            
            // อัพเดท postItCount ให้มากกว่าค่า z_index สูงสุดที่มี
            if (data.z_index >= postItCount) {
                postItCount = data.z_index + 1;
            }

            postIt.innerHTML = `
                <textarea placeholder="เขียนสิ่งที่ต้องทำ...">${data.content || ''}</textarea>
                <button class="delete-btn" onclick="deletePostIt(this)">×</button>
            `;

            addDragFunctionality(postIt);
            board.appendChild(postIt);
            
            // เพิ่ม event listener สำหรับการเปลี่ยนแปลงเนื้อหา
            postIt.querySelector('textarea').addEventListener('input', debounce(() => {
                updatePostItInDatabase(postIt);
            }, 500));
        }

        // สร้าง Post-it ใหม่ทั้งในหน้าเว็บและฐานข้อมูล
        async function createPostItAt(x, y) {
            const postitData = {
                content: '',
                color: selectedColor,
                pattern: selectedPattern,
                pos_x: Math.max(10, Math.min(x - 100, window.innerWidth - 210)),
                pos_y: Math.max(80, Math.min(y - 100, window.innerHeight - 250)),
                z_index: ++postItCount
            };

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'save',
                        ...postitData
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    postitData.id = data.id;
                    createPostItElement(postitData);
                    updateStats();
                    
                    // โฟกัสที่ textarea
                    const newPostIt = document.querySelector(`.post-it[data-id="${data.id}"]`);
                    if (newPostIt) {
                        newPostIt.querySelector('textarea').focus();
                    }
                }
            } catch (error) {
                console.error('Error saving postit:', error);
            }
        }

        // อัพเดท Post-it ในฐานข้อมูล
        async function updatePostItInDatabase(postItElement) {
            const id = postItElement.dataset.id;
            const content = postItElement.querySelector('textarea').value;
            const pos_x = parseInt(postItElement.style.left) || 0;
            const pos_y = parseInt(postItElement.style.top) || 0;
            const z_index = parseInt(postItElement.style.zIndex) || 1;

            try {
                await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'update',
                        id: id,
                        content: content,
                        pos_x: pos_x,
                        pos_y: pos_y,
                        z_index: z_index
                    })
                });
            } catch (error) {
                console.error('Error updating postit:', error);
            }
        }

        // ลบ Post-it ทั้งในหน้าเว็บและฐานข้อมูล
        async function deletePostIt(button) {
            const postItElement = button.parentElement;
            const id = postItElement.dataset.id;

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        id: id
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    postItElement.remove();
                    updateStats();
                }
            } catch (error) {
                console.error('Error deleting postit:', error);
            }
        }

        // ฟังก์ชัน Debounce สำหรับลดจำนวนการเรียก API
        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        // Show Toolbar
        function showToolbar() {
            const toolbar = document.getElementById('toolbar');
            const floatingBtn = document.getElementById('addBtnFloating');
            
            toolbar.classList.add('show', 'expanded');
            floatingBtn.classList.add('hidden');
            toolbarVisible = true;
        }

        // Hide Toolbar
        function hideToolbar() {
            const toolbar = document.getElementById('toolbar');
            const floatingBtn = document.getElementById('addBtnFloating');
            
            toolbar.classList.remove('show', 'expanded');
            floatingBtn.classList.remove('hidden');
            toolbarVisible = false;
        }

        // Double Click Detection
        function handleBoardClick(e) {
            if (e.target !== document.getElementById('board')) return;
            
            clickCount++;
            
            if (clickCount === 1) {
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 300);
            } else if (clickCount === 2) {
                clearTimeout(clickTimer);
                clickCount = 0;
                
                // สร้าง Post-it ที่ตำแหน่งที่คลิก
                createPostItAt(e.clientX, e.clientY);
            }
        }

        // เลือกสี
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
                selectedColor = option.dataset.color;
            });
        });

        // เลือกลวดลาย
        document.querySelectorAll('.pattern-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.pattern-option').forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
                selectedPattern = option.dataset.pattern;
            });
        });

        function addPostIt() {
            // สุ่มตำแหน่งเริ่มต้น (ปรับให้ไม่ทับ navbar และ toolbar)
            const x = Math.random() * (window.innerWidth - 250) + 100;
            const y = Math.random() * (window.innerHeight - 350) + 150;
            createPostItAt(x, y);
        }

        function addDragFunctionality(element) {
            element.addEventListener('mousedown', startDrag);
            element.addEventListener('touchstart', startDrag);
        }

        function startDrag(e) {
            if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'BUTTON') return;
            
            e.preventDefault();
            dragElement = e.currentTarget;
            dragElement.classList.add('dragging');
            dragElement.style.zIndex = ++postItCount;

            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;
            
            // คำนวณ offset จากตำแหน่งปัจจุบันของ element
            const currentLeft = parseInt(dragElement.style.left) || 0;
            const currentTop = parseInt(dragElement.style.top) || 0;
            
            dragOffset.x = clientX - currentLeft;
            dragOffset.y = clientY - currentTop;

            document.addEventListener('mousemove', drag, { passive: false });
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchmove', drag, { passive: false });
            document.addEventListener('touchend', stopDrag);
        }

        function drag(e) {
            if (!dragElement) return;
            
            e.preventDefault();
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;
            
            let newX = clientX - dragOffset.x;
            let newY = clientY - dragOffset.y;

            // จำกัดขอบเขตการลาก
            newX = Math.max(0, Math.min(newX, window.innerWidth - dragElement.offsetWidth));
            newY = Math.max(0, Math.min(newY, window.innerHeight - dragElement.offsetHeight));

            // ใช้ left/top โดยตรง เพื่อความแม่นยำ
            dragElement.style.left = newX + 'px';
            dragElement.style.top = newY + 'px';
        }

        function stopDrag() {
            if (dragElement) {
                dragElement.classList.remove('dragging');
                dragElement.style.willChange = 'auto';
                updatePostItInDatabase(dragElement);
                dragElement = null;
            }
            
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
            document.removeEventListener('touchmove', drag);
            document.removeEventListener('touchend', stopDrag);
        }

        function updateStats() {
            const count = document.querySelectorAll('.post-it').length;
            document.getElementById('totalCount').textContent = count;
        }

        // ป้องกันการเลือกข้อความเมื่อลาก
        document.addEventListener('selectstart', (e) => {
            if (dragElement) e.preventDefault();
        });
    </script>
</body>
</html>