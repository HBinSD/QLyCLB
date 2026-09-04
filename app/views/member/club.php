<?php 
$pageTitle = "Giới Thiệu Câu Lạc Bộ";
$club = $club ?? null;
$createdAt = (!empty($club) && !empty($club['created_at'])) 
    ? date('d/m/Y', strtotime($club['created_at'])) 
    : 'Chưa cập nhật';
require_once __DIR__ . '/../includes/headers.php';
?>

<link rel="stylesheet" href="/css/club.css">

<div class="club-layout">
    <!-- SUB SIDEBAR CLB -->
    <aside class="club-sidebar">
        <div class="club-sidebar-title">QUẢN LÝ CLB</div>
        <nav class="club-menu">
            <a href="index.php?page=club" class="club-menu-item active">
                <span>Giới thiệu CLB</span>
            </a>
            <a href="index.php?page=event" class="club-menu-item">
                <span>Sự kiện CLB</span>
            </a>
        </nav>
    </aside>

    <!-- NỘI DUNG THÔNG TIN CLB -->
    <main class="club-content">
        <?php if (empty($club)): ?>
            <div style="padding: 20px; color: #856404; background: #fff3cd; border-radius: 8px; font-weight: bold; text-align: center;">
                 Bạn chưa tham gia Câu lạc bộ nào trong hệ thống.
            </div>
        <?php else: ?>
            <div class="club-intro">
                <h2> CÂU LẠC BỘ BẠN ĐANG THAM GIA</h2>
                <div class="club-grid">
                    <div class="club-card">
                        <div class="club-label">Tên CLB</div>
                        <div class="club-value"><?= htmlspecialchars($club['club_name'] ?? 'Chưa cập nhật') ?></div>
                    </div>
                    <div class="club-card">
                        <div class="club-label">Người chủ nhiệm</div>
                        <div class="club-value"><?= htmlspecialchars($club['creator_name'] ?? 'Chưa cập nhật') ?></div>
                    </div>
                    <div class="club-card">
                        <div class="club-label">Ngày thành lập</div>
                        <div class="club-value"><?= $createdAt ?></div>
                    </div>
                    <div class="club-card">
                        <div class="club-label">Trạng thái</div>
                        <div class="club-value">
                            <?php if (($club['status'] ?? 0) == 1): ?>
                                <span class="club-status active">● Đang hoạt động</span>
                            <?php else: ?>
                                <span class="club-status inactive">● Ngưng hoạt động</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="club-card full">
                        <div class="club-label">Mô tả giới thiệu</div>
                        <div class="club-value normal"><?= nl2br(htmlspecialchars($club['description'] ?? 'Chưa có thông tin mô tả.')) ?></div>
                    </div>
                    <div class="club-card full">
                        <div class="club-label">Nội quy CLB</div>
                        <div class="club-value normal"><?= nl2br(htmlspecialchars($club['rule'] ?? 'Chưa có nội quy quy định.')) ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>