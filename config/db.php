<?php
// config/db.php
$servername = "db";
$username = "root";
$password = "1234"; 
$dbname = "comtech_db";

try {
    // สร้างการเชื่อมต่อแบบ PDO และเก็บไว้ในตัวแปร $pdo ตามที่หน้า Admin เรียกหา
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // ตั้งค่า Error Mode ให้มันแจ้งเตือนเวลา Query พัง
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ตั้งค่า Fetch Mode ให้ดึงข้อมูลเป็นแบบ Array (Key-Value) เป็นค่าเริ่มต้น
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // ถ้าเชื่อมต่อไม่ได้ ให้ด่าออกมาเลย
    die("Connection failed (PDO): " . $e->getMessage());
}
?>