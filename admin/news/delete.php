<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คสิทธิ์แอดมิน (ความปลอดภัยต้องมาก่อน)
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. เช็คว่าส่ง ID ที่จะลบมาหรือเปล่า
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. ดึงข้อมูลรูปภาพออกมาก่อน (ชื่อที่เก็บใน DB คือ YYYY/MM/news_xxx.jpg)
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();

        if ($news) {
            // --- 💡 จุดสำคัญ: ลบไฟล์จริงในเครื่อง ---
            if (!empty($news['image'])) {
                // ต้องถอย Path ออกไปนอก admin เพื่อเข้า uploads
                $upload_base = __DIR__ . '/../../uploads/news/';
                $file_path = $upload_base . $news['image'];

                if (file_exists($file_path)) {
                    unlink($file_path); // สั่งลบไฟล์จริงออกจาก Server
                }
            }

            // 4. ลบข้อมูลข่าวออกจาก Database
            $deleteStmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $deleteStmt->execute([$id]);

            // ลบเสร็จส่ง status กลับไปบอกหน้า index ให้ SweetAlert ทำงาน
            header("Location: index.php?status=deleted");
            exit();
        } else {
            header("Location: index.php?status=notfound");
            exit();
        }

    } catch (PDOException $e) {
        die("ลบไม่ลงว่ะเพื่อน ติดปัญหา: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}