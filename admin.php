<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $act = $_GET['action'];

    foreach ($_SESSION['eventList'] as &$item) {
        if (isset($item['id']) && $item['id'] == $id) {
            if ($act === 'approve') {
                $item['status'] = 'Đã duyệt';
            } elseif ($act === 'reject') {
                $item['status'] = 'Từ chối';
            }
            break;
        }
    }
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Duyệt Đề Xuất Sự Kiện</title>
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
            max-width: 900px;
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
            vertical-align: top; 
        }
        th { 
            background: #f8fafc; 
            color: #475569;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none; 
        }

        .badge-warning { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .btn-approve { background: #1e3a8a; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; margin-right: 4px; display: inline-block; margin-bottom: 4px; }
        .btn-reject { background: #ef4444; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-block; }
        
        .detail-box { background: #f8fafc; padding: 10px 14px; border-left: 3px solid #1e3a8a; border-radius: 0 6px 6px 0; font-style: italic; color: #334155; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>DANH SÁCH ĐỀ XUẤT CHỜ DUYỆT</h2>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Chi Tiết Sự Kiện</th>
                <th>Quy Mô</th>
                <th>Trạng Thái</th>
                <th>Quyết Định Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasPending = false;
            foreach ($_SESSION['eventList'] as $item): 
                if (isset($item['status']) && $item['status'] === 'Chờ duyệt'):
                    $hasPending = true;
            ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><b><?= $item['name'] ?></b></td>
                        <td>
                            <b>Loại:</b> <?= $item['type'] ?><br>
                            <b>Thời gian:</b> <?= date('d/m/Y H:i', strtotime($item['time'])) ?><br>
                            <b>Địa điểm:</b> <?= $item['location'] ?> (<?= $item['participants'] ?> người)
                        </td>
                        <td>
                            <div class="detail-box">
                                <?= nl2br($item['detail'] ?? 'Không có nội dung mô tả.') ?>
                            </div>
                        </td>
                        <td><?= $item['scale'] ?></td>
                        <td><span class="badge-warning">Chờ duyệt</span></td>
                        <td>
                            <a href="admin.php?action=approve&id=<?= $item['id'] ?>" class="btn-approve">Duyệt</a>
                            <a href="admin.php?action=reject&id=<?= $item['id'] ?>" class="btn-reject" onclick="return confirm('Từ chối đề xuất này?')">Từ chối</a>
                        </td>
                    </tr>
            <?php 
                endif;
            endforeach; 

            if (!$hasPending): ?>
                <tr><td colspan="7" style="text-align:center; color: #94a3b8;">Chưa có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>