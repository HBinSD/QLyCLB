<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Sự Kiện</title>
    <link rel="stylesheet" href="css/event.css">
</head>
<body>
    <div class="table-wrapper">
        <h2>CẬP NHẬT SỰ KIỆN</h2>

        <?php if (!empty($event)): ?>
        <form action="index.php?page=event-edit" method="POST">
            <!-- Đẩy ẩn ID sự kiện để Controller biết đang sửa bản ghi nào -->
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event->getEventId()) ?>">

            <p>Mã CLB: <input type="number" name="club_id" value="<?= htmlspecialchars($event->getClubId()) ?>" required></p>
            <p>Tên sự kiện: <input type="text" name="event_name" value="<?= htmlspecialchars($event->getEventName()) ?>" required></p>
            <p>Thời gian: <input type="datetime-local" name="event_date" value="<?= $event->getEventDate()->format('Y-m-d\TH:i') ?>" required></p>
            <p>Số lượng slot: <input type="number" name="slots" value="<?= $event->getSlot() ?>" required></p>
            <p>Địa điểm: <input type="text" name="location" value="<?= htmlspecialchars($event->getLocation()) ?>" required></p>
            <p>Trạng thái: 
                <select name="status">
                    <option value="1" <?= $event->getStatus() == '1' ? 'selected' : '' ?>>Mở</option>
                    <option value="0" <?= $event->getStatus() == '0' ? 'selected' : '' ?>>Đóng</option>
                </select>
            </p>
            <button type="submit">Cập nhật</button>
            <a href="index.php?page=event">Hủy</a>
        </form>
        <?php else: ?>
            <p style="color: red;">Không tìm thấy thông tin sự kiện!</p>
            <a href="index.php?page=event">Quay lại danh sách</a>
        <?php endif; ?>
    </div>
</body>
</html>