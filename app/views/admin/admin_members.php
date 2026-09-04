<?php 
$pageTitle = "Quản Lý Tài Khoản";
$users = $users ?? [];
$successMessage = $successMessage ?? "";
$errorMessage   = $errorMessage ?? "";
require_once __DIR__ . '/../includes/headers.php'; 
?>

<div style="display: flex; flex-direction: column; gap: 20px;">
    <?php if (!empty($successMessage)): ?>
        <div style="background: #dcfce7; color: #166534; padding: 14px 20px; border-radius: 8px; border: 1px solid #86efac; font-weight: bold;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 14px 20px; border-radius: 8px; border: 1px solid #fca5a5; font-weight: bold;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1e3a5f; margin: 0; font-size: 20px;">QUẢN LÝ TÀI KHOẢN HỆ THỐNG</h2>
        <p style="color: #64748b; margin: 4px 0 0 0; font-size: 13px;">Phân quyền người dùng và khóa/mở tài khoản</p>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Họ và Tên</th>
                    <th style="padding: 12px;">Username</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Vai Trò</th>
                    <th style="padding: 12px;">Trạng Thái</th>
                    <th style="padding: 12px; text-align: center;">Cập Nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): foreach ($users as $index => $u): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <form action="index.php?page=admin-members" method="POST">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($u['username']) ?>">
                            <td style="padding: 12px; color: #64748b;"><?= $index + 1 ?></td>
                            <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($u['fullname'] ?? '') ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($u['username']) ?></td>
                            <td style="padding: 12px; color: #475569;"><?= htmlspecialchars($u['email'] ?? 'N/A') ?></td>
                            <td style="padding: 12px;">
                                <select name="role" style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <option value="member" <?= $u['role'] === 'member' ? 'selected' : '' ?>>Thành viên</option>
                                    <option value="organizer" <?= $u['role'] === 'organizer' ? 'selected' : '' ?>>Ban tổ chức</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </td>
                            <td style="padding: 12px;">
                                <select name="status" style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <option value="1" <?= (int)$u['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="0" <?= (int)$u['status'] === 0 ? 'selected' : '' ?>>Khóa</option>
                                </select>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <button type="submit" style="background: #1e3a5f; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">Lưu</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>