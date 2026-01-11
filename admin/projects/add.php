<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = false; $error = "";

// จำค่าเดิมกรณี Error
$old_project_name = $_POST['project_name'] ?? "";
$old_student_names = $_POST['student_names'] ?? "";
$old_advisor = $_POST['advisor'] ?? "";
$old_academic_year = $_POST['academic_year'] ?? (date('Y') + 543);
$old_description = $_POST['description'] ?? "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = trim($_POST['project_name']);
    $student_names = trim($_POST['student_names']);
    $advisor = trim($_POST['advisor']);
    $academic_year = $_POST['academic_year'];
    $description = $_POST['description'];
    
    $image_name = "";
    $upload_ok = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $sub_dir = date('Y/m'); 
        $upload_base = __DIR__ . '/../../uploads/projects/';
        $upload_dir = $upload_base . $sub_dir . '/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = "proj_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                $image_name = $sub_dir . '/' . $new_name;
            } else {
                $error = "อัปโหลดรูปไม่สำเร็จ";
                $upload_ok = false;
            }
        } else {
            $error = "รองรับเฉพาะไฟล์รูปภาพเท่านั้น";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($project_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (project_name, student_names, advisor, academic_year, description, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$project_name, $student_names, $advisor, $academic_year, $description, $image_name]);
            $success = true;
        } catch (PDOException $e) { $error = "DB Error: " . $e->getMessage(); }
    } else if (empty($project_name)) {
        $error = "กรุณากรอกชื่อโครงงานด้วยเพื่อน!";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มผลงานนักศึกษา | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>เพิ่มผลงานนักศึกษา</h3>
                <p class="text-muted small m-0">บันทึกโครงงานและโปรเจกต์จบการศึกษา</p>
            </div>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border btn-sm">กลับ</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-15 border-0 shadow-sm mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-custom p-4 card-stretch">
                        <div class="card-body-left">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-file-signature me-2 text-muted"></i>ชื่อโครงงาน/โปรเจกต์</label>
                                <input type="text" name="project_name" class="form-control form-control-lg rounded-12" value="<?= htmlspecialchars($old_project_name) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-users me-2 text-muted"></i>ผู้จัดทำ</label>
                                <textarea name="student_names" class="form-control rounded-12" rows="2" placeholder="ชื่อผู้จัดทำ..."><?= htmlspecialchars($old_student_names) ?></textarea>
                            </div>
                            <div class="mb-0 flex-grow-1 d-flex flex-column">
                                <label class="form-label fw-bold"><i class="fa-solid fa-align-left me-2 text-muted"></i>รายละเอียดโครงงาน</label>
                                <textarea name="description" id="editor"><?= $old_description ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-custom p-4 card-stretch">
                            <div class="card-body-right">
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fa-solid fa-calendar-days me-2 text-muted"></i>ปีการศึกษา</label>
                                        <input type="number" name="academic_year" class="form-control rounded-12" value="<?= $old_academic_year ?>">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fa-solid fa-user-tie me-2 text-muted"></i>อาจารย์ที่ปรึกษา</label>
                                        <input type="text" name="advisor" class="form-control rounded-12" value="<?= htmlspecialchars($old_advisor) ?>">
                                    </div>

                                    <hr class="opacity-10 mb-4">

                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปภาพพรีวิว (แนวนอน)</label>
                                        <div class="preview-container mb-3" id="previewBox">
                                            <button type="button" class="btn-remove-preview" id="btnRemove"><i class="fa-solid fa-xmark"></i></button>
                                            <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted" id="placeholderIcon"></i>
                                            <img src="" id="imgPreview">
                                        </div>
                                        <input type="file" name="image" id="imageInput" class="form-control rounded-12" accept="image/*">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow-sm">
                                    <i class="fa-solid fa-save me-2"></i> บันทึกข้อมูลโครงงาน
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(error => console.error(error));

    const imageInput = document.getElementById('imageInput');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const btnRemove = document.getElementById('btnRemove');

    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            placeholderIcon.style.display = 'none';
            btnRemove.style.display = 'flex';
        }
    };

    btnRemove.onclick = () => {
        imageInput.value = "";
        imgPreview.style.display = 'none';
        placeholderIcon.style.display = 'block';
        btnRemove.style.display = 'none';
    };

    <?php if($success): ?>
        Swal.fire({ title: 'สำเร็จ!', text: 'เพิ่มผลงานนักศึกษาแล้วเพื่อน', icon: 'success', timer: 1500, showConfirmButton: false })
        .then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>