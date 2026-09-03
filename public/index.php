<?php 
session_start(); 
require_once __DIR__ . '/../core/Database.php'; 

// 1. Mặc định nếu không truyền ?page= trên URL thì vào trang chủ 'home' 
$page = $_GET['page'] ?? 'home'; 

switch ($page) { 
    // ======================================== 
    // TRANG CHỦ / DASHBOARD
    // ======================================== 
    case 'home': 
        if (isset($_SESSION['user'])) { 
            require_once __DIR__ . '/../app/controllers/DashboardController.php'; 
            $dashboard = new DashboardController(); 
                         
            // Chuyển hướng theo Role người dùng
            switch ($_SESSION['user']['role']) { 
                case 'admin': 
                    $dashboard->admin(); 
                    break; 
                case 'organizer': 
                    $dashboard->organizer(); 
                    break; 
                case 'member': 
                default: 
                    $dashboard->member(); 
                    break; 
            } 
        } else { 
            header('Location: index.php?page=login'); 
            exit(); 
        } 
        break; 

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

    case 'event-create': 
        require_once __DIR__ . '/../app/controllers/Event_Controller.php'; 
        (new Event_Controller())->create(); 
        break; 

    case 'event-edit': 
        require_once __DIR__ . '/../app/controllers/Event_Controller.php'; 
        (new Event_Controller())->edit(); 
        break; 

    case 'event-delete': 
        require_once __DIR__ . '/../app/controllers/Event_Controller.php'; 
        (new Event_Controller())->delete(); 
        break; 

    // ======================================== 
    // THÔNG TIN CÁ NHÂN & DÀNH CHO THÀNH VIÊN
    // ======================================== 
    case 'profile': 
        require_once __DIR__ . '/../app/controllers/UserController.php'; 
        (new UserController())->profile(); 
        break; 

    case 'edit-profile': 
        require_once __DIR__ . '/../app/controllers/UserController.php'; 
        (new UserController())->editProfile(); 
        break; 

    case 'club': 
        require_once __DIR__ . '/../app/controllers/ClubController.php'; 
        (new ClubController())->index(); 
        break; 

    case 'club-member': 
        require_once __DIR__ . '/../app/controllers/ClubController.php'; 
        (new ClubController())->members(); 
        break; 

    // ======================================== 
    // DÀNH RIÊNG CHO BAN TỔ CHỨC (ORGANIZER)
    // ======================================== 
    case 'my-club': 
        require_once __DIR__ . '/../app/controllers/OrganizerController.php'; 
        (new OrganizerController())->myClub(); 
        break; 

    case 'club-members': 
        require_once __DIR__ . '/../app/controllers/OrganizerController.php'; 
        (new OrganizerController())->clubMembers(); 
        break; 

    case 'club-member-add': 
        require_once __DIR__ . '/../app/controllers/OrganizerController.php'; 
        (new OrganizerController())->addMember(); 
        break; 

    case 'club-member-edit': 
        require_once __DIR__ . '/../app/controllers/OrganizerController.php'; 
        (new OrganizerController())->editMember(); 
        break; 

    case 'club-member-delete': 
        require_once __DIR__ . '/../app/controllers/OrganizerController.php'; 
        (new OrganizerController())->deleteMember(); 
        break; 

    // ======================================== 
    // MẶC ĐỊNH CHUYỂN VỀ TRANG CHỦ
    // ======================================== 
    default: 
        header('Location: index.php?page=home'); 
        exit(); 
        break; 
}