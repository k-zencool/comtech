<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. เช็คสิทธิ์: ถ้าไม่ได้ login ให้ดีดกลับไปหน้า login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 2. ดึงข้อมูลสรุปยอดข่าวสารมาโชว์ (SQL นับจำนวนแถว)
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

    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="welcome-box d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['admin_name']) ?> 👋</h3>
                    <p class="text-muted m-0 small">ระบบจัดการข้อมูล แผนกวิชาช่างเทคโนโลยีคอมพิวเตอร์ UTC</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success p-2 px-3 rounded-pill">
                        <i class="fa-solid fa-circle fa-xs me-1"></i> ระบบออนไลน์
                    </span>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card stat-card border-0">
                        <div class="card-body p-4">
                            <div class="icon-box">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            <h6 class="text-muted mb-1">ข่าวสารทั้งหมด</h6>
                            <h2 class="fw-bold m-0"><?= $totalNews ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stat-card border-0">
                        <div class="card-body p-4">
                            <div class="icon-box" style="color: #0d6efd;">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h6 class="text-muted mb-1">ข้อมูลคณาจารย์</h6>
                            <h2 class="fw-bold m-0">12</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stat-card border-0">
                        <div class="card-body p-4">
                            <div class="icon-box" style="color: #198754;">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>
                            <h6 class="text-muted mb-1">ผลงานนักเรียน</h6>
                            <h2 class="fw-bold m-0">8</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-5" style="border-radius: 25px;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">เริ่มจัดการเว็บไซต์ตอนนี้</h4>
                                <p class="text-muted m-0">มึงสามารถเพิ่มข่าวสารใหม่ หรือแก้ไขข้อมูลบุคลากรได้จากเมนูด้านซ้าย หรือใช้ทางลัดด้านล่างนี้</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="news/add.php" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fa-solid fa-plus me-2"></i> สร้างโพสต์ข่าว
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>