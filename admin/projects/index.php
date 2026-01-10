<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูลโปรเจกต์ทั้งหมด (เรียงจากใหม่ไปเก่า)
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY academic_year DESC, created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
    $error_msg = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผลงานนักศึกษา | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>จัดการผลงานนักศึกษา</h3>
                <p class="text-muted small m-0">รวมโครงงานและโปรเจกต์จบการศึกษาของนักศึกษา</p>
            </div>
            <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> เพิ่มผลงานใหม่
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3" width="150">รูปพรีวิว</th>
                                <th class="py-3">ชื่อโครงงาน / ผู้จัดทำ</th>
                                <th class="py-3" width="120">ปีการศึกษา</th>
                                <th class="py-3" width="180">อาจารย์ที่ปรึกษา</th>
                                <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php 
                                            $img_path = "../../uploads/projects/" . $row['image'];
                                            $has_img = (!empty($row['image']) && file_exists($img_path));
                                            ?>
                                            <div class="project-img-wrapper">
                                                <?php if ($has_img): ?>
                                                    <img src="<?= $img_path ?>" class="rounded-3 shadow-sm border" width="120" height="70" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="no-img-placeholder rounded-3 border d-flex align-items-center justify-content-center bg-light" style="width:120px; height:70px;">
                                                        <i class="fa-solid fa-image text-muted" style="opacity: 0.4;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['project_name']) ?></div>
                                            <div class="small text-muted">
                                                <i class="fa-solid fa-user-graduate me-1"></i> 
                                                <?= htmlspecialchars($row['student_names']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3">
                                                ปี <?= htmlspecialchars($row['academic_year']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-secondary">
                                                <i class="fa-solid fa-chalkboard-user me-1 text-muted"></i>
                                                <?= htmlspecialchars($row['advisor'] ?: '-') ?>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group gap-2">
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <button type="button" onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">ยังไม่มีข้อมูลผลงานนักศึกษาในคลัง</td>
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
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'ลบโปรเจกต์นี้?',
        text: "ลบแล้วข้อมูลและรูปพรีวิวจะหายไปถาวรนะเพื่อน!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ลบทิ้งเลย!',
        cancelButtonText: 'ยกเลิก',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete.php?id=' + id;
        }
    })
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'deleted') {
    Swal.fire({
        title: 'กำจัดเสร็จสิ้น!',
        text: 'ผลงานถูกลบออกจากคลังแล้ว',
        icon: 'success',
        confirmButtonColor: '#1a1a1a'
    });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

<style>
    /* สไตล์สำหรับรูปแนวนอนในตาราง */
    .project-img-wrapper {
        width: 120px;
        height: 70px;
        overflow: hidden;
    }
</style>

</body>
</html>