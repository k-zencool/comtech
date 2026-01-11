<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = false;
$error = "";

// 2. ดึงข้อมูลเดิมมาโชว์
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. จัดการเมื่อมีการกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name  = trim($_POST['project_name']);
    $student_names = trim($_POST['student_names']);
    $advisor       = trim($_POST['advisor']);
    $academic_year = $_POST['academic_year'];
    $description   = $_POST['description'];
    $image_name    = $project['image']; 
    $upload_ok     = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $sub_dir = date('Y/m'); 
        $upload_base = __DIR__ . '/../../uploads/projects/';
        $upload_dir = $upload_base . $sub_dir . '/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = "proj_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                // ลบรูปเก่าทิ้ง
                if (!empty($project['image']) && file_exists($upload_base . $project['image'])) {
                    unlink($upload_base . $project['image']);
                }
                $image_name = $sub_dir . '/' . $new_name;
            }
        } else {
            $error = "รองรับเฉพาะไฟล์รูปภาพเท่านั้นนะเพื่อน";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($project_name)) {
        try {
            $stmt = $pdo->prepare("UPDATE projects SET project_name = ?, student_names = ?, advisor = ?, academic_year = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$project_name, $student_names, $advisor, $academic_year, $description, $image_name, $id]);
            $success = true;
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผลงานนักศึกษา | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* 🛠 สไตล์จัดก้นเท่ากันเป๊ะ */
        .equal-height-row { display: flex; flex-wrap: wrap; }
        .equal-height-row > [class*='col-'] { display: flex; flex-direction: column; }
        .card-stretch { flex: 1; display: flex; flex-direction: column; border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; }
        
        .preview-container {
            width: 100%; height: 250px;
            border: 2px dashed #ddd; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: #fafafa; position: relative;
        }
        .preview-container img { width: 100%; height: 100%; object-fit: cover; z-index: 1; }
        .btn-remove-img {
            position: absolute; top: 15px; right: 15px;
            background: #ff4d4d; color: white; border: none;
            border-radius: 50%; width: 35px; height: 35px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .sticky-side { position: sticky; top: 20px; }
        .ck-editor__editable { min-height: 400px; border-radius: 0 0 12px 12px !important; flex-grow: 1; }
        .ck-toolbar { border-radius: 12px 12px 0 0 !important; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>แก้ไขผลงานนักศึกษา</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border btn-sm text-muted">
                <i class="fa-solid fa-chevron-left me-1"></i> กลับ
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-15 mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-stretch p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-file-signature me-2 text-muted"></i>ชื่อโครงงาน/โปรเจกต์</label>
                            <input type="text" name="project_name" class="form-control form-control-lg rounded-12" value="<?= htmlspecialchars($project['project_name']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-users me-2 text-muted"></i>ผู้จัดทำ</label>
                            <textarea name="student_names" class="form-control rounded-12" rows="2" placeholder="เช่น นายสมชาย สายคอม, นายสมหญิง ไอที"><?= htmlspecialchars($project['student_names']) ?></textarea>
                        </div>
                        <div class="mb-0 flex-grow-1 d-flex flex-column">
                            <label class="form-label fw-bold"><i class="fa-solid fa-align-left me-2 text-muted"></i>รายละเอียดโครงงาน</label>
                            <textarea name="description" id="editor"><?= $project['description'] ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-stretch p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-calendar-days me-2 text-muted"></i>ปีการศึกษา</label>
                                <input type="number" name="academic_year" class="form-control rounded-12" value="<?= htmlspecialchars($project['academic_year']) ?>" placeholder="เช่น 2567">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user-tie me-2 text-muted"></i>อาจารย์ที่ปรึกษา</label>
                                <input type="text" name="advisor" class="form-control rounded-12" value="<?= htmlspecialchars($project['advisor']) ?>" placeholder="ระบุชื่ออาจารย์">
                            </div>
                            <hr class="opacity-10 my-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปภาพพรีวิว (แนวนอน)</label>
                                <div class="preview-container mb-3" id="previewBox">
                                    <?php 
                                        $img_src = "../../uploads/projects/" . ($project['image'] ?? '');
                                        $has_img = (!empty($project['image']) && file_exists($img_src));
                                    ?>
                                    <button type="button" class="btn-remove-img" id="btnDelImg" style="<?= !$has_img ? 'display:none;' : '' ?>" title="ลบรูปภาพ">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted" id="placeholderIcon" style="<?= $has_img ? 'display:none;' : '' ?>"></i>
                                    <img src="<?= $has_img ? $img_src : '' ?>" id="imgPreview" style="<?= $has_img ? 'display:block;' : 'display:none;' ?>">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control rounded-12" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow-sm mt-auto">
                                <i class="fa-solid fa-floppy-disk me-2"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 🎨 CKEditor 5
    ClassicEditor.create(document.querySelector('#editor')).catch(error => console.error(error));

    const imageInput = document.getElementById('imageInput');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const btnDelImg = document.getElementById('btnDelImg');

    // 📸 Preview รูปใหม่
    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            placeholderIcon.style.display = 'none';
            btnDelImg.style.display = 'none';
        }
    };

    // 🗑️ ลบรูปภาพผ่าน AJAX
    if (btnDelImg) {
        btnDelImg.onclick = () => {
            Swal.fire({
                title: 'ลบรูปโปรเจกต์?',
                text: "รูปจะหายไปถาวรนะเพื่อน! [cite: 2025-05-31]",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_image.php?id=<?= $id ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            imgPreview.style.display = 'none';
                            placeholderIcon.style.display = 'block';
                            btnDelImg.style.display = 'none';
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: 'ลบรูปภาพเรียบร้อยแล้วเพื่อน [cite: 2025-05-30]',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        };
    }

    // ✨ แจ้งเตือนบันทึกสำเร็จ
    <?php if($success): ?>
        Swal.fire({ 
            title: 'สำเร็จ!', 
            text: 'อัปเดตข้อมูลโครงงานแล้วเพื่อน [cite: 2025-07-09]', 
            icon: 'success', 
            timer: 1500, 
            showConfirmButton: false 
        }).then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>