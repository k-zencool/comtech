<?php
// ใน Docker ต้องใช้ชื่อ service แทน localhost (ดูใน docker-compose.yml บรรทัดที่ 16)
$servername = "db";
$username = "root";
$password = "1234"; // รหัสที่ตั้งใน docker-compose
$dbname = "comtech_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
