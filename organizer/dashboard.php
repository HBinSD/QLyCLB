<?php
session_start();
require_once "../includes/auth.php";

$pageTitle = "Trang chủ";

require_once "../includes/headers.php";
$user = $_SESSION['user'] ?? [];
?>

<!-- Nội dung trang được đặt trong thẻ main đã mở từ headers.php -->
<div class="dashboard-content">
    <h2 style="margin-bottom: 12px; color: #1e3a5f;">Xin chào, <?= htmlspecialchars($user['fullname'] ?? 'Thành viên') ?>!</h2>
    <p style="color: #64748b;">Chào mừng bạn đến với hệ thống quản lý câu lạc bộ.</p>
</div>


<?php
    require_once "../includes/footer.php";
?>
</body>