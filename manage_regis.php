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

// 2. Xử lý Cập nhật Trạng thái Đăng ký / Điểm danh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $username = $_POST['username'] ?? '';
    $eventId = $_POST['event_id'] ?? '';
    $actionType = $_POST['action_type'];

    if (!empty($username) && !empty($eventId)) {
        if ($actionType === 'approve_reg') {
            // Duyệt đăng ký
            $stmt = mysqli_prepare($conn, "UPDATE register_event SET register_status = 'Đã duyệt' WHERE username = ? AND event_id = ?");
            mysqli_stmt_bind_param($stmt, "ss", $username, $eventId);
            mysqli_stmt_execute($stmt);
        } elseif ($actionType === 'reject_reg') {
            // Từ chối đăng ký
            $stmt = mysqli_prepare($conn, "UPDATE register_event SET register_status = 'Từ chối' WHERE username = ? AND event_id = ?");
            mysqli_stmt_bind_param($stmt, "ss", $username, $eventId);
            mysqli_stmt_execute($stmt);
        } elseif ($actionType === 'update_attendance') {
            // Điểm danh (Bình thường / Đi muộn / Vắng)
            $attendanceStatus = $_POST['attendance_status'] ?? 'Bình thường';
            $stmt = mysqli_prepare($conn, "UPDATE register_event SET attendance_status = ? WHERE username = ? AND event_id = ?");
            mysqli_stmt_bind_param($stmt, "sss", $attendanceStatus, $username, $eventId);
            mysqli_stmt_execute($stmt);
        }
    }

    header("Location: manage_regis.php");
    exit();
}

// 3. Lấy danh sách sinh viên đăng ký tham gia các sự kiện
$sql = "SELECT r.*, e.event_name, u.fullname, u.email 
        FROM register_event r
        JOIN event e ON r.event_id = e.event_id
        LEFT JOIN UserInfo u ON r.username = u.username
        ORDER BY r.register_time DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đăng Ký & Điểm Danh</title>
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

        .badge {
            display: inline-block;
            white-space: nowrap;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        .btn-sm {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-size: 12px;
            transition: 0.2s;
        }

        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover { background: #059669; }

        .btn-reject { background: #ef4444; color: white; }
        .btn-reject:hover { background: #dc2626; }

        .form-inline {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        select.select-attendance {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            outline: none;
            background: #fff;
        }

        select.select-attendance:focus {
            border-color: #3b82f6;
        }
    </style>
</head>
<body>

<div class="main-container">
    <h2>QUẢN LÝ ĐĂNG KÝ & ĐIỂM DANH SỰ KIỆN</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th>Thành Viên Đăng Ký</th>
                <th>Sự Kiện Tham Gia</th>
                <th>Thời Gian Đăng Ký</th>
                <th style="text-align: center;">Duyệt Đăng Ký</th>
                <th style="text-align: center;">Trạng Thái Điểm Danh</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasData = false;

            if ($result && mysqli_num_rows($result) > 0):
                while ($item = mysqli_fetch_assoc($result)): 
                    $hasData = true;
                    $regStatus = $item['register_status'] ?? 'Chờ duyệt';
                    $attStatus = $item['attendance_status'] ?? 'Bình thường';
                    $username = htmlspecialchars($item['username'] ?? '');
                    $eventId = htmlspecialchars($item['event_id'] ?? '');
            ?>
                    <tr>
                        <td style="text-align: center;"><?= $stt++ ?></td>
                        <td>
                            <b><?= htmlspecialchars($item['fullname'] ?? $username) ?></b><br>
                            <span style="color: #64748b; font-size: 12px;">MSSV: <?= $username ?></span>
                        </td>
                        <td><b><?= htmlspecialchars($item['event_name'] ?? '') ?></b></td>
                        <td><?= isset($item['register_time']) ? date('d/m/Y H:i', strtotime($item['register_time'])) : '' ?></td>
                        
                        <!-- Cột Xử lý Đăng ký -->
                        <td style="text-align: center;">
                            <?php if ($regStatus === 'Đã duyệt'): ?>
                                <span class="badge badge-approved">Đã duyệt</span>
                            <?php elseif ($regStatus === 'Từ chối'): ?>
                                <span class="badge badge-rejected">Từ chối</span>
                            <?php else: ?>
                                <div class="form-inline">
                                    <form method="POST" action="manage_regis.php" style="display:inline;">
                                        <input type="hidden" name="username" value="<?= $username ?>">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        <input type="hidden" name="action_type" value="approve_reg">
                                        <button type="submit" class="btn-sm btn-approve">Duyệt</button>
                                    </form>
                                    <form method="POST" action="manage_regis.php" style="display:inline;">
                                        <input type="hidden" name="username" value="<?= $username ?>">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        <input type="hidden" name="action_type" value="reject_reg">
                                        <button type="submit" class="btn-sm btn-reject">Hủy</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Cột Điểm danh -->
                        <td style="text-align: center;">
                            <?php if ($regStatus === 'Đã duyệt'): ?>
                                <form method="POST" action="manage_regis.php" class="form-inline">
                                    <input type="hidden" name="username" value="<?= $username ?>">
                                    <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                    <input type="hidden" name="action_type" value="update_attendance">
                                    
                                    <select name="attendance_status" class="select-attendance" onchange="this.form.submit()">
                                        <option value="Bình thường" <?= $attStatus === 'Bình thường' ? 'selected' : '' ?>>🟢 Bình thường</option>
                                        <option value="Đi muộn" <?= $attStatus === 'Đi muộn' ? 'selected' : '' ?>>🟡 Đi muộn</option>
                                        <option value="Vắng" <?= $attStatus === 'Vắng' ? 'selected' : '' ?>>🔴 Vắng mặt</option>
                                    </select>
                                </form>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic; font-size: 13px;">Cần duyệt đăng ký trước</span>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php 
                endwhile; 
            endif;

            if (!$hasData): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color: #94a3b8; padding: 25px;">
                        Chưa có dữ liệu đăng ký tham gia sự kiện nào.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>