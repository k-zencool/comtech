<?php
require_once __DIR__ . '/config/db.php';

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// 🟢 1. เช็คว่ากดดูรายละเอียดวิชาหรือไม่ [cite: 2025-07-09]
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$curr_detail = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM curriculum WHERE id = ?");
    $stmt->execute([$id]);
    $curr_detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 🟢 2. ดึงข้อมูลทั้งหมดจัดกลุ่มตามระดับ [cite: 2026-01-18]
$stmt = $pdo->query("SELECT * FROM curriculum ORDER BY level ASC, year ASC, subject_code ASC");
$raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$group = [];
foreach ($raw as $r) {
    // รวมค่า level และ year ให้ตรงกับชื่อแท็บ (ปวช.1, ปวส.2) [cite: 2026-01-18]
    $lv = trim($r['level']) . trim($r['year']);
    $group[$lv][] = $r;
}

$levels = ['ปวช.1','ปวช.2','ปวช.3','ปวส.1','ปวส.2'];
foreach (array_keys($group) as $lv) {
    if (!in_array($lv, $levels, true)) $levels[] = $lv;
}

$active = $levels[0] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หลักสูตรสาขาวิชา | ComTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        :root { --pink: #db2777; --ink: #0f172a; }
        body { background-color: #f8fafc; }
        .bg-pink { background-color: var(--pink) !important; color: #fff; }
        .text-pink { color: var(--pink) !important; }
        .rounded-20 { border-radius: 20px; }
        .rounded-24 { border-radius: 24px; }
        
        .tab-btn { transition: 0.3s; border: 1px solid #e2e8f0 !important; }
        .tab-btn.active { box-shadow: 0 8px 15px rgba(219, 39, 119, 0.2); border-color: var(--pink) !important; }
        
        .subject-code-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-family: monospace; font-weight: 600; color: var(--ink); }
        .table thead th { background: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; }
        .curr-row:hover { background-color: #fff1f2 !important; cursor: pointer; }
        
        .btn-detail { color: var(--pink); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="container" style="margin-top: 120px; padding-bottom: 100px;">

    <?php if ($curr_detail): ?>
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <a href="curriculum-view.php" class="text-pink d-flex align-items-center gap-2 mb-4" style="text-decoration:none; font-weight:600;">
                    <span class="material-symbols-outlined">arrow_back</span> กลับไปหน้าหลักสูตร
                </a>
                <div class="card border-0 shadow-sm rounded-24 overflow-hidden">
                    <div class="card-header bg-dark text-white p-5 border-0">
                        <span class="badge bg-pink mb-3"><?= h($curr_detail['level']) . h($curr_detail['year']) ?></span>
                        <h1 class="fw-bold mb-2"><?= h($curr_detail['subject_name']) ?></h1>
                        <p class="mb-0 opacity-75">รหัสวิชา: <?= h($curr_detail['subject_code']) ?> | หน่วยกิต: <?= h($curr_detail['credits']) ?></p>
                    </div>
                    <div class="card-body p-5 bg-white">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-pink">description</span> คำอธิบายรายวิชา
                        </h5>
                        <p class="text-secondary lh-lg fs-5"><?= nl2br(h($curr_detail['description'])) ?: 'ไม่มีข้อมูลคำอธิบายรายวิชา' ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6">โครงสร้างหลักสูตร</h2>
            <p class="text-muted">ข้อมูลรายวิชาเรียนแยกตามแผนการเรียน ปวช. และ ปวส.</p>
        </div>

        <div class="card border-0 shadow-sm rounded-20 p-3 mb-4 bg-white">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0"><span class="material-symbols-outlined text-muted">search</span></span>
                <input id="searchBox" class="form-control border-0 shadow-none" placeholder="ค้นหารหัสวิชา หรือชื่อวิชาในแท็บนี้...">
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-20 p-2 mb-4 bg-white">
            <div class="curr-tabs d-flex gap-2 overflow-auto p-1">
                <?php foreach($levels as $i => $lv): ?>
                    <button type="button" class="tab-btn btn <?= ($lv === $active ? 'active' : 'btn-white') ?> px-4 py-3 rounded-15 fw-bold text-nowrap" 
                            data-tab="<?= h($lv) ?>" style="min-width: 140px; <?= ($lv === $active ? 'background:#111; color:#fff;' : 'background:#fff; color:#111;') ?>">
                        <?= h($lv) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach($levels as $lv): ?>
            <div class="tab-pane" data-pane="<?= h($lv) ?>" style="<?= ($lv === $active ? '' : 'display:none;') ?>">
                <div class="card border-0 shadow-sm rounded-20 overflow-hidden bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4 py-3">รหัสวิชา</th>
                                    <th class="py-3">ชื่อรายวิชา</th>
                                    <th class="py-3 text-center">หน่วยกิต</th>
                                    <th class="py-3">หมวดวิชา</th>
                                    <th class="pe-4 py-3 text-end">รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($group[$lv])): ?>
                                    <?php foreach($group[$lv] as $s): ?>
                                        <tr class="row-item curr-row" onclick="window.location='curriculum-view.php?id=<?= $s['id'] ?>'">
                                            <td class="ps-4"><span class="subject-code-tag"><?= h($s['subject_code']) ?></span></td>
                                            <td class="fw-bold"><?= h($s['subject_name']) ?></td>
                                            <td class="text-center"><span class="badge bg-light text-dark border-0 fw-normal"><?= h($s['credits']) ?></span></td>
                                            <td><small class="text-muted"><?= h($s['category']) ?></small></td>
                                            <td class="pe-4 text-end">
                                                <a href="curriculum-view.php?id=<?= $s['id'] ?>" class="btn-detail">ดูวิชา <span class="material-symbols-outlined fs-6">arrow_forward</span></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">ยังไม่มีข้อมูลของระดับ <?= h($lv) ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
(function(){
    const tabBtns = document.querySelectorAll('.tab-btn');
    const panes = document.querySelectorAll('.tab-pane');

    function setActive(tab){
        tabBtns.forEach(b => {
            const on = b.dataset.tab === tab;
            b.classList.toggle('active', on);
            b.style.background = on ? '#111' : '#fff';
            b.style.color = on ? '#fff' : '#111';
        });
        panes.forEach(p => { p.style.display = (p.dataset.pane === tab) ? '' : 'none'; });
        localStorage.setItem('curr_tab', tab);
    }

    tabBtns.forEach(btn => { btn.addEventListener('click', () => setActive(btn.dataset.tab)); });

    const saved = localStorage.getItem('curr_tab');
    if (saved && [...tabBtns].some(b => b.dataset.tab === saved)) setActive(saved);

    // Search Logic [cite: 2025-05-31]
    const box = document.getElementById('searchBox');
    if (box) {
        box.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            const activePane = [...panes].find(p => p.style.display !== 'none');
            if(!activePane) return;
            activePane.querySelectorAll('tbody tr.row-item').forEach(tr => {
                tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
})();
</script>
</body>
</html>