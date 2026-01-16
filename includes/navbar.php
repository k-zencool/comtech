<?php
// includes/navbar.php
// Navbar v2 (Clean / Modern / White-Pink)

if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
}

$current = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? 'index.php');
function active($target, $current){
  return $current === $target ? 'is-active' : '';
}
?>
<nav class="nav" id="nav">
  <div class="nav__inner">

    <a class="brand" href="index.php" aria-label="Go to home">
      <span class="brand__logo">
        <img src="assets/images/logo.png" alt="COMTECH Logo" onerror="this.style.display='none'">
      </span>
      <span class="brand__text">
        <span class="brand__title">COMTECH</span>
        <span class="brand__sub">Computer Technology Department</span>
      </span>
    </a>

    <button class="nav__toggle" type="button" id="navToggle" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <div class="nav__menu" id="navMenu">
      <ul class="nav__links">
        <li><a class="<?= active('index.php',$current) ?>" href="index.php"><i class="fa-solid fa-house"></i>หน้าแรก</a></li>
        <li><a href="#news"><i class="fa-regular fa-newspaper"></i>ข่าวสาร</a></li>
        <li><a href="#projects"><i class="fa-solid fa-rocket"></i>ผลงาน</a></li>
        <li><a href="#teachers"><i class="fa-solid fa-chalkboard-user"></i>อาจารย์</a></li>
        <li><a href="#curriculum"><i class="fa-solid fa-book-open"></i>หลักสูตร</a></li>
      </ul>

      <div class="nav__actions">
        <a class="pill pill--ghost" href="tel:0000000000" title="โทร">
          <i class="fa-solid fa-phone"></i><span>โทร</span>
        </a>
        <a class="pill pill--primary" href="#" target="_blank" rel="noopener">
          <i class="fa-brands fa-facebook-f"></i><span>Facebook</span>
        </a>
      </div>
    </div>

  </div>
</nav>

<script>
// Navbar v2 JS (inline เพื่อให้มึงไม่ต้องแตกไฟล์ตอนนี้)
(function(){
  const nav = document.getElementById('nav');
  const btn = document.getElementById('navToggle');
  const menu = document.getElementById('navMenu');

  // Sticky shadow on scroll
  const onScroll = () => {
    nav.classList.toggle('nav--scrolled', window.scrollY > 6);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, {passive:true});

  // Mobile toggle
  const closeMenu = () => {
    menu.classList.remove('is-open');
    btn.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('nav-lock');
  };

  btn.addEventListener('click', () => {
    const open = !menu.classList.contains('is-open');
    menu.classList.toggle('is-open', open);
    btn.classList.toggle('is-open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.documentElement.classList.toggle('nav-lock', open);
  });

  // Close on link click (mobile)
  menu.querySelectorAll('a[href^="#"], a[href$=".php"]').forEach(a=>{
    a.addEventListener('click', () => {
      if (window.matchMedia('(max-width: 900px)').matches) closeMenu();
    });
  });

  // Close on ESC
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape') closeMenu();
  });

  // Smooth anchor offset (so it doesn't hide behind navbar)
  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', (e)=>{
      const id = a.getAttribute('href');
      if(!id || id === '#') return;
      const el = document.querySelector(id);
      if(!el) return;
      e.preventDefault();
      const y = el.getBoundingClientRect().top + window.pageYOffset - 90;
      window.scrollTo({top:y, behavior:'smooth'});
    });
  });
})();
</script>
