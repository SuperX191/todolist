<?php
session_start();
// ตรวจสอบว่ามี session หรือไม่
$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-it Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;700&family=Charmonman:wght@400;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #6c5ce7 !important;
        --secondary: #a29bfe !important;
        --light: #f8f9fa !important;
        --dark: #343a40 !important;
        --yellow: #fdcb6e !important;
        --pink: #fd79a8 !important;
        --blue: #74b9ff !important;
        --green: #00b894 !important;
    }

    .navbar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        background: rgba(255, 255, 255, 0.95) !important;
        padding: 15px 20px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        z-index: 1000 !important;
        backdrop-filter: blur(10px) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
        font-family: 'Mali', cursive !important;
    }

    .navbar-brand {
        font-size: 24px !important;
        font-weight: bold !important;
        color: var(--primary) !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .nav-links {
        display: flex !important;
        gap: 20px !important;
        align-items: center !important;
    }

    .nav-link {
        color: #555 !important;
        text-decoration: none !important;
        padding: 8px 16px !important;
        border-radius: 20px !important;
        transition: all 0.3s !important;
        font-weight: 600 !important;
        position: relative !important;
    }

    .nav-link:hover {
        color: var(--primary) !important;
        background: rgba(108, 92, 231, 0.1) !important;
    }

    .nav-link.active {
        color: var(--primary) !important;
        background: rgba(108, 92, 231, 0.15) !important;
    }

    .nav-link.active:after {
        content: '' !important;
        position: absolute !important;
        bottom: -5px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 50% !important;
        height: 3px !important;
        background: var(--primary) !important;
        border-radius: 3px !important;
    }

    .btn {
        padding: 10px 20px !important;
        border-radius: 25px !important;
        font-weight: bold !important;
        text-decoration: none !important;
        transition: all 0.3s !important;
        display: inline-block !important;
        font-family: 'Mali', cursive !important;
        cursor: pointer !important;
        border: none !important;
        font-size: 14px !important;
    }

    .btn-primary {
        background: linear-gradient(45deg, var(--primary), var(--secondary)) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4) !important;
    }

    .btn-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(108, 92, 231, 0.6) !important;
    }

    .btn-logout {
        background: linear-gradient(45deg, #ff7675, #d63031) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(214, 48, 49, 0.4) !important;
    }

    .btn-logout:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(214, 48, 49, 0.6) !important;
    }

    .user-greeting {
        font-weight: 600 !important;
        color: var(--primary) !important;
        margin-right: 10px !important;
    }

    .menu-toggle {
        display: none !important;
        background: none !important;
        border: none !important;
        font-size: 24px !important;
        color: var(--primary) !important;
        cursor: pointer !important;
    }

    @media (max-width: 768px) {
        .nav-links {
            position: fixed !important;
            top: 70px !important;
            left: 0 !important;
            width: 100% !important;
            background: rgba(255, 255, 255, 0.98) !important;
            flex-direction: column !important;
            align-items: center !important;
            padding: 20px 0 !important;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1) !important;
            transform: translateY(-150%) !important;
            transition: transform 0.3s ease !important;
            z-index: 999 !important;
        }

        .nav-links.active {
            transform: translateY(0) !important;
        }

        .nav-link {
            width: 100% !important;
            text-align: center !important;
            padding: 12px 0 !important;
        }

        .menu-toggle {
            display: block !important;
        }

        .user-greeting {
            display: block !important;
            text-align: center !important;
            margin: 10px 0 !important;
            width: 100% !important;
        }
    }
</style>

</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            <span>📝 Post-it Board</span>
        </a>
        
        <button class="menu-toggle" id="menuToggle">☰</button>
        
        <div class="nav-links" id="navLinks">
            <?php if($isLoggedIn): ?>
                <a href="index.php" class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>">หน้าแรก</a>
                <a href="note.php" class="nav-link <?= ($currentPage == 'note.php') ? 'active' : '' ?>">กระดานโน้ต</a>
                <a href="room.php" class="nav-link <?= ($currentPage == 'room.php') ? 'active' : '' ?>">สร้างห้อง</a>
                
                <span class="user-greeting">สวัสดี, <?= htmlspecialchars($_SESSION['username'] ?? 'ผู้ใช้') ?></span>
                <a href="logout.php" class="btn btn-logout">ออกจากระบบ</a>
            <?php else: ?>
                <a href="index.php" class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>">หน้าแรก</a>
                <a href="features.php" class="nav-link <?= ($currentPage == 'features.php') ? 'active' : '' ?>">ฟีเจอร์</a>
                <a href="login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
        
        // Close menu when clicking on a link (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
        
        // Redirect to login if not logged in and trying to access protected pages
        const protectedPages = ['note.php', 'room.php'];
        const currentPage = '<?= $currentPage ?>';
        
        <?php if(!$isLoggedIn): ?>
            if(protectedPages.includes(currentPage)) {
                window.location.href = 'login.php';
            }
        <?php endif; ?>
    </script>
</body>
</html>