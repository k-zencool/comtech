<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// --- 🛠️ รับค่า Search และ Filter ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';

// --- 🛠️ ระบบแบ่งหน้า (Pagination Logic) ---
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // 🔍 สร้างเงื่อนไข Query
    $where = "WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $where .= " AND (title LIKE :search OR organizer LIKE :search)";
        $params[':search'] = "%$search%";
    }
    if ($filter_year !== '') {
        $where .= " AND YEAR(award_date) = :year";
        $params[':year'] = $filter_year;
    }

    // นับจำนวนทั้งหมดเพื่อคำนวณหน้า
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM awards $where");
    foreach ($params as $key => $val) {
        $total_stmt->bindValue($key, $val);
    }
    $total_stmt->execute();
    $total_items = $total_stmt->fetchColumn();
    $total_pages = ceil($total_items / $limit);

    // ดึงข้อมูลตามเงื่อนไขพร้อมแบ่งหน้า
    $stmt = $pdo->prepare("SELECT * FROM awards $where ORDER BY award_date DESC LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $awards = $stmt->fetchAll();

    // ดึงปีที่มีรางวัลทั้งหมดมาทำ Dropdown
    $years_stmt = $pdo->query("SELECT DISTINCT YEAR(award_date) as y FROM awards ORDER BY y DESC");
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $awards = [];
    $total_pages = 0;
    $total_items = 0;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการรางวัล | ComTech Admin</title>
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
                        <h3 class="fw-bold m-0"><i class="fa-solid fa-trophy me-2 text-warning"></i>จัดการรางวัล</h3>
                        <p class="text-muted small m-0">บันทึกผลงานและความภูมิใจ [cite: 2025-05-30]</p>
                    </div>
                    <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow">
                        <i class="fa-solid fa-plus me-2"></i> เพิ่มรางวัล
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-20 p-3 mb-5 bg-white">
                    <form method="GET" class="row g-2">
                        <div class="col-md-6 col-lg-7">
                            <input type="text" name="search" class="form-control search-box" placeholder="ค้นหาชื่อรางวัลหรือหน่วยงาน..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <select name="year" class="form-select search-box" onchange="this.form.submit()">
                                <option value="">ทุกปี พ.ศ.</option>
                                <?php foreach ($available_years as $y): ?>
                                    <option value="<?= $y ?>" <?= ($filter_year == $y) ? 'selected' : '' ?>>ปี <?= $y + 543 ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-15 fw-500 h-100">
                                <i class="fa-solid fa-magnifying-glass me-2"></i>ค้นหา
                            </button>
                        </div>
                    </form>
                </div>

                <div class="row g-4">
                    <?php if (!empty($awards)): ?>
                        <?php foreach ($awards as $key => $item): ?>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card award-card" style="animation-delay: <?= $key * 0.1 ?>s;">
                                    <div class="award-img-wrapper">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../../assets/images/awards/<?= $item['image'] ?>"
                                                class="award-img" id="img-<?= $item['id'] ?>"
                                                onerror="replaceWithIcon(<?= $item['id'] ?>)">
                                        <?php endif; ?>

                                        <div id="icon-<?= $item['id'] ?>" class="placeholder-icon <?= (!empty($item['image'])) ? 'd-none' : '' ?>">
                                            <i class="fa-solid fa-trophy fa-4x mb-2"></i>
                                            <span class="small text-muted fw-bold">AWARDS</span>
                                        </div>

                                        <span class="award-badge">ปี <?= date('Y', strtotime($item['award_date'])) + 543 ?></span>

                                        <div class="btn-action-overlay">
                                            <a href="edit.php?id=<?= $item['id'] ?>" class="btn-circle-edit" title="แก้ไข">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            <button onclick="confirmDelete(<?= $item['id'] ?>)" class="btn-circle-del" title="ลบ">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body border-top">
                                        <h6 class="fw-bold award-title mb-2 text-truncate" title="<?= htmlspecialchars($item['title']) ?>"><?= htmlspecialchars($item['title']) ?></h6>
                                        <p class="text-muted small mb-0 text-truncate"><i class="fa-solid fa-university me-1"></i> <?= htmlspecialchars($item['organizer']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fa-solid fa-magnifying-glass fa-4x mb-3 text-light"></i>
                            <h5 class="text-muted">ไม่พบข้อมูลที่ค้นหาเพื่อนรัก [cite: 2025-05-30]</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($total_items > 0): ?>
                <div class="footer-pagination-sticky">
                    <div class="card footer-card shadow-sm border-0">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted small fw-600">
                                    พบ <?= number_format($total_items) ?> รายการ <span class="mx-2 text-light">|</span> หน้า <?= $page ?> / <?= $total_pages ?>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex gap-2">
                                        <?php $query_str = "&search=" . urlencode($search) . "&year=" . urlencode($filter_year); ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function replaceWithIcon(id) {
            const img = document.getElementById('img-' + id);
            const icon = document.getElementById('icon-' + id);
            if (img) img.style.display = 'none';
            if (icon) icon.classList.remove('d-none');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'จะลบจริงดิ?',
                text: "ประวัติศาสตร์ความภูมิใจหายหมดนะเพื่อน! [cite: 2025-05-31]",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete.php?id=' + id;
                }
            })
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'deleted') {
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'ลบข้อมูลเรียบร้อยแล้วเพื่อน [cite: 2025-05-30]',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>

</html>