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
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([$id]);
    $teacher = $stmt->fetch();
    if (!$teacher) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. จัดการเมื่อมีการกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $position = $_POST['position'];
    $education = trim($_POST['education']);
    $expertise = trim($_POST['expertise']);
    $image_name = $teacher['image']; // ใช้รูปเดิมไว้ก่อน
    $upload_ok = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $sub_dir = date('Y/m'); 
        $upload_base = __DIR__ . '/../../uploads/teachers/';
        $upload_dir = $upload_base . $sub_dir . '/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = "teacher_" . time() . "." . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                // ลบรูปเก่าทิ้งจาก Server (ถ้ามี)
                if (!empty($teacher['image']) && file_exists($upload_base . $teacher['image'])) {
                    unlink($upload_base . $teacher['image']);
                }
                $image_name = $sub_dir . '/' . $new_name;
            }
        } else {
            $error = "รองรับเฉพาะไฟล์รูปภาพเท่านั้นนะเพื่อน";
            $upload_ok = false;
        }
    }

    if ($upload_ok && !empty($name)) {
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET name = ?, position = ?, education = ?, expertise = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $position, $education, $expertise, $image_name, $id]);
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
    <title>แก้ไขข้อมูลคณาจารย์ | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .preview-container {
            width: 100%; height: 450px;
            border: 2px dashed #ddd; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: #fafafa; position: relative;
        }
        .preview-container img { width: 100%; height: 100%; object-fit: cover; z-index: 1; }
        .placeholder-icon { position: absolute; color: #ccc; z-index: 0; }
        .btn-remove-img {
            position: absolute; top: 15px; right: 15px;
            background: #ff4d4d; color: white; border: none;
            border-radius: 50%; width: 35px; height: 35px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .sticky-side { position: sticky; top: 20px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-user-pen me-2 text-primary"></i>แก้ไขข้อมูลคณาจารย์</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 btn-sm border text-muted">
                <i class="fa-solid fa-chevron-left me-1"></i> กลับหน้ารายการ
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-15 mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-signature me-2 text-muted"></i>ชื่อ-นามสกุล</label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-12" value="<?= htmlspecialchars($teacher['name']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-user-graduate me-2 text-muted"></i>วุฒิการศึกษา</label>
                            <textarea name="education" class="form-control rounded-12" rows="5"><?= htmlspecialchars($teacher['education']) ?></textarea>
                        </div>
                        <div class="mb-0 d-flex flex-column flex-grow-1">
                            <label class="form-label fw-bold"><i class="fa-solid fa-star me-2 text-muted"></i>ความเชี่ยวชาญ / งานวิจัย</label>
                            <textarea name="expertise" class="form-control rounded-12 flex-grow-1" rows="6"><?= htmlspecialchars($teacher['expertise']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card card-custom p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-id-badge me-2 text-muted"></i>ตำแหน่งวิชาการ</label>
                                <select name="position" class="form-select rounded-12">
                                    <?php $pos = ['อาจารย์', 'ผู้ช่วยศาสตราจารย์', 'รองศาสตราจารย์', 'ศาสตราจารย์']; ?>
                                    <?php foreach($pos as $p): ?>
                                        <option value="<?= $p ?>" <?= ($teacher['position'] == $p) ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปถ่ายอาจารย์ (แนวตั้ง)</label>
                                <div class="preview-container" id="previewBox">
                                    <?php 
                                        $img_src = "../../uploads/teachers/" . ($teacher['image'] ?? '');
                                        $has_img = (!empty($teacher['image']) && file_exists($img_src));
                                    ?>
                                    <button type="button" class="btn-remove-img" id="btnDelImg" style="<?= !$has_img ? 'display:none;' : '' ?>" title="ลบรูปภาพ">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <i class="fa-solid fa-user-plus fa-4x text-muted placeholder-icon" id="placeholderIcon" style="<?= $has_img ? 'display:none;' : '' ?>"></i>
                                    <img src="<?= $has_img ? $img_src : '' ?>" id="imgPreview" style="<?= $has_img ? 'display:block;' : 'display:none;' ?>">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control rounded-12 mt-3" accept="image/*">
                            </div>

                            <hr class="opacity-10 my-4">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">
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

    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = 'block';
            placeholderIcon.style.display = 'none';
            btnDelImg.style.display = 'none';
        }
    };

    if (btnDelImg) {
        btnDelImg.onclick = () => {
            Swal.fire({
                title: 'ลบรูปอาจารย์?',
                text: "รูปจะหายไปจากเครื่องทันทีนะเพื่อน!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_image.php?id=<?= $id ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            imgPreview.style.display = 'none';
                            placeholderIcon.style.display = 'block';
                            btnDelImg.style.display = 'none';
                            Swal.fire('ลบแล้ว!', '', 'success');
                        }
                    });
                }
            });
        };
    }

    <?php if($success): ?>
        Swal.fire({ title: 'สำเร็จ!', text: 'อัปเดตข้อมูลแล้ว', icon: 'success', timer: 1500, showConfirmButton: false })
        .then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>