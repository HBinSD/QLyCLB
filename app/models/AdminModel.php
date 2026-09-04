<?php
require_once __DIR__ . '/../../core/Database.php';

class AdminModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // ================= QUẢN LÝ TÀI KHOẢN =================
    public function getAllUsers(): array {
        $sql = "SELECT u.username, u.role, u.status, u.created_at, 
                       ui.fullname, ui.email, ui.phone, ui.id_number
                FROM user u
                LEFT JOIN userinfo ui ON u.username = ui.username
                ORDER BY u.created_at DESC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserRoleAndStatus(string $username, string $role, int $status): bool {
        $sql = "UPDATE user SET role = :role, status = :status WHERE username = :username";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':role'     => $role,
            ':status'   => $status,
            ':username' => $username
        ]);
    }

    // ================= QUẢN LÝ CÂU LẠC BỘ =================
    public function getAllClubs(): array {
        $sql = "SELECT c.*, ui1.fullname AS creator_name, ui2.fullname AS owner_name
                FROM clubs c
                LEFT JOIN userinfo ui1 ON c.created_by = ui1.username
                LEFT JOIN userinfo ui2 ON c.owner_id = ui2.username
                ORDER BY c.created_at DESC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createClub(string $clubName, string $description, string $rule, string $ownerId, string $createdBy): bool {
        $sql = "INSERT INTO clubs (club_name, description, rule, owner_id, created_by, created_at, status)
                VALUES (:club_name, :description, :rule, :owner_id, :created_by, NOW(), 1)";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_name'   => $clubName,
            ':description' => $description,
            ':rule'        => $rule,
            ':owner_id'    => $ownerId,
            ':created_by'  => $createdBy
        ]);
    }

    public function updateClub(int $clubId, string $clubName, string $description, string $rule, string $ownerId, int $status): bool {
        $sql = "UPDATE clubs 
                SET club_name = :club_name, description = :description, rule = :rule, owner_id = :owner_id, status = :status
                WHERE club_id = :club_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_name'   => $clubName,
            ':description' => $description,
            ':rule'        => $rule,
            ':owner_id'    => $ownerId,
            ':status'      => $status,
            ':club_id'     => $clubId
        ]);
    }

    public function deleteClub(int $clubId): bool {
        $sql = "DELETE FROM clubs WHERE club_id = :club_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([':club_id' => $clubId]);
    }

    // ================= THỐNG KÊ & BÁO CÁO =================
    public function getSystemStats(): array {
        $stats = [];
        
        // Tổng số sinh viên, câu lạc bộ, sự kiện
        $stats['total_users'] = $this->pdo->query("SELECT COUNT(*) FROM user WHERE role = 'member'")->fetchColumn();
        $stats['total_clubs'] = $this->pdo->query("SELECT COUNT(*) FROM clubs WHERE status = 1")->fetchColumn();
        $stats['total_events'] = $this->pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();
        $stats['total_registrations'] = $this->pdo->query("SELECT COUNT(*) FROM register_event WHERE register_status = 'approved'")->fetchColumn();

        // Thống kê số lượt đăng ký & điểm danh theo từng sự kiện
        $sqlEvents = "SELECT e.event_id, e.event_name, c.club_name, e.slots,
                             COUNT(re.username) AS registered_count,
                             SUM(CASE WHEN re.attendance_status = 1 THEN 1 ELSE 0 END) AS attended_count
                      FROM event e
                      LEFT JOIN clubs c ON e.club_id = c.club_id
                      LEFT JOIN register_event re ON e.event_id = re.event_id AND re.register_status = 'approved'
                      GROUP BY e.event_id, e.event_name, c.club_name, e.slots
                      ORDER BY registered_count DESC";
        $stm = $this->pdo->prepare($sqlEvents);
        $stm->execute();
        $stats['event_reports'] = $stm->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }
}