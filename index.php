<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

foreach ($_SESSION['eventList'] as $k => $v) {
    if (!isset($v['id'])) {
        unset($_SESSION['eventList'][$k]);
    }
}
$_SESSION['eventList'] = array_values($_SESSION['eventList']);

$eventName = $eventType = $eventTime = $eventLocation = $maxParticipants = $eventDetail = "";
$fieldErrors = [
    'eventName' => '', 'eventType' => '', 'eventTime' => '', 
    'eventLocation' => '', 'maxParticipants' => '', 'eventDetail' => ''
];
$successMessage = "";
$editId = null;

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($_SESSION['eventList'] as $key => $item) {
        if ($item['id'] == $id && $item['status'] === 'Chờ duyệt') {
            unset($_SESSION['eventList'][$key]);
            break;
        }
    }
    $_SESSION['eventList'] = array_values($_SESSION['eventList']);
    header("Location: index.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($_SESSION['eventList'] as $item) {
        if ($item['id'] == $id && $item['status'] === 'Chờ duyệt') {
            $editId = $id;
            $eventName = $item['name'];
            $eventType = $item['type'];
            $eventTime = $item['time'];
            $eventLocation = $item['location'];
            $maxParticipants = $item['participants'];
            $eventDetail = $item['detail'] ?? '';
            break;
        }
    }
}

