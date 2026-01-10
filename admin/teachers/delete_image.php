<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// บังคับให้พ่นค่าเป็น JSON เท่านั้น
header('Content-Type: application/json');

// 1. เช็คสิทธิ์แอดมิน (กันคนนอกแอบยิง URL ลบรูป)
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'มึงไม่มีสิทธิ์ทำรายการนี้!']);
    exit();
}

// 2. เช็คว่าส่ง ID อาจารย์มาไหม
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ระบุ ID จะลบรูปใครล่ะเพื่อน?']);
    exit();
}

$id = $_GET['id'];

try {
    // 3. ดึงชื่อไฟล์รูปปัจจุบันจาก DB (ค่าที่เก็บคือ YYYY/MM/teacher_xxx.jpg)
    $stmt = $pdo->prepare("SELECT image FROM teachers WHERE id = ?");
    $stmt->execute([$id]);
    $teacher = $stmt->fetch();

    if ($teacher && !empty($teacher['image'])) {
        // --- 💡 ส่วนการกำจัดไฟล์จริงบน Server ---
        // ถอยจาก admin/teachers/ ไปหา uploads/teachers/
        $upload_base = __DIR__ . '/../../uploads/teachers/';
        $file_path = $upload_base . $teacher['image'];

        // ลบไฟล์ในโฟลเดอร์ทิ้ง
        if (file_exists($file_path)) {
            unlink($file_path); 
        }

        // 4. อัปเดต Database ให้คอลัมน์ image กลายเป็นค่าว่าง
        $update = $pdo->prepare("UPDATE teachers SET image = '' WHERE id = ?");
        $update->execute([$id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรูปภาพในระบบ']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}