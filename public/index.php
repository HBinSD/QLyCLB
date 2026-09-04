<?php 
session_start(); 
require_once __DIR__ . '/../core/Database.php'; 

$page = $_GET['page'] ?? 'home'; 

switch ($page) { 
    case 'home': 
        if (isset($_SESSION['user'])) { 
            require_once __DIR__ . '/../app/controllers/DashboardController.php'; 
            $dashboard = new DashboardController(); 
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

    // QUẢN LÝ SỰ KIỆN (EVENT)
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

    case 'event-register':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->register();
        break;

    case 'event-cancel':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->cancelRegistration();
        break;

    case 'event-registered':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->registeredEvents();
        break;

    case 'event-export-csv':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->exportRegisteredCSV();
        break;
    case 'event-participants':
        require_once __DIR__ . '/../app/controllers/Event_Controller.php';
        (new Event_Controller())->participants();
        break;

    // THÔNG TIN CÁ NHÂN & CLB
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

    // ORGANIZER
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
    case 'attendance':
    require_once __DIR__ . '/../app/controllers/Event_Controller.php';
    (new Event_Controller())->attendance();
    break;

    case 'api-attendance':
    require_once __DIR__ . '/../app/controllers/Event_Controller.php';
    (new Event_Controller())->apiAttendance();
    break;
    
    default: 
        header('Location: index.php?page=home'); 
        exit(); 
        break; 
}