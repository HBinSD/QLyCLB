<?php 
$pageTitle = "Trang Chủ Sinh Viên";
require_once __DIR__ . '/includes/headers.php'; 
$user = $_SESSION['user'] ?? [];
$avatar = $user['avatar'] ?? '';
?>

<div class="dashboard-content">
    <!-- Hero Banner Chào Sinh Viên -->
    <div style="background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 30px; border-radius: 12px; display: flex; align-items: center; gap: 20px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid white; flex-shrink: 0; background: #ffffff;">
            <?php if (!empty($avatar)): ?>
                <img src="<?= htmlspecialchars($avatar) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #dbeafe; color: #1e3a5f; font-size: 32px; font-weight: bold;">
                    <?= strtoupper(substr($user['fullname'] ?? 'S', 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h2 style="margin: 0 0 6px 0; font-size: 24px;">Xin chào sinh viên, <?= htmlspecialchars($user['fullname'] ?? 'Bạn') ?>! 👋</h2>
            <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                Mã SV: <strong><?= htmlspecialchars($user['id_number'] ?? 'Chưa cập nhật') ?></strong> | Email: <strong><?= htmlspecialchars($user['email'] ?? '') ?></strong>
            </p>
        </div>
    </div>

    <!-- Quick Links -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
            <h3 style="color: #1e3a5f; margin-top: 0;">📅 Sự Kiện Sắp Diễn Ra</h3>
            <p style="color: #64748b; font-size: 14px;">Khám phá các hoạt động ngoại khóa của các CLB và đăng ký tham gia ngay.</p>
            <a href="index.php?page=event" style="color: #2563eb; font-weight: bold; text-decoration: none;">Xem danh sách sự kiện &rarr;</a>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
            <h3 style="color: #1e3a5f; margin-top: 0;">👤 Hồ Sơ Cá Nhân</h3>
            <p style="color: #64748b; font-size: 14px;">Kiểm tra và cập nhật thông tin cá nhân, ảnh đại diện, số điện thoại, ngày sinh.</p>
            <a href="index.php?page=profile" style="color: #2563eb; font-weight: bold; text-decoration: none;">Chỉnh sửa hồ sơ &rarr;</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>