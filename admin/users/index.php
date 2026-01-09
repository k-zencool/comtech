<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์: ถ้าไม่ได้ login ให้ดีดกลับ
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูล Admin ทั้งหมด
try {
    $stmt = $pdo->query("SELECT id, username, fullname, last_login FROM admins ORDER BY id ASC");
    $adminList = $stmt->fetchAll();
} catch (PDOException $e) {
    $adminList = [];
    $error_msg = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ดูแลระบบ | ComTech Admin</title>
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
                    <h3 class="fw-bold m-0">จัดการผู้ดูแลระบบ</h3>
                    <p class="text-muted small m-0">รายชื่อผู้ที่มีสิทธิ์เข้าถึงระบบหลังบ้าน</p>
                </div>
                <a href="add.php" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-user-plus me-2"></i> เพิ่มแอดมินใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3" width="80">ID</th>
                                    <th class="py-3">ชื่อ-นามสกุล</th>
                                    <th class="py-3">Username</th>
                                    <th class="py-3">เข้าใช้งานล่าสุด</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($adminList)): ?>
                                    <?php foreach ($adminList as $user): ?>
                                    <tr>
                                        <td class="ps-4 text-muted">#<?= $user['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <i class="fa-solid fa-circle-user me-2 text-primary"></i>
                                                <?= htmlspecialchars($user['fullname']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded text-danger"><?= htmlspecialchars($user['username']) ?></code>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fa-regular fa-clock me-1"></i>
                                                <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'ยังไม่เคยเข้าใช้งาน' ?>
                                            </small>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group gap-2">
                                                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                                    <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('ลบแอดมินคนนี้เหรอ? คิดดีๆ นะเพื่อน')">
                                                        <i class="fa-solid fa-user-minus"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light rounded-pill px-3" disabled title="ลบตัวเองไม่ได้นะไอ้ชาย">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลผู้ดูแลระบบ</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-light rounded-20 border border-dashed">
                <small class="text-muted">
                    <i class="fa-solid fa-circle-info me-2"></i> 
                    **คำเตือน:** การลบผู้ดูแลระบบจะทำให้บุคคลนั้นไม่สามารถเข้าถึงส่วนจัดการนี้ได้อีก โปรดตรวจสอบให้แน่ใจก่อนดำเนินการ
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>