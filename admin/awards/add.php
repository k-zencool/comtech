<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = false; $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $award_date  = $_POST['award_date'];
    $organizer   = trim($_POST['organizer']);
    $description = $_POST['description']; // รับจาก CKEditor
    
    // 🛡️ ระบบจัดการอัปโหลดรูปภาพ
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/images/awards/";
        
        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = "award_" . time() . "." . $file_ext; // ตั้งชื่อใหม่กันชื่อซ้ำ
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $error = "อัปโหลดรูปไม่สำเร็จว่ะเพื่อน!";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO awards (title, award_date, organizer, description, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $award_date, $organizer, $description, $image_name]);
            $success = true;
        } catch (PDOException $e) { $error = "DB Error: " . $e->getMessage(); }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มรางวัล | ComTech Admin</title>
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

        /* 📸 ส่วน Preview รูปภาพ */
        .img-preview-container {
            width: 100%;
            height: 250px;
            border-radius: 15px;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fdfdfd;
            position: relative;
        }
        .img-preview-container img { width: 100%; height: 100%; object-fit: cover; display: none; }
        
        .ck-editor__editable { min-height: 300px; border-radius: 0 0 12px 12px !important; }
        .ck-toolbar { border-radius: 12px 12px 0 0 !important; border: 1px solid #eee !important; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>เพิ่มรางวัลใหม่</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border btn-sm">กลับหน้าหลัก</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-15 border-0 shadow-sm mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data"> <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-stretch p-4">
                        <h5 class="fw-bold mb-4">ข้อมูลรางวัล</h5>
                        <div class="mb-4">
                            <label class="fw-bold text-muted small mb-2">ชื่อรางวัลที่ได้รับ</label>
                            <input type="text" name="title" class="form-control form-control-custom" placeholder="เช่น ชนะเลิศอันดับ 1 การแข่งขันทักษะ..." required>
                        </div>
                        <div class="mb-0 flex-grow-1">
                            <label class="fw-bold text-muted small mb-2">รายละเอียดรางวัล / รายชื่อนักศึกษาที่ได้รับ</label>
                            <textarea name="description" id="editor"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-stretch p-4">
                        <h5 class="fw-bold mb-4">รูปภาพและวันที่</h5>
                        
                        <div class="mb-4">
                            <label class="fw-bold text-muted small mb-2">รูปภาพผลงาน / เกียรติบัตร</label>
                            <div class="img-preview-container mb-3" id="previewBox">
                                <i class="fa-solid fa-image fa-4x text-light" id="uploadIcon"></i>
                                <img id="imagePreview">
                            </div>
                            <input type="file" name="image" id="imageInput" class="form-control form-control-custom" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-muted small mb-2">วันที่ได้รับรางวัล</label>
                            <input type="date" name="award_date" class="form-control form-control-custom" required>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold text-muted small mb-2">หน่วยงานที่มอบรางวัล</label>
                            <input type="text" name="organizer" class="form-control form-control-custom" placeholder="เช่น สอศ. หรือ อวท.">
                        </div>

                        <div class="mt-auto pt-4">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">
                                <i class="fa-solid fa-save me-2"></i> บันทึกข้อมูลรางวัล
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 📸 ฟังก์ชัน Preview รูปภาพทันทีที่เลือก
    document.getElementById('imageInput').onchange = evt => {
        const [file] = document.getElementById('imageInput').files
        if (file) {
            const preview = document.getElementById('imagePreview');
            const icon = document.getElementById('uploadIcon');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            icon.style.display = 'none';
        }
    }

    // ✨ เรียกใช้งาน CKEditor 5
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        })
        .catch(error => { console.error(error); });

    <?php if($success): ?>
        Swal.fire({ 
            title: 'บันทึกสำเร็จ!', 
            text: 'เพิ่มรางวัลใหม่ลงในระบบเรียบร้อย', 
            icon: 'success', 
            timer: 1500, 
            showConfirmButton: false 
        }).then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>