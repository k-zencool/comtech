<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComTech Uttaradit | แผนกวิชาช่างเทคโนโลยีคอมพิวเตอร์</title>

    <link rel="icon" type="image/png" href="assets/images/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/nav-style.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <header class="hero">
        <div class="hero-overlay">
            <div class="container hero-content">
                <span class="badge">UTTARADIT TECHNICAL COLLEGE</span>
                <h1>COMPUTER <br><span>TECHNOLOGY</span></h1>
                <p>มุ่งเน้นทักษะ สร้างสรรค์นวัตกรรม ก้าวทันเทคโนโลยีระดับมืออาชีพ พร้อมสร้างบุคลากรคุณภาพสู่สังคมดิจิทัล</p>
                <div class="hero-btns">
                    <a href="#projects" class="btn-primary">ดูผลงานนักศึกษา</a>
                    <a href="#news" class="btn-secondary">ข่าวสารล่าสุด</a>
                </div>
            </div>
        </div>
    </header>

    <section class="news-section container" id="news">
        <div class="section-header">
            <h2 class="section-title">ข่าวประชาสัมพันธ์</h2>
            <a href="all-news.php" class="view-all">ดูทั้งหมด <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="news-grid">
            <article class="news-card">
                <div class="news-img"><img src="assets/images/news-sample.jpg" alt="News"></div>
                <div class="news-body">
                    <span class="news-date"><i class="fa-regular fa-calendar"></i> 06 ม.ค. 2569</span>
                    <h3>เปิดรับสมัครนักศึกษาใหม่ ประจำปีการศึกษา 2569</h3>
                    <p>โอกาสมาถึงแล้ว! แผนกวิชาช่างเทคโนโลยีคอมพิวเตอร์ เปิดรับสมัครผู้สนใจเข้าศึกษาต่อทั้งระดับ ปวช. และ ปวส.</p>
                    <a href="#" class="read-more">อ่านต่อ <i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </article>
            <article class="news-card">
                <div class="news-img"><img src="assets/images/news-sample.jpg" alt="News"></div>
                <div class="news-body">
                    <span class="news-date"><i class="fa-regular fa-calendar"></i> 05 ม.ค. 2569</span>
                    <h3>อบรมเชิงปฏิบัติการ "IoT For Smart Life"</h3>
                    <p>พัฒนานักประดิษฐ์ยุคใหม่ด้วยการเขียนโปรแกรมควบคุมอุปกรณ์ผ่านสมาร์ทโฟน</p>
                    <a href="#" class="read-more">อ่านต่อ <i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </article>
            <article class="news-card">
                <div class="news-img"><img src="assets/images/news-sample.jpg" alt="News"></div>
                <div class="news-body">
                    <span class="news-date"><i class="fa-regular fa-calendar"></i> 04 ม.ค. 2569</span>
                    <h3>กิจกรรมค่ายคอมพิวเตอร์จิตอาสา</h3>
                    <p>นักศึกษาแผนกคอมฯ ร่วมใจลงพื้นที่ซ่อมบำรุงคอมพิวเตอร์ให้โรงเรียนในชนบท</p>
                    <a href="#" class="read-more">อ่านต่อ <i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </article>
        </div>
    </section>

    <section class="teachers-section" id="teachers">
        <div class="container">
            <div class="section-header center">
                <h2 class="section-title">คณาจารย์ผู้เชี่ยวชาญ</h2>
                <p>ทีมผู้สอนที่พร้อมถ่ายทอดประสบการณ์และดูแลนักศึกษาอย่างใกล้ชิด</p>
            </div>
            <div class="teacher-grid">
                <div class="teacher-card">
                    <div class="teacher-img"><img src="assets/images/teacher1.jpg" alt="Teacher"></div>
                    <div class="teacher-info">
                        <h4>ชื่อ-นามสกุล</h4>
                        <span>หัวหน้าแผนกวิชา</span>
                        <div class="teacher-contact">
                            <a href="#"><i class="fa-solid fa-envelope"></i></a>
                            <a href="#"><i class="fa-solid fa-phone"></i></a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </section>

    <section class="projects-section" id="projects">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title text-white">ผลงานนักศึกษา</h2>
                <div class="slider-controls">
                    <button id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="project-slider-wrapper">
                <div class="project-slider">
                    <div class="project-item">
                        <div class="project-card">
                            <img src="assets/images/project1.jpg" alt="Project">
                            <div class="project-overlay">
                                <h3>ระบบสมาร์ทฟาร์ม IoT</h3>
                                <p>คว้ารางวัลชนะเลิศสิ่งประดิษฐ์คนรุ่นใหม่</p>
                                <a href="#" class="view-btn">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                    <div class="project-item">
                        <div class="project-card">
                            <img src="assets/images/project2.jpg" alt="Project">
                            <div class="project-overlay">
                                <h3>แอปฯ บริการชุมชน</h3>
                                <p>โปรเจกต์พัฒนาระบบคิวออนไลน์</p>
                                <a href="#" class="view-btn">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                    <div class="project-item">
                        <div class="project-card">
                            <img src="assets/images/project3.jpg" alt="Project">
                            <div class="project-overlay">
                                <h3>เกมแนว VR Education</h3>
                                <p>สื่อการสอนยุคใหม่เสมือนจริง</p>
                                <a href="#" class="view-btn">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script>
        const slider = document.querySelector('.project-slider');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        let scrollAmount = 0;

        nextBtn.addEventListener('click', () => {
            const maxScroll = slider.scrollWidth - slider.clientWidth;
            if (scrollAmount < maxScroll) {
                scrollAmount += 320; 
                slider.style.transform = `translateX(-${scrollAmount}px)`;
            }
        });

        prevBtn.addEventListener('click', () => {
            if (scrollAmount > 0) {
                scrollAmount -= 320;
                slider.style.transform = `translateX(-${scrollAmount}px)`;
            }
        });
    </script>
</body>
</html>