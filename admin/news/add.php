<?php 
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic = $_POST['topic'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    // จัดการไฟล์ภาพ
    $file = $_FILES['thumbnail'];
    $file_name = time() . "_" . $file['name'];
    $target = "../../assets/images/uploads/" . $file_name;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $sql = "INSERT INTO news (topic, content, thumbnail, category) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$topic, $content, $file_name, $category]);
        header("Location: index.php");
        exit();
    }
}
?>
<form action="add.php" method="POST" enctype="multipart/form-data">
    </form>