<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// --- 🛠️ ระบบค้นหา ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $where = "WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (subject_code LIKE :search OR subject_name LIKE :search OR category LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $stmt = $pdo->prepare("SELECT * FROM curriculum $where ORDER BY level ASC, year ASC, subject_code ASC");
    $stmt->execute($params);
    $raw_data = $stmt->fetchAll();
    
    $curriculum_data = [];
    foreach ($raw_data as $row) {
        $key = $row['level'] . $row['year'];
        $curriculum_data[$key][] = $row;
    }
} catch (PDOException $e) { $curriculum_data = []; $raw_data = []; }

$levels = ['ปวช.1', 'ปวช.2', 'ปวช.3', 'ปวส.1', 'ปวส.2'];
$total_items = count($raw_data);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหลักสูตร | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
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
                    <h3 class="fw-bold m-0"><i class="fa-solid fa-book-bookmark me-2 text-primary"></i>จัดการหลักสูตร</h3>
                    <p class="text-muted small m-0">ข้อมูลวิชาเรียนแยกตามแผนการเรียน [cite: 2025-05-30]</p>
                </div>
                <a href="#" id="addBtnWithTab" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus-circle me-2"></i> เพิ่มรายวิชา
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-20 p-3 mb-4 bg-white">
                <form method="GET" class="row g-2">
                    <div class="col-md-9 col-lg-10">
                        <input type="text" name="search" class="form-control search-box" placeholder="ค้นหารหัสวิชา หรือชื่อวิชา..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-15 fw-500 h-100">ค้นหา</button>
                    </div>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-20 p-2 mb-4 bg-white">
                <ul class="nav nav-pills nav-fill" id="curriculumTab" role="tablist">
                    <?php foreach($levels as $index => $lv): ?>
                    <li class="nav-item">
                        <button class="nav-link curriculum-nav <?= $index === 0 ? 'active' : '' ?>" id="btn-tab-<?= $index ?>" data-bs-toggle="pill" data-bs-target="#tab-<?= $index ?>" type="button" onclick="saveTab('tab-<?= $index ?>')"><?= $lv ?></button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tab-content">
                <?php foreach($levels as $index => $lv): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="tab-<?= $index ?>">
                    <div class="card card-table shadow-sm overflow-hidden border-0 rounded-20">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle m-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 col-code">รหัสวิชา</th>
                                            <th class="py-3">ชื่อรายวิชา</th>
                                            <th class="py-3 text-center" width="100">ท-ป-น</th>
                                            <th class="py-3">หมวดวิชา</th>
                                            <th class="pe-4 py-3 text-center" width="140">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($curriculum_data[$lv])): ?>
                                            <?php foreach($curriculum_data[$lv] as $subject): ?>
                                            <tr>
                                                <td class="ps-4"><span class="subject-code-tag"><?= htmlspecialchars($subject['subject_code']) ?></span></td>
                                                <td><div class="fw-bold text-dark"><?= htmlspecialchars($subject['subject_name']) ?></div></td>
                                                <td class="text-center"><span class="badge bg-light text-dark border fw-normal px-2"><?= $subject['credits'] ?></span></td>
                                                <td><small class="text-muted"><?= $subject['category'] ?></small></td>
                                                <td class="pe-4 text-center">
                                                    <div class="btn-group gap-2">
                                                        <a href="edit.php?id=<?= $subject['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3"><i class="fa-solid fa-pen"></i></a>
                                                        <button onclick="confirmDelete(<?= $subject['id'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fa-solid fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลของ <?= $lv ?></td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($total_items > 0): ?>
        <div class="footer-pagination-sticky">
            <div class="card footer-card shadow-sm border-0">
                <div class="card-body px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="text-muted small fw-600">
                            พบทั้งหมด <?= number_format($total_items) ?> รายวิชา <span class="mx-2 text-light">|</span> จัดการข้อมูลหลักสูตร [cite: 2025-05-30]
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex gap-2">
                                <button class="btn-nav disabled"><i class="fa-solid fa-chevron-left small"></i></button>
                                <button class="btn-nav disabled"><i class="fa-solid fa-chevron-right small"></i></button>
                            </div>
                            <select class="form-select form-select-sm rounded-12 border-light text-muted" style="width: 130px;">
                                <option>แสดงทั้งหมด</option>
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
    function saveTab(tabId) { localStorage.setItem('activeCurriculumTab', tabId); }
    document.addEventListener("DOMContentLoaded", function() {
        const activeTab = localStorage.getItem('activeCurriculumTab');
        if (activeTab) {
            document.querySelectorAll('.nav-link, .tab-pane').forEach(el => el.classList.remove('active', 'show'));
            const btn = document.querySelector(`[data-bs-target="#${activeTab}"]`);
            const pane = document.getElementById(activeTab);
            if (btn && pane) { btn.classList.add('active'); pane.classList.add('show', 'active'); }
        }
        const addBtn = document.getElementById('addBtnWithTab');
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentTabId = localStorage.getItem('activeCurriculumTab') || 'tab-0';
                const tabIndex = currentTabId.split('-')[1];
                window.location.href = `add.php?t=${tabIndex}`;
            });
        }
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'deleted') {
            Swal.fire({ title: 'สำเร็จ!', text: 'ลบเรียบร้อยเพื่อน!', icon: 'success', timer: 1500, showConfirmButton: false });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
    function confirmDelete(id) {
        Swal.fire({
            title: 'ลบวิชานี้จริงดิ?', icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#1a1a1a', confirmButtonText: 'ลบเลย', cancelButtonText: 'ยกเลิก', reverseButtons: true
        }).then((result) => { if (result.isConfirmed) { window.location.href = 'delete.php?id=' + id; } })
    }
</script>
</body>
</html>