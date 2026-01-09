<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. ดึงข้อมูลข่าวสารทั้งหมด
try {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $newsList = $stmt->fetchAll();
} catch (PDOException $e) {
    $newsList = [];
    $error_msg = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข่าวสาร | ComTech Admin</title>
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
                    <h3 class="fw-bold m-0"><i class="fa-solid fa-newspaper me-2 text-primary"></i>จัดการข่าวสาร</h3>
                    <p class="text-muted small m-0">ระบบจัดเก็บรูปภาพแบบแยกโฟลเดอร์รายเดือน</p>
                </div>
                <a href="add.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> เพิ่มข่าวใหม่
                </a>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3" width="120">รูปภาพ</th>
                                    <th class="py-3">หัวข้อข่าว</th>
                                    <th class="py-3" width="150">หมวดหมู่</th>
                                    <th class="py-3" width="150">วันที่ลงข่าว</th>
                                    <th class="pe-4 py-3 text-center" width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($newsList)): ?>
                                    <?php foreach ($newsList as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php
                                                // แก้ Path ตรงนี้เพื่อน ชี้ไปที่โฟลเดอร์ uploads/news/
                                                $img_path = "../../uploads/news/" . $row['image'];
                                                $has_img = (!empty($row['image']) && file_exists($img_path));
                                                ?>
                                                <div class="preview-box-sm">
                                                    <?php if ($has_img): ?>
                                                        <img src="<?= $img_path ?>" class="rounded-3 shadow-sm" width="80" height="50" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="no-img-icon">
                                                            <i class="fa-solid fa-image text-muted" style="opacity:0.4;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['topic']) ?></div>
                                                <small class="text-muted"><?= mb_strimwidth(strip_tags($row['content']), 0, 60, "...") ?></small>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light text-dark border px-3">
                                                    <?= htmlspecialchars($row['category']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fa-regular fa-calendar-check me-1"></i>
                                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                                </small>
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
                                        <td colspan="5" class="text-center py-5 text-muted">ยังไม่มีข้อมูลข่าวสาร</td>
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
    // แจ้งเตือนยืนยันการลบ
    function confirmDelete(id) {
        Swal.fire({
            title: 'ลบข่าวนี้แน่นะเพื่อน?',
            text: "รูปภาพในโฟลเดอร์ uploads จะถูกลบออกไปด้วยนะ!",
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

    // เช็คสถานะหลังลบสำเร็จ
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'deleted') {
        Swal.fire({
            title: 'ลบเรียบร้อย!',
            text: 'ข้อมูลและไฟล์รูปภาพถูกกำจัดแล้ว',
            icon: 'success',
            confirmButtonColor: '#1a1a1a'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    </script>
</body>

</html>