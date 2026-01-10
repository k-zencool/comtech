<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// บังคับให้พ่นค่าเป็น JSON เท่านั้น เพื่อให้ AJAX รับไปทำงานต่อได้
header('Content-Type: application/json');

// 1. เช็คสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'มึงไม่มีสิทธิ์ลบรูปนะเพื่อน!']);
    exit();
}

// 2. เช็ค ID โปรเจกต์
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ระบุ ID จะลบรูปโปรเจกต์ไหน?']);
    exit();
}

$id = $_GET['id'];

try {
    // 3. ดึงชื่อไฟล์รูปปัจจุบันจาก DB (ค่าที่เก็บคือ YYYY/MM/proj_xxx.jpg)
    $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if ($project && !empty($project['image'])) {
        // --- 💡 ส่วนการกำจัดไฟล์จริงบน Server ---
        $upload_base = __DIR__ . '/../../uploads/projects/';
        $file_path = $upload_base . $project['image'];

        // สั่งลบไฟล์ทิ้งถ้ามันมีตัวตนอยู่จริง
        if (file_exists($file_path)) {
            unlink($file_path); 
        }

        // 4. ล้างค่าใน Database ให้เป็นว่าง
        $update = $pdo->prepare("UPDATE projects SET image = '' WHERE id = ?");
        $update->execute([$id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์รูปในระบบอยู่แล้ว']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}