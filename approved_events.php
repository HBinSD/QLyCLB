<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'quanly_sukien';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// 2. Lấy danh sách sự kiện đã được duyệt (status = 1)
$sql = "SELECT * FROM event WHERE status = 1 ORDER BY start_time ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Sự Kiện Đã Duyệt</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f0f4f8; 
            margin: 0;
            padding: 40px 20px; 
            color: #334155;
            display: flex;
            justify-content: center;
        }

        .form-container { 
            background: #ffffff;
            padding: 45px 50px;
            border-radius: 28px;
            width: 100%;
            max-width: 1100px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        h2 { 
            color: #1e3a8a; 
            font-size: 22px;
            font-weight: 700;
            border-bottom: 2px solid #3b82f6; 
            padding-bottom: 12px; 
            margin-top: 0;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-top: 20px; 
            font-size: 14px; 
            border: 1px solid #e2e8f0;
            border-radius: 12px; 
            overflow: hidden;
            table-layout: auto;
        }

        th, td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 16px 14px; 
            text-align: left; 
            vertical-align: middle; 
        }

        th { 
            background: #f8fafc; 
            color: #475569;
            font-weight: 600;
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none; 
        }

        .badge-blue, .badge-gray { 
            display: inline-block;
            white-space: nowrap; 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            text-align: center;
        }

        .badge-blue    { background: #dbeafe; color: #1e40af; }
        .badge-gray    { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>DANH SÁCH SỰ KIỆN ĐÃ DUYỆT</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Mô Tả Nội Dung</th>
                <th style="text-align: center;">Tiến Độ Sự Kiện</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            if ($result && mysqli_num_rows($result) > 0):
                while ($item = mysqli_fetch_assoc($result)):
                    $isPast = isset($item['start_time']) ? (strtotime($item['start_time']) < time()) : false;
            ?>
                    <tr>
                        <td style="text-align: center;"><?= $stt++ ?></td>
                        <td><b><?= htmlspecialchars($item['event_name'] ?? '') ?></b></td>
                        <td>
                            <b>Thời gian:</b> <?= isset($item['start_time']) ? date('d/m/Y H:i', strtotime($item['start_time'])) : '' ?><br>
                            <b>Địa điểm:</b> <?= htmlspecialchars($item['location'] ?? '') ?><br>
                            <b>Số chỗ:</b> <?= htmlspecialchars($item['slots'] ?? '0') ?> người
                        </td>
                        <td><?= nl2br(htmlspecialchars($item['description'] ?? 'Không có nội dung mô tả.')) ?></td>
                        <td style="text-align: center;">
                            <?php if ($isPast): ?>
                                <span class="badge-gray">Đã diễn ra</span>
                            <?php else: ?>
                                <span class="badge-blue">Chưa diễn ra</span>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php 
                endwhile;
            else: ?>
                <tr><td colspan="5" style="text-align:center; color: #94a3b8; padding: 25px;">Chưa có sự kiện nào được duyệt</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