function getScale($participants) {
    if ($participants >= 200) return "Quy mô Lớn";
    if ($participants >= 50) return "Quy mô Vừa";
    return "Quy mô Nhỏ";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveEvent'])) {
    $editId = $_POST['editId'] !== "" ? $_POST['editId'] : null;

    $eventName = trim($_POST['eventName'] ?? '');
    $eventType = trim($_POST['eventType'] ?? '');
    $eventTime = trim($_POST['eventTime'] ?? '');
    $eventLocation = trim($_POST['eventLocation'] ?? '');
    $maxParticipants = trim($_POST['maxParticipants'] ?? '');
    $eventDetail = trim($_POST['eventDetail'] ?? '');

    $hasError = false;

    if (empty($eventName)) { $fieldErrors['eventName'] = "Tên sự kiện không được trống."; $hasError = true; }
    if (empty($eventType)) { $fieldErrors['eventType'] = "Vui lòng chọn loại sự kiện."; $hasError = true; }
    if (empty($eventTime)) { $fieldErrors['eventTime'] = "Vui lòng chọn thời gian."; $hasError = true; }
    if (empty($eventLocation)) { $fieldErrors['eventLocation'] = "Địa điểm không được trống."; $hasError = true; }
    if (empty($eventDetail)) { $fieldErrors['eventDetail'] = "Nội dung chi tiết không được trống."; $hasError = true; }
    if (empty($maxParticipants) || !filter_var($maxParticipants, FILTER_VALIDATE_INT) || (int)$maxParticipants <= 0) {
        $fieldErrors['maxParticipants'] = "Nhập số nguyên dương."; $hasError = true;
    }

    if (!$hasError) {
        $pCount = (int)$maxParticipants;
        
        if ($editId !== null) {
            foreach ($_SESSION['eventList'] as &$item) {
                if ($item['id'] == $editId) {
                    $item['name'] = htmlspecialchars($eventName);
                    $item['type'] = htmlspecialchars($eventType);
                    $item['time'] = htmlspecialchars($eventTime);
                    $item['location'] = htmlspecialchars($eventLocation);
                    $item['participants'] = $pCount;
                    $item['scale'] = getScale($pCount);
                    $item['detail'] = htmlspecialchars($eventDetail);
                    break;
                }
            }
            $successMessage = "Cập nhật đề xuất thành công!";
        } else { 
            $_SESSION['eventList'][] = [
                'id' => uniqid('evt_'),
                'name' => htmlspecialchars($eventName),
                'type' => htmlspecialchars($eventType),
                'time' => htmlspecialchars($eventTime),
                'location' => htmlspecialchars($eventLocation),
                'participants' => $pCount,
                'scale' => getScale($pCount),
                'detail' => htmlspecialchars($eventDetail),
                'status' => 'Chờ duyệt'
            ];
            $successMessage = "Gửi đề xuất thành công!";
        }
        $eventName = $eventType = $eventTime = $eventLocation = $maxParticipants = $eventDetail = "";
        $editId = null;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi Đề Xuất Sự Kiện</title>
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

        .form-row { 
            display: flex; 
            align-items: flex-start; 
            margin-bottom: 18px; 
        }
        .form-row label { 
            width: 180px; 
            font-weight: 600; 
            font-size: 14px;
            color: #1e293b;
            padding-top: 10px; 
        }
        
        .form-row input, 
        .form-row select, 
        .form-row textarea { 
            width: 420px; 
            padding: 11px 16px; 
            border: 1px solid #cbd5e1; 
            border-radius: 10px; 
            font-family: inherit;
            font-size: 14px;
            color: #0f172a;
            background-color: #ffffff;
            box-sizing: border-box; 
            outline: none;
            transition: all 0.2s ease;
        }

        .form-row input:focus, 
        .form-row select:focus, 
        .form-row textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input-error { 
            border-color: #ef4444 !important; 
            background-color: #fef2f2 !important; 
        }
        .error-text { 
            color: #ef4444; 
            font-size: 13px; 
            margin-left: 12px; 
            padding-top: 10px; 
        }

        .btn-submit { 
            background: #1e3a8a; 
            color: #ffffff; 
            border: none; 
            padding: 12px 28px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .btn-submit:hover { 
            background: #1d4ed8; 
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
        .btn-edit { background: #f59e0b; padding: 6px 14px; border-radius: 6px; text-decoration: none; color: white; font-weight: 600; font-size: 13px; }
        .btn-delete { background: #ef4444; padding: 6px 14px; border-radius: 6px; text-decoration: none; color: white; font-weight: 600; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?= $editId !== null ? "CẬP NHẬT ĐỀ XUẤT SỰ KIỆN" : "GỬI ĐỀ XUẤT SỰ KIỆN MỚI" ?></h2>

    <?php if ($successMessage): ?>
        <p style="color: #16a34a; font-weight: 600; margin-bottom: 18px;"><?= $successMessage ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="saveEvent" value="1">
        <input type="hidden" name="editId" value="<?= $editId ?>">

        <div class="form-row">
            <label>Tên sự kiện:</label>
            <input type="text" name="eventName" class="<?= $fieldErrors['eventName'] ? 'input-error' : '' ?>" value="<?= htmlspecialchars($eventName) ?>" placeholder="VD: Workshop Kỹ Năng Soft Skills">
            <span class="error-text"><?= $fieldErrors['eventName'] ?></span>
        </div>

        <div class="form-row">
            <label>Loại sự kiện:</label>
            <select name="eventType" class="<?= $fieldErrors['eventType'] ? 'input-error' : '' ?>">
                <option value="">-- Chọn loại --</option>
                <?php
                $opts = ["Hội thảo / Workshop", "Cuộc thi", "Hoạt động Ngoại khóa"];
                foreach ($opts as $o) {
                    $sel = ($eventType === $o) ? "selected" : "";
                    echo "<option value='$o' $sel>$o</option>";
                }
                ?>
            </select>
            <span class="error-text"><?= $fieldErrors['eventType'] ?></span>
        </div>

        <div class="form-row">
            <label>Thời gian diễn ra:</label>
            <input type="datetime-local" name="eventTime" class="<?= $fieldErrors['eventTime'] ? 'input-error' : '' ?>" value="<?= htmlspecialchars($eventTime) ?>">
            <span class="error-text"><?= $fieldErrors['eventTime'] ?></span>
        </div>

        <div class="form-row">
            <label>Địa điểm tổ chức:</label>
            <input type="text" name="eventLocation" class="<?= $fieldErrors['eventLocation'] ? 'input-error' : '' ?>" value="<?= htmlspecialchars($eventLocation) ?>" placeholder="VD: Hội trường A2">
            <span class="error-text"><?= $fieldErrors['eventLocation'] ?></span>
        </div>

        <div class="form-row">
            <label>Số lượng tối đa:</label>
            <input type="number" name="maxParticipants" class="<?= $fieldErrors['maxParticipants'] ? 'input-error' : '' ?>" value="<?= htmlspecialchars($maxParticipants) ?>" placeholder="VD: 50">
            <span class="error-text"><?= $fieldErrors['maxParticipants'] ?></span>
        </div>

        <div class="form-row">
            <label>Chi tiết sự kiện:</label>
            <textarea name="eventDetail" rows="4" class="<?= $fieldErrors['eventDetail'] ? 'input-error' : '' ?>" placeholder="Mô tả mục đích sự kiện, kế hoạch tổ chức cho Chủ CLB duyệt..."><?= htmlspecialchars($eventDetail) ?></textarea>
            <span class="error-text"><?= $fieldErrors['eventDetail'] ?></span>
        </div>

        <button type="submit" class="btn-submit"><?= $editId !== null ? "Cập Nhật" : "Gửi Đề Xuất Sự Kiện" ?></button>
    </form>

    <h2 style="margin-top: 45px;">DANH SÁCH ĐỀ XUẤT ĐANG CHỜ DUYỆT</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông tin chi tiết</th>
                <th>Chi tiết sự kiện</th>
                <th>Quy Mô</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
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
                        <td><?= nl2br($item['detail'] ?? '<i>Chưa có mô tả</i>') ?></td>
                        <td><?= $item['scale'] ?></td>
                        <td><span class="badge-warning">Chờ duyệt</span></td>
                        <td>
                            <a href="index.php?action=edit&id=<?= $item['id'] ?>" class="btn-edit">Sửa</a>
                            <a href="index.php?action=delete&id=<?= $item['id'] ?>" class="btn-delete" onclick="return confirm('Xóa đề xuất này?')">Xóa</a>
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