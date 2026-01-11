<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมินก่อนลบ
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. รับ ID ของรางวัล
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // 3. ดึงชื่อไฟล์รูปเดิมออกมาเพื่อลบไฟล์ทิ้ง
        $stmt = $pdo->prepare("SELECT image FROM awards WHERE id = ?");
        $stmt->execute([$id]);
        $award = $stmt->fetch();

        if ($award && !empty($award['image'])) {
            $upload_dir = '../../assets/images/awards/';
            $file_path = $upload_dir . $award['image'];

            // ลบไฟล์ออกจากเครื่องจริง
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // 4. อัปเดต Database ให้ค่า image เป็น NULL
            $update_stmt = $pdo->prepare("UPDATE awards SET image = NULL WHERE id = ?");
            $update_stmt->execute([$id]);

            // ลบสำเร็จ ให้เด้งกลับไปหน้าเดิมพร้อมส่งสถานะ
            header("Location: edit.php?id=$id&status=img_deleted");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ถ้าไม่มี ID หรือทำงานไม่สำเร็จให้ดีดกลับหน้าหลัก
header("Location: index.php");
exit();