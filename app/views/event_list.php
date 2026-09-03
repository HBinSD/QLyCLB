<?php 
$pageTitle = "Danh Sách Sự Kiện";
require_once __DIR__ . '/includes/headers.php'; 
?>

<div class="table-wrapper" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #1e3a5f; margin: 0;">DANH SÁCH SỰ KIỆN</h2>
        <a href="index.php?page=event-create" style="background: #1e3a5f; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;">+ Thêm Sự Kiện</a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0;">
        <thead>
            <tr style="background: #f8fafc; color: #475569;">
                <th>STT</th>
                <th>Mã Sự Kiện</th>
                <th>Mã CLB</th>
                <th>Tên Sự Kiện</th>
                <th>Thời Gian</th>
                <th>Slots</th>
                <th>Địa Điểm</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 0;
            if (!empty($ds)): 
                foreach ($ds as $sk): 
                    $stt++;
            ?>
            <tr>
                <td style="text-align: center;"><?= $stt; ?></td>
                <td><?= htmlspecialchars($sk->getEventId()); ?></td>
                <td><?= htmlspecialchars($sk->getClubId()); ?></td>
                <td><strong><?= htmlspecialchars($sk->getEventName()); ?></strong></td>
                <td><?= $sk->getEventDate()->format('d/m/Y H:i'); ?></td>
                <td style="text-align: center;"><?= $sk->getSlot(); ?></td>
                <td><?= htmlspecialchars($sk->getLocation()); ?></td>
                <td><?= $sk->getStatus() == '1' ? '<span style="color: green; font-weight: bold;">Mở</span>' : '<span style="color: red;">Đóng</span>'; ?></td>
                <td>
                    <a href="index.php?page=event-edit&id=<?= $sk->getEventId(); ?>" style="color: #2563eb;">Sửa</a> | 
                    <a href="index.php?page=event-delete&id=<?= $sk->getEventId(); ?>" onclick="return confirm('Bạn có chắc muốn xóa?')" style="color: #ef4444;">Xóa</a>
                </td>
            </tr>
            <?php 
                endforeach;
            else:
            ?>
            <tr>
                <td colspan="9" style="text-align: center; color: #64748b; padding: 20px;">Chưa có sự kiện nào trong hệ thống!</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>