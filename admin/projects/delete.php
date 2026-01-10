<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน (ป้องกันคนนอกแอบยิง URL)
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. เช็ค ID โปรเจกต์ที่จะลบ
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. ดึงชื่อไฟล์รูปจาก DB ออกมาก่อนลบ Row
        $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if ($project) {
            // --- 💡 ส่วนการกำจัดไฟล์รูปภาพจริง ---
            if (!empty($project['image'])) {
                // ถอยออกจาก admin/projects/ ไปหา uploads/projects/
                $upload_base = __DIR__ . '/../../uploads/projects/';
                $file_path = $upload_base . $project['image'];

                if (file_exists($file_path)) {
                    unlink($file_path); // ลบไฟล์ทิ้งจาก Server ไม่ให้รก
                }
            }

            // 4. ลบข้อมูลโปรเจกต์ออกจาก Database
            $deleteStmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $deleteStmt->execute([$id]);

            // ส่งกลับไปหน้า index พร้อม status เพื่อโชว์ SweetAlert ที่เราเขียนดักไว้
            header("Location: index.php?status=deleted");
            exit();
        } else {
            // ถ้าไม่เจอโปรเจกต์ (อาจจะโดนลบไปแล้วหรือ ID มั่ว)
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