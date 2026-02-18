/**
 * 🚀 Universal Modal Handler - ตัวเดียวจบทั้งหน้าเว็บ
 */
function openDetailModal(title, img, content, tag) {
    const modal = document.getElementById('newsModal'); // ใช้ ID เดียวกันทั้งหน้า [cite: 2026-01-18]
    const imgContainer = document.getElementById('modalImgContainer');
    
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalTag').innerText = tag;
    document.getElementById('modalText').innerHTML = content;

    // ถ้ามีรูปโชว์รูป ถ้าไม่มีก็ซ่อน
    if (img) {
        imgContainer.innerHTML = `<img src="${img}" class="modal-img" alt="detail-img">`;
        imgContainer.style.display = 'block';
    } else {
        imgContainer.style.display = 'none';
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // ล็อกหน้าจอหลังไม่ให้เลื่อน
}

function closeNewsModal(e) {
    const modal = document.getElementById('newsModal');
    if (e == null || e.target.id === 'newsModal') {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}


/**
 * 🚀 ฟังก์ชันสลับการแสดงผลหลักสูตรแบบแผ่ขยาย (Expand)
 */
function toggleCurriculum() {
    const tablePvoc = document.getElementById('table-pvoc');
    const tableHvoc = document.getElementById('table-hvoc');
    const btn = document.getElementById('btn-expand-curr');
    const btnText = btn.querySelector('span:first-child');
    const btnIcon = document.getElementById('expand-icon');

    // สลับ Class expanded เพื่อโชว์แถวที่ซ่อน
    tablePvoc.classList.toggle('expanded');
    tableHvoc.classList.toggle('expanded');

    if (tablePvoc.classList.contains('expanded')) {
        btnText.innerText = 'แสดงน้อยลง';
        btnIcon.innerText = 'expand_less';
    } else {
        btnText.innerText = 'ดูโครงสร้างหลักสูตรทั้งหมด';
        btnIcon.innerText = 'expand_more';
        // เลื่อนหน้าจอกลับมาที่หัวข้อหลักสูตรเพื่อให้ User ไม่หลง
        document.getElementById('curriculum').scrollIntoView({ behavior: 'smooth' });
    }
}