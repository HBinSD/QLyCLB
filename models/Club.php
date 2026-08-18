<?php

class Club
{
    private $conn;
    private $table = "clubs";

    public function __construct($db)
    {
        $this->conn = $db;
    }


    // Lấy tất cả CLB
    public function getAll()
    {
        $sql = "SELECT *
                FROM clubs
                ORDER BY name ASC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    // Lấy CLB theo ID
    public function findById($id)
    {
        $sql = "SELECT *
                FROM clubs
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        return $stmt->fetch();
    }


    // Lấy thành viên của CLB
    public function getMembers($clubId)
    {
        $sql = "SELECT
                    u.id,
                    u.full_name,
                    u.msv,
                    u.class_name,
                    u.faculty,
                    cm.position
                FROM club_members cm

                INNER JOIN users u
                    ON cm.user_id = u.id

                WHERE cm.club_id = :club_id

                ORDER BY
                    CASE
                        WHEN cm.position = 'President' THEN 1
                        WHEN cm.position = 'Vice President' THEN 2
                        WHEN cm.position = 'Organizer' THEN 3
                        ELSE 4
                    END";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":club_id", $clubId);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}