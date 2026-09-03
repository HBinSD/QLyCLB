<?php
require_once __DIR__ . '/../models/OrganizerModel.php';

class OrganizerController {
    private OrganizerModel $organizerModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->organizerModel = new OrganizerModel();
    }

    // Hàm dùng chung: Kiểm tra quyền đăng nhập Organizer / Admin
    private function checkRole(): string {
        if (!isset($_SESSION['user']['username'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $role = strtolower($_SESSION['user']['role'] ?? '');
        if ($role !== 'organizer' && $role !== 'admin') {
            header('Location: index.php?page=home');
            exit();
        }

        return $_SESSION['user']['username'];
    }

    // 1. Xem thông tin CLB của tôi
    public function myClub(): void {
        $username = $this->checkRole();

        // Lấy thông tin CLB từ Model
        $club = $this->organizerModel->getClubByOrganizer($username);

        require_once __DIR__ . '/../views/organizer_my_club.php';
    }

    // 2. Xem danh sách thành viên trong CLB
    public function clubMembers(): void {
        $username = $this->checkRole();

        // Lấy thông báo Flash nếu có
        $successMessage = $_SESSION['flash_success'] ?? "";
        $errorMessage   = $_SESSION['flash_error'] ?? "";
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Lấy thông tin CLB của tài khoản đang đăng nhập
        $club = $this->organizerModel->getClubByOrganizer($username);

        $members = [];
        if (!empty($club) && isset($club['club_id'])) {
            // Chỉ gọi truy vấn lấy thành viên khi tìm thấy CLB
            $members = $this->organizerModel->getMembersByClubId((string)$club['club_id']);
        }

        require_once __DIR__ . '/../views/organizer_club_members.php';
    }

    // 3. Trang thêm thành viên mới
    public function addMember(): void {
        $username = $this->checkRole();
        $club = $this->organizerModel->getClubByOrganizer($username);

        $error = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memberUsername = trim($_POST['username'] ?? '');
            $position       = trim($_POST['position'] ?? 'Thành viên');
            $status         = (int)($_POST['status'] ?? 1);

            if (!$club) {
                $error = "Bạn chưa được phân công quản lý CLB nào!";
            } elseif (empty($memberUsername)) {
                $error = "Vui lòng nhập Username thành viên!";
            } elseif (!$this->organizerModel->checkUserExists($memberUsername)) {
                $error = "Tài khoản '$memberUsername' không tồn tại trên hệ thống!";
            } elseif ($this->organizerModel->isMemberInClub((int)$club['club_id'], $memberUsername)) {
                $error = "Tài khoản '$memberUsername' đã là thành viên của CLB rồi!";
            } else {
                if ($this->organizerModel->addMember((int)$club['club_id'], $memberUsername, $position, $status)) {
                    $_SESSION['flash_success'] = "🎉 Thêm thành viên mới thành công!";
                    header('Location: index.php?page=club-members');
                    exit();
                } else {
                    $error = "Không thể thêm thành viên, vui lòng thử lại!";
                }
            }
        }

        require_once __DIR__ . '/../views/organizer_member_add.php';
    }

    // 4. Trang chỉnh sửa vai trò / trạng thái
    public function editMember(): void {
        $username = $this->checkRole();
        $club = $this->organizerModel->getClubByOrganizer($username);

        $targetUsername = $_GET['username'] ?? ($_POST['target_username'] ?? '');
        $error = "";

        if (!$club || empty($targetUsername)) {
            header('Location: index.php?page=club-members');
            exit();
        }

        $member = $this->organizerModel->getMemberByUsername((int)$club['club_id'], $targetUsername);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $position = trim($_POST['position'] ?? 'Thành viên');
            $status   = (int)($_POST['status'] ?? 1);

            if ($this->organizerModel->updateMember((int)$club['club_id'], $targetUsername, $position, $status)) {
                $_SESSION['flash_success'] = "🎉 Cập nhật thông tin thành viên thành công!";
                header('Location: index.php?page=club-members');
                exit();
            } else {
                $error = "Cập nhật thất bại!";
            }
        }

        require_once __DIR__ . '/../views/organizer_member_edit.php';
    }

    // 5. Xóa thành viên khỏi CLB
    public function deleteMember(): void {
        $username = $this->checkRole();
        $club = $this->organizerModel->getClubByOrganizer($username);

        $targetUsername = $_GET['username'] ?? '';

        if ($club && !empty($targetUsername)) {
            if ($this->organizerModel->deleteMember((int)$club['club_id'], $targetUsername)) {
                $_SESSION['flash_success'] = "Đã xóa thành viên khỏi CLB!";
            } else {
                $_SESSION['flash_error'] = "Không thể xóa thành viên này!";
            }
        }

        header('Location: index.php?page=club-members');
        exit();
    }
}