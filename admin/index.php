<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// เช็คสิทธิ์: ถ้าไม่ได้ login ให้ดีดกลับไปหน้า login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลสรุปยอดข่าวสาร
try {
    $stmtNews = $pdo->query("SELECT COUNT(*) FROM news");
    $totalNews = $stmtNews->fetchColumn();
} catch (PDOException $e) {
    $totalNews = 0;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo.png" width="60" class="mb-2">
            <h6 class="fw-bold m-0">COMTECH ADMIN</h6>
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="index.php"><i class="fa-solid fa-chart-line me-2"></i> แดชบอร์ด</a>
            </li>
            <li>
                <a href="news/index.php"><i class="fa-solid fa-newspaper me-2"></i> จัดการข่าวสาร</a>
            </li>
            <li>
                <a href="teachers/index.php"><i class="fa-solid fa-user-tie me-2"></i> ข้อมูลคณาจารย์</a>
            </li>
            <li>
                <a href="projects/index.php"><i class="fa-solid fa-laptop-code me-2"></i> ผลงานนักเรียน</a>
            </li>
            <hr style="opacity: 0.1; margin: 10px 20px; border-color: #fff;">
            <li>
                <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="welcome-box d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['admin_name']) ?> 👋</h3>
                <p class="text-muted m-0">ระบบจัดการข้อมูล แผนกวิชาช่างเทคโนโลยีคอมพิวเตอร์</p>
            </div>
            <div>
                <span class="badge bg-primary-light p-2 px-3 rounded-pill">
                    <i class="fa-solid fa-circle fa-xs me-1"></i> Admin Online
                </span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="icon-box me-3">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">ข่าวสารทั้งหมด</h6>
                            <h2 class="fw-bold m-0"><?= $totalNews ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="icon-box me-3" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">บุคลากร</h6>
                            <h2 class="fw-bold m-0">12</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="icon-box me-3" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">ผลงานนักเรียน</h6>
                            <h2 class="fw-bold m-0">8</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 rounded-20">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-bolt me-2 text-warning"></i>ทางลัดการจัดการ</h5>
                    <div class="d-flex gap-3">
                        <a href="news/add.php" class="btn btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-plus me-2"></i>เพิ่มข่าวใหม่
                        </a>
                        <a href="news/index.php" class="btn btn-outline-dark rounded-pill px-4">
                            จัดการข่าวสารทั้งหมด
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>