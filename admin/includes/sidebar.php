<?php
// หาชื่อไฟล์ปัจจุบันและโฟลเดอร์เพื่อเอาไปทำ class 'active' ให้เมนู
$current_page = basename($_SERVER['PHP_SELF']);
$directory = basename(dirname($_SERVER['PHP_SELF']));

// เช็คว่าอยู่ชั้นไหน เพื่อกำหนด Path ไปยัง Assets หรือ Link ต่างๆ
$base_path = ($directory == 'admin') ? '' : '../';
$asset_path = ($directory == 'admin') ? '../' : '../../';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?= $asset_path ?>assets/images/logo.png" width="60" class="mb-2">
        <h6 class="fw-bold m-0 text-dark">COMTECH ADMIN</h6>
    </div>
    <ul class="sidebar-menu">
        <li class="<?= ($current_page == 'index.php' && $directory == 'admin') ? 'active' : '' ?>">
            <a href="<?= $base_path ?>index.php">
                <i class="fa-solid fa-chart-line me-2"></i> แดชบอร์ด
            </a>
        </li>

        <li class="<?= ($directory == 'news') ? 'active' : '' ?>">
            <a href="<?= $base_path ?>news/index.php">
                <i class="fa-solid fa-newspaper me-2"></i> จัดการข่าวสาร
            </a>
        </li>

        <li class="<?= ($directory == 'teachers') ? 'active' : '' ?>">
            <a href="<?= $base_path ?>teachers/index.php">
                <i class="fa-solid fa-user-tie me-2"></i> ข้อมูลคณาจารย์
            </a>
        </li>

        <li class="<?= ($directory == 'projects') ? 'active' : '' ?>">
            <a href="<?= $base_path ?>projects/index.php">
                <i class="fa-solid fa-laptop-code me-2"></i> ผลงานนักเรียน
            </a>
        </li>

        <hr style="opacity: 0.1; margin: 10px 20px;">
        
        <li class="<?= ($directory == 'users') ? 'active' : '' ?>">
            <a href="<?= $base_path ?>users/index.php">
                <i class="fa-solid fa-user-gear me-2"></i> จัดการผู้ดูแลระบบ
            </a>
        </li>

        <li>
            <a href="<?= $base_path ?>logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
            </a>
        </li>
    </ul>
</div>