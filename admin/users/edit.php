<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

$success = false; $error = "";

// 1. ดึงข้อมูลเดิมมาโชว์
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) { header("Location: index.php"); exit(); }
} catch (PDOException $e) { die($e->getMessage()); }

// 2. จัดการเมื่อกด Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $role = $_POST['role'] ?? $user['role']; // ถ้าไม่มีสิทธิ์แก้ ให้ใช้ค่าเดิม

    // 🛡️ ดักสิทธิ์: ถ้าคนแก้ไม่ใช่ Super แต่พยายามเปลี่ยนยศ หรือแก้ข้อมูลคนอื่นที่เป็น Super
    if ($_SESSION['admin_role'] !== 'superadmin') {
        if ($role === 'superadmin' && $user['role'] !== 'superadmin') {
            $error = "อย่าซ่าเพื่อน! มึงไม่มีสิทธิ์ตั้งใครเป็น Super Admin";
        }
    }

    if (empty($error)) {
        try {
            if (!empty($new_password)) {
                // ถ้ามีการกรอกรหัสใหม่ ให้ Hash
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET fullname = ?, username = ?, password = ?, role = ? WHERE id = ?");
                $stmt->execute([$fullname, $username, $hashed_password, $role, $id]);
            } else {
                // ถ้าไม่กรอกรหัสใหม่ ให้แก้แค่ชื่อกับยศ
                $stmt = $pdo->prepare("UPDATE admins SET fullname = ?, username = ?, role = ? WHERE id = ?");
                $stmt->execute([$fullname, $username, $role, $id]);
            }
            $success = true;
        } catch (PDOException $e) { $error = "Username นี้อาจจะมีคนใช้แล้วเพื่อน"; }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขแอดมิน | ComTech Admin</title>
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
        .form-control-custom { border-radius: 12px; padding: 12px 15px; border: 1px solid #eee; transition: 0.3s; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-user-pen me-2 text-warning"></i>แก้ไขข้อมูลแอดมิน</h3>
            <a href="index.php" class="btn btn-light rounded-pill px-4 border">กลับ</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-15 border-0 shadow-sm mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4 equal-height-row">
                <div class="col-lg-7">
                    <div class="card card-custom p-4 card-stretch">
                        <h5 class="fw-bold mb-4">ข้อมูลส่วนตัว</h5>
                        <div class="mb-4">
                            <label class="form-label fw-bold">ชื่อ-นามสกุล</label>
                            <input type="text" name="fullname" class="form-control form-control-custom" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control form-control-custom" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">ระดับสิทธิ์ (Role)</label>
                            <select name="role" class="form-select form-control-custom" <?= ($_SESSION['admin_role'] !== 'superadmin') ? 'disabled' : '' ?>>
                                <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                <option value="superadmin" <?= ($user['role'] == 'superadmin') ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                            <?php if ($_SESSION['admin_role'] !== 'superadmin'): ?>
                                <small class="text-danger mt-1 d-block">* มึงไม่มีสิทธิ์เปลี่ยนยศนะเพื่อน</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-custom p-4 card-stretch">
                        <div class="card-body-content">
                            <div>
                                <h5 class="fw-bold mb-4">เปลี่ยนรหัสผ่าน</h5>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">รหัสผ่านใหม่</label>
                                    <input type="password" name="new_password" class="form-control form-control-custom" placeholder="ปล่อยว่างถ้าไม่ต้องการเปลี่ยน">
                                </div>
                                <div class="p-3 bg-light rounded-15">
                                    <small class="text-muted"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>หากไม่ต้องการเปลี่ยนรหัสผ่าน ไม่ต้องกรอกข้อมูลในช่องด้านบน</small>
                                </div>
                            </div>
                            <div class="mt-4 pt-4">
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-15 fw-bold shadow">บันทึกการแก้ไข</button>
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
        Swal.fire({ title: 'สำเร็จ!', text: 'อัปเดตข้อมูลแอดมินแล้ว', icon: 'success', timer: 1500, showConfirmButton: false })
        .then(() => { window.location.href = 'index.php'; });
    <?php endif; ?>
</script>
</body>
</html>