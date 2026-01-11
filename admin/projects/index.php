<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// --- 🛠️ ระบบค้นหาและแบ่งหน้า (Search Logic) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $where = "WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (project_name LIKE :search OR student_names LIKE :search OR advisor LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // ดึงข้อมูลโปรเจกต์ทั้งหมดตามเงื่อนไข
    $stmt = $pdo->prepare("SELECT * FROM projects $where ORDER BY academic_year DESC, created_at DESC");
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
    $total_items = count($projects);
} catch (PDOException $e) {
    $projects = [];
    $total_items = 0;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผลงานนักศึกษา | ComTech Admin</title>
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
                    <h3 class="fw-bold m-0"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>จัดการผลงานนักศึกษา</h3>
                    <p class="text-muted small m-0">คลังโครงงานและโปรเจกต์จบการศึกษา [cite: 2025-05-30]</p>
                </div>
                <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> เพิ่มผลงานใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-20 p-3 mb-4 bg-white">
                <form method="GET" class="row g-2">
                    <div class="col-md-9 col-lg-10">
                        <input type="text" name="search" class="form-control search-box" placeholder="ค้นหาชื่อโครงงาน, ผู้จัดทำ หรืออาจารย์ที่ปรึกษา..." value="<?= htmlspecialchars($search) ?>">
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
                                    <th class="ps-4 py-3" width="150">รูปพรีวิว</th>
                                    <th class="py-3">ชื่อโครงงาน / ผู้จัดทำ</th>
                                    <th class="py-3" width="130">ปีการศึกษา</th>
                                    <th class="py-3" width="180">อาจารย์ที่ปรึกษา</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($projects)): ?>
                                    <?php foreach ($projects as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="preview-box-sm" style="width: 120px; height: 70px;">
                                                    <?php 
                                                    $img_path = "../../uploads/projects/" . $row['image'];
                                                    if (!empty($row['image']) && file_exists($img_path)): ?>
                                                        <img src="<?= $img_path ?>" class="w-100 h-100 shadow-sm" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-image text-muted opacity-30"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark mb-1 text-truncate" style="max-width: 300px;">
                                                    <?= htmlspecialchars($row['project_name']) ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="fa-solid fa-user-graduate me-1"></i> 
                                                    <?= mb_strimwidth(htmlspecialchars($row['student_names']), 0, 60, "...") ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light text-dark border px-3 fw-normal">
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
                                    <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลผลงานนักศึกษาที่ค้นหาเพื่อน! [cite: 2025-05-31]</td></tr>
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
                            พบทั้งหมด <?= number_format($total_items) ?> โครงงาน <span class="mx-2 text-light">|</span> คลังผลงานนักศึกษา ComTech [cite: 2025-05-30]
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
// 🗑️ ยืนยันการลบ
function confirmDelete(id) {
    Swal.fire({
        title: 'ลบโปรเจกต์นี้จริงดิ?',
        text: "ลบแล้วไฟล์รูปพรีวิวจะโดนทำลายไปด้วยนะเพื่อน! [cite: 2025-05-31]",
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
        title: 'กำจัดเรียบร้อย!', 
        text: 'ผลงานถูกลบออกจากคลังแล้วเพื่อนรัก [cite: 2025-05-30]', 
        icon: 'success', 
        timer: 1500, 
        showConfirmButton: false 
    });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
</body>
</html>