<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// บังคับพ่น JSON เท่านั้น
header('Content-Type: application/json');

// 1. เช็คสิทธิ์
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'มึงไม่มีสิทธิ์นะจ๊ะ']);
    exit();
}

// 2. เช็ค ID
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ส่ง ID มาแล้วจะลบยังไง']);
    exit();
}

$id = $_GET['id'];

try {
    // 3. ดึงชื่อไฟล์จาก DB (ค่าที่เก็บคือ YYYY/MM/news_xxx.jpg)
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if ($news && !empty($news['image'])) {
        // --- 💡 จุดตายอยู่ตรงนี้เพื่อน! ---
        // __DIR__ คือตำแหน่งไฟล์ปัจจุบัน (admin/news/) 
        // ถอยไป 2 ชั้น (../../) เพื่อออกไปนอก admin แล้วเข้า uploads/news/
        $upload_base = __DIR__ . '/../../uploads/news/';
        $file_path = $upload_base . $news['image'];

        // 4. ลบไฟล์จริงในเครื่องก่อน
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                // ถ้ามีไฟล์แต่ลบไม่ได้ (Permission) ให้แจ้งเตือน
                echo json_encode(['success' => false, 'message' => 'Permission Denied: ลบไฟล์ไม่ได้']);
                exit();
            }
        }

        // 5. ค่อยมาเคลียร์ชื่อใน Database ให้ว่างเปล่า
        $update = $pdo->prepare("UPDATE news SET image = '' WHERE id = ?");
        $update->execute([$id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่เจอรูปในระบบว่ะ']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}