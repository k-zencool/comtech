<?php
// includes/navbar.php

if (!function_exists('h')) {
  function h($s)
  {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
  }
}

$brandTitle = 'แผนกคอมพิวเตอร์';
$brandSub   = 'Computer Technology';
$logoPath   = 'assets/images/logo.png';

/* เมนู + icon (Material Symbols) */
$navItems = [
  ['label' => 'หน้าแรก',   'href' => '#home',       'icon' => 'home'],
  ['label' => 'หลักสูตร',  'href' => '#curriculum', 'icon' => 'menu_book'],
  ['label' => 'บุคลากร',   'href' => '#teachers',   'icon' => 'groups'],
  ['label' => 'ผลงาน',     'href' => '#projects',   'icon' => 'emoji_events'],
  ['label' => 'ข่าวสาร',   'href' => '#news',       'icon' => 'campaign'],
  ['label' => 'รางวัล',    'href' => '#awards',     'icon' => 'workspace_premium'],
];

$fbUrl = 'https://www.facebook.com/profile.php?id=100063756753586';
?>

<!-- Google Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

<div class="navbar-wrap">
  <div class="container">
    <div class="navbar" role="navigation" aria-label="เมนูหลัก">

      <!-- Brand (แยก logo กับ text) -->
      <a class="brand" href="#home" aria-label="กลับหน้าแรก">
        <div class="brand-logo" aria-hidden="true">
          <img src="<?= h($logoPath) ?>" alt="Logo"
            onerror="this.style.display='none';">
        </div>

        <div class="brand-text">
          <b><?= h($brandTitle) ?></b>
          <small><?= h($brandSub) ?></small>
        </div>
      </a>

      <!-- Desktop Nav -->
      <nav class="navlinks" aria-label="ลิงก์หน้าเว็บ">
        <?php foreach ($navItems as $it): ?>
          <a href="<?= h($it['href']) ?>">
            <span class="material-symbols-outlined"><?= h($it['icon']) ?></span>
            <?= h($it['label']) ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <!-- Search -->
      <!-- Search -->
      <form class="nav-search" role="search" action="#" method="get">
        <span class="material-symbols-outlined" aria-hidden="true">search</span>

        <input
          type="search"
          name="q"
          placeholder="ค้นหา..."
          aria-label="ค้นหา"
          autocomplete="off">

        <button type="submit" aria-label="ค้นหา">
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </button>
      </form>


      <!-- Actions -->
      <div class="nav-actions">
        <a href="<?= h($fbUrl) ?>" target="_blank" class="fb-link" aria-label="Facebook">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
          </svg>
          <span class="fb-text">Facebook</span>
        </a>

        <button class="burger" type="button" data-open-sidebar aria-label="เปิดเมนูมือถือ">
          <span aria-hidden="true"></span>
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Overlay -->
<div class="mobile-overlay" data-overlay></div>

<!-- Mobile Sidebar -->
<aside class="mobile-sidebar" data-sidebar aria-label="เมนูมือถือ">
  <div class="ms-head">
    <div class="ms-title">
      <div class="badge">MENU</div>
      <div style="line-height:1.1">
        <div style="font-weight:900; font-size:14px;"><?= h($brandTitle) ?></div>
        <div style="color:var(--muted); font-size:12px;"><?= h($brandSub) ?></div>
      </div>
    </div>
    <button class="ms-close" type="button" data-close-sidebar aria-label="ปิดเมนู">✕</button>
  </div>

  <div class="ms-body">
    <nav class="ms-nav">
      <?php foreach ($navItems as $it): ?>
        <a href="<?= h($it['href']) ?>" class="ms-link">
          <span style="display:flex; align-items:center; gap:10px;">
            <span class="material-symbols-outlined" style="font-size:20px;"><?= h($it['icon']) ?></span>
            <?= h($it['label']) ?>
          </span>
          <span style="color:var(--muted);">›</span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="ms-actions">
      <a class="btn btn-primary" href="#news" style="display:block; text-align:center;">ดูข่าวล่าสุด</a>
      <a class="btn" href="<?= h($fbUrl) ?>" target="_blank" style="display:block; text-align:center;">Facebook</a>
    </div>
  </div>
</aside>

<script>
  (() => {
    const openBtn = document.querySelector('[data-open-sidebar]');
    const closeBtn = document.querySelector('[data-close-sidebar]');
    const overlay = document.querySelector('[data-overlay]');
    const sidebar = document.querySelector('[data-sidebar]');
    const msLinks = document.querySelectorAll('.ms-link');

    if (!openBtn || !closeBtn || !overlay || !sidebar) return;

    const open = () => {
      overlay.classList.add('open');
      sidebar.classList.add('open');
      document.body.style.overflow = 'hidden';
    };

    const close = () => {
      overlay.classList.remove('open');
      sidebar.classList.remove('open');
      document.body.style.overflow = '';
    };

    openBtn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', close);

    msLinks.forEach(link => link.addEventListener('click', close));

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  })();
</script>