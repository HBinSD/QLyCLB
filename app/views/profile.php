<?php 
$pageTitle = "Thông Tin Cá Nhân";
require_once __DIR__ . '/includes/headers.php'; 
$avatar = $user['avt_links'] ?? $user['avatar'] ?? '';
?>

<link rel="stylesheet" href="css/profile.css">

<div class="profile-page">
    <h1 class="profile-title">THÔNG TIN CÁ NHÂN</h1>
    
    <div class="profile-container" style="background: white; padding: 30px; border-radius: 12px; display: flex; gap: 30px; border: 1px solid #e2e8f0;">
        <!-- Avatar Section -->
        <div class="profile-avatar-section" style="text-align: center; width: 200px;">
            <div class="profile-avatar" style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid #1e3a5f;">
                <?php if (!empty($avatar)): ?>
                    <img src="<?= htmlspecialchars($avatar); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; background: #cee5ff; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #1e3a5f;">
                        <?= strtoupper(substr($user['fullname'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-role" style="margin-top: 15px; padding: 6px 12px; background: #e0f2fe; color: #0369a1; border-radius: 20px; font-weight: bold; font-size: 13px;">
                <?php 
                    if (($user['role'] ?? '') === 'admin') echo 'Người quản trị';
                    elseif (($user['role'] ?? '') === 'organizer') echo 'Ban tổ chức';
                    else echo 'Thành viên CLB';
                ?>
            </div>
        </div>

        <!-- Info Details -->
        <div class="profile-info" style="flex: 1;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="width: 160px; font-weight: bold; color: #475569;">Họ và tên:</td>
                    <td><?= htmlspecialchars($user['fullname'] ?? ''); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Tên đăng nhập:</td>
                    <td><?= htmlspecialchars($user['username'] ?? ''); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Email:</td>
                    <td><?= htmlspecialchars($user['email'] ?? ''); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Số điện thoại:</td>
                    <td><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật'); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Giới tính:</td>
                    <td><?= htmlspecialchars($user['gender'] ?? 'Chưa cập nhật'); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Ngày sinh:</td>
                    <td><?= !empty($user['dob']) ? date('d/m/Y', strtotime($user['dob'])) : 'Chưa cập nhật'; ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9; height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Mã Sinh Viên / CCCD:</td>
                    <td><?= htmlspecialchars($user['id_number'] ?? 'Chưa cập nhật'); ?></td>
                </tr>
                <tr style="height: 45px;">
                    <td style="font-weight: bold; color: #475569;">Ngày tạo tài khoản:</td>
                    <td><?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                </tr>
            </table>

            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php?page=edit-profile" style="background: #1e3a5f; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    Chỉnh Sửa Thông Tin
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>