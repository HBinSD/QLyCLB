<?php 
$pageTitle = "Quản Lý Câu Lạc Bộ";
$clubs = $clubs ?? [];
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

    <!-- FORM TẠO CÂU LẠC BỘ MỚI -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h3 style="color: #1e3a5f; margin-top: 0; margin-bottom: 15px;">THÊM CÂU LẠC BỘ MỚI</h3>
        <form action="index.php?page=admin-club-create" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tên CLB (*)</label>
                <input type="text" name="club_name" required placeholder="Nhập tên CLB..." style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Chủ Nhiệm / Quản Lý (*)</label>
                <select name="owner_id" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; background: white; box-sizing: border-box;">
                    <option value="">-- Chọn Chủ Nhiệm --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars($u['username']) ?>">
                            <?= htmlspecialchars($u['fullname'] ?? $u['username']) ?> (<?= $u['username'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="grid-column: 1 / -1;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Mô Tả CLB</label>
                <textarea name="description" rows="2" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; box-sizing: border-box;"></textarea>
            </div>
            <div style="grid-column: 1 / -1; text-align: right;">
                <button type="submit" style="background: #16a34a; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">+ Tạo CLB</button>
            </div>
        </form>
    </div>

    <!-- DANH SÁCH CÂU LẠC BỘ -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h3 style="color: #1e3a5f; margin-top: 0; margin-bottom: 15px;">DANH SÁCH CÂU LẠC BỘ</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">Mã CLB</th>
                    <th style="padding: 12px;">Tên Câu Lạc Bộ</th>
                    <th style="padding: 12px;">Chủ Nhiệm</th>
                    <th style="padding: 12px;">Ngày Thành Lập</th>
                    <th style="padding: 12px;">Trạng Thái</th>
                    <th style="padding: 12px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clubs)): foreach ($clubs as $c): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: bold;">#<?= $c['club_id'] ?></td>
                        <td style="padding: 12px; color: #1e3a5f; font-weight: bold;"><?= htmlspecialchars($c['club_name']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($c['owner_name'] ?? $c['owner_id']) ?></td>
                        <td style="padding: 12px; color: #64748b;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        <td style="padding: 12px;">
                            <?= (int)$c['status'] === 1 ? '<span style="color:green; font-weight:bold;">Hoạt động</span>' : '<span style="color:red;">Ngừng</span>' ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="index.php?page=admin-club-delete&id=<?= $c['club_id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa CLB này?')" style="color: #ef4444; font-weight: bold; text-decoration: none;">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">Chưa có CLB nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>