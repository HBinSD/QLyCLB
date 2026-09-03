<?php
require_once __DIR__ . '/../../core/Database.php';

class ClubModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Lấy thông tin CLB mà sinh viên đang tham gia
    public function getClubByUsername(string $username): ?array {
        $sql = "SELECT cm.*, c.*, ui.fullname AS creator_name
                FROM clubmember AS cm
                JOIN clubs AS c ON cm.club_id = c.club_id
                LEFT JOIN userinfo AS ui ON c.created_by = ui.username
                WHERE cm.username = :username
                LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':username' => $username]);
        $res = $stm->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    // Lấy danh sách thành viên thuộc CLB
    public function getClubMembers(int $clubId): array {
        $sql = "SELECT cm.username, cm.club_id, cm.joined_at, cm.position, cm.status,
                       us.fullname, us.avt_links, cb.band_name
                FROM clubmember AS cm
                JOIN userinfo AS us ON us.username = cm.username
                LEFT JOIN clubbandmember AS cbm ON cbm.username = cm.username
                LEFT JOIN clubband AS cb ON cb.band_id = cbm.band_id
                WHERE cm.club_id = :club_id
                ORDER BY 
                    CASE WHEN cm.position IS NULL OR cm.position = '' THEN 1 ELSE 0 END,
                    cm.joined_at ASC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':club_id' => $clubId]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>