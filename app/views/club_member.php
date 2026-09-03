<?php 
$pageTitle = "Danh Sách Thành Viên CLB";

// Khai báo giá trị mặc định tránh lỗi Undefined variable
$clubInfo = $clubInfo ?? null;
$members  = $members ?? [];

require_once __DIR__ . '/includes/headers.php'; 
?>

<link rel="stylesheet" href="css/club.css">
<link rel="stylesheet" href="css/club_member.css">

<div class="club-layout">
    <!-- SUB SIDEBAR CLB -->
    <aside class="club-sidebar">
        <div class="club-sidebar-title">
            <span>QUẢN LÝ CLB</span>
        </div>
        <nav class="club-menu">
            <a href="index.php?page=club" class="club-menu-item">
                <span>Giới thiệu CLB</span>
            </a>
            <a href="index.php?page=club-member" class="club-menu-item active">
                <span>Danh sách thành viên</span>
            </a>
            <a href="index.php?page=event" class="club-menu-item">
                <span>Sự kiện CLB</span>
            </a>
        </nav>
    </aside>

    <!-- CONTENT -->
    <main class="club-content">
        <?php if (empty($clubInfo)): ?>
            <div class="members-empty" style="background: white; padding: 40px; text-align: center; border-radius: 10px;">
                <h2>Danh sách thành viên</h2>
                <p>Bạn chưa tham gia Câu lạc bộ nào.</p>
            </div>
        <?php else: ?>
            <div class="members-page">
                <div class="members-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h2 style="margin: 0; color: #1e3a5f;">DANH SÁCH THÀNH VIÊN</h2>
                        <p style="margin: 5px 0 0; color: #64748b;"><?= htmlspecialchars($clubInfo['club_name'] ?? '') ?></p>
                    </div>
                    <div class="member-count" style="background: #e8f0f8; padding: 8px 16px; border-radius: 8px; font-weight: bold; color: #1e3a5f;">
                        Tổng số: <?= count($members) ?> thành viên
                    </div>
                </div>

                <div class="members-table-wrapper" style="background: white; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <table class="members-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                <th style="padding: 12px 16px;">STT</th>
                                <th style="padding: 12px 16px;">Họ tên</th>
                                <th style="padding: 12px 16px;">Chức vụ</th>
                                <th style="padding: 12px 16px;">Ngày tham gia</th>
                                <th style="padding: 12px 16px;">Thuộc ban</th>
                                <th style="padding: 12px 16px;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $index => $member): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 16px;"><?= $index + 1 ?></td>
                                    <td style="padding: 12px 16px; font-weight: bold;"><?= htmlspecialchars($member['fullname'] ?? $member['username']) ?></td>
                                    <td style="padding: 12px 16px;"><?= htmlspecialchars($member['position'] ?: 'Thành viên') ?></td>
                                    <td style="padding: 12px 16px;"><?= !empty($member['joined_at']) ? date('d/m/Y', strtotime($member['joined_at'])) : 'Chưa cập nhật' ?></td>
                                    <td style="padding: 12px 16px;"><?= htmlspecialchars($member['band_name'] ?? 'Chưa phân ban') ?></td>
                                    <td style="padding: 12px 16px;">
                                        <?= (int)($member['status'] ?? 0) === 1 ? '<span style="color: green; font-weight: bold;">Hoạt động</span>' : '<span style="color: red;">Ngưng</span>' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">Chưa có thành viên nào trong CLB.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>