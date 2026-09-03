<?php
class DashboardController {

    public function admin() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        require_once __DIR__ . '/../views/admin_dashboard.php';
    }

    public function organizer() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        require_once __DIR__ . '/../views/organizer_dashboard.php';
    }

    public function member() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        require_once __DIR__ . '/../views/member_dashboard.php';
    }
}
?>