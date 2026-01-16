<?php
// includes/navbar.php — Capsule Floating Navbar (Formal)
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
}
$current = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? 'index.php');
function active($target, $current){ return $current === $target ? 'is-active' : ''; }
?>
<div class="nav-shell">
  <nav class="nav-pill" id="navPill" aria-label="Main navigation">
    <a class="pill-brand" href="index.php" aria-label="Home">
      <span class="pill-logo">
        <img src="assets/images/logo.png" alt="COMTECH Logo" />
      </span>
      <span class="pill-brand-text">
        <span class="pill-title">COMTECH</span>
        <span class="pill-sub">Computer Technology Department</span>
      </span>
    </a>

    <button class="pill-toggle" type="button" id="pillToggle" aria-expanded="false" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>

    <div class="pill-menu" id="pillMenu">
      <ul class="pill-links">
        <li><a class="<?= active('index.php',$current) ?>" href="index.php">หน้าแรก</a></li>
        <li><a href="#news">ข่าวสาร</a></li>
        <li><a href="#projects">ผลงาน</a></li>
        <li><a href="#teachers">อาจารย์</a></li>
        <li><a href="#curriculum">หลักสูตร</a></li>
      </ul>

      <div class="pill-actions">
        <a class="pill-btn pill-btn--ghost" href="tel:0000000000" title="โทร">
          <i class="fa-solid fa-phone"></i>
        </a>
        <a class="pill-btn pill-btn--primary" href="#" target="_blank" rel="noopener">
          <i class="fa-brands fa-facebook-f"></i>
          <span>Facebook</span>
        </a>
      </div>
    </div>
  </nav>
</div>

<script>
(function(){
  const btn = document.getElementById('pillToggle');
  const menu = document.getElementById('pillMenu');

  const open = () => {
    menu.classList.add('is-open');
    btn.classList.add('is-open');
    btn.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('nav-lock');
  };
  const close = () => {
    menu.classList.remove('is-open');
    btn.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('nav-lock');
  };

  btn.addEventListener('click', () => {
    menu.classList.contains('is-open') ? close() : open();
  });

  // close on click link (mobile)
  menu.querySelectorAll('a').forEach(a=>{
    a.addEventListener('click', ()=>{
      if (window.matchMedia('(max-width: 980px)').matches) close();
    });
  });

  // esc close
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape') close();
  });

  // Smooth anchor offset
  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', (e)=>{
      const id = a.getAttribute('href');
      if(!id || id === '#') return;
      const el = document.querySelector(id);
      if(!el) return;
      e.preventDefault();
      const y = el.getBoundingClientRect().top + window.pageYOffset - 110;
      window.scrollTo({top:y, behavior:'smooth'});
    });
  });
})();
</script>
