<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

$success = false; $error = "";

// 2. ดึงข้อมูลวิชาเดิมมาโชว์
try {
    $stmt = $pdo->prepare("SELECT * FROM curriculum WHERE id = ?");
    $stmt->execute([$id]);
    $subject = $stmt->fetch();
    if (!$subject) { header("Location: index.php"); exit(); }
} catch (PDOException $e) { die($e->getMessage()); }

// 3. จัดการเมื่อกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $credits      = trim($_POST['credits']);
    $category     = trim($_POST['category']);
    $level        = $_POST['level'];
    $year         = $_POST['year'];
    $description  = $_POST['description']; // รับจาก CKEditor

    try {
        $stmt = $pdo->prepare("UPDATE curriculum SET subject_code = ?, subject_name = ?, credits = ?, category = ?, level = ?, year = ?, description = ? WHERE id = ?");
        $stmt->execute([$subject_code, $subject_name, $credits, $category, $level, $year, $description, $id]);
        $success = true;
    } catch (PDOException $e) { 
        $error = "เกิดข้อผิดพลาด: " . $e->getMessage(); 
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขรายวิชา | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        /* 🛠 ระบบดึงก้นให้เท่ากันเป๊ะ */
        .equal-height-row { display: flex; flex-wrap: wrap; }
        .equal-height-row > [class*='col-'] { display: flex; flex-direction: column; }
        .card-stretch { flex: 1; display: flex; flex-direction: column; border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; }
        
        .form-control-custom { border-radius: 12px; padding: 12px; border: 1px solid #eee; transition: 0.3s; }
        .form-control-custom:focus { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.05); }

        .ck-editor__editable { min-height: 250px; border-radius: 0 0 12px 12px !important; }
        .ck-toolbar { border-radius: 12px 12px 0 0 !important; border: 1px solid #eee !important; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-user-pen me-2 text-warning"></i>แก้ไขรายวิชา</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border btn-sm">กลับ</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-15 border-0 shadow-sm mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-stretch p-4">
                        <h5 class="fw-bold mb-4">รายละเอียดวิชา</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="fw-bold text-muted small">รหัสวิชา</label>
                                <input type="text" name="subject_code" class="form-control form-control-custom" value="<?= htmlspecialchars($subject['subject_code']) ?>" required>
                            </div>
                            <div class="col-md-8">
                                <label class="fw-bold text-muted small">ชื่อรายวิชา</label>
                                <input type="text" name="subject_name" class="form-control form-control-custom" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-0 flex-grow-1">
                            <label class="fw-bold text-muted small mb-2">คำอธิบายรายวิชา (จัดย่อหน้าได้ที่นี่)</label>
                            <textarea name="description" id="editor"><?= htmlspecialchars($subject['description']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-stretch p-4">
                        <h5 class="fw-bold mb-4">แผนการเรียน</h5>
                        
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">ระดับชั้น</label>
                            <select name="level" class="form-select form-control-custom">
                                <option value="ปวช." <?= ($subject['level'] == "ปวช.") ? 'selected' : '' ?>>ปวช.</option>
                                <option value="ปวส." <?= ($subject['level'] == "ปวส.") ? 'selected' : '' ?>>ปวส.</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">ชั้นปีที่เรียน</label>
                            <select name="year" class="form-select form-control-custom">
                                <option value="1" <?= ($subject['year'] == "1") ? 'selected' : '' ?>>ปี 1</option>
                                <option value="2" <?= ($subject['year'] == "2") ? 'selected' : '' ?>>ปี 2</option>
                                <option value="3" <?= ($subject['year'] == "3") ? 'selected' : '' ?>>ปี 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">หน่วยกิต (ท-ป-น)</label>
                            <input type="text" name="credits" class="form-control form-control-custom" value="<?= htmlspecialchars($subject['credits']) ?>">
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold text-muted small">หมวดวิชา</label>
                            <input type="text" name="category" class="form-control form-control-custom" value="<?= htmlspecialchars($subject['category']) ?>">
                        </div>

                        <div class="mt-auto">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">
                                <i class="fa-solid fa-check-double me-2"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // เรียกใช้งาน CKEditor 5 ให้เหมือนหน้า Add
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        })
        .catch(error => { console.error(error); });

    <?php if($success): ?>
        Swal.fire({ 
            title: 'สำเร็จ!', 
            text: 'แก้ไขข้อมูลวิชาเรียบร้อยแล้ว', 
            icon: 'success', 
            timer: 1500, 
            showConfirmButton: false 
        }).then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>