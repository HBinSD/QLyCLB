<?php
// 1. Khởi tạo kết nối CSDL quanly_sukien
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'quanly_sukien';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// 2. Xử lý hành động Duyệt hoặc Từ Chối từ Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $eventId = $_POST['event_id'] ?? '';
    $action = $_POST['action_type']; // 'approve' hoặc 'reject'
    $adminNote = trim($_POST['admin_note'] ?? '');

    if (!empty($eventId)) {
        if ($action === 'approve') {
            // Duyệt sự kiện (status = 1)
            $stmt = mysqli_prepare($conn, "UPDATE event SET status = 1 WHERE event_id = ?");
            mysqli_stmt_bind_param($stmt, "s", $eventId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } elseif ($action === 'reject') {
            // Từ chối / Báo sửa (status = 2)
            $stmt = mysqli_prepare($conn, "UPDATE event SET status = 2 WHERE event_id = ?");
            mysqli_stmt_bind_param($stmt, "s", $eventId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: admin.php");
    exit();
}

// 3. Lấy danh sách các đề xuất cần xử lý (status = 0: Chờ duyệt, status = 2: Từ chối / Chờ sửa)
$sql = "SELECT * FROM event WHERE status IN (0, 2) ORDER BY start_time ASC";
$eventResult = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Duyệt Đề Xuất Sự Kiện - Admin</title>
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

        .main-container {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 24px;
            width: 100%;
            max-width: 1150px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            box-sizing: border-box;
        }

        h2 {
            color: #1e3a8a;
            font-size: 20px;
            font-weight: 700;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-top: 0;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 16px;
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

        .desc-box {
            background: #f8fafc;
            border-left: 3px solid #1e3a8a;
            padding: 8px 12px;
            border-radius: 4px;
            font-style: italic;
            color: #334155;
        }

        .badge {
            display: inline-block;
            white-space: nowrap;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-action {
            background: #1e3a8a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-action:hover {
            background: #2563eb;
        }

        /* Modal Overlay & Dialog UI */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-title {
            color: #1e3a8a;
            font-size: 18px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .modal-body label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .modal-body textarea {
            width: 100%;
            height: 100px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            resize: vertical;
        }

        .modal-body textarea:focus {
            border-color: #3b82f6;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-cancel {
            background: #94a3b8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-approve {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-cancel:hover { background: #64748b; }
        .btn-reject:hover { background: #dc2626; }
        .btn-approve:hover { background: #059669; }
    </style>
</head>
<body>

<div class="main-container">
    <h2>DUYỆT ĐỀ XUẤT SỰ KIỆN</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Mô Tả Nội Dung</th>
                <th style="text-align: center;">Trạng Thái</th>
                <th style="text-align: center;">Thao Tác Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasData = false;

            if ($eventResult && mysqli_num_rows($eventResult) > 0):
                while ($item = mysqli_fetch_assoc($eventResult)): 
                    $status = $item['status'] ?? 0;
                    $hasData = true;
                    $itemId = htmlspecialchars($item['event_id'] ?? '');
                    $itemName = htmlspecialchars($item['event_name'] ?? '');
            ?>
                    <tr>
                        <td style="text-align: center;"><?= $stt++ ?></td>
                        <td><b><?= $itemName ?></b></td>
                        <td>
                            <b>Thời gian:</b> <?= isset($item['start_time']) && !empty($item['start_time']) ? date('d/m/Y H:i', strtotime($item['start_time'])) : '' ?><br>
                            <b>Địa điểm:</b> <?= htmlspecialchars($item['location'] ?? '') ?> (<?= htmlspecialchars($item['slots'] ?? '0') ?> người)
                        </td>
                        <td>
                            <div class="desc-box">
                                <?= nl2br(htmlspecialchars($item['description'] ?? 'Không có nội dung mô tả.')) ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($status == 2): ?>
                                <span class="badge badge-rejected">Từ chối / Chờ sửa</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-action" onclick="openModal('<?= $itemId ?>', '<?= addslashes($itemName) ?>')">
                                Xử lý đề xuất
                            </button>
                        </td>
                    </tr>
            <?php 
                endwhile; 
            endif;

            if (!$hasData): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color: #94a3b8; padding: 25px;">
                        Không có đề xuất nào chờ duyệt
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Popup Đánh giá -->
<div class="modal-overlay" id="reviewModal">
    <div class="modal-card">
        <h3 class="modal-title" id="modalTitle">Đánh giá: </h3>
        
        <form method="POST" action="admin.php" id="modalForm">
            <input type="hidden" name="event_id" id="modalEventId" value="">
            <input type="hidden" name="action_type" id="modalActionType" value="">

            <div class="modal-body">
                <label>Ghi chú / Lý do (Gửi Ban Chủ Nhiệm):</label>
                <textarea name="admin_note" placeholder="Nhập ghi chú hoặc lý do từ chối nếu có..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="button" class="btn-reject" onclick="submitAction('reject')">Từ Chối / Báo Sửa</button>
                <button type="button" class="btn-approve" onclick="submitAction('approve')">Duyệt Sự Kiện</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, name) {
        document.getElementById('modalEventId').value = id;
        document.getElementById('modalTitle').innerText = 'Đánh giá: ' + name;
        document.getElementById('reviewModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('reviewModal').classList.remove('active');
    }

    function submitAction(type) {
        document.getElementById('modalActionType').value = type;
        document.getElementById('modalForm').submit();
    }
</script>

</body>
</html>