<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password']; // ไม่เข้ารหัสแล้ว

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$user, $pass])) {
        header("Location: login.php");
        exit;
    } else {
        $error = "ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Post-it Board</title>
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
            max-width: 400px;
            transform: rotate(2deg);
            animation: float 8s ease-in-out infinite;
        }

        .post-it-note {
            background: linear-gradient(45deg, #74b9ff, #a8e6ff);
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
            transform: rotate(-1deg);
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
            transform: rotate(1deg);
        }

        .form-group {
            margin-bottom: 20px;
            transform: rotate(-0.5deg);
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
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
            transform: rotate(1deg);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: rotate(1deg) translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            transform: rotate(-0.5deg);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
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
                transform: rotate(1deg);
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

    <!-- Main Register Post-it -->
    <div class="post-it-container">
        <div class="post-it-note">
            <div class="logo">
                <h1>สมัครสมาชิก</h1>
                <p>เริ่มต้นใช้งาน Post-it Board วันนี้</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            </form>
            
            <div class="login-link">
                มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
            </div>
        </div>
    </div>
</body>
</html>