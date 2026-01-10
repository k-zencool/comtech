<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $countNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
    $countTeachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    $countProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    
    // ดึงโปรเจกต์ล่าสุดพร้อมวันที่
    $latestProjects = $pdo->query("SELECT project_name, student_names, created_at FROM projects ORDER BY id DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) { $countNews = $countTeachers = $countProjects = 0; }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark mb-1">สวัสดี, คุณ<?= htmlspecialchars($_SESSION['admin_name']) ?></h2>
                <p class="text-muted">วันนี้มีข่าวสารและผลงานนักศึกษาอัปเดตเข้ามาเพียบ ลองตรวจสอบดูนะ!</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-inline-flex align-items-center bg-white p-2 px-3 rounded-pill shadow-sm border">
                    <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                    <small class="fw-medium">ระบบกำลังทำงานปกติ</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-primary-subtle text-primary">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <h6 class="text-muted fw-normal">ข่าวสารประชาสัมพันธ์</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($countNews) ?> <small class="fs-6 text-muted fw-normal">โพสต์</small></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-danger-subtle text-danger">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h6 class="text-muted fw-normal">คณาจารย์และบุคลากร</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($countTeachers) ?> <small class="fs-6 text-muted fw-normal">ท่าน</small></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-warning-subtle text-warning">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h6 class="text-muted fw-normal">ผลงานนักศึกษาในคลัง</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($countProjects) ?> <small class="fs-6 text-muted fw-normal">ผลงาน</small></h2>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-table shadow-sm">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h5 class="fw-bold m-0">ผลงานนักศึกษาล่าสุด</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr>
                                        <th>โครงงาน</th>
                                        <th>ผู้จัดทำ</th>
                                        <th>วันที่บันทึก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($latestProjects as $p): ?>
                                    <tr>
                                        <td class="fw-medium"><?= htmlspecialchars($p['project_name']) ?></td>
                                        <td class="text-muted small"><?= htmlspecialchars($p['student_names']) ?></td>
                                        <td class="text-muted small"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 text-center border-top">
                            <a href="projects/index.php" class="text-primary text-decoration-none small fw-bold">ดูผลงานทั้งหมด <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <h6 class="fw-bold mb-3 px-1">ทางลัดการจัดการ</h6>
                <div class="d-grid gap-3">
                    <a href="news/add.php" class="btn btn-add-quick p-3 rounded-20 text-start">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-plus-circle fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold text-dark small">เขียนข่าวใหม่</div>
                                <div class="small opacity-75" style="font-size: 11px;">อัปเดตข่าวสารให้หน้าเว็บ</div>
                            </div>
                        </div>
                    </a>
                    <a href="teachers/add.php" class="btn btn-add-quick p-3 rounded-20 text-start">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user-plus fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold text-dark small">เพิ่มข้อมูลอาจารย์</div>
                                <div class="small opacity-75" style="font-size: 11px;">เพิ่มรายชื่อบุคลากรใหม่</div>
                            </div>
                        </div>
                    </a>
                    <a href="projects/add.php" class="btn btn-add-quick p-3 rounded-20 text-start">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-file-circle-plus fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold text-dark small">บันทึกโปรเจกต์</div>
                                <div class="small opacity-75" style="font-size: 11px;">เพิ่มผลงานนักศึกษาลงในคลัง</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>