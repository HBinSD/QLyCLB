<?php

class Event
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    // =========================
    // LẤY TẤT CẢ SỰ KIỆN
    // =========================

    public function getAll()
    {
        $sql = "SELECT
                    e.*,
                    c.name AS club_name
                FROM events e

                INNER JOIN clubs c
                    ON e.club_id = c.id

                ORDER BY e.start_time ASC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    // =========================
    // LẤY SỰ KIỆN THEO ID
    // =========================

    public function findById($id)
    {
        $sql = "SELECT
                    e.*,
                    c.name AS club_name
                FROM events e

                INNER JOIN clubs c
                    ON e.club_id = c.id

                WHERE e.id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        return $stmt->fetch();
    }


    // =========================
    // ĐẾM NGƯỜI ĐÃ ĐĂNG KÝ
    // =========================

    public function countRegistrations($eventId)
    {
        $sql = "SELECT COUNT(*)
                FROM event_registrations

                WHERE event_id = :event_id

                AND status = 'registered'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":event_id", $eventId);

        $stmt->execute();

        return $stmt->fetchColumn();
    }
}