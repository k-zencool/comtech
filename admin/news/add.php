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
$old_topic = $_POST['topic'] ?? "";
$old_content = $_POST['content'] ?? "";
$old_category = $_POST['category'] ?? "ประชาสัมพันธ์";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic = trim($_POST['topic']);
    $content = $_POST['content'];
    $category = $_POST['category'];

    $image_name = "";
    $upload_ok = true;

    // ระบบอัปโหลดรูปภาพ
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    // 1. กำหนด Path ตาม ปี/เดือน (เช่น 2026/01)
    $sub_dir = date('Y/m'); 
    $upload_base = __DIR__ . '/../../uploads/news/';
    $upload_dir = $upload_base . $sub_dir . '/';
    
    // 2. สร้างโฟลเดอร์ถ้ายังไม่มี (Recursive)
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $new_name = "news_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
        // 3. บันทึกค่าลง DB เป็น '2026/01/news_xxx.jpg'
        $image_name = $sub_dir . '/' . $new_name; 
    }
}

    // บันทึกลง Database
    if ($upload_ok && !empty($topic) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO news (topic, content, category, image, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$topic, $content, $category, $image_name]);
            $success = true;
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    } else if (empty($error)) {
        $error = "กรอกข้อมูลหัวข้อและเนื้อหาให้ครบด้วยดิ๊!";
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มข่าวใหม่ | ComTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            min-height: 450px !important;
            border-radius: 0 0 15px 15px !important;
        }

        .preview-container {
            width: 100%;
            height: 280px;
            border: 2px dashed #ddd;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fafafa;
            margin-bottom: 15px;
            position: relative;
        }

        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            display: none;
        }

        .placeholder-icon {
            position: absolute;
            color: #ccc;
            z-index: 0;
        }

        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .sticky-side {
            position: sticky;
            top: 20px;
        }

        /* ปุ่ม X ลบพรีวิวรูป */
        .btn-remove-preview {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4d4d;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-remove-preview:hover {
            background: red;
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i>เพิ่มข่าวใหม่</h3>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                    <i class="fa-solid fa-chevron-left me-1"></i> กลับหน้ารายการ
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-15 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="mainForm">
                <div class="row g-4 equal-height-row">
                    <div class="col-lg-8">
                        <div class="card card-custom p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-heading me-2 text-muted"></i>หัวข้อข่าว</label>
                                <input type="text" name="topic" class="form-control form-control-lg rounded-12" placeholder="ระบุหัวข้อข่าวที่นี่..." value="<?= htmlspecialchars($old_topic) ?>" required>
                            </div>
                            <div class="mb-0 d-flex flex-column flex-grow-1">
                                <label class="form-label fw-bold"><i class="fa-solid fa-pen-nib me-2 text-muted"></i>เนื้อหาข่าว</label>
                                <textarea name="content" id="editor"><?= $old_content ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sticky-side">
                            <div class="card card-custom p-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold"><i class="fa-solid fa-tags me-2 text-muted"></i>หมวดหมู่</label>
                                    <select name="category" class="form-select rounded-12">
                                        <option value="ประชาสัมพันธ์" <?= ($old_category == "ประชาสัมพันธ์") ? "selected" : "" ?>>ประชาสัมพันธ์</option>
                                        <option value="กิจกรรม" <?= ($old_category == "กิจกรรม") ? "selected" : "" ?>>กิจกรรม</option>
                                        <option value="วิชาการ" <?= ($old_category == "วิชาการ") ? "selected" : "" ?>>วิชาการ</option>
                                        <option value="ประกาศ" <?= ($old_category == "ประกาศ") ? "selected" : "" ?>>ประกาศ</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold"><i class="fa-solid fa-image me-2 text-muted"></i>รูปภาพประกอบ</label>
                                    <div class="preview-container" id="previewBox">
                                        <button type="button" class="btn-remove-preview" id="btnRemovePreview" title="ยกเลิกรูปนี้">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <i class="fa-solid fa-cloud-arrow-up fa-4x placeholder-icon" id="placeholderIcon"></i>
                                        <img src="" id="imgPreview">
                                    </div>
                                    <input type="file" name="image" id="imageInput" class="form-control rounded-12" accept="image/*">
                                    <small class="text-muted d-block mt-2"><i class="fa-solid fa-circle-info me-1"></i> รองรับ JPG, PNG, WEBP</small>
                                </div>

                                <hr class="opacity-10 my-4">

                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold mb-3 shadow">
                                    <i class="fa-solid fa-paper-plane me-2"></i> บันทึกและเผยแพร่
                                </button>
                                <button type="button" onclick="location.href='index.php'" class="btn btn-light w-100 py-3 rounded-15 text-muted">
                                    <i class="fa-solid fa-xmark me-2"></i> ยกเลิก
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

        // Image Preview & Remove Logic
        const imageInput = document.getElementById('imageInput');
        const imgPreview = document.getElementById('imgPreview');
        const placeholderIcon = document.getElementById('placeholderIcon');
        const btnRemovePreview = document.getElementById('btnRemovePreview');

        // เมื่อเลือกไฟล์
        imageInput.onchange = evt => {
            const [file] = imageInput.files;
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.style.display = 'block';
                placeholderIcon.style.display = 'none';
                btnRemovePreview.style.display = 'flex';
            }
        };

        // เมื่อกดปุ่ม X (ยกเลิกการเลือกรูป)
        btnRemovePreview.onclick = () => {
            imageInput.value = ""; // ล้างค่าใน Input file
            imgPreview.src = "";
            imgPreview.style.display = 'none';
            placeholderIcon.style.display = 'block';
            btnRemovePreview.style.display = 'none';
        };

        // SweetAlert เมื่อสำเร็จ
        <?php if ($success): ?>
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: 'ระบบกำลังพามึงกลับหน้าหลัก...',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = 'index.php';
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>