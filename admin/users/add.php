<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 🛡️ ดักชั้นแรก: ถ้าไม่ใช่ Super Admin อย่าริอ่านเข้ามาหน้านี้!
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: index.php?status=no_perm");
    exit();
}

$success = false; $error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // รับค่า Role

    // 🛡️ ดักชั้นที่สอง: เผื่อมัน Inspect Element แก้ Value มาส่ง
    if ($role === 'superadmin' && $_SESSION['admin_role'] !== 'superadmin') {
        $error = "มึงไม่มีสิทธิ์สร้างระดับ Super Admin นะไอ้ชาย!";
    } elseif (empty($fullname) || empty($username) || empty($password)) {
        $error = "กรอกให้ครบดิ๊!";
    } elseif ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกันเพื่อน";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
            $check->execute([$username]);
            if ($check->fetch()) {
                $error = "Username นี้มีคนใช้แล้ว";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admins (username, password, fullname, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $fullname, $role]);
                $success = true;
            }
        } catch (PDOException $e) { $error = "DB Error: " . $e->getMessage(); }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มแอดมินใหม่ | ComTech Admin</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .equal-height-row { display: flex; flex-wrap: wrap; }
        .equal-height-row > [class*='col-'] { display: flex; flex-direction: column; }
        .card-stretch { flex: 1; display: flex; flex-direction: column; border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card-body-content { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .form-control-custom { border-radius: 12px; padding: 12px 15px; border: 1px solid #eee; transition: all 0.3s; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-user-shield me-2 text-primary"></i>เพิ่มผู้ดูแลระบบ</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border">กลับ</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-15 border-0 shadow-sm mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-7">
                    <div class="card card-custom p-4 card-stretch">
                        <h5 class="fw-bold mb-4">ข้อมูลบัญชี</h5>
                        <div class="mb-4">
                            <label class="form-label fw-bold">ชื่อ-นามสกุล</label>
                            <input type="text" name="fullname" class="form-control form-control-custom" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control form-control-custom" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">ระดับสิทธิ์ (Role)</label>
                            <select name="role" class="form-select form-control-custom" required>
                                <option value="admin">Admin (จัดการข้อมูลทั่วไป)</option>
                                <option value="superadmin">Super Admin (สูงสุด)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-custom p-4 card-stretch">
                        <div class="card-body-content">
                            <div>
                                <h5 class="fw-bold mb-4">ความปลอดภัย</h5>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">รหัสผ่าน</label>
                                    <input type="password" name="password" class="form-control form-control-custom" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-bold">ยืนยันรหัสผ่าน</label>
                                    <input type="password" name="confirm_password" class="form-control form-control-custom" required>
                                </div>
                            </div>
                            <div class="mt-4 pt-4">
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">ยืนยันการสร้าง</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    <?php if($success): ?>
        Swal.fire({ title: 'สำเร็จ!', text: 'สร้างแอดมินใหม่เรียบร้อย', icon: 'success', timer: 1500, showConfirmButton: false })
        .then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>