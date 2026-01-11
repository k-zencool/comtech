<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // 2. ดึงข้อมูลมานับจำนวน (ใช้จริงใน Stat Cards)
    $count_news = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
    $count_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    $count_awards = $pdo->query("SELECT COUNT(*) FROM awards")->fetchColumn();
    $count_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

    // 3. ดึงข่าวสารล่าสุด 5 รายการมาโชว์
    $stmt = $pdo->query("SELECT topic, category, created_at FROM news ORDER BY created_at DESC LIMIT 5");
    $latest_news = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_style.css"> </head>
<body>

<div class="admin-wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="mb-4">
                <h3 class="fw-bold m-0">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?> 👋</h3>
                <p class="text-muted small m-0">นี่คือภาพรวมข้อมูลทั้งหมดในระบบ ComTech UTC [cite: 2025-05-30]</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($count_news) ?></h2>
                        <p class="text-muted m-0">ข่าวสารทั้งหมด</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($count_teachers) ?></h2>
                        <p class="text-muted m-0">บุคลากร/อาจารย์</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($count_awards) ?></h2>
                        <p class="text-muted m-0">รางวัลและความภูมิใจ</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($count_projects) ?></h2>
                        <p class="text-muted m-0">ผลงานนักศึกษา</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-table border-0 shadow-sm rounded-20 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold m-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>ข่าวสารล่าสุด</h5>
                            <a href="news/index.php" class="btn btn-sm btn-light rounded-pill border px-3">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle m-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">หัวข้อข่าว</th>
                                            <th>หมวดหมู่</th>
                                            <th class="pe-4 text-end">วันที่ลงข่าว</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($latest_news)): ?>
                                            <?php foreach ($latest_news as $news): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 300px;">
                                                        <?= htmlspecialchars($news['topic']) ?>
                                                    </div>
                                                </td>
                                                <td><span class="badge rounded-pill bg-light text-dark border px-3"><?= htmlspecialchars($news['category']) ?></span></td>
                                                <td class="pe-4 text-end text-muted small"><?= date('d/m/Y', strtotime($news['created_at'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center py-5 text-muted">ยังไม่มีการลงข่าวสารเพื่อน! [cite: 2025-05-31]</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm rounded-20 p-4 bg-white h-100">
                        <h5 class="fw-bold mb-4">ทางลัดจัดการระบบ</h5>
                        <div class="d-grid gap-3">
                            <a href="news/add.php" class="btn btn-add-quick">
                                <i class="fa-solid fa-plus-circle me-2"></i> เพิ่มข่าวใหม่
                            </a>
                            <a href="teachers/add.php" class="btn btn-add-quick">
                                <i class="fa-solid fa-user-plus me-2"></i> เพิ่มข้อมูลอาจารย์
                            </a>
                            <a href="awards/add.php" class="btn btn-add-quick">
                                <i class="fa-solid fa-medal me-2"></i> เพิ่มรางวัลใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-pagination-sticky">
            <div class="card footer-card border-0 shadow-sm">
                <div class="card-body px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small fw-600">
                            สถานะระบบ: <span class="text-success"><i class="fa-solid fa-circle small me-1"></i> ออนไลน์</span>
                        </div>
                        <div class="small text-muted">
                            Update: <?= date('d/m/Y H:i') ?>
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