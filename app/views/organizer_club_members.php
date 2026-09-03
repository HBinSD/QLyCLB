<?php 
$pageTitle = "Danh Sách Thành Viên CLB";
require_once __DIR__ . '/includes/headers.php'; 
?>

<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- THÔNG BÁO -->
    <?php if (!empty($successMessage)): ?>
        <div style="background: #dcfce7; color: #166534; padding: 14px 20px; border-radius: 8px; font-weight: bold;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 14px 20px; border-radius: 8px; font-weight: bold;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <!-- HEADER & NÚT THÊM THÀNH VIÊN -->
    <div style="background: white; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="color: #1e3a5f; margin: 0; font-size: 20px;">DANH SÁCH THÀNH VIÊN CLB</h2>
            <p style="color: #64748b; margin: 4px 0 0 0; font-size: 13px;">Quản lý thành viên, vai trò và trạng thái hoạt động</p>
        </div>
        <a href="index.php?page=club-member-add" style="background: #1e3a5f; color: white; padding: 10px 18px; border-radius: 8px; font-weight: bold; text-decoration: none;">
             Thêm Thành Viên
        </a>
    </div>

    <!-- BẢNG DANH SÁCH THÀNH VIÊN -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Họ Và Tên</th>
                    <th style="padding: 12px;">Username</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Vai Trò / Chức Vụ</th>
                    <th style="padding: 12px;">Ngày Tham Gia</th>
                    <th style="padding: 12px;">Trạng Thái</th>
                    <th style="padding: 12px; text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)): foreach ($members as $index => $m): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; color: #64748b;"><?= $index + 1 ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($m['fullname']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($m['username']) ?></td>
                        <td style="padding: 12px; color: #475569;"><?= htmlspecialchars($m['email']) ?></td>
                        <td style="padding: 12px;">
                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">
                                <?= htmlspecialchars($m['position'] ?: 'Thành viên') ?>
                            </span>
                        </td>
                        <td style="padding: 12px; color: #64748b;"><?= date('d/m/Y', strtotime($m['joined_at'])) ?></td>
                        <td style="padding: 12px;">
                            <?= (int)$m['status'] === 1 
                                ? '<span style="color: green; font-weight: bold;">● Hoạt động</span>' 
                                : '<span style="color: red; font-weight: bold;">● Tạm ngưng</span>'; 
                            ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="index.php?page=club-member-edit&username=<?= urlencode($m['username']) ?>" style="color: #2563eb; font-weight: bold; text-decoration: none; margin-right: 8px;">Sửa</a> | 
                            <a href="index.php?page=club-member-delete&username=<?= urlencode($m['username']) ?>" onclick="return confirm('Bạn có chắc muốn xóa thành viên này khỏi CLB?')" style="color: #ef4444; font-weight: bold; text-decoration: none; margin-left: 8px;">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">Chưa có thành viên nào trong câu lạc bộ!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>