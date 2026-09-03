<?php
require_once __DIR__ . '/../../core/Database.php';

class OrganizerModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Lấy CLB do Organizer quản lý
    public function getClubByOrganizer(string $username): ?array {
        $sql = "SELECT c.*, ui.fullname AS creator_name 
                FROM clubs AS c
                LEFT JOIN userinfo AS ui ON c.created_by = ui.username
                WHERE c.owner_id = :username
                LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':username' => $username]);
        $res = $stm->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    // Lấy danh sách thành viên trong CLB
    public function getMembersByClubId(int $clubId): array {
        $sql = "SELECT cm.username, cm.club_id, cm.joined_at, cm.position, cm.status,
                       us.fullname, us.email, us.phone
                FROM clubmember AS cm
                JOIN userinfo AS us ON us.username = cm.username
                WHERE cm.club_id = :club_id
                ORDER BY cm.joined_at DESC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':club_id' => $clubId]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra tài khoản người dùng có tồn tại trong hệ thống hay không
    public function checkUserExists(string $username): bool {
        $sql = "SELECT COUNT(*) FROM user WHERE username = :username";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':username' => $username]);
        return $stm->fetchColumn() > 0;
    }

    // Kiểm tra xem người dùng đã ở trong CLB chưa
    public function isMemberInClub(int $clubId, string $username): bool {
        $sql = "SELECT COUNT(*) FROM clubmember WHERE club_id = :club_id AND username = :username";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':club_id' => $clubId, ':username' => $username]);
        return $stm->fetchColumn() > 0;
    }

    // Thêm thành viên mới vào CLB
    public function addMember(int $clubId, string $username, string $position, int $status): bool {
        $sql = "INSERT INTO clubmember (club_id, username, position, status, joined_at) 
                VALUES (:club_id, :username, :position, :status, NOW())";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_id'  => $clubId,
            ':username' => $username,
            ':position' => $position,
            ':status'   => $status
        ]);
    }

    // Lấy thông tin 1 thành viên trong CLB
    public function getMemberByUsername(int $clubId, string $username): ?array {
        $sql = "SELECT cm.*, us.fullname, us.email 
                FROM clubmember AS cm 
                JOIN userinfo AS us ON us.username = cm.username 
                WHERE cm.club_id = :club_id AND cm.username = :username 
                LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':club_id' => $clubId, ':username' => $username]);
        $res = $stm->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    // Cập nhật vai trò / trạng thái thành viên
    public function updateMember(int $clubId, string $username, string $position, int $status): bool {
        $sql = "UPDATE clubmember 
                SET position = :position, status = :status 
                WHERE club_id = :club_id AND username = :username";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':position' => $position,
            ':status'   => $status,
            ':club_id'  => $clubId,
            ':username' => $username
        ]);
    }

    // Xóa thành viên khỏi CLB
    public function deleteMember(int $clubId, string $username): bool {
        $sql = "DELETE FROM clubmember WHERE club_id = :club_id AND username = :username";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_id'  => $clubId,
            ':username' => $username
        ]);
    }
}