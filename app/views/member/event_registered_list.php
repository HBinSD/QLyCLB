<?php  
$pageTitle = "Danh Sách Sự Kiện Đã Đăng Ký"; 
require_once __DIR__ . '/../includes/headers.php';
?>

<div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #1e3a5f; margin: 0; font-size: 20px;">DANH SÁCH SỰ KIỆN ĐÃ ĐĂNG KÝ</h2>
        <a href="index.php?page=event-export-csv" style="background: #16a34a; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
            Xuất File CSV
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px;">STT</th>
                <th style="padding: 12px;">Tên Sự Kiện</th>
                <th style="padding: 12px;">Thời Gian</th>
                <th style="padding: 12px;">Địa Điểm</th>
                <th style="padding: 12px; text-align: center;">Trạng Thái</th>
                <th style="padding: 12px; text-align: center;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($registeredEvents)): foreach ($registeredEvents as $index => $sk): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; text-align: center; color: #64748b;"><?= $index + 1 ?></td>
                    <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($sk->getEventName()) ?></td>
                    <td style="padding: 12px; color: #475569;"><?= $sk->getEventDate()->format('d/m/Y H:i') ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($sk->getLocation()) ?></td>
                    <td style="padding: 12px; text-align: center;">
                        <?php 
                            $status = $sk->getRegisterStatus();
                            if ($status === 'approved') {
                                echo '<span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">Đã duyệt</span>';
                            } elseif ($status === 'rejected') {
                                echo '<span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">Từ chối</span>';
                            } else {
                                echo '<span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">Chờ duyệt</span>';
                            }
                        ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="index.php?page=event-cancel&id=<?= $sk->getEventId() ?>" 
                           onclick="return confirm('Bạn có chắc muốn hủy đăng ký?')"
                           style="background: #fee2e2; color: #dc2626; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                            Hủy Đăng Ký
                        </a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">Chưa có sự kiện nào được đăng ký.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>