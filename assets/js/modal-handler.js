/**
 * 🚀 Computer Technology UTC - Master JS Handler [cite: 2026-02-19]
 * แก้บั๊ก Slide Stacking ด้วย CrossFade และจัดการ Modal/Curriculum ครบจบในที่เดียว
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ JS Loaded & Ready!");

    // --- Helper Function สำหรับเปิดใช้งาน Swiper ---
    const initSwiper = (cls, opts) => document.querySelector(cls) ? new Swiper(cls, opts) : null;

    // 1. News Slider [cite: 2026-02-19]
    initSwiper('.newsSwiper', {
        slidesPerView: 1, spaceBetween: 25,
        navigation: { nextEl: ".swiper-button-next-custom", prevEl: ".swiper-button-prev-custom" },
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 }, 1280: { slidesPerView: 4 } }
    });

    // 2. Staff Slider (แก้บั๊กซ้อนทับที่นี่!) [cite: 2026-02-19]
    initSwiper('.staffSwiper', {
        slidesPerView: 1,
        spaceBetween: 0, // ต้องเป็น 0 เมื่อใช้ Fade เพื่อไม่ให้ Layout เด้ง [cite: 2026-02-19]
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: ".staff-pagination", clickable: true },
        effect: 'fade', // ใช้เอฟเฟกต์จางหาย [cite: 2026-02-19]
        fadeEffect: {
            crossFade: true // 🛠️ หัวใจหลัก: สั่งให้สไลด์เก่าจางออกทันทีที่สไลด์ใหม่จางเข้า [cite: 2026-02-19]
        }
    });

    // 3. Projects Slider [cite: 2026-02-19]
    initSwiper('.projectsSwiper', {
        slidesPerView: 1, spaceBetween: 25, loop: true,
        pagination: { el: ".pj-pagination", clickable: true },
        navigation: { nextEl: ".pj-next", prevEl: ".pj-prev" },
        breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 }, 1440: { slidesPerView: 4 } }
    });
});

// --- 🎓 Curriculum Toggle Function (แผ่-หุบ ตาราง) [cite: 2026-02-19] ---
function toggleCurriculum() {
    const hiddenRows = document.querySelectorAll('.curr-row-hidden');
    const btn = document.getElementById('btn-show-curr');
    
    if (!btn) return;

    const isShowingAll = btn.innerText.includes('แสดงน้อยลง');

    if (isShowingAll) {
        hiddenRows.forEach(row => { row.style.display = 'none'; });
        btn.innerHTML = `ดูโครงสร้างหลักสูตรทั้งหมด <span class="material-symbols-outlined">expand_more</span>`;
        document.getElementById('curriculum').scrollIntoView({ behavior: 'smooth' });
    } else {
        hiddenRows.forEach(row => { row.style.display = 'table-row'; });
        btn.innerHTML = `แสดงน้อยลง <span class="material-symbols-outlined">expand_less</span>`;
    }
}

// --- 🏛️ Universal Modal Functions (สำหรับข่าวและโปรเจกต์) [cite: 2026-02-19] ---
function openDetailModal(title, img, content, tag) {
    const modal = document.getElementById('detailModal');
    if (!modal) return;

    document.getElementById('mTitle').innerText = title;
    document.getElementById('mTag').innerText = tag;
    document.getElementById('mContent').innerHTML = content.replace(/\n/g, '<br>');

    const mImg = document.getElementById('mImg');
    if (img && img !== '') {
        mImg.src = img;
        mImg.style.display = 'block';
    } else {
        mImg.style.display = 'none';
    }

    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}

function closeModal(e) {
    const modal = document.getElementById('detailModal');
    if (e == null || e.target.id === 'detailModal') {
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
}

// --- 📑 Tabs Logic (ปวช./ปวส.) [cite: 2026-02-19] ---
function openTab(evt, tabName) {
    let i, content, links;
    content = document.getElementsByClassName("tab-content");
    for (i = 0; i < content.length; i++) content[i].classList.remove("active");
    links = document.getElementsByClassName("tab-btn");
    for (i = 0; i < links.length; i++) links[i].classList.remove("active");
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

// ⌨️ ปิด Modal ด้วยปุ่ม Escape เพื่อความเทพ [cite: 2026-02-19]
window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(null); });