<?php 
$pageTitle = "Báo Cáo Thống Kê";
$stats = $stats ?? [];
$reports = $stats['event_reports'] ?? [];
require_once __DIR__ . '/../includes/headers.php'; 
?>

<div style="display: flex; flex-direction: column; gap: 20px;">
    <!-- KHỐI THỐNG KÊ TỔNG QUAN -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #2563eb;">
            <span style="font-size: 13px; color: #64748b; font-weight: bold;">TỔNG SINH VIÊN</span>
            <h2 style="margin: 8px 0 0 0; color: #1e3a5f; font-size: 28px;"><?= $stats['total_users'] ?? 0 ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #16a34a;">
            <span style="font-size: 13px; color: #64748b; font-weight: bold;">CÂU LẠC BỘ HOẠT ĐỘNG</span>
            <h2 style="margin: 8px 0 0 0; color: #1e3a5f; font-size: 28px;"><?= $stats['total_clubs'] ?? 0 ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #d97706;">
            <span style="font-size: 13px; color: #64748b; font-weight: bold;">TỔNG SỰ KIỆN</span>
            <h2 style="margin: 8px 0 0 0; color: #1e3a5f; font-size: 28px;"><?= $stats['total_events'] ?? 0 ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #0284c7;">
            <span style="font-size: 13px; color: #64748b; font-weight: bold;">LƯỢT ĐĂNG KÝ THÀNH CÔNG</span>
            <h2 style="margin: 8px 0 0 0; color: #1e3a5f; font-size: 28px;"><?= $stats['total_registrations'] ?? 0 ?></h2>
        </div>
    </div>

    <!-- BẢNG THỐNG KÊ CHI TIẾT SỰ KIỆN -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h3 style="color: #1e3a5f; margin-top: 0; margin-bottom: 15px;">THỐNG KÊ ĐĂNG KÝ VÀ ĐIỂM DANH THEO SỰ KIỆN</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Tên Sự Kiện</th>
                    <th style="padding: 12px;">Câu Lạc Bộ</th>
                    <th style="padding: 12px; text-align: center;">Tổng Slot</th>
                    <th style="padding: 12px; text-align: center;">Đã Đăng Ký</th>
                    <th style="padding: 12px; text-align: center;">Đã Điểm Danh</th>
                    <th style="padding: 12px; text-align: center;">Tỷ Lệ Tham Gia</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): foreach ($reports as $index => $r): 
                    $reg = (int)$r['registered_count'];
                    $att = (int)$r['attended_count'];
                    $rate = $reg > 0 ? round(($att / $reg) * 100, 1) : 0;
                ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; color: #64748b;"><?= $index + 1 ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($r['event_name']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($r['club_name'] ?? 'N/A') ?></td>
                        <td style="padding: 12px; text-align: center;"><?= $r['slots'] ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #2563eb;"><?= $reg ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #16a34a;"><?= $att ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">
                                <?= $rate ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 25px;">Chưa có dữ liệu thống kê.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>