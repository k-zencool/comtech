<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์
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
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();
    if (!$news) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. จัดการเมื่อมีการกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic = trim($_POST['topic']);
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image_name = $news['image']; 
    $upload_ok = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $sub_dir = date('Y/m'); 
        $upload_base = __DIR__ . '/../../uploads/news/'; // บ้านใหม่ของเรา
        $upload_dir = $upload_base . $sub_dir . '/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_name = "news_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
            // ลบรูปเก่าทิ้ง (ใน DB เก็บ Path เต็มไว้อยู่แล้ว เช่น 2026/01/news_xxx.jpg)
            if (!empty($news['image']) && file_exists($upload_base . $news['image'])) {
                unlink($upload_base . $news['image']);
            }
            $image_name = $sub_dir . '/' . $new_name;
        } else {
            $error = "อัปโหลดรูปไม่สำเร็จว่ะเพื่อน";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($topic) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("UPDATE news SET topic = ?, content = ?, category = ?, image = ? WHERE id = ?");
            $stmt->execute([$topic, $content, $category, $image_name, $id]);
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
    <title>แก้ไขข่าวสาร | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable { min-height: 450px !important; border-radius: 0 0 15px 15px !important; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .preview-container {
            width: 100%; height: 280px;
            border: 2px dashed #ddd; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: #fafafa; margin-bottom: 15px;
            position: relative;
        }
        .preview-container img { width: 100%; height: 100%; object-fit: cover; z-index: 1; }
        .placeholder-icon { position: absolute; color: #ccc; z-index: 0; }
        
        .btn-remove-img {
            position: absolute; top: 10px; right: 10px;
            background: #ff4d4d; color: white; border: none;
            border-radius: 50%; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.3s; z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .btn-remove-img:hover { background: red; transform: scale(1.1); }
        .rounded-12 { border-radius: 12px !important; }
        .rounded-15 { border-radius: 15px !important; }
        .sticky-side { position: sticky; top: 20px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>แก้ไขข่าวสาร</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 btn-sm border text-muted">
                <i class="fa-solid fa-chevron-left me-1"></i> กลับหน้ารายการ
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-15 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-heading me-2 text-muted"></i>หัวข้อข่าว</label>
                            <input type="text" name="topic" class="form-control form-control-lg rounded-12" value="<?= htmlspecialchars($news['topic']) ?>" required>
                        </div>
                        <div class="mb-0 d-flex flex-column flex-grow-1">
                            <label class="form-label fw-bold"><i class="fa-solid fa-pen-nib me-2 text-muted"></i>เนื้อหาข่าว</label>
                            <textarea name="content" id="editor"><?= $news['content'] ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-custom p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-tags me-2 text-muted"></i>หมวดหมู่</label>
                                <select name="category" class="form-select rounded-12">
                                    <?php $cats = ['ประชาสัมพันธ์', 'กิจกรรม', 'วิชาการ', 'ประกาศ']; ?>
                                    <?php foreach($cats as $c): ?>
                                        <option value="<?= $c ?>" <?= ($news['category'] == $c) ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปภาพประกอบ</label>
                                <div class="preview-container" id="previewBox">
                                    <?php 
                                        // ปรับ Path ให้ดึงจาก uploads/news/
                                        $img_src = "../../uploads/news/" . ($news['image'] ?? '');
                                        $has_img = (!empty($news['image'] ?? '') && file_exists($img_src));
                                        $display = $has_img ? $img_src : ""; 
                                    ?>
                                    
                                    <button type="button" class="btn-remove-img" id="btnDelImg" style="<?= !$has_img ? 'display:none;' : '' ?>" title="ลบรูปภาพ">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>

                                    <i class="fa-solid fa-cloud-arrow-up fa-4x text-muted placeholder-icon" id="placeholderIcon" style="<?= $has_img ? 'display:none;' : 'display:block;' ?>"></i>

                                    <img src="<?= $display ?>" id="imgPreview" style="<?= $has_img ? 'display:block;' : 'display:none;' ?>">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control rounded-12" accept="image/*">
                                <small class="text-muted d-block mt-2"><i class="fa-solid fa-circle-info me-1"></i> ทิ้งว่างไว้ถ้าไม่ต้องการเปลี่ยนรูป</small>
                            </div>

                            <hr class="opacity-10 my-4">

                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold mb-3 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i>บันทึกการแก้ไข
                            </button>
                            <button type="button" onclick="location.href='index.php'" class="btn btn-light w-100 py-3 rounded-15 text-muted border">
                                ยกเลิก
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    // CKEditor
    ClassicEditor.create(document.querySelector('#editor')).catch(error => console.error(error));

    const imageInput = document.getElementById('imageInput');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const btnDelImg = document.getElementById('btnDelImg');

    // Preview เมื่อเลือกไฟล์ใหม่
    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            imgPreview.style.opacity = '1';
            if(placeholderIcon) placeholderIcon.style.display = 'none';
            if(btnDelImg) btnDelImg.style.display = 'none'; // ซ่อนปุ่ม X เดิม เพราะกำลังจะเปลี่ยนรูป
        }
    };

    // ระบบลบรูปภาพเดิม (AJAX)
    if (btnDelImg) {
        btnDelImg.onclick = () => {
            Swal.fire({
                title: 'ยืนยันการลบรูปภาพ?',
                text: "รูปจะถูกลบออกจาก Server ทันทีนะเพื่อน!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_image.php?id=<?= $id ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            imgPreview.style.display = 'none';
                            placeholderIcon.style.display = 'block';
                            btnDelImg.style.display = 'none';
                            Swal.fire('สำเร็จ!', 'ลบรูปภาพเรียบร้อย', 'success');
                        } else {
                            Swal.fire('ผิดพลาด!', data.message, 'error');
                        }
                    });
                }
            });
        };
    }

    <?php if($success): ?>
        Swal.fire({ 
            title: 'อัปเดตสำเร็จ!', 
            text: 'ระบบบันทึกข้อมูลใหม่เรียบร้อยแล้ว', 
            icon: 'success', 
            timer: 1500, 
            showConfirmButton: false,
            borderRadius: '15px'
        }).then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>