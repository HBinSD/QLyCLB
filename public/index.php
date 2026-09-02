<?php
// 1. Bật Session toàn hệ thống
session_start();

// 2. Nạp kết nối CSDL dùng chung
require_once __DIR__ . '/../core/Database.php';

// 3. Nhận tham số page từ URL, mặc định là 'login'
$page = $_GET['page'] ?? 'login';

// 4. Bộ điều hướng (Router)
switch ($page) {

    // ========================================
    // CHỨC NĂNG XÁC THỰC (AUTH)
    // ========================================
    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'register':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->register();
        break;

    case 'logout':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    // ========================================
    // QUẢN LÝ SỰ KIỆN (EVENT)
    // ========================================
    case 'event':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->index();
        break;

    // ========================================
    // DASHBOARD (ĐÃ BỎ CHECK ROLE)
    // ========================================
    case 'admin-dashboard':
        $fullname = $_SESSION['user']['fullname'] ?? 'Admin';
        echo "<h2>Chào mừng Admin: " . htmlspecialchars($fullname) . "</h2>";
        echo "<a href='index.php?page=event'>Quản lý sự kiện</a> | <a href='index.php?page=logout'>Đăng xuất</a>";
        break;

    case 'organizer-dashboard':
        echo "<h2>Trang Ban Tổ Chức (Organizer)</h2>";
        echo "<a href='index.php?page=event'>Quản lý sự kiện</a> | <a href='index.php?page=logout'>Đăng xuất</a>";
        break;

    // ========================================
    // MẶC ĐỊNH / ERROR 404
    // ========================================
    default:
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->index();
        break;
}
?>