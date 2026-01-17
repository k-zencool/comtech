<footer class="user-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-logo mb-3">
                    <img src="assets/images/logo.png" width="60" alt="UTC Logo" class="mb-2">
                    <h5 class="fw-bold text-dark m-0">COMTECH UTC</h5>
                    <p class="text-pink small fw-bold">UTTARADIT TECHNICAL COLLEGE</p>
                </div>
                <p class="text-muted small">
                    มุ่งเน้นผลิตศาสตราจารย์และนักเทคโนโลยีที่มีคุณภาพ 
                    ก้าวทันโลกดิจิทัลด้วยมาตรฐานการศึกษาระดับวิชาชีพ [cite: 2025-05-30]
                </p>
                <div class="social-links d-flex gap-3 mt-4">
                    <a href="#" class="social-icon fb"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-icon line"><i class="fa-brands fa-line"></i></a>
                    <a href="#" class="social-icon yt"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 ps-lg-5">
                <h6 class="fw-bold mb-4 text-dark border-start border-pink border-4 ps-3">เมนูหลัก</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="index.php">หน้าแรก</a></li>
                    <li><a href="news.php">ข่าวสารกิจกรรม</a></li>
                    <li><a href="curriculum.php">หลักสูตรที่เปิดสอน</a></li>
                    <li><a href="teachers.php">คณาจารย์</a></li>
                    <li><a href="awards.php">รางวัลและความภูมิใจ</a></li>
                </ul>
            </div>

            <div class="col-lg-6 col-md-12">
                <h6 class="fw-bold mb-4 text-dark border-start border-pink border-4 ps-3">ติดต่อเรา</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-2">
                            <i class="fa-solid fa-location-dot text-pink me-2"></i>
                            เลขที่ 9 ถ.แปดวา ต.ท่าอิฐ <br>อ.เมือง จ.อุตรดิตถ์ 53000
                        </p>
                        <p class="text-muted small mb-2">
                            <i class="fa-solid fa-phone text-pink me-2"></i>
                            055-411-065 ต่อ 123
                        </p>
                        <p class="text-muted small">
                            <i class="fa-solid fa-envelope text-pink me-2"></i>
                            comtech@utc.ac.th
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <div class="footer-map-container rounded-15 overflow-hidden shadow-sm">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3795.343464115933!2d100.0911423!3d17.6321272!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30ec539322222223%3A0x8888888888888888!2sUttaradit%20Technical%20College!5e0!3m2!1sen!2sth!4v1700000000000!5m2!1sen!2sth" 
                                width="100%" height="120" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom py-3 bg-dark text-white text-center">
        <div class="container">
            <p class="m-0 small">
                © <?= date('2025') ?> แผนกวิชาคอมพิวเตอร์ธุรกิจ - วิทยาลัยเทคนิคอุตรดิตถ์ [cite: 2025-05-30, 2026]
            </p>
        </div>
    </div>
</footer>

<style>
.user-footer { background: #fff; border-top: 1px solid #eee; }
.border-pink { border-color: var(--official-pink) !important; }
.text-pink { color: var(--official-pink); }
.footer-links li { margin-bottom: 12px; }
.footer-links li a { color: var(--text-gray); text-decoration: none; transition: 0.3s; font-size: 14px; }
.footer-links li a:hover { color: var(--official-pink); padding-left: 8px; }

.social-icon { 
    width: 38px; height: 38px; border-radius: 50%; 
    display: flex; align-items: center; justify-content: center; 
    background: var(--bg-light); color: var(--text-gray); 
    transition: 0.3s; text-decoration: none; 
}
.social-icon:hover { background: var(--official-pink); color: #fff; transform: translateY(-3px); }
.rounded-15 { border-radius: 15px !important; }
</style>