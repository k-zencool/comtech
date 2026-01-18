<?php
require_once 'config/db.php';

// ดึงข้อมูลข่าวทั้งหมดมาเตรียมไว้ให้ Swiper จัดการ
$newsList = [];
try {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $newsList = $stmt->fetchAll();
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

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

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/nav-style.css">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&family=Material+Symbols+Outlined" rel="stylesheet">


</head>

<body>

  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <section id="home" class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <span class="hero-badge">
        <span class="material-symbols-outlined">school</span>
        เว็บไซต์ทางการแผนก
      </span>
      <h1 class="hero-title">แผนกวิชาเทคโนโลยี <br><span style="color: var(--pink-2);">คอมพิวเตอร์</span></h1>
      <p class="hero-desc">เรียนรู้ด้านเทคโนโลยีคอมพิวเตอร์อย่างเป็นระบบ เน้นปฏิบัติจริง พัฒนาทักษะสู่สายอาชีพและการศึกษาต่อ</p>
      <div class="hero-actions">
        <a href="#curriculum" class="hero-btn primary"><span class="material-symbols-outlined">menu_book</span>หลักสูตร</a>
        <a href="#projects" class="hero-btn ghost"><span class="material-symbols-outlined">emoji_events</span>ผลงานนักศึกษา</a>
      </div>
    </div>
  </section>

  <section class="features-overlap">
    <div class="container">
      <div class="features-grid">
        <div class="feature-card">
          <div class="f-icon"><span class="material-symbols-outlined">verified_user</span></div>
          <div class="f-info"><h4>มาตรฐานสากล</h4><p>หลักสูตรรับรองโดย สอศ. มุ่งเน้นทักษะจริง</p></div>
        </div>
        <div class="feature-card">
          <div class="f-icon"><span class="material-symbols-outlined">devices</span></div>
          <div class="f-info"><h4>อุปกรณ์ทันสมัย</h4><p>ห้องแล็บคอมพิวเตอร์สเปกสูง ครบทุกสาขางาน</p></div>
        </div>
        <div class="feature-card">
          <div class="f-icon"><span class="material-symbols-outlined">rocket_launch</span></div>
          <div class="f-info"><h4>สู่อาชีพดิจิทัล</h4><p>ต่อยอดการทำงานและศึกษาต่อในระดับอุดมศึกษา</p></div>
        </div>
      </div>
    </div>
  </section>

  <section id="news" class="news-section">
    <div class="tech-bg-elements">
      <span class="material-symbols-outlined t-icon t-1">terminal</span>
      <span class="material-symbols-outlined t-icon t-2">database</span>
      <span class="material-symbols-outlined t-icon t-3">memory</span>
      <span class="material-symbols-outlined t-icon t-4">cloud_done</span>
      <span class="material-symbols-outlined t-icon t-5">code</span>
      <span class="material-symbols-outlined t-icon t-6">data_object</span>
    </div>

    <div class="container">
      <div class="section-head">
        <span class="badge">อัปเดตล่าสุด</span>
        <h2 class="section-title">ข่าวสารและประกาศ</h2>
      </div>

      <?php if (isset($db_error)): ?>
        <div class="news-empty"><p style="color:red;">Database Error: <?= $db_error ?></p></div>
      <?php elseif (empty($newsList)): ?>
        <div class="news-empty">
          <div class="empty-icon"><span class="material-symbols-outlined">campaign</span></div>
          <h3>ยังไม่มีประกาศในขณะนี้</h3>
        </div>
      <?php else: ?>
        
        <div class="news-slider-container">
          <div class="swiper newsSwiper">
            <div class="swiper-wrapper">
              <?php foreach ($newsList as $news): ?>
                <div class="swiper-slide">
                  <article class="news-card">
                    <div class="news-thumb">
                      <?php if (!empty($news['image']) && file_exists('uploads/news/' . $news['image'])): ?>
                        <img src="uploads/news/<?= $news['image'] ?>" alt="news">
                      <?php else: ?>
                        <div class="thumb-placeholder">
                          <div class="p-icon-wrap"><span class="material-symbols-outlined">code_blocks</span></div>
                          <small>COMPUTER TECHNOLOGY</small>
                        </div>
                      <?php endif; ?>
                      <span class="news-date"><?= date('d M Y', strtotime($news['created_at'])) ?></span>
                    </div>
                    <div class="news-body">
                      <span class="news-tag"><?= htmlspecialchars($news['category'] ?? 'ประชาสัมพันธ์') ?></span>
                      <h4><?= htmlspecialchars($news['topic']) ?></h4>
                      <p><?= mb_strimwidth(strip_tags($news['content']), 0, 100, "...") ?></p>
                      <div class="news-footer">
                        <a href="news-detail.php?id=<?= $news['id'] ?>" class="read-more">อ่านเพิ่มเติม</a>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
          </div>

          <div class="swiper-button-prev-custom"><span class="material-symbols-outlined">chevron_left</span></div>
          <div class="swiper-button-next-custom"><span class="material-symbols-outlined">chevron_right</span></div>
        </div>

      <?php endif; ?>
    </div>
  </section>



  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper('.newsSwiper', {
      slidesPerView: 1,
      spaceBetween: 25,
      grabCursor: true,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
        dynamicBullets: true,
      },
      navigation: {
        nextEl: ".swiper-button-next-custom",
        prevEl: ".swiper-button-prev-custom",
      },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
        1280: { slidesPerView: 4 }, // แสดง 4 ข่าวตามขนาดจอที่เหมาะสม [cite: 2025-07-09]
      }
    });
  </script>

</body>
</html>