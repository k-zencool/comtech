<?php
// 🛠️ หาชื่อไฟล์และโฟลเดอร์ปัจจุบันเพื่อทำเมนู Active
$current_page = basename($_SERVER['PHP_SELF']);
$directory    = basename(dirname($_SERVER['PHP_SELF']));

// 🛠️ กำหนด Path ให้ถูกต้อง (รองรับทั้งไฟล์ในรูท admin และโฟลเดอร์ย่อย)
$base_path  = ($directory == 'admin') ? '' : '../';
$asset_path = ($directory == 'admin') ? '../' : '../../';
?>

<!-- Mobile Header -->
<div class="mobile-header d-lg-none">
  <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-white shadow-sm border-bottom">
    <div class="d-flex align-items-center">
      <img src="<?= $asset_path ?>assets/images/logo.png" width="35" class="me-2" alt="COMTECH">
      <span class="fw-bold text-dark small">COMTECH ADMIN</span>
    </div>

    <button class="btn btn-dark btn-sm rounded-10 shadow-sm" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
      <i class="fa-solid fa-bars-staggered"></i>
    </button>
  </div>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

<!-- Sidebar -->
<div class="sidebar" id="mainSidebar">
  <div class="sidebar-header">
    <div class="text-center w-100 position-relative">
      <img src="<?= $asset_path ?>assets/images/logo.png" width="55" class="mb-2" alt="COMTECH">
      <h6 class="fw-bold m-0 text-dark">COMTECH ADMIN</h6>
    </div>
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

    <li class="<?= ($directory == 'curriculum') ? 'active' : '' ?>">
      <a href="<?= $base_path ?>curriculum/index.php">
        <i class="fa-solid fa-book-open me-2"></i> วิชาที่เรียน/หลักสูตร
      </a>
    </li>

    <li class="<?= ($directory == 'teachers') ? 'active' : '' ?>">
      <a href="<?= $base_path ?>teachers/index.php">
        <i class="fa-solid fa-user-tie me-2"></i> ข้อมูลคณาจารย์
      </a>
    </li>

    <li class="<?= ($directory == 'awards') ? 'active' : '' ?>">
      <a href="<?= $base_path ?>awards/index.php">
        <i class="fa-solid fa-trophy me-2"></i> รางวัลที่ได้รับ
      </a>
    </li>

    <li class="<?= ($directory == 'projects') ? 'active' : '' ?>">
      <a href="<?= $base_path ?>projects/index.php">
        <i class="fa-solid fa-laptop-code me-2"></i> ผลงานนักเรียน
      </a>
    </li>

    <hr style="opacity:.1;margin:15px 20px;">

    <li class="<?= ($directory == 'users') ? 'active' : '' ?>">
      <a href="<?= $base_path ?>users/index.php">
        <i class="fa-solid fa-user-gear me-2"></i> จัดการแอดมิน
      </a>
    </li>

    <li>
      <a href="<?= $base_path ?>logout.php" class="text-danger fw-500">
        <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
      </a>
    </li>
  </ul>
</div>

<script>
(function () {
  const init = () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('mainSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    if (!toggleBtn || !sidebar || !overlay) return;

    const mq = window.matchMedia('(min-width: 992px)');

    const setOpen = (open) => {
      // sidebar
      sidebar.classList.toggle('show', open);

      // overlay + icon + lock scroll เฉพาะ mobile
      const isDesktop = mq.matches;
      overlay.classList.toggle('show', open && !isDesktop);
      toggleBtn.classList.toggle('active', open && !isDesktop);
      document.body.style.overflow = (open && !isDesktop) ? 'hidden' : '';
    };

    const toggleMenu = (e) => {
      if (e) e.preventDefault();
      if (mq.matches) return; // desktop: sidebar ติดอยู่แล้ว ไม่ต้อง toggle
      setOpen(!sidebar.classList.contains('show'));
    };

    // close on overlay
    const closeMenu = (e) => {
      if (e) e.preventDefault();
      if (mq.matches) return;
      setOpen(false);
    };

    toggleBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', closeMenu);

    // ป้องกันค้างตอน resize/rotate
    const sync = () => {
      if (mq.matches) setOpen(true);   // desktop: เปิดค้าง
      else setOpen(false);             // mobile: ปิดเริ่มต้น
    };

    sync();
    if (mq.addEventListener) mq.addEventListener('change', sync);
    else mq.addListener(sync);

    window.addEventListener('orientationchange', sync);

    // เผื่อบางหน้า reload พร้อม state ค้างจาก back/forward cache
    window.addEventListener('pageshow', sync);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

<script>
(function () {
  const ensureViewport = () => {
    let meta = document.querySelector('meta[name="viewport"]');
    if (!meta) {
      meta = document.createElement('meta');
      meta.name = 'viewport';
      meta.content = 'width=device-width, initial-scale=1, viewport-fit=cover';
      document.head.appendChild(meta);
    } else {
      meta.setAttribute(
        'content',
        'width=device-width, initial-scale=1, viewport-fit=cover'
      );
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureViewport);
  } else {
    ensureViewport();
  }
})();
</script>
