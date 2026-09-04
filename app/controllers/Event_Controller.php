<?php 
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../models/EventModel.php';
$eventDate = $_POST['event_date'] ?? ''; // VD: 2026-05-10
$startTime = $_POST['start_time'] ?? ''; // VD: 08:30
$endTime   = $_POST['end_time'] ?? '';   // VD: 11:30

$startDateTime = new DateTime($eventDate . ' ' . $startTime);
$endDateTime   = new DateTime($eventDate . ' ' . $endTime);
class Event_Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $eventModel = new EventModel();
        
        $user = $_SESSION['user'] ?? null;
        $role = strtolower(trim($user['role'] ?? 'member'));
        $successMessage = $_SESSION['flash_success'] ?? "";
        $errorMessage   = $_SESSION['flash_error'] ?? "";
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        if ($role === 'member' || $role === 'user') {
            $username = $user['username'] ?? '';
            $registeredEvents = $eventModel->getEventsByStudent($username);
            $availableEvents  = $eventModel->getAvailableEventsForStudent($username);
            require_once __DIR__ . '/../views/member/event_list_member.php';
        } else {
            $ds = $eventModel->getAllEvent();
            require_once __DIR__ . '/../views/organizer/event_list.php';
        }
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header('Location: index.php?page=login');
            exit();
        }

        $eventId = $_GET['id'] ?? null;
        if ($eventId) {
            $eventModel = new EventModel();
            $result = $eventModel->registerEventWithTransaction($eventId, $user['username']);
            
            if ($result['success']) {
                $_SESSION['flash_success'] = $result['message'];
            } else {
                $_SESSION['flash_error'] = $result['message'];
            }
        }
        header('Location: index.php?page=event');
        exit();
    }

    public function cancelRegistration() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $eventId = $_GET['id'] ?? null;

        if ($user && $eventId) {
            $eventModel = new EventModel();
            $result = $eventModel->unregisterEventWithTransaction($eventId, $user['username']);
            
            if ($result['success']) {
                $_SESSION['flash_success'] = $result['message'];
            } else {
                $_SESSION['flash_error'] = $result['message'];
            }
        }
        header('Location: index.php?page=event');
        exit();
    }

    public function registeredEvents() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header('Location: index.php?page=login');
            exit();
        }
        $eventModel = new EventModel();
        $registeredEvents = $eventModel->getEventsByStudent($user['username'] ?? '');
        require_once __DIR__ . '/../views/member/event_registered_list.php';
    }

    public function exportRegisteredCSV() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header('Location: index.php?page=login');
            exit();
        }
        $eventModel = new EventModel();
        $registeredEvents = $eventModel->getEventsByStudent($user['username'] ?? '');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=danh_sach_su_kien_da_dang_ky.csv');
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, ['STT', 'Mã Sự Kiện', 'Mã CLB', 'Tên Sự Kiện', 'Thời Gian', 'Địa Điểm', 'Trạng Thái']);

        foreach ($registeredEvents as $index => $sk) {
            fputcsv($output, [
                $index + 1,
                $sk->getEventId(),
                $sk->getClubId(),
                $sk->getEventName(),
                $sk->getEventDate()->format('d/m/Y H:i'),
                $sk->getLocation(),
                $sk->getRegisterStatus()
            ]);
        }
        fclose($output);
        exit();
    }

    public function create() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $error = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $eventModel = new EventModel();
        $clubId    = $_POST['club_id'] ?? '1';
        $eventName = trim($_POST['event_name'] ?? '');
        $eventDate = $_POST['event_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime   = $_POST['end_time'] ?? '';
        $slots     = (int)($_POST['slots'] ?? 0);
        $location  = trim($_POST['location'] ?? '');
        $status    = (string)($_POST['status'] ?? '1');

        if (empty($eventName) || empty($eventDate) || empty($startTime) || empty($endTime)) {
            $error = "Vui lòng nhập đầy đủ thông tin thời gian bắt đầu và kết thúc!";
        } else {
            $event = new Event(
                '',
                $clubId,
                $eventName,
                new DateTime($eventDate),
                new DateTime($startTime),
                new DateTime($endTime),
                $slots,
                $location,
                $status
            );
            if ($eventModel->insert($event)) {
                $_SESSION['flash_success'] = "Thêm sự kiện thành công!";
                header('Location: index.php?page=event');
                exit();
            } else {
                $error = "Thêm sự kiện thất bại!";
            }
        }
    }
    require_once __DIR__ . '/../views/organizer/event_create.php';
}

