<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน (ป้องกันคนมั่วแอบกดลบ)
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. เช็ค ID อาจารย์ที่จะลบ
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. ดึงชื่อไฟล์รูปจาก DB ออกมาก่อนลบ Row
        $stmt = $pdo->prepare("SELECT image FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();

        if ($teacher) {
            // --- 💡 ส่วนการกำจัดไฟล์รูปภาพจริง ---
            if (!empty($teacher['image'])) {
                // ถอยออกจาก admin/teachers/ ไปหา uploads/teachers/
                $upload_base = __DIR__ . '/../../uploads/teachers/';
                $file_path = $upload_base . $teacher['image'];

                if (file_exists($file_path)) {
                    unlink($file_path); // ฆ่าไฟล์ทิ้งจาก Server
                }
            }

            // 4. ลบข้อมูลอาจารย์ออกจาก Database
            $deleteStmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $deleteStmt->execute([$id]);

            // ส่งกลับไปหน้า index พร้อม status เพื่อโชว์ SweetAlert
            header("Location: index.php?status=deleted");
            exit();
        } else {
            header("Location: index.php?status=notfound");
            exit();
        }

    } catch (PDOException $e) {
        die("ลบไม่ได้ว่ะเพื่อน ติดปัญหา DB: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}