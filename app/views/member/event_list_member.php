<?php 
$pageTitle = "Sự Kiện Của Tôi";
require_once __DIR__ . '/../includes/headers.php';
?>

<div style="display: flex; flex-direction: column; gap: 25px;">

    <!-- Thông báo -->
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

    <!-- 1. BẢNG SỰ KIỆN ĐÃ ĐĂNG KÝ -->
    <!-- 1. BẢNG SỰ KIỆN ĐÃ ĐĂNG KÝ -->
<div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div style="border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #1e3a5f; margin: 0; font-size: 18px;">SỰ KIỆN ĐÃ ĐĂNG KÝ</h2>
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
                    <td style="padding: 12px; color: #475569;">
    <?= $sk->getStartTime() ? $sk->getStartTime()->format('d/m/Y H:i') : 'N/A' ?> 
    <br>đến<br> 
    <?= $sk->getEndTime() ? $sk->getEndTime()->format('d/m/Y H:i') : 'N/A' ?>
</td>
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
                           onclick="return confirm('Bạn có chắc chắn muốn hủy đăng ký?')"
                           style="background: #fee2e2; color: #dc2626; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                            Hủy Đăng Ký
                        </a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">Bạn chưa đăng ký tham gia sự kiện nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <!-- 2. BẢNG DANH SÁCH SỰ KIỆN ĐANG MỞ ĐĂNG KÝ -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="border-bottom: 2px solid #16a34a; padding-bottom: 10px; margin-bottom: 15px;">
            <h2 style="color: #1e3a5f; margin: 0; font-size: 18px;"> CÁC SỰ KIỆN ĐANG MỞ ĐĂNG KÝ</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">STT</th>
                    <th style="padding: 12px;">Tên Sự Kiện</th>
                    <th style="padding: 12px;">Thời Gian</th>
                    <th style="padding: 12px; text-align: center;">Slots</th>
                    <th style="padding: 12px;">Địa Điểm</th>
                    <th style="padding: 12px; text-align: center;">Đăng Ký</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($availableEvents)): foreach ($availableEvents as $index => $sk): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; text-align: center; color: #64748b;"><?= $index + 1 ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #1e3a5f;"><?= htmlspecialchars($sk->getEventName()) ?></td>
                        <td style="padding: 12px; color: #475569;"><?= $sk->getEventDate()->format('d/m/Y H:i') ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold;"><?= $sk->getSlot() ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($sk->getLocation()) ?></td>
<td style="padding: 12px; text-align: center;">
    <?php if ($sk->getSlot() > 0): ?>
        <a href="index.php?page=event-register&id=<?= $sk->getEventId() ?>" 
           style="background: #16a34a; color: white; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block;">
            Đăng Ký Ngay
        </a>
    <?php else: ?>
        <span style="background: #94a3b8; color: white; padding: 6px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-block; cursor: not-allowed;">
            Hết Slot
        </span>
    <?php endif; ?>
</td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">Hiện tại không có sự kiện mới nào đang mở đăng ký.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php';?>