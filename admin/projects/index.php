<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์: ถ้าไม่ได้ login ให้ดีดกลับไปหน้า login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูลผลงานนักเรียนทั้งหมด
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projectList = $stmt->fetchAll();
} catch (PDOException $e) {
    $projectList = [];
    $error_msg = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผลงานนักเรียน | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>

    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold m-0">จัดการผลงานนักเรียน</h3>
                    <p class="text-muted small m-0">Showcase ผลงานและนวัตกรรมของนักศึกษา ComTech</p>
                </div>
                <a href="add.php" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-folder-plus me-2"></i> เพิ่มผลงานใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3" width="120">Preview</th>
                                    <th class="py-3">ชื่อผลงาน / โปรเจกต์</th>
                                    <th class="py-3">เจ้าของผลงาน</th>
                                    <th class="py-3">ประเภท</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($projectList)): ?>
                                    <?php foreach ($projectList as $project): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php 
                                                $img_path = "../../assets/images/projects/" . $project['image'];
                                                $display_img = (!empty($project['image']) && file_exists($img_path)) ? $img_path : "../../assets/images/no-project.png";
                                            ?>
                                            <img src="<?= $display_img ?>" class="rounded-3 shadow-sm" width="90" height="60" style="object-fit: cover;">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($project['project_name']) ?></div>
                                            <small class="text-muted"><?= mb_strimwidth(strip_tags($project['description']), 0, 60, "...") ?></small>
                                        </td>
                                        <td>
                                            <div class="small fw-600"><?= htmlspecialchars($project['student_name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">ชั้นปี: <?= htmlspecialchars($project['year'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border px-3">
                                                <?= htmlspecialchars($project['type']) ?>
                                            </span>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group gap-2">
                                                <a href="edit.php?id=<?= $project['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $project['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('ลบโปรเจกต์นี้ทิ้งนะเพื่อน?')">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-diagram-project fa-3x mb-3 opacity-25"></i>
                                            <p class="m-0">ยังไม่มีข้อมูลผลงานนักเรียน</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>