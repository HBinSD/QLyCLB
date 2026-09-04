<?php 
$pageTitle = "Danh Sách Sinh Viên Đăng Ký";
require_once __DIR__ . '/../includes/headers.php';
?>

<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- THÔNG TIN SỰ KIỆN -->
    <div style="background: white; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <?php if (!empty($event)): ?>
                <h2 style="color: #1e3a5f; margin: 0; font-size: 20px;">
                    DANH SÁCH SINH VIÊN ĐĂNG KÝ: <?= htmlspecialchars($event->getEventName()) ?>
                </h2>
                <p style="color: #64748b; margin: 6px 0 0 0; font-size: 14px;">
                    Địa điểm: <strong><?= htmlspecialchars($event->getLocation()) ?></strong> | 
                    Ngày: <strong><?= $event->getEventDate()->format('d/m/Y') ?></strong> | 
                    Còn lại: <strong><?= $event->getSlot() ?> slot</strong>
                </p>
            <?php else: ?>
                <h2 style="color: #dc2626; margin: 0; font-size: 20px;">
                    Không tìm thấy thông tin sự kiện!
                </h2>
            <?php endif; ?>
        </div>
        <a href="index.php?page=event" style="background: #e2e8f0; color: #475569; padding: 10px 18px; border-radius: 8px; font-weight: bold; text-decoration: none;">
            Quay Lại
        </a>
    </div>

    <!-- BẢNG DANH SÁCH SINH VIÊN -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Họ Và Tên</th>
                    <th style="padding: 12px;">Username</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Số Điện Thoại</th>
                    <th style="padding: 12px;">Thời Gian Đăng Ký</th>
                    <th style="padding: 12px; text-align: center;">Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($participants)): foreach ($participants as $index => $p): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; color: #64748b;"><?= $index + 1 ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($p['fullname'] ?? '') ?></td>
                        <td style="padding: 12px; color: #334155;"><?= htmlspecialchars($p['username']) ?></td>
                        <td style="padding: 12px; color: #475569;"><?= htmlspecialchars($p['email'] ?? 'N/A') ?></td>
                        <td style="padding: 12px; color: #475569;"><?= htmlspecialchars($p['phone'] ?? 'N/A') ?></td>
                        <td style="padding: 12px; color: #64748b;"><?= date('d/m/Y H:i', strtotime($p['register_time'])) ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if ($p['register_status'] === 'approved'): ?>
                                <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">Đã Duyệt</span>
                            <?php elseif ($p['register_status'] === 'cancelled'): ?>
                                <span style="background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">Đã Hủy</span>
                            <?php else: ?>
                                <span style="background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">Chờ Duyệt</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">
                            Chưa có sinh viên nào đăng ký sự kiện này.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php';?>