<?php
session_start();

// ถ้าไม่มี session admin_id แสดงว่ายังไม่ได้ login
if (!isset($_SESSION['admin_id'])) {
    // ไล่มันกลับไปหน้า login
    header("Location: /admin/login.php");
    exit();
}
?>