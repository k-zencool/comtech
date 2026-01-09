<?php
session_start();

// ถ้ามีการกด Submit หรือหน่วงเวลาจนจบ ให้เคลียร์ Session จริงๆ
if (isset($_GET['action']) && $_GET['action'] == 'confirm') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out... | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400&display=swap" rel="stylesheet">
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

        /* พื้นหลังเคลื่อนไหวแบบละมุนๆ (Gradient เดิมจากหน้า Login) */
        .bg-animated {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);
            z-index: -1;
        }

        .logout-container {
            text-align: center;
            width: 100%;
            max-width: 300px;
        }

        /* หลอดโหลดกลางหน้าจอ */
        .bar-container {
            width: 100%; height: 4px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .bar-fill {
            width: 0%; height: 100%;
            background: linear-gradient(90deg, var(--primary-color), #ff4d94);
            box-shadow: 0 0 20px var(--primary-color);
            /* วิ่งปรื๊ดแบบเร่งตอนจบ */
            transition: width 1.5s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .status-text {
            color: #fff;
            font-size: 0.8rem;
            letter-spacing: 3px;
            opacity: 0.6;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="bg-animated"></div>

    <div class="logout-container">
        <div class="bar-container">
            <div class="bar-fill" id="barFill"></div>
        </div>
        <div class="status-text" id="statusText">Signing Out...</div>
    </div>

    <script>
        window.onload = function() {
            const bar = document.getElementById('barFill');
            
            // 1. สั่งให้หลอดวิ่งทันทีที่หน้าโหลดเสร็จ
            setTimeout(() => {
                bar.style.width = '100%';
            }, 100);

            // 2. พอหลอดวิ่งเสร็จ (1.5s + 0.5s เผื่อไว้) ให้ดีดไปหน้า login.php พร้อมเคลียร์ session
            setTimeout(() => {
                window.location.href = 'logout.php?action=confirm';
            }, 2000);
        };
    </script>
</body>
</html>