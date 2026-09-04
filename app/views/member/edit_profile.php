<?php 
$pageTitle = "Chỉnh Sửa Thông Tin Cá Nhân";
require_once __DIR__ . '/../includes/headers.php';
?>

<div class="profile-page" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; max-width: 800px; margin: 0 auto;">
    <h1 style="color: #1e3a5f; font-size: 22px; margin-bottom: 20px;">CHỈNH SỬA THÔNG TIN CÁ NHÂN</h1>

    <?php if (!empty($error)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=edit-profile" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Họ và tên (*)</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tên đăng nhập</label>
            <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f1f5f9;">
            <small style="color: #64748b;">Tên đăng nhập không thể thay đổi.</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Email (*)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Số điện thoại</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Giới tính</label>
            <select name="gender" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                <option value="">-- Chọn giới tính --</option>
                <option value="Nam" <?= (($user['gender'] ?? '') === 'Nam') ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= (($user['gender'] ?? '') === 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                <option value="Khác" <?= (($user['gender'] ?? '') === 'Khác') ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Ngày sinh</label>
            <input type="date" name="dob" value="<?= !empty($user['dob']) ? date('Y-m-d', strtotime($user['dob'])) : '' ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Mã Sinh Viên / Số CCCD</label>
            <input type="text" name="id_number" value="<?= htmlspecialchars($user['id_number'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Link Ảnh Đại Diện (URL)</label>
            <input type="url" name="avatar" value="<?= htmlspecialchars($user['avt_links'] ?? '') ?>" placeholder="https://example.com/avatar.jpg" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Hoặc Upload Ảnh Từ Máy Tính</label>
            <input type="file" name="avatar_file" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="background: #1e3a5f; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Lưu Thay Đổi</button>
            <a href="index.php?page=profile" style="background: #e2e8f0; color: #334155; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>