<?php
$brandTitle = 'แผนกคอมพิวเตอร์';
$brandSub   = 'Computer Technology';
$logoPath   = 'assets/images/logo.png';
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($brandTitle) ?></title>

  <!-- CSS หลักไฟล์เดียว -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/nav-style.css">

  <!-- Google Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>
<div class="topbar-space"></div>

<!-- =========================
     HERO SECTION
========================= -->
<section id="home" class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  <div class="container hero-content">
    <span class="hero-badge">
      <span class="material-symbols-outlined">school</span>
      เว็บไซต์ทางการแผนก
    </span>

    <h1 class="hero-title">
      แผนกวิชาเทคโนโลยีคอมพิวเตอร์
    </h1>

    <p class="hero-desc">
      เรียนรู้ด้านเทคโนโลยีคอมพิวเตอร์อย่างเป็นระบบ  
      เน้นปฏิบัติจริง พัฒนาทักษะสู่สายอาชีพและการศึกษาต่อ
    </p>

    <div class="hero-actions">
      <a href="#curriculum" class="hero-btn primary">
        <span class="material-symbols-outlined">menu_book</span>
        หลักสูตร
      </a>

      <a href="#projects" class="hero-btn ghost">
        <span class="material-symbols-outlined">emoji_events</span>
        ผลงานนักศึกษา
      </a>
    </div>
  </div>
</section>

</body>
</html>
