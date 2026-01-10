<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูลคณาจารย์ทั้งหมด
try {
    $stmt = $pdo->query("SELECT * FROM teachers ORDER BY id DESC");
    $teachers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
    $error_msg = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลคณาจารย์ | ComTech Admin</title>
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
                <h3 class="fw-bold m-0"><i class="fa-solid fa-users-rectangle me-2 text-primary"></i>จัดการข้อมูลคณาจารย์</h3>
                <p class="text-muted small m-0">รายชื่อบุคลากรและคณาจารย์ในสาขาวิชา</p>
            </div>
            <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> เพิ่มอาจารย์ใหม่
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3" width="100">รูปภาพ</th>
                                <th class="py-3">ชื่อ-นามสกุล</th>
                                <th class="py-3">ตำแหน่งวิชาการ</th>
                                <th class="py-3">ความเชี่ยวชาญ</th>
                                <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php 
                                            $img_path = "../../uploads/teachers/" . $row['image'];
                                            $has_img = (!empty($row['image']) && file_exists($img_path));
                                            ?>
                                            <div class="portrait-preview-sm">
                                                <?php if ($has_img): ?>
                                                    <img src="<?= $img_path ?>" class="rounded-3 shadow-sm" width="60" height="80" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="no-img-portrait rounded-3 border d-flex align-items-center justify-content-center bg-light" width="60" height="80">
                                                        <i class="fa-solid fa-user-tie text-muted" style="opacity: 0.5;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                            <small class="text-muted d-block"><?= mb_strimwidth(strip_tags($row['education']), 0, 50, "...") ?></small>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border px-3">
                                                <?= htmlspecialchars($row['position']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= mb_strimwidth($row['expertise'], 0, 60, "...") ?></small>
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
                                    <td colspan="5" class="text-center py-5 text-muted">ยังไม่มีข้อมูลคณาจารย์ในระบบ</td>
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
        title: 'ยืนยันการลบ?',
        text: "ข้อมูลและรูปถ่ายอาจารย์จะหายไปถาวรนะเพื่อน!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete.php?id=' + id;
        }
    })
}

// เช็คสถานะหลังลบ
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'deleted') {
    Swal.fire({
        title: 'สำเร็จ!',
        text: 'กำจัดข้อมูลเรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonColor: '#1a1a1a'
    });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

<style>
    /* สไตล์เพิ่มเติมสำหรับรูปแนวตั้งในตาราง */
    .portrait-preview-sm {
        width: 60px;
        height: 80px;
        overflow: hidden;
    }
    .no-img-portrait {
        width: 60px;
        height: 80px;
        font-size: 24px;
    }
</style>

</body>
</html>