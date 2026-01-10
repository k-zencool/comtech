<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คว่าล็อกอินยัง
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. 🛡️ เช็คสิทธิ์: ถ้าคนลบไม่ใช่ superadmin ไม่มีสิทธิ์ลบใครทั้งนั้น!
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin') {
    // ถ้าพยายามมั่ว URL มาลบ ดีดกลับไปหน้า index พร้อมแจ้งเตือน
    header("Location: index.php?status=error_perm");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 3. 🛡️ ห้ามลบตัวเองเด็ดขาด (เดี๋ยวมึงเข้าเครื่องไม่ได้)
    if ($id == $_SESSION['admin_id']) {
        header("Location: index.php?status=error_self");
        exit();
    }

    try {
        // 4. 🛡️ เช็คเป้าหมาย: ห้ามลบ Super Admin คนอื่น (ถ้ามีหลายคน)
        // เพื่อป้องกัน Admin ยึดอำนาจหรือลบเบอร์ใหญ่ในระบบ
        $check = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
        $check->execute([$id]);
        $target = $check->fetch();

        if ($target && $target['role'] === 'superadmin') {
            header("Location: index.php?status=error_super");
            exit();
        }

        // 5. 🗑️ สั่งลบจริงเมื่อผ่านเงื่อนไขทั้งหมด
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);

        // ลบเสร็จส่ง status กลับไปให้หน้า index โชว์ SweetAlert
        header("Location: index.php?status=deleted");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}