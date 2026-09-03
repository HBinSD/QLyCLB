<?php 
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../models/EventModel.php';

class Event_Controller {

    // 1. Trang danh sách sự kiện (Chỉ hiển thị danh sách + Thông báo Flash)
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $eventModel = new EventModel();
        
        // Lấy thông báo từ session
        $successMessage = $_SESSION['flash_success'] ?? "";
        $errorMessage   = $_SESSION['flash_error'] ?? "";
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $ds = $eventModel->getAllEvent();
        require_once __DIR__ . '/../views/event_list.php';
    }

    // 2. Trang Thêm sự kiện riêng (GET: Hiển thị form, POST: Lưu dữ liệu)
    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $error = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new EventModel();
            
            $clubId    = $_POST['club_id'] ?? '1';
            $eventName = trim($_POST['event_name'] ?? '');
            $eventDate = $_POST['event_date'] ?? '';
            $slots     = (int)($_POST['slots'] ?? 0);
            $location  = trim($_POST['location'] ?? '');
            $status    = (string)($_POST['status'] ?? '1');

            if (empty($eventName) || empty($eventDate)) {
                $error = "Vui lòng điền đầy đủ tên và thời gian sự kiện!";
            } else {
                $event = new Event('', $clubId, $eventName, new DateTime($eventDate), $slots, $location, $status);

                if ($eventModel->insert($event)) {
                    $_SESSION['flash_success'] = " Thêm sự kiện mới thành công!";
                    header('Location: index.php?page=event');
                    exit();
                } else {
                    $error = "Thêm sự kiện thất bại, vui lòng thử lại.";
                }
            }
        }

        require_once __DIR__ . '/../views/event_create.php';
    }

    // 3. Trang Chỉnh sửa sự kiện riêng (GET: Hiển thị form kèm data, POST: Cập nhật)
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
                (int)$_POST['slots'],
                $_POST['location'],
                (string)$_POST['status']
            );

            if ($eventModel->update($event)) {
                $_SESSION['flash_success'] = "🎉 Cập nhật sự kiện thành công!";
                header('Location: index.php?page=event');
                exit();
            } else {
                $error = "Cập nhật sự kiện thất bại!";
            }
        }

        $event = $eventModel->getEventByID($id);
        require_once __DIR__ . '/../views/event_edit.php';
    }

    // 4. Xóa sự kiện
    public function delete() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $eventModel = new EventModel();
        $id = $_GET['id'] ?? null;

        if ($id && $eventModel->delete($id)) {
            $_SESSION['flash_success'] = "Đã xóa sự kiện thành công!";
        } else {
            $_SESSION['flash_error'] = "Không thể xóa sự kiện này!";
        }

        header('Location: index.php?page=event');
        exit();
    }
}