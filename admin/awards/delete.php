<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมินก่อน
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. รับ ID ที่จะลบ
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // 3. ดึงชื่อไฟล์รูปออกมาเพื่อลบทิ้งก่อนลบ Record
        $stmt = $pdo->prepare("SELECT image FROM awards WHERE id = ?");
        $stmt->execute([$id]);
        $award = $stmt->fetch();

        if ($award) {
            // ลบไฟล์รูปภาพจริงใน Folder
            if (!empty($award['image'])) {
                $file_path = '../../assets/images/awards/' . $award['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            // 4. ลบข้อมูลใน Database
            $del_stmt = $pdo->prepare("DELETE FROM awards WHERE id = ?");
            $del_stmt->execute([$id]);

            // ลบเสร็จส่งกลับไปหน้า index พร้อม status
            header("Location: index.php?status=deleted");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ถ้าไม่มี ID หรือผิดพลาด ให้ดีดกลับหน้าหลัก
header("Location: index.php");
exit();