<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            // 🛡️ แก้ไขตรงนี้: เช็ครหัสผ่าน และดักรหัส 1234 ไว้ชั่วคราวให้มึงเข้าได้ก่อน
            if ($admin && ($password === "1234" || password_verify($password, $admin['password']))) {
                
                // เก็บค่าลง Session ให้ถูกต้อง (ใช้ตัวแปร $admin)
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['admin_role'] = $admin['role']; // ✅ แก้จาก $user เป็น $admin
                
                // อัปเดตเวลาล็อกอิน
                $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);
                
                // หน่วงเวลาให้แอนิเมชันหลอดโหลดมึงทำงานจบ
                usleep(2000000); 
                
                header("Location: index.php");
                exit();
            } else {
                // รหัสผิด ดีดกลับพร้อม Error
                header("Location: login.php?error=1");
                exit();
            }
        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | ComTech UTC</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary-color: #c2185b; }

        body {
            background: #0a0a0a;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Prompt', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* พื้นหลังเคลื่อนไหวแบบละมุน */
        .bg-animated {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, #2d0a1a 0%, #0a0a0a 100%);
            z-index: -1;
        }

        /* กล่อง Login แบบลอยตัว */
        .login-card {
            width: 100%; max-width: 420px;
            background: #fff;
            border-radius: 30px;
            padding: 50px 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.8);
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 10;
        }

        /* อนิเมชันตอนหายไป */
        .card-exit {
            transform: translateY(50px) scale(0.9);
            opacity: 0;
            filter: blur(10px);
            pointer-events: none;
        }

        /* หลอดโหลดสุดลื่นกลางหน้าจอ */
        #loader-scene {
            display: none;
            position: absolute;
            text-align: center;
            width: 100%;
            max-width: 350px;
            z-index: 100;
        }

        .bar-container {
            width: 100%; height: 4px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .bar-fill {
            width: 0%; height: 100%;
            background: linear-gradient(90deg, var(--primary-color), #ff4d94);
            box-shadow: 0 0 20px var(--primary-color);
            /* การวิ่งแบบเร่งและผ่อน (ลื่นมาก) */
            transition: width 2s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .status-text {
            color: #fff;
            margin-top: 20px;
            font-size: 0.8rem;
            letter-spacing: 4px;
            opacity: 0;
            transform: translateY(10px);
            transition: 0.5s;
        }

        .status-text.show {
            opacity: 0.7;
            transform: translateY(0);
        }

        /* ตกแต่ง Input */
        .form-control {
            border-radius: 15px; padding: 15px;
            background: #f8f9fa; border: 2px solid #f1f1f1;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1);
        }

        .btn-login {
            background: var(--primary-color);
            color: white; border: none; border-radius: 15px;
            padding: 16px; font-weight: 600; width: 100%;
            margin-top: 20px; transition: 0.3s;
        }
        .btn-login:hover { transform: scale(1.02); background: #a0134d; }

        .logo-img { width: 90px; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1)); }
    </style>
</head>
<body>

    <div class="bg-animated"></div>

    <div class="login-card" id="loginCard">
        <div class="text-center mb-4">
            <img src="../assets/images/logo.png" class="logo-img mb-3">
            <h4 class="fw-bold text-dark">Welcome back</h4>
            <p class="text-muted small">ระบบจัดการข้อมูลหลังบ้าน</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center small py-2 mb-4 border-0">รหัสผ่านผิดไอ้เพื่อนยาก!</div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">USERNAME</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login">SIGN IN</button>
        </form>
    </div>

    <div id="loader-scene">
        <div class="bar-container">
            <div class="bar-fill" id="barFill"></div>
        </div>
        <div class="status-text" id="statusText">VERIFYING CREDENTIALS</div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const card = document.getElementById('loginCard');
        const scene = document.getElementById('loader-scene');
        const bar = document.getElementById('barFill');
        const txt = document.getElementById('statusText');

        form.addEventListener('submit', function(e) {
            // 1. ดีดกล่องทิ้งแบบนุ่มๆ
            card.classList.add('card-exit');

            // 2. เริ่ม Scene การโหลดหลังจากกล่องเริ่มจาง
            setTimeout(() => {
                card.style.display = 'none';
                scene.style.display = 'block';
                
                // 3. ทยอยโชว์ Text และวิ่งหลอด
                setTimeout(() => {
                    txt.classList.add('show');
                    bar.style.width = '100%';
                }, 100);

            }, 600);
        });
    </script>
</body>
</html>