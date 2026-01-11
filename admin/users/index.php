<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์พื้นฐาน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 🛡️ ดักบั๊ก Undefined: ถ้าไม่มีค่า ให้ตั้งเป็นว่างไว้ก่อน
$my_role = $_SESSION['admin_role'] ?? '';

// --- 🛠️ ระบบค้นหา (Search Logic) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $where = "WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (fullname LIKE :search OR username LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $stmt = $pdo->prepare("SELECT id, username, fullname, role, last_login FROM admins $where ORDER BY id ASC");
    $stmt->execute($params);
    $adminList = $stmt->fetchAll();
    $total_items = count($adminList);
} catch (PDOException $e) {
    $adminList = [];
    $total_items = 0;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแอดมิน | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css"> <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold m-0"><i class="fa-solid fa-user-shield me-2 text-primary"></i>จัดการแอดมิน</h3>
                        <p class="text-muted small m-0">จัดการสิทธิ์เฉพาะผู้ดูแลระบบเท่านั้น [cite: 2025-05-30]</p>
                    </div>
                    <?php if ($my_role === 'superadmin'): ?>
                        <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-user-plus me-2"></i> เพิ่มแอดมินใหม่
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card border-0 shadow-sm rounded-20 p-3 mb-4 bg-white">
                    <form method="GET" class="row g-2">
                        <div class="col-md-9 col-lg-10">
                            <input type="text" name="search" class="form-control search-box" placeholder="ค้นหาชื่อ-นามสกุล หรือ Username..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-15 fw-500 h-100">
                                <i class="fa-solid fa-magnifying-glass me-2"></i>ค้นหา
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-20 overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3" width="80">ID</th>
                                        <th class="py-3">ชื่อ-นามสกุล</th>
                                        <th class="py-3">ระดับสิทธิ์</th>
                                        <th class="pe-4 py-3 text-center" width="180">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($adminList)): ?>
                                        <?php foreach ($adminList as $user): ?>
                                        <tr>
                                            <td class="ps-4 text-muted small">#<?= $user['id'] ?></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($user['fullname']) ?></div>
                                                <small class="text-muted">User: <?= htmlspecialchars($user['username']) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($user['role'] == 'superadmin'): ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-crown me-1"></i> Super Admin
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-user me-1"></i> Admin
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="btn-group gap-2">
                                                    <?php if ($my_role === 'superadmin' || $user['id'] == $_SESSION['admin_id']): ?>
                                                        <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="แก้ไข">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($my_role === 'superadmin' && $user['id'] != $_SESSION['admin_id'] && $user['role'] !== 'superadmin'): ?>
                                                        <button type="button" onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="ลบ">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-light rounded-pill px-3 border" disabled title="ไม่มีสิทธิ์ลบไอดีนี้">
                                                            <i class="fa-solid fa-lock text-muted"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted">ไม่พบข้อมูลแอดมินที่มึงค้นหาเพื่อน! [cite: 2025-05-31]</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($total_items > 0): ?>
            <div class="footer-pagination-sticky">
                <div class="card footer-card shadow-sm border-0">
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-muted small fw-600">
                                พบแอดมินทั้งหมด <?= number_format($total_items) ?> ท่าน <span class="mx-2 text-light">|</span> สิทธิ์การเข้าถึงระดับสูง
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex gap-2">
                                    <button class="btn-nav disabled"><i class="fa-solid fa-chevron-left small"></i></button>
                                    <button class="btn-nav disabled"><i class="fa-solid fa-chevron-right small"></i></button>
                                </div>
                                <select class="form-select form-select-sm rounded-12 border-light text-muted" style="width: 130px;">
                                    <option selected>แสดงทั้งหมด</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ลบแล้วกู้คืนไม่ได้นะเพื่อน! [cite: 2025-05-31]",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => { if (result.isConfirmed) { window.location.href = 'delete.php?id=' + id; } })
        }

        // เช็คสถานะหลังลบ
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'deleted') {
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'กำจัดแอดมินเรียบร้อยแล้วเพื่อน [cite: 2025-05-30]',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>
</html>