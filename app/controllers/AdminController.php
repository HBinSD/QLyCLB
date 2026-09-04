<?php
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    private AdminModel $adminModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->checkAdminRole();
        $this->adminModel = new AdminModel();
    }

    private function checkAdminRole(): void {
        if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    // 1. Quản lý Tài khoản (page=admin-members)
    public function members(): void {
        $successMessage = $_SESSION['flash_success'] ?? "";
        $errorMessage   = $_SESSION['flash_error'] ?? "";
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $role     = trim($_POST['role'] ?? 'member');
            $status   = (int)($_POST['status'] ?? 1);

            if (!empty($username)) {
                if ($this->adminModel->updateUserRoleAndStatus($username, $role, $status)) {
                    $_SESSION['flash_success'] = "Cập nhật tài khoản $username thành công!";
                } else {
                    $_SESSION['flash_error'] = "Cập nhật tài khoản thất bại!";
                }
            }
            header('Location: index.php?page=admin-members');
            exit();
        }

        $users = $this->adminModel->getAllUsers();
        require_once __DIR__ . '/../views/admin/admin_members.php';
    }

    // 2. Quản lý Câu lạc bộ (page=admin-clubs)
    public function clubs(): void {
        $successMessage = $_SESSION['flash_success'] ?? "";
        $errorMessage   = $_SESSION['flash_error'] ?? "";
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $clubs = $this->adminModel->getAllClubs();
        $users = $this->adminModel->getAllUsers(); // Lấy danh sách chọn Chủ nhiệm (Organizer)
        require_once __DIR__ . '/../views/admin/admin_clubs.php';
    }

    // Thêm CLB mới (page=admin-club-create)
    public function createClub(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clubName    = trim($_POST['club_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $rule        = trim($_POST['rule'] ?? '');
            $ownerId     = trim($_POST['owner_id'] ?? '');
            $createdBy   = $_SESSION['user']['username'];

            if (!empty($clubName) && !empty($ownerId)) {
                if ($this->adminModel->createClub($clubName, $description, $rule, $ownerId, $createdBy)) {
                    $_SESSION['flash_success'] = "Thêm câu lạc bộ mới thành công!";
                } else {
                    $_SESSION['flash_error'] = "Thêm câu lạc bộ thất bại!";
                }
            } else {
                $_SESSION['flash_error'] = "Vui lòng nhập tên CLB và chọn người quản lý!";
            }
        }
        header('Location: index.php?page=admin-clubs');
        exit();
    }

    // Xóa CLB (page=admin-club-delete)
    public function deleteClub(): void {
        $clubId = (int)($_GET['id'] ?? 0);
        if ($clubId > 0) {
            if ($this->adminModel->deleteClub($clubId)) {
                $_SESSION['flash_success'] = "Xóa câu lạc bộ thành công!";
            } else {
                $_SESSION['flash_error'] = "Khổng thể xóa câu lạc bộ này!";
            }
        }
        header('Location: index.php?page=admin-clubs');
        exit();
    }

    // 3. Thống kê & Báo cáo (page=admin-reports)
    public function reports(): void {
        $stats = $this->adminModel->getSystemStats();
        require_once __DIR__ . '/../views/admin/admin_reports.php';
    }
}