<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// 1. เช็คว่าล็อกอินหรือยัง
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. รับค่า ID ที่จะลบ
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. สั่งลบข้อมูลรายวิชา
        $stmt = $pdo->prepare("DELETE FROM curriculum WHERE id = ?");
        $stmt->execute([$id]);

        // 4. ลบเสร็จแล้วส่งกลับหน้า Index พร้อมแนบ Status ไปโชว์ SweetAlert
        header("Location: index.php?status=deleted");
        exit();

    } catch (PDOException $e) {
        // กรณีเกิด Error เช่น มีข้อมูลอื่นผูกอยู่ (Foreign Key)
        die("Error: " . $e->getMessage());
    }
} else {
    // ถ้าไม่มี ID ส่งมา ให้ดีดกลับหน้า Index
    header("Location: index.php");
    exit();
}