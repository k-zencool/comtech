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
    $stmt = $pdo->prepare("SELECT * FROM awards WHERE id = ?");
    $stmt->execute([$id]);
    $award = $stmt->fetch();
    if (!$award) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. จัดการเมื่อมีการกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $organizer   = trim($_POST['organizer']);
    $award_date  = $_POST['award_date'];
    $description = $_POST['description']; // รับค่าจาก CKEditor
    $image_name  = $award['image']; 
    $upload_ok   = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../assets/images/awards/'; 
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = "award_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                if (!empty($award['image']) && file_exists($upload_dir . $award['image'])) {
                    unlink($upload_dir . $award['image']);
                }
                $image_name = $new_name;
            }
        } else {
            $error = "รองรับเฉพาะไฟล์รูปภาพเท่านั้นนะเพื่อน";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($title)) {
        try {
            $stmt = $pdo->prepare("UPDATE awards SET title = ?, organizer = ?, award_date = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $organizer, $award_date, $description, $image_name, $id]);
            $success = true;
        } catch (PDOException $e) { $error = "DB Error: " . $e->getMessage(); }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลรางวัล | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        /* 🛠 ระบบดึงก้นให้เท่ากันเป๊ะ */
        .equal-height-row { display: flex; flex-wrap: wrap; }
        .equal-height-row > [class*='col-'] { display: flex; flex-direction: column; }
        .card-stretch { flex: 1; display: flex; flex-direction: column; border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; }
        
        .preview-container {
            width: 100%; height: 350px;
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
        /* 🎨 จูน CKEditor ให้เข้ากับดีไซน์ */
        .ck-editor__editable { min-height: 350px; border-radius: 0 0 12px 12px !important; flex-grow: 1; }
        .ck-toolbar { border-radius: 12px 12px 0 0 !important; border: 1px solid #eee !important; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-trophy me-2 text-primary"></i>แก้ไขรางวัล</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 btn-sm border text-muted">กลับหน้ารายการ</a>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-stretch p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold">ชื่อรางวัลที่ได้รับ</label>
                            <input type="text" name="title" class="form-control form-control-lg rounded-12 shadow-sm" value="<?= htmlspecialchars($award['title']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">หน่วยงานที่มอบ</label>
                            <input type="text" name="organizer" class="form-control rounded-12 shadow-sm" value="<?= htmlspecialchars($award['organizer']) ?>">
                        </div>
                        <div class="mb-0 d-flex flex-column flex-grow-1">
                            <label class="form-label fw-bold">รายละเอียด / รายชื่อผู้ได้รับรางวัล</label>
                            <textarea name="description" id="editor"><?= htmlspecialchars($award['description']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-stretch p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold">วันที่ได้รับรางวัล</label>
                                <input type="date" name="award_date" class="form-control rounded-12 shadow-sm" value="<?= $award['award_date'] ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">รูปภาพเกียรติบัตร</label>
                                <div class="preview-container" id="previewBox">
                                    <?php 
                                        $img_path = "../../assets/images/awards/" . ($award['image'] ?? '');
                                        $has_img = (!empty($award['image']) && file_exists($img_path));
                                    ?>
                                    <button type="button" class="btn-remove-img" id="btnDelImg" style="<?= !$has_img ? 'display:none;' : '' ?>">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <i class="fa-solid fa-cloud-arrow-up fa-4x text-muted placeholder-icon" id="placeholderIcon" style="<?= $has_img ? 'display:none;' : '' ?>"></i>
                                    <img src="<?= $has_img ? $img_path : '' ?>" id="imgPreview" style="<?= $has_img ? 'display:block;' : 'display:none;' ?>">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control rounded-12 mt-3 shadow-sm" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow mt-auto">
                                <i class="fa-solid fa-save me-2"></i>บันทึกการแก้ไข
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const imageInput = document.getElementById('imageInput');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const btnDelImg = document.getElementById('btnDelImg');

    // 📸 Preview รูปเมื่อเลือกไฟล์ใหม่
    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            placeholderIcon.style.display = 'none';
            btnDelImg.style.display = 'none'; // ซ่อนปุ่ม X รูปเก่าเพราะเรากำลังจะอัปรูปใหม่
        }
    };

    // 🗑️ ยืนยันการลบรูปภาพเดิม
    if (btnDelImg) {
        btnDelImg.onclick = () => {
            Swal.fire({
                title: 'ลบรูปภาพ?',
                text: "รูปจะหายไปทันทีนะเพื่อน! [cite: 2025-05-30]",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_image.php?id=<?= $id ?>';
                }
            });
        };
    }

    // ✨ เรียกใช้งาน CKEditor 5
    ClassicEditor.create(document.querySelector('#editor')).catch(error => { console.error(error); });

    // 🟢 แจ้งเตือนเมื่อทำงานสำเร็จ
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // กรณีลบรูปภาพสำเร็จ
        if (urlParams.get('status') === 'img_deleted') {
            Swal.fire({ 
                title: 'สำเร็จ!', 
                text: 'ลบรูปภาพเรียบร้อยแล้วเพื่อน', 
                icon: 'success', 
                timer: 1500, 
                showConfirmButton: false 
            });
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?= $id ?>'); // ล้าง status แต่เก็บ id ไว้
        }

        // กรณีอัปเดตข้อมูลทั้งหมดสำเร็จ
        <?php if($success): ?>
            Swal.fire({ 
                title: 'สำเร็จ!', 
                text: 'อัปเดตข้อมูลเรียบร้อย', 
                icon: 'success', 
                timer: 1500, 
                showConfirmButton: false 
            }).then(() => { 
                window.location.href = 'index.php'; 
            });
        <?php endif; ?>
    });
</script>
</body>
</html>