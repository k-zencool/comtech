<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// --- 🛠️ ระบบค้นหาและนับจำนวน (Search Logic) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $where = "WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (name LIKE :search OR position LIKE :search OR expertise LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $stmt = $pdo->prepare("SELECT * FROM teachers $where ORDER BY id DESC");
    $stmt->execute($params);
    $teachers = $stmt->fetchAll();
    $total_items = count($teachers);
} catch (PDOException $e) {
    $teachers = [];
    $total_items = 0;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลคณาจารย์ | ComTech Admin</title>
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
                    <h3 class="fw-bold m-0"><i class="fa-solid fa-users-rectangle me-2 text-primary"></i>จัดการข้อมูลคณาจารย์</h3>
                    <p class="text-muted small m-0">รายชื่อบุคลากรและคณาจารย์ในสาขาวิชา [cite: 2025-05-30]</p>
                </div>
                <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> เพิ่มอาจารย์ใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-20 p-3 mb-4 bg-white">
                <form method="GET" class="row g-2">
                    <div class="col-md-9 col-lg-10">
                        <input type="text" name="search" class="form-control search-box" placeholder="ค้นหาชื่ออาจารย์, ตำแหน่ง หรือความเชี่ยวชาญ..." value="<?= htmlspecialchars($search) ?>">
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
                                    <th class="ps-4 py-3" width="100">รูปภาพ</th>
                                    <th class="py-3">ชื่อ-นามสกุล</th>
                                    <th class="py-3" width="180">ตำแหน่งวิชาการ</th>
                                    <th class="py-3">ความเชี่ยวชาญ</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($teachers)): ?>
                                    <?php foreach ($teachers as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="portrait-preview-sm">
                                                    <?php 
                                                    $img_path = "../../uploads/teachers/" . $row['image'];
                                                    if (!empty($row['image']) && file_exists($img_path)): ?>
                                                        <img src="<?= $img_path ?>" class="w-100 h-100 shadow-sm" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="no-img-portrait d-flex align-items-center justify-content-center bg-light border">
                                                            <i class="fa-solid fa-user-tie text-muted opacity-50"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                                    <?= mb_strimwidth(strip_tags($row['education']), 0, 60, "...") ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light text-dark border px-3 fw-normal">
                                                    <?= htmlspecialchars($row['position']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= mb_strimwidth($row['expertise'], 0, 60, "...") ?></small>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="btn-group gap-2">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="แก้ไข">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="ลบ">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลคณาจารย์</td></tr>
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
                            พบทั้งหมด <?= number_format($total_items) ?> ท่าน 
                            <span class="mx-2 text-light">|</span> 
                            จัดการรายชื่อบุคลากร ComTech [cite: 2025-05-30]
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex gap-2">
                                <button class="btn-nav disabled"><i class="fa-solid fa-chevron-left small"></i></button>
                                <button class="btn-nav disabled"><i class="fa-solid fa-chevron-right small"></i></button>
                            </div>
                            <select class="form-select form-select-sm rounded-12 border-light text-muted" style="width: 130px; cursor: pointer;">
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
// 🗑️ ยืนยันการลบแบบเพื่อนสนิท [cite: 2025-05-30]
function confirmDelete(id) {
    Swal.fire({
        title: 'ลบข้อมูลอาจารย์จริงดิ?',
        text: "ลบแล้วกู้คืนไม่ได้นะเพื่อน! [cite: 2025-05-31]",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        confirmButtonText: 'ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => { if (result.isConfirmed) { window.location.href = 'delete.php?id=' + id; } })
}

// ✨ แจ้งเตือนเมื่อลบสำเร็จ
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'deleted') {
    Swal.fire({ 
        title: 'สำเร็จ!', 
        text: 'กำจัดข้อมูลเรียบร้อยแล้วเพื่อนรัก [cite: 2025-05-30]', 
        icon: 'success', 
        timer: 1500, 
        showConfirmButton: false 
    });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
</body>
</html>