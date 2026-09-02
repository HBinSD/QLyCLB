<?php 
// Vai trò là nhà quản lý đứng ở giữa để kết nối và điều phối công việc 
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../models/EventModel.php';

    class Event_Controller{
        //Hàm xử lí sự kiện danh sách 
        // gọi model lấy danh sách cơ sở dữ liệu
        public function index(){
        $eventModel = new EventModel();
        $ds = $eventModel->getAllEvent();
        require_once __DIR__ . '/../views/event_list.php';
        }
    }
?>