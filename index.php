<?php
// 1. เรียกไฟล์เชื่อมต่อฐานข้อมูล [cite: 2025-05-31]
require_once 'config/db.php';

// 2. ดึงข้อมูลข่าวทั้งหมดมาเตรียมไว้ให้ Swiper จัดการ
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
<body>

  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <section id="home" class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <span class="hero-badge"><span class="material-symbols-outlined">school</span> เว็บไซต์ทางการแผนก</span>
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

  <section id="curriculum" class="curriculum-section-v2">
    <div class="container">
      <div class="section-head">
        <span class="badge">หลักสูตร</span>
        <h2 class="section-title">โครงสร้างหลักสูตร</h2>
      </div>
      <div class="curr-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'pvoc')">ระดับ ปวช.</button>
        <button class="tab-btn" onclick="openTab(event, 'hvoc')">ระดับ ปวส.</button>
      </div>
      <div id="pvoc" class="tab-content active">
        <div class="table-responsive">
          <table class="curr-table">
            <thead><tr><th>รหัสวิชา</th><th>ชื่อรายวิชา</th><th>หน่วยกิต</th><th>ชั้นปี</th></tr></thead>
            <tbody>
              <?php
              $stmt1 = $pdo->prepare("SELECT * FROM curriculum WHERE level = 'ปวช.' ORDER BY year ASC, subject_code ASC LIMIT 6");
              $stmt1->execute();
              while ($row = $stmt1->fetch()):
              ?>
                <tr><td class="code-col"><?= $row['subject_code'] ?></td><td class="name-col"><?= $row['subject_name'] ?></td><td><?= $row['credits'] ?></td><td>ปี <?= $row['year'] ?></td></tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="hvoc" class="tab-content">
        <div class="table-responsive">
          <table class="curr-table">
            <thead><tr><th>รหัสวิชา</th><th>ชื่อรายวิชา</th><th>หน่วยกิต</th><th>ชั้นปี</th></tr></thead>
            <tbody>
              <?php
              $stmt2 = $pdo->prepare("SELECT * FROM curriculum WHERE level = 'ปวส.' ORDER BY year ASC, subject_code ASC LIMIT 6");
              $stmt2->execute();
              while ($row = $stmt2->fetch()):
              ?>
                <tr><td class="code-col"><?= $row['subject_code'] ?></td><td class="name-col"><?= $row['subject_name'] ?></td><td><?= $row['credits'] ?></td><td>ปี <?= $row['year'] ?></td></tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="curr-footer-link">
        <a href="curriculum-view.php" class="text-link-all">ดูโครงสร้างหลักสูตรทั้งหมด <span class="material-symbols-outlined">arrow_right_alt</span></a>
      </div>
    </div>
  </section>

  <section id="teachers" class="staff-section">
    <div class="staff-bg-overlay"></div>
    <div class="container staff-container">
        <div class="section-head white">
            <span class="badge">บุคลากร</span>
            <h2 class="section-title">คณะครูผู้สอน</h2>
        </div>
        <div class="swiper staffSwiper">
            <div class="swiper-wrapper">
                <?php
                try {
                    $teacherStmt = $pdo->query("SELECT * FROM teachers ORDER BY id ASC");
                    $teachers = $teacherStmt->fetchAll();
                    foreach ($teachers as $teacher):
                ?>
                    <div class="swiper-slide">
                        <div class="staff-card">
                            <div class="staff-image">
                                <?php 
                                $t_img = !empty($teacher['image']) ? 'assets/images/teachers/'.$teacher['image'] : ''; 
                                if ($t_img && file_exists($t_img)):
                                ?>
                                    <img src="<?= $t_img ?>" alt="<?= htmlspecialchars($teacher['name']) ?>">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($teacher['name']) ?>&background=db2777&color=fff&size=512" alt="placeholder">
                                <?php endif; ?>
                            </div>
                            <div class="staff-info">
                                <div class="staff-meta">
                                    <span class="staff-tag">Computer Technology</span>
                                    <h3><?= htmlspecialchars($teacher['name']) ?></h3>
                                    <p class="staff-pos"><?= htmlspecialchars($teacher['position']) ?></p>
                                </div>
                                <?php if (!empty($teacher['education'])): ?>
                                    <p class="staff-edu"><strong>การศึกษา:</strong> <?= htmlspecialchars($teacher['education']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($teacher['expertise'])): ?>
                                    <p class="staff-exp"><strong>ความเชี่ยวชาญ:</strong> <?= htmlspecialchars($teacher['expertise']) ?></p>
                                <?php endif; ?>
                                <div class="staff-social">
                                    <span class="material-symbols-outlined">mail</span>
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; } catch (Exception $e) { echo $e->getMessage(); } ?>
            </div>
            <div class="swiper-pagination staff-pagination"></div>
        </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Slider Init [cite: 2025-07-09]
    const swiper = new Swiper('.newsSwiper', {
      slidesPerView: 1, spaceBetween: 25, grabCursor: true,
      navigation: { nextEl: ".swiper-button-next-custom", prevEl: ".swiper-button-prev-custom" },
      pagination: { el: ".swiper-pagination", clickable: true, dynamicBullets: true },
      breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 }, 1280: { slidesPerView: 4 } }
    });

    const staffSwiper = new Swiper('.staffSwiper', {
        slidesPerView: 1, spaceBetween: 30, loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: ".staff-pagination", clickable: true },
        effect: 'fade', fadeEffect: { crossFade: true }
    });

    // Tab Logic [cite: 2025-07-09]
    function openTab(evt, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].classList.remove("active"); }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
  </script>
</body>
</html>