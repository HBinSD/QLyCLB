<?php 
$pageTitle = "Trang Chủ Admin";
require_once __DIR__ . '/../includes/headers.php';
?>

<div class="dashboard-content">
    <h2 style="margin-bottom: 12px; color: #1e3a5f;">
        Xin chào Admin: <?= htmlspecialchars($_SESSION['user']['fullname'] ?? 'Quản trị viên') ?>!
    </h2>
    <p style="color: #64748b; margin-bottom: 20px;">Chào mừng bạn đến với bảng điều khiển Quản trị hệ thống.</p>

    <!-- Thống kê nhanh / Lối tắt -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
            <h4 style="color: #1e3a5f; margin-bottom: 10px;">Quản lý Sự kiện</h4>
            <a href="index.php?page=event" style="color: #2563eb; text-decoration: none; font-weight: bold;">Xem tất cả sự kiện &rarr;</a>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
            <h4 style="color: #1e3a5f; margin-bottom: 10px;">Hồ sơ cá nhân</h4>
            <a href="index.php?page=profile" style="color: #2563eb; text-decoration: none; font-weight: bold;">Xem thông tin &rarr;</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>