<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// --- 🛠️ Logic Search & Pagination ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    $where = "WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (topic LIKE :search OR category LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM news $where");
    foreach($params as $key => $val) { $total_stmt->bindValue($key, $val); }
    $total_stmt->execute();
    $total_items = $total_stmt->fetchColumn();
    $total_pages = ceil($total_items / $limit);

    $stmt = $pdo->prepare("SELECT * FROM news $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    foreach($params as $key => $val) { $stmt->bindValue($key, $val); }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $newsList = $stmt->fetchAll();
} catch (PDOException $e) { $newsList = []; $total_pages = 0; $total_items = 0; }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข่าวสาร | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold m-0"><i class="fa-solid fa-newspaper me-2 text-primary"></i>จัดการข่าวสาร</h3>
                    <p class="text-muted small m-0">อัปเดตข่าวสารแผนกคอมพิวเตอร์ธุรกิจ [cite: 2025-05-30]</p>
                </div>
                <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> เพิ่มข่าวใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-20 p-3 mb-5 bg-white">
                <form method="GET" class="row g-2">
                    <div class="col-md-9 col-lg-10">
                        <input type="text" name="search" class="form-control search-box" placeholder="ค้นหาหัวข้อข่าว หรือหมวดหมู่..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-15 fw-500 h-100">ค้นหา</button>
                    </div>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-20 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 py-3" width="120">รูปภาพ</th>
                                    <th class="py-3">หัวข้อข่าว</th>
                                    <th class="py-3" width="150">หมวดหมู่</th>
                                    <th class="py-3" width="150">วันที่</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($newsList)): ?>
                                    <?php foreach ($newsList as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="preview-box-sm">
                                                    <?php 
                                                    $img_path = "../../uploads/news/" . $row['image'];
                                                    if (!empty($row['image']) && file_exists($img_path)): ?>
                                                        <img src="<?= $img_path ?>" class="w-100 h-100" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-image text-muted opacity-30"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 350px;"><?= htmlspecialchars($row['topic']) ?></div>
                                                <small class="text-muted"><?= mb_strimwidth(strip_tags($row['content']), 0, 70, "...") ?></small>
                                            </td>
                                            <td><span class="badge rounded-pill bg-light text-dark border px-3"><?= htmlspecialchars($row['category']) ?></span></td>
                                            <td><small class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></small></td>
                                            <td class="pe-4 text-center">
                                                <div class="btn-group gap-2">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3"><i class="fa-solid fa-pen"></i></a>
                                                    <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลที่มึงค้นหาเพื่อน! [cite: 2025-05-31]</td></tr>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small fw-600">
                            พบ <?= number_format($total_items) ?> รายการ <span class="mx-2 text-light">|</span> หน้า <?= $page ?> / <?= $total_pages ?>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex gap-2">
                                <?php $query_str = "&search=" . urlencode($search); ?>
                                <a href="?page=<?= $page - 1 ?><?= $query_str ?>" class="btn-nav <?= ($page <= 1) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left small"></i></a>
                                <a href="?page=<?= $page + 1 ?><?= $query_str ?>" class="btn-nav <?= ($page >= $total_pages) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-right small"></i></a>
                            </div>
                            <select class="form-select form-select-sm rounded-12 border-light text-muted" style="width: 110px;">
                                <option><?= $limit ?>/หน้า</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'ลบข่าวนี้จริงดิ?',
        text: "ลบแล้วรูปหายเกลี้ยงนะเพื่อน! [cite: 2025-05-31]",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        confirmButtonText: 'ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => { if (result.isConfirmed) { window.location.href = 'delete.php?id=' + id; } })
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'deleted') {
    Swal.fire({ title: 'สำเร็จ!', text: 'กำจัดข่าวเรียบร้อยแล้ว [cite: 2025-05-30]', icon: 'success', timer: 1500, showConfirmButton: false });
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
</body>
</html>