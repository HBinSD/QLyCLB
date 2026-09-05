<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "", "quanly_sukien");

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// 2. Xử lý Duyệt / Từ chối đơn đăng ký (GET)
if (isset($_GET['action']) && isset($_GET['username']) && isset($_GET['event_id'])) {
    $username = $_GET['username'];
    $event_id = $_GET['event_id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $status = 'Đã duyệt';
    } elseif ($action === 'reject') {
        $status = 'Từ chối';
    }

    if (isset($status)) {
        // Cập nhật trạng thái duyệt đơn trong bảng register_event
        $stmt = mysqli_prepare($conn, "UPDATE register_event SET register_status = ? WHERE username = ? AND event_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $status, $username, $event_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            die("Lỗi Prepared Statement: " . mysqli_error($conn));
        }
    }

    header("Location: manage_regis.php");
    exit();
}

// 3. Xử lý Cập nhật điểm danh & Lý do vắng (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_attendance') {
    $username = $_POST['username'];
    $event_id = $_POST['event_id'];
    $attendance_status = $_POST['attendance_status'];
    $absence_reason = trim($_POST['absence_reason']);

    // Nếu không chọn Vắng thì tự động xóa lý do vắng
    if ($attendance_status !== 'Vắng') {
        $absence_reason = NULL;
    }

    // Cập nhật thông tin điểm danh trong bảng register_event
    $stmt = mysqli_prepare($conn, "UPDATE register_event SET attendance_status = ?, absence_reason = ? WHERE username = ? AND event_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $attendance_status, $absence_reason, $username, $event_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die("Lỗi Prepared Statement: " . mysqli_error($conn));
    }

    header("Location: manage_regis.php");
    exit();
}

header("Location: manage_regis.php");
exit();
?>
