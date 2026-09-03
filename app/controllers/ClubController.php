<?php
require_once __DIR__ . '/../models/ClubModel.php';

class ClubController {
    private ClubModel $clubModel;

    public function __construct() {
        $this->clubModel = new ClubModel();
    }

    // 1. Xem Giới thiệu CLB
    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        $user = $_SESSION['user'];
        $club = $this->clubModel->getClubByUsername($user['username']);
        require_once __DIR__ . '/../views/club.php';
    }

    // 2. Xem Danh sách thành viên CLB
    public function members() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        $user = $_SESSION['user'];
        $userClub = $this->clubModel->getClubByUsername($user['username']);
        
        $members = [];
        $clubInfo = null;

        if ($userClub) {
            $members = $this->clubModel->getClubMembers((int)$userClub['club_id']);
            $clubInfo = $userClub;
        }

        require_once __DIR__ . '/../views/club_member.php';
    }
}
?>