public function edit() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $eventModel = new EventModel();
    $id = $_GET['id'] ?? ($_POST['event_id'] ?? null);
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $event = new Event(
            $_POST['event_id'],
            $_POST['club_id'],
            $_POST['event_name'],
            new DateTime($_POST['event_date']),
            !empty($_POST['start_time']) ? new DateTime($_POST['start_time']) : null,
            !empty($_POST['end_time']) ? new DateTime($_POST['end_time']) : null,
            (int)$_POST['slots'],
            $_POST['location'],
            (string)$_POST['status']
        );
        if ($eventModel->update($event)) {
            $_SESSION['flash_success'] = "Cập nhật sự kiện thành công!";
            header('Location: index.php?page=event');
            exit();
        } else {
            $error = "Cập nhật thất bại!";
        }
    }
    $event = $eventModel->getEventByID($id);
    require_once __DIR__ . '/../views/organizer/event_edit.php';
}

    public function delete() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $eventModel = new EventModel();
        $id = $_GET['id'] ?? null;
        if ($id && $eventModel->delete($id)) {
            $_SESSION['flash_success'] = "Xóa sự kiện thành công!";
        } else {
            $_SESSION['flash_error'] = "Không thể xóa sự kiện!";
        }
        header('Location: index.php?page=event');
        exit();
    }
    public function participants() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            header('Location: index.php?page=event');
            exit();
        }

        $eventModel = new EventModel();
        $event = $eventModel->getEventByID($eventId);

        if (!$event) {
            header('Location: index.php?page=event');
            exit();
        }

        $participants = $eventModel->getParticipantsByEventId($eventId);
        require_once __DIR__ . '/../views/organizer/event_participants.php';
    }
    // Hiển thị trang điểm danh
public function attendance() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $user = $_SESSION['user'] ?? null;
    $role = strtolower($user['role'] ?? '');

    // Ràng buộc quyền: Chỉ BTC hoặc Admin mới được vào trang này
    if (!$user || ($role !== 'organizer' && $role !== 'admin')) {
        header('Location: index.php?page=home');
        exit();
    }

    $eventModel = new EventModel();
    $eventId = $_GET['id'] ?? null;
    
    // Lấy danh sách tất cả sự kiện để BTC chọn
    $events = $eventModel->getAllEvent();
    $attendanceList = [];
    $selectedEvent = null;

    if ($eventId) {
        $selectedEvent = $eventModel->getEventByID($eventId);
        if ($selectedEvent) {
            $attendanceList = $eventModel->getAttendanceListByEvent($eventId);
        }
    }

    require_once __DIR__ . '/../views/organizer/event_attendance.php';
}

// Endpoint JSON trả về dữ liệu cho Fetch API
public function apiAttendance() {
    header('Content-Type: application/json; charset=utf-8');
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $user = $_SESSION['user'] ?? null;
    $role = strtolower($user['role'] ?? '');

    if (!$user || ($role !== 'organizer' && $role !== 'admin')) {
        echo json_encode(['success' => false, 'message' => 'Không có quyền thực hiện!']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $eventId  = $_POST['event_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 0;

        if (empty($eventId) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu đầu vào!']);
            exit();
        }

        $eventModel = new EventModel();
        $success = $eventModel->updateAttendanceStatus($eventId, $username, $status);

        if ($success) {
            echo json_encode([
                'success' => true,
                'status'  => $status,
                'time'    => $status === 1 ? date('d/m/Y H:i:s') : 'Chưa điểm danh',
                'message' => 'Cập nhật điểm danh thành công!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL!']);
        }
        exit();
    }
}
}