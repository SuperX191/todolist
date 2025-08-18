<?php
 include 'nav.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-it Todo Board - แนะนำเว็บ</title>
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
            --orange: #f0932b;
            --purple: #6c5ce7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Mali', cursive, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            line-height: 1.6;
            overflow-x: hidden;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.6);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 20px 60px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .hero-text {
            flex: 1;
            min-width: 300px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 48px;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-image {
            flex: 1;
            min-width: 300px;
            position: relative;
            height: 500px;
        }

        /* Post-it Demo */
        .post-it-demo {
            position: absolute;
            width: 200px;
            height: 200px;
            padding: 15px;
            border-radius: 3px;
            font-family: 'Charmonman', cursive;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1), 0 6px 20px rgba(0,0,0,0.1);
            transform-origin: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            animation: float 6s ease-in-out infinite;
        }

        .post-it-content {
            flex: 1;
            overflow: hidden;
        }

        .post-it-color {
            height: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .post-it-1 {
            background: linear-gradient(45deg, var(--yellow), #fdcb6e);
            top: 50px;
            left: 50%;
            animation-delay: 0s;
        }

        .post-it-2 {
            background: linear-gradient(45deg, var(--pink), #e84393);
            top: 150px;
            left: 30%;
            animation-delay: 1s;
        }

        .post-it-3 {
            background: linear-gradient(45deg, var(--blue), #0984e3);
            top: 250px;
            left: 60%;
            animation-delay: 2s;
        }

        .post-it-4 {
            background: linear-gradient(45deg, var(--green), #00b894);
            top: 350px;
            left: 40%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(-5deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-title {
            font-size: 24px;
            margin-bottom: 15px;
        }

        /* How to Use */
        .how-to-use {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .steps {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 50px;
        }

        .step {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            text-align: center;
            padding: 20px;
            position: relative;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px 20px;
            background: rgba(0, 0, 0, 0.3);
            margin-top: 80px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            
            .hero-subtitle {
                font-size: 18px;
            }
            
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text {
                margin-bottom: 40px;
            }
            
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Post-it Todo Board</h1>
                <p class="hero-subtitle">แพลตฟอร์มจัดการงานแบบอินเทอร์แอคทีฟด้วยโน้ตเสมือนจริง บนกระดานไร้ขีดจำกัด</p>
                <a href="note.php" class="btn btn-primary">เริ่มใช้งานทันที</a>
            </div>
            <div class="hero-image">
                <div class="post-it-demo post-it-1">
                    <div class="post-it-content">
                        <h3>สิ่งที่ต้องทำวันนี้</h3>
                        <ul>
                            <li>ประชุมทีม 10:00 น.</li>
                            <li>ส่งรายงานลูกค้า</li>
                            <li>ออกแบบ UI ใหม่</li>
                        </ul>
                    </div>
                    <div class="post-it-color" style="background: #fdcb6e;"></div>
                </div>
                <div class="post-it-demo post-it-2">
                    <div class="post-it-content">
                        <h3>ไอเดียโปรเจคใหม่</h3>
                        <p>แอปจัดการเวลาสำหรับฟรีแลนซ์ + ระบบแจ้งเตือน + สถิติการทำงาน</p>
                    </div>
                    <div class="post-it-color" style="background: #e84393;"></div>
                </div>
                <div class="post-it-demo post-it-3">
                    <div class="post-it-content">
                        <h3>สิ่งที่ต้องซื้อ</h3>
                        <ul>
                            <li>นม</li>
                            <li>ไข่</li>
                            <li>ผลไม้</li>
                        </ul>
                    </div>
                    <div class="post-it-color" style="background: #0984e3;"></div>
                </div>
                <div class="post-it-demo post-it-4">
                    <div class="post-it-content">
                        <h3>เป้าหมายสัปดาห์นี้</h3>
                        <p>ออกกำลังกาย 3 ครั้ง<br>เรียนคอร์สออนไลน์ 1 บท<br>อ่านหนังสือ 50 หน้า</p>
                    </div>
                    <div class="post-it-color" style="background: #00b894;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2 class="section-title">ทำไมต้องใช้ Post-it Board?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">✏️</div>
                <h3 class="feature-title">เขียนโน้ตได้อย่างอิสระ</h3>
                <p>สร้างโน้ตได้ไม่จำกัดจำนวน จัดวางตำแหน่งใดก็ได้บนกระดานเสมือน เหมือนใช้งาน Post-it จริงๆ</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3 class="feature-title">เลือกสีและลวดลาย</h3>
                <p>มีสีสันให้เลือกมากมาย พร้อมลวดลายจุด เส้น และกริด เพื่อจัดกลุ่มงานและเพิ่มความสวยงาม</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3 class="feature-title">ลากและวางได้</h3>
                <p>จัดเรียงโน้ตได้อย่างง่ายดายด้วยการลากและวาง ปรับตำแหน่งตามใจชอบ</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3 class="feature-title">ใช้งานบนทุกอุปกรณ์</h3>
                <p>เข้าถึงได้ทั้งคอมพิวเตอร์ แท็บเล็ต และสมาร์ทโฟน รองรับการทำงานข้ามอุปกรณ์</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">บันทึกอัตโนมัติ</h3>
                <p>ข้อมูลทั้งหมดจะถูกบันทึกอัตโนมัติ ไม่ต้องกังวลว่าโน้ตจะหาย</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💡</div>
                <h3 class="feature-title">ใช้งานง่าย</h3>
                <p>อินเทอร์เฟซที่เรียบง่าย เข้าใจได้ทันที ไม่จำเป็นต้องเรียนรู้วิธีใช้</p>
            </div>
        </div>
    </section>

    <!-- How to Use -->
    <section class="how-to-use">
        <h2 class="section-title">วิธีใช้งาน</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>สร้างโน้ต</h3>
                <p>คลิกปุ่ม "+ เพิ่ม Post-it" หรือดับเบิลคลิกบนกระดานเพื่อสร้างโน้ตใหม่</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>เลือกสีและลวดลาย</h3>
                <p>ปรับแต่งโน้ตด้วยสีและลวดลายที่ชอบจากแถบเครื่องมือ</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>เขียนเนื้อหา</h3>
                <p>คลิกที่โน้ตเพื่อเขียนสิ่งที่ต้องทำหรือความคิดต่างๆ</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>จัดเรียง</h3>
                <p>ลากโน้ตไปวางตำแหน่งใดก็ได้บนกระดานเพื่อจัดระเบียบงาน</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 Post-it Todo Board | สร้างด้วย ❤️ สำหรับคนชอบจัดระเบียบงาน</p>
        <p>ติดต่อเรา: 68319010011@chontech.ac.th</p>
    </footer>

    <script>
        // Simple animation for demo post-its
        document.addEventListener('DOMContentLoaded', () => {
            const postIts = document.querySelectorAll('.post-it-demo');
            
            postIts.forEach((postIt, index) => {
                // Random initial rotation
                const rotation = (Math.random() * 10) - 5;
                postIt.style.transform = `rotate(${rotation}deg)`;
                
                // Random delay for animation
                postIt.style.animationDelay = `${index * 0.5}s`;
            });
        });
    </script>
</body>
</html>