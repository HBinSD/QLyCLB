<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sự Kiện Mới</title>
    <link rel="stylesheet" href="/css/event.css">
</head>
<body>
    <div class="table-wrapper">
    <h2>THÊM SỰ KIỆN MỚI</h2>
    <form action="index.php?page=event-create" method="POST">
        <p>Mã CLB: <input type="number" name="club_id" value="1" required></p>
        <p>Tên sự kiện: <input type="text" name="event_name" required></p>
        <p>Thời gian: <input type="datetime-local" name="event_date" required></p>
        <p>Số lượng slot: <input type="number" name="slots" required></p>
        <p>Địa điểm: <input type="text" name="location" required></p>
        <p>Trạng thái: 
            <select name="status">
                <option value="1">Mở</option>
                <option value="0">Đóng</option>
            </select>
        </p>
        <button type="submit">Lưu Sự Kiện</button>
        <a href="index.php?page=event">Hủy</a>
    </form>
    </div>
</body>
</html>