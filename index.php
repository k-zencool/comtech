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
                    // ดึงข้อมูลจากตาราง teachers
                    $teacherStmt = $pdo->query("SELECT * FROM teachers ORDER BY id ASC");
                    $teachers = $teacherStmt->fetchAll();

                    foreach ($teachers as $teacher):
                        // ตรวจสอบ Path รูปภาพให้ตรงกับที่ Admin อัปโหลด (uploads/teachers/) [cite: 2026-01-18]
                        $t_img_name = $teacher['image'];
                        $t_img_path = 'uploads/teachers/' . $t_img_name;
                        $has_t_img = (!empty($t_img_name) && file_exists($t_img_path));
                ?>
                    <div class="swiper-slide">
                        <div class="staff-card">
                            <div class="staff-image">
                                <?php if ($has_t_img): ?>
                                    <img src="<?= $t_img_path ?>" alt="<?= htmlspecialchars($teacher['name']) ?>">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($teacher['name']) ?>&background=db2777&color=fff&size=512" alt="placeholder">
                                <?php endif; ?>
                            </div>

                            <div class="staff-info">
                                <span class="staff-tag">Computer Technology</span>
                                <h3><?= htmlspecialchars($teacher['name']) ?></h3>
                                <p class="staff-pos"><?= htmlspecialchars($teacher['position']) ?></p>
                                
                                <?php if (!empty($teacher['education'])): ?>
                                    <p class="staff-edu">
                                        <strong>การศึกษา:</strong> 
                                        <?= htmlspecialchars($teacher['education']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($teacher['expertise'])): ?>
                                    <p class="staff-exp">
                                        <strong>ความเชี่ยวชาญ:</strong> 
                                        <?= htmlspecialchars($teacher['expertise']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="staff-social">
                                    <span class="material-symbols-outlined">mail</span>
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach; 
                } catch (Exception $e) { 
                    echo "<p class='text-white'>Error: " . $e->getMessage() . "</p>"; 
                } 
                ?>
            </div>
            <div class="swiper-pagination staff-pagination"></div>
        </div>
    </div>
</section>

<section id="awards" class="awards-section">
    <div class="container">
        <div class="section-head">
            <span class="badge">เกียรติประวัติ</span>
            <h2 class="section-title">รางวัลและความสำเร็จ</h2>
        </div>

        <div class="awards-grid">
            <?php
            try {
                // ดึงข้อมูลรางวัล 8 อันล่าสุด [cite: 2026-01-18]
                $awardStmt = $pdo->query("SELECT * FROM awards ORDER BY award_date DESC LIMIT 8");
                $awards = $awardStmt->fetchAll();

                if ($awards):
                    foreach ($awards as $award):
                        // ปี พ.ศ. [cite: 2025-07-09]
                        $awardYear = date('Y', strtotime($award['award_date'])) + 543;
                        
                        // *** จุดสำคัญ: แก้ Path ให้ตรงกับฝั่ง Admin ของมึง [cite: 2026-01-18] ***
                        $award_img_name = $award['image'];
                        $award_img_path = 'assets/images/awards/' . $award_img_name;
                        
                        // เช็คไฟล์รูป [cite: 2026-01-18]
                        $has_image = (!empty($award_img_name) && file_exists($award_img_path));
            ?>
                <div class="award-card">
                    <div class="award-visual">
                        <span class="award-tag-year">ปี <?= $awardYear ?></span>
                        
                        <?php if ($has_image): ?>
                            <img src="<?= $award_img_path ?>" class="award-img-render" alt="<?= htmlspecialchars($award['title']) ?>">
                        <?php else: ?>
                            <div class="award-placeholder">
                                <span class="material-symbols-outlined">emoji_events</span>
                                <small>AWARDS</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="award-body">
                        <h4><?= htmlspecialchars($award['title']) ?></h4>
                        <div class="award-info-meta">
                            <span class="material-symbols-outlined">account_balance</span>
                            <span><?= htmlspecialchars($award['organizer']) ?></span>
                        </div>
                    </div>
                </div>
            <?php 
                    endforeach;
                else:
                    echo '<div class="news-empty"><h3>ยังไม่มีข้อมูลรางวัลในขณะนี้</h3></div>';
                endif;
            } catch (Exception $e) { echo "Error: " . $e->getMessage(); } 
            ?>
        </div>
        
        <div class="curr-footer-link">
            <a href="awards-all.php" class="text-link-all">
                ดูรางวัลทั้งหมด 
                <span class="material-symbols-outlined">arrow_right_alt</span>
            </a>
        </div>
    </div>
</section>
  

<section id="projects" class="projects-section-dark">
    <div class="pj-bg-overlay"></div>
    
    <div class="container">
        <div class="section-head white">
            <span class="badge">Showcase</span>
            <h2 class="section-title">ผลงานนักศึกษา</h2>
        </div>

        <div class="news-slider-wrapper"> 
            <div class="swiper projectsSwiper">
                <div class="swiper-wrapper">
                    <?php
                    try {
                        // 1. แก้ชื่อตารางเป็น projects ตามรูป DB มึง
                        $pjStmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC LIMIT 12");
                        $projects = $pjStmt->fetchAll();

                        foreach ($projects as $pj):
                            // 2. เช็ค Path รูปภาพ [cite: 2026-01-18]
                            $pj_img = 'uploads/projects/' . $pj['image'];
                            $has_pj_img = (!empty($pj['image']) && file_exists($pj_img));
                    ?>
                        <div class="swiper-slide">
                            <div class="pj-dark-card">
                                <div class="pj-thumb">
                                    <?php if ($has_pj_img): ?>
                                        <img src="<?= $pj_img ?>" alt="<?= htmlspecialchars($pj['project_name']) ?>">
                                    <?php else: ?>
                                        <div class="pj-placeholder">
                                            <span class="material-symbols-outlined">terminal</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="pj-tag">ปีการศึกษา <?= htmlspecialchars($pj['academic_year']) ?></div>
                                </div>
                                <div class="pj-body">
                                    <h4><?= htmlspecialchars($pj['project_name']) ?></h4>
                                    
                                    <p class="pj-student">
                                        <span class="material-symbols-outlined">person</span>
                                        <?= htmlspecialchars($pj['student_names']) ?>
                                    </p>
                                    
                                    <a href="project-detail.php?id=<?= $pj['id'] ?>" class="pj-btn">
                                        รายละเอียด <span class="material-symbols-outlined">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; } catch (Exception $e) { echo "<p class='text-white'>Error: " . $e->getMessage() . "</p>"; } ?>
                </div>
                <div class="swiper-pagination pj-pagination"></div>
            </div>
            <div class="swiper-button-prev-custom pj-prev"><span class="material-symbols-outlined">chevron_left</span></div>
            <div class="swiper-button-next-custom pj-next"><span class="material-symbols-outlined">chevron_right</span></div>
        </div>
    </div>
</section>


  <?php require __DIR__ . '/includes/footer.php'; ?>

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

    // Student Projects Slider [cite: 2025-07-09]
const projectsSwiper = new Swiper('.projectsSwiper', {
    slidesPerView: 1,
    spaceBetween: 25,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: ".pj-pagination", clickable: true },
    navigation: { nextEl: ".pj-next", prevEl: ".pj-prev" },
    breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
        1440: { slidesPerView: 4 }
    }
});
  </script>
</body>
</html>