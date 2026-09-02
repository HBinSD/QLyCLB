
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>STT</th>
            <th>Mã sự kiện</th>
            <th>Mã câu lạc bộ</th>
            <th>Tên sự kiện</th>
            <th>Thời gian</th>
            <th>Slot</th>
            <th>Địa chỉ</th>
            <th>Trạng thái</th>
        </tr>
        </thead>
        <tbody>
            <?php 
                $stt =0;
                if(!empty($ds)): // Lấy biến ds từ file Event_Controller
                    foreach($ds as $sk):
                        $stt++;
            ?>
            <tr>
            <td><?= $stt; ?></td>
            <td><?= $sk->getEventId();?> </td>
            <td><?= $sk->getCLubID(); ?>></td>
            <td><?= $sk-> getEventNam(); ?>></td>
            <td><?= $sk ->getEventDate()->format('d/m/Y H:i'); ?></td>
            <td><?= $sk ->getSlot ?></td>
            <td><?= $sk ->getLocation ?></td>
            <td><?= $sk->getStatus() == 1 ? 'Đang mở' : 'Đã đóng'; ?></td>
            </tr>
            <?php 
                endforeach;
            else:
            ?>
            <tr>
                    <td colspan="8">Chưa có sự kiện nào!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>