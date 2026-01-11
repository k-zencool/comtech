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

// จำค่าเดิมกันหาย (Old Values)
$old_name = $_POST['name'] ?? "";
$old_position = $_POST['position'] ?? "อาจารย์";
$old_education = $_POST['education'] ?? "";
$old_expertise = $_POST['expertise'] ?? "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $position = $_POST['position'];
    $education = trim($_POST['education']);
    $expertise = trim($_POST['expertise']);
    
    $image_name = "";
    $upload_ok = true;

    // ระบบอัปโหลดรูปภาพ (แยกโฟลเดอร์ uploads/teachers/YYYY/MM/)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $sub_dir = date('Y/m'); 
        $upload_base = __DIR__ . '/../../uploads/teachers/';
        $upload_dir = $upload_base . $sub_dir . '/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = "teacher_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                $image_name = $sub_dir . '/' . $new_name;
            } else {
                $error = "อัปโหลดรูปไม่สำเร็จ";
                $upload_ok = false;
            }
        } else {
            $error = "รองรับเฉพาะไฟล์รูปภาพ (JPG, PNG, WEBP)";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO teachers (name, position, education, expertise, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $position, $education, $expertise, $image_name]);
            $success = true;
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    } else if (empty($error)) {
        $error = "กรุณากรอกชื่อ-นามสกุลอาจารย์ด้วย";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลคณาจารย์ | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ปรับกรอบ Preview เป็นทรงแนวตั้ง Portrait */
        .preview-container {
            width: 100%; 
            height: 450px; /* เพิ่มความสูงเพื่อให้รับกับแนวตั้ง */
            border: 2px dashed #dee2e6; 
            border-radius: 15px;
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden; 
            background: #fdfdfd; 
            position: relative;
            transition: 0.3s;
        }
        .preview-container:hover { border-color: #c2185b; }
        .preview-container img { width: 100%; height: 100%; object-fit: cover; z-index: 1; display: none; }
        .placeholder-icon { position: absolute; color: #adb5bd; z-index: 0; text-align: center; }
        .placeholder-icon span { display: block; font-size: 0.9rem; margin-top: 10px; font-weight: 300; }
        
        .btn-remove-preview {
            position: absolute; top: 15px; right: 15px;
            background: rgba(255, 77, 77, 0.9); color: white; border: none;
            border-radius: 50%; width: 35px; height: 35px;
            display: none; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.3s; z-index: 10;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-remove-preview:hover { background: #ff0000; transform: scale(1.1); }
        .sticky-side { position: sticky; top: 20px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0"><i class="fa-solid fa-user-plus me-2 text-primary"></i>เพิ่มข้อมูลคณาจารย์</h3>
                <p class="text-muted small m-0">จัดการประวัติและข้อมูลวิชาการของบุคลากร</p>
            </div>
            <a href="index.php" class="btn btn-light rounded-pill px-4 btn-sm border">
                <i class="fa-solid fa-chevron-left me-1"></i> กลับ
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-15 mb-4">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-signature me-2 text-muted"></i>ชื่อ-นามสกุล</label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-12" placeholder="ระบุชื่อพร้อมตำแหน่ง (ถ้ามี) เช่น ดร.สมชาย ใจดี" value="<?= htmlspecialchars($old_name) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-user-graduate me-2 text-muted"></i>วุฒิการศึกษา</label>
                            <textarea name="education" class="form-control rounded-12" rows="5" placeholder="ระบุประวัติการศึกษา เช่น ปริญญาเอก..."><?= htmlspecialchars($old_education) ?></textarea>
                        </div>

                        <div class="mb-0 d-flex flex-column flex-grow-1">
                            <label class="form-label fw-bold"><i class="fa-solid fa-briefcase me-2 text-muted"></i>ความเชี่ยวชาญ / งานวิจัย</label>
                            <textarea name="expertise" class="form-control rounded-12 flex-grow-1" rows="6" placeholder="ระบุสาขาที่เชี่ยวชาญหรือผลงานวิจัย..."><?= htmlspecialchars($old_expertise) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-custom p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-id-badge me-2 text-muted"></i>ตำแหน่งวิชาการ</label>
                                <select name="position" class="form-select rounded-12">
                                    <option value="อาจารย์" <?= ($old_position == 'อาจารย์') ? 'selected' : '' ?>>อาจารย์</option>
                                    <option value="ผู้ช่วยศาสตราจารย์" <?= ($old_position == 'ผู้ช่วยศาสตราจารย์') ? 'selected' : '' ?>>ผู้ช่วยศาสตราจารย์ (ผศ.)</option>
                                    <option value="รองศาสตราจารย์" <?= ($old_position == 'รองศาสตราจารย์') ? 'selected' : '' ?>>รองศาสตราจารย์ (รศ.)</option>
                                    <option value="ศาสตราจารย์" <?= ($old_position == 'ศาสตราจารย์') ? 'selected' : '' ?>>ศาสตราจารย์ (ศ.)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปถ่ายอาจารย์ (แนวตั้ง)</label>
                                <div class="preview-container" id="previewBox">
                                    <button type="button" class="btn-remove-preview" id="btnRemovePreview" title="ยกเลิกรูปนี้">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <div class="placeholder-icon" id="placeholderIcon">
                                        <i class="fa-solid fa-user-tie fa-4x mb-2"></i>
                                        <span>คลิกอัปโหลดรูปแนวตั้ง</span>
                                    </div>
                                    <img src="" id="imgPreview">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control rounded-12 mt-3" accept="image/*">
                                <div class="mt-2 small text-muted text-center">
                                    <i class="fa-solid fa-info-circle me-1"></i> ขนาดที่แนะนำ: 600 x 800 px
                                </div>
                            </div>

                            <hr class="opacity-10 my-4">

                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">
                                <i class="fa-solid fa-save me-2"></i> บันทึกข้อมูลอาจารย์
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
    const btnRemovePreview = document.getElementById('btnRemovePreview');

    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            placeholderIcon.style.display = 'none';
            btnRemovePreview.style.display = 'flex';
        }
    };

    btnRemovePreview.onclick = () => {
        imageInput.value = "";
        imgPreview.src = "";
        imgPreview.style.display = 'none';
        placeholderIcon.style.display = 'block';
        btnRemovePreview.style.display = 'none';
    };

    <?php if($success): ?>
        Swal.fire({
            title: 'บันทึกสำเร็จ!',
            text: 'เพิ่มข้อมูลอาจารย์ใหม่ลงในระบบแล้ว',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            borderRadius: '15px'
        }).then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>