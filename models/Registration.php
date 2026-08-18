<?php

class Registration
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    // =========================
    // KIỂM TRA ĐÃ ĐĂNG KÝ CHƯA
    // =========================

    public function isRegistered($eventId, $userId)
    {
        $sql = "SELECT id
                FROM event_registrations
                WHERE event_id = :event_id
                AND user_id = :user_id
                AND status != 'cancelled'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ":event_id" => $eventId,
            ":user_id" => $userId
        ]);

        return $stmt->fetch() !== false;
    }


    // =========================
    // ĐĂNG KÝ SỰ KIỆN
    // =========================

    public function register($eventId, $userId)
    {
        $sql = "INSERT INTO event_registrations
                (
                    event_id,
                    user_id,
                    status
                )
                VALUES
                (
                    :event_id,
                    :user_id,
                    'registered'
                )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":event_id" => $eventId,
            ":user_id" => $userId
        ]);
    }


    // =========================
    // HỦY ĐĂNG KÝ
    // =========================

    public function cancel($eventId, $userId)
    {
        $sql = "UPDATE event_registrations

                SET status = 'cancelled'

                WHERE event_id = :event_id

                AND user_id = :user_id

                AND status = 'registered'";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":event_id" => $eventId,
            ":user_id" => $userId
        ]);
    }


    // =========================
    // LẤY SỰ KIỆN CỦA USER
    // =========================

    public function getMyEvents($userId)
    {
        $sql = "SELECT
                    er.id AS registration_id,

                    er.status AS registration_status,

                    er.registered_at,

                    e.id AS event_id,

                    e.title,

                    e.description,

                    e.start_time,

                    e.end_time,

                    e.location,

                    e.capacity,

                    c.name AS club_name

                FROM event_registrations er

                INNER JOIN events e
                    ON er.event_id = e.id

                INNER JOIN clubs c
                    ON e.club_id = c.id

                WHERE er.user_id = :user_id

                ORDER BY e.start_time DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":user_id", $userId);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    // =========================
    // ĐẾM ĐĂNG KÝ
    // =========================

    public function countMyRegistrations($userId)
    {
        $sql = "SELECT COUNT(*)

                FROM event_registrations

                WHERE user_id = :user_id

                AND status = 'registered'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":user_id", $userId);

        $stmt->execute();

        return $stmt->fetchColumn();
    }
}