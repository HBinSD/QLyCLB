<?php 
$pageTitle = "Quản Lý Sự Kiện";
require_once __DIR__ . '/includes/headers.php'; 
?>

<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- THÔNG BÁO TỪ SESSION NẾU VỪA THÊM/SỬA/XÓA XONG -->
    <?php if (!empty($successMessage)): ?>
        <div id="alert-box" style="background: #dcfce7; color: #166534; padding: 14px 20px; border-radius: 8px; border: 1px solid #86efac; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><?= htmlspecialchars($successMessage) ?></span>
            <button onclick="document.getElementById('alert-box').style.display='none'" style="background: transparent; border: none; font-size: 18px; cursor: pointer; color: #166534;">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div id="alert-box-err" style="background: #fee2e2; color: #dc2626; padding: 14px 20px; border-radius: 8px; border: 1px solid #fca5a5; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><?= htmlspecialchars($errorMessage) ?></span>
            <button onclick="document.getElementById('alert-box-err').style.display='none'" style="background: transparent; border: none; font-size: 18px; cursor: pointer; color: #dc2626;">&times;</button>
        </div>
    <?php endif; ?>

    <!-- HEADER & NÚT CHUYỂN TRANG THÊM MỚI -->
    <div style="background: white; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="color: #1e3a5f; margin: 0; font-size: 20px;">DANH SÁCH SỰ KIỆN</h2>
            <p style="color: #64748b; margin: 4px 0 0 0; font-size: 13px;">Quản lý và theo dõi toàn bộ các sự kiện của CLB</p>
        </div>
        <a href="index.php?page=event-create" style="background: #1e3a5f; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: bold; text-decoration: none; display: inline-block;">
            + Thêm Sự Kiện Mới
        </a>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Mã Sự Kiện</th>
                    <th style="padding: 12px;">Mã CLB</th>
                    <th style="padding: 12px;">Tên Sự Kiện</th>
                    <th style="padding: 12px;">Thời Gian</th>
                    <th style="padding: 12px; text-align: center;">Slots</th>
                    <th style="padding: 12px;">Địa Điểm</th>
                    <th style="padding: 12px;">Trạng Thái</th>
                    <th style="padding: 12px; text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 0;
                if (!empty($ds)): 
                    foreach ($ds as $sk): 
                        $stt++;
                ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; text-align: center; color: #64748b;"><?= $stt; ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($sk->getEventId()); ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($sk->getClubId()); ?></td>
                    <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($sk->getEventName()); ?></td>
                    <td style="padding: 12px; color: #475569;"><?= $sk->getEventDate()->format('d/m/Y H:i'); ?></td>
                    <td style="padding: 12px; text-align: center; font-weight: bold;"><?= $sk->getSlot(); ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($sk->getLocation()); ?></td>
                    <td style="padding: 12px;">
                        <?= $sk->getStatus() == '1' 
                            ? '<span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">Mở</span>' 
                            : '<span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">Đóng</span>'; 
                        ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="index.php?page=event-edit&id=<?= $sk->getEventId(); ?>" style="color: #2563eb; font-weight: bold; text-decoration: none; margin-right: 8px;">Sửa</a> | 
                        <a href="index.php?page=event-delete&id=<?= $sk->getEventId(); ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')" style="color: #ef4444; font-weight: bold; text-decoration: none; margin-left: 8px;">Xóa</a>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 30px;">Chưa có sự kiện nào!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>