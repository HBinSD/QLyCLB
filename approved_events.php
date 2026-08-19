<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Sự Kiện</title>
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
            max-width: 950px;
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
        }
        th, td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 14px 16px; 
            text-align: left; 
            vertical-align: middle; 
        }
        th { 
            background: #f8fafc; 
            color: #475569;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none; 
        }

        /* Chống gãy dòng cho badge */
        .badge-success, .badge-blue, .badge-gray { 
            display: inline-block;
            white-space: nowrap; 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            text-align: center;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-blue    { background: #dbeafe; color: #1e40af; }
        .badge-gray    { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>DANH SÁCH SỰ KIỆN</h2>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông tin chi tiết</th>
                <th>Chi tiết sự kiện</th>
                <th>Quy Mô</th>
                <th>Trạng Thái Duyệt</th>
                <th>Tiến Độ Sự Kiện</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasApproved = false;
            foreach ($_SESSION['eventList'] as $item): 
                if (isset($item['status']) && $item['status'] === 'Đã duyệt'):
                    $hasApproved = true;
                    $isPast = strtotime($item['time']) < time();
            ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><b><?= $item['name'] ?></b></td>
                        <td>
                            <b>Loại:</b> <?= $item['type'] ?><br>
                            <b>Thời gian:</b> <?= date('d/m/Y H:i', strtotime($item['time'])) ?><br>
                            <b>Địa điểm:</b> <?= $item['location'] ?> (<?= $item['participants'] ?> người)
                        </td>
                        <td><?= nl2br($item['detail'] ?? 'Không có nội dung mô tả.') ?></td>
                        <td><?= $item['scale'] ?></td>
                        <td><span class="badge-success">Đã duyệt</span></td>
                        <td>
                            <?php if ($isPast): ?>
                                <span class="badge-gray">Đã diễn ra</span>
                            <?php else: ?>
                                <span class="badge-blue">Chưa diễn ra</span>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php 
                endif;
            endforeach; 

            if (!$hasApproved): ?>
                <tr><td colspan="7" style="text-align:center; color: #94a3b8;">Chưa có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>