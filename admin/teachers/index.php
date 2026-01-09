<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์: ถ้าไม่ได้ login ให้ดีดกลับไปหน้า login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูลคณาจารย์ทั้งหมด
try {
    $stmt = $pdo->query("SELECT * FROM teachers ORDER BY id ASC");
    $teacherList = $stmt->fetchAll();
} catch (PDOException $e) {
    $teacherList = [];
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
</head>
<body>

    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold m-0">จัดการข้อมูลคณาจารย์</h3>
                    <p class="text-muted small m-0">ข้อมูลบุคลากรแผนกวิชาช่างเทคโนโลยีคอมพิวเตอร์</p>
                </div>
                <a href="add.php" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-user-plus me-2"></i> เพิ่มบุคลากร
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
                                    <th class="py-3">ตำแหน่ง / วิทยฐานะ</th>
                                    <th class="py-3">การติดต่อ</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($teacherList)): ?>
                                    <?php foreach ($teacherList as $teacher): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php 
                                                $img_path = "../../assets/images/teachers/" . $teacher['image'];
                                                $display_img = (!empty($teacher['image']) && file_exists($img_path)) ? $img_path : "../../assets/images/no-avatar.png";
                                            ?>
                                            <img src="<?= $display_img ?>" class="rounded-circle shadow-sm" width="60" height="60" style="object-fit: cover; border: 2px solid #fff;">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($teacher['fullname']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($teacher['education'] ?? 'วุฒิการศึกษา') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border px-3">
                                                <?= htmlspecialchars($teacher['position']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small"><i class="fa-solid fa-phone me-1 text-muted"></i> <?= htmlspecialchars($teacher['phone'] ?? '-') ?></div>
                                            <div class="small"><i class="fa-solid fa-envelope me-1 text-muted"></i> <?= htmlspecialchars($teacher['email'] ?? '-') ?></div>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group gap-2">
                                                <a href="edit.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $teacher['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('จะลบข้อมูลครูจริงเหรอเพื่อน?')">
                                                    <i class="fa-solid fa-user-minus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-user-slash fa-3x mb-3 opacity-25"></i>
                                            <p class="m-0">ยังไม่มีข้อมูลคณาจารย์ในระบบ</p>
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