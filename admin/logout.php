<?php
// 1. เริ่มต้น Session เพื่อให้ระบบรู้ว่าจะคุยกับใคร
session_start();

// 2. ล้างข้อมูลทุกอย่างใน Session ทิ้งให้เกลี้ยง
session_unset();

// 3. ทำลาย Session นั้นทิ้งไปซะ
session_destroy();

// 4. ดีดมันกลับไปหน้า Login เหมือนเดิม
header("Location: login.php");
exit();
?>