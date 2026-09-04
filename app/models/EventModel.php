<?php  
require_once __DIR__ . '/../../core/Database.php';
require_once 'event.php';

class EventModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Hàm ánh xạ từ dữ liệu DB sang Object Event
    private function mapRowToEvent(array $row): Event {
        return new Event(
            (string)$row['event_id'],
            (string)$row['club_id'],
            $row['event_name'],
            new DateTime($row['event_date']),
            !empty($row['start_time']) ? new DateTime($row['start_time']) : null,
            !empty($row['end_time']) ? new DateTime($row['end_time']) : null,
            (int)$row['slots'],
            $row['location'],
            (string)$row['status'],
            (string)($row['register_status'] ?? 'pending')
        );
    }

    public function getAllEvent(): array {
        $sql = "SELECT * FROM event ORDER BY event_date DESC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'mapRowToEvent'], $rows);
    }

    // 1. Lấy danh sách sự kiện sinh viên ĐÃ ĐĂNG KÝ
    public function getEventsByStudent(string $username): array {
        $sql = "SELECT e.*, re.register_status 
                FROM event e 
                JOIN register_event re ON e.event_id = re.event_id
                WHERE re.username = :username 
                AND (re.register_status IS NULL OR re.register_status != 'cancelled')
                ORDER BY e.event_date DESC";
                
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':username' => $username]);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToEvent'], $rows);
    }

    // 2. Lấy danh sách sự kiện ĐANG MỞ mà sinh viên CHƯA ĐĂNG KÝ
    public function getAvailableEventsForStudent(string $username): array {
        $sql = "SELECT e.* FROM event e 
                WHERE e.status = '1' 
                AND e.event_id NOT IN (
                    SELECT event_id FROM register_event 
                    WHERE username = :username 
                    AND register_status IN ('pending', 'approved')
                )
                ORDER BY e.event_date ASC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':username' => $username]);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToEvent'], $rows);
    }

    // 3. ĐĂNG KÝ SỰ KIỆN CÓ TRANSACTION ROLLBACK
    public function registerEventWithTransaction(string $eventId, string $username): array {
        try {
            $this->pdo->beginTransaction();

            $sqlEvent = "SELECT slots, status FROM event WHERE event_id = :event_id FOR UPDATE";
            $stmEvent = $this->pdo->prepare($sqlEvent);
            $stmEvent->execute([':event_id' => $eventId]);
            $event = $stmEvent->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Sự kiện không tồn tại!'];
            }

            if ((string)$event['status'] !== '1') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Sự kiện hiện tại đã đóng đăng ký!'];
            }

            if ((int)$event['slots'] <= 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Đăng ký thất bại: Sự kiện đã hết slot!'];
            }

            $sqlCheck = "SELECT register_status FROM register_event WHERE event_id = :event_id AND username = :username";
            $stmCheck = $this->pdo->prepare($sqlCheck);
            $stmCheck->execute([':event_id' => $eventId, ':username' => $username]);
            $existingStatus = $stmCheck->fetchColumn();

            if ($existingStatus !== false && in_array($existingStatus, ['pending', 'approved'], true)) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Bạn đã đăng ký sự kiện này rồi!'];
            }

            $sqlUpdateSlot = "UPDATE event SET slots = slots - 1 WHERE event_id = :event_id AND slots > 0";
            $stmUpdateSlot = $this->pdo->prepare($sqlUpdateSlot);
            $stmUpdateSlot->execute([':event_id' => $eventId]);

            if ($stmUpdateSlot->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Rất tiếc, sự kiện vừa hết slot!'];
            }

            if ($existingStatus !== false) {
                $sqlInsert = "UPDATE register_event 
                              SET register_status = 'pending', register_time = NOW() 
                              WHERE event_id = :event_id AND username = :username";
            } else {
                $sqlInsert = "INSERT INTO register_event (username, event_id, register_time, register_status) 
                              VALUES (:username, :event_id, NOW(), 'pending')";
            }
            
            $stmInsert = $this->pdo->prepare($sqlInsert);
            $stmInsert->execute([
                ':username' => $username,
                ':event_id' => $eventId
            ]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Gửi yêu cầu đăng ký tham gia sự kiện thành công!'];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    // 4. HỦY ĐĂNG KÝ
    public function unregisterEventWithTransaction(string $eventId, string $username): array {
        try {
            $this->pdo->beginTransaction();

            $sqlEvent = "SELECT slots FROM event WHERE event_id = :event_id FOR UPDATE";
            $stmEvent = $this->pdo->prepare($sqlEvent);
            $stmEvent->execute([':event_id' => $eventId]);

            $sqlCancel = "UPDATE register_event 
                          SET register_status = 'cancelled', register_time = NOW() 
                          WHERE event_id = :event_id AND username = :username AND register_status IN ('pending', 'approved')";
            $stmCancel = $this->pdo->prepare($sqlCancel);
            $stmCancel->execute([':event_id' => $eventId, ':username' => $username]);

            if ($stmCancel->rowCount() > 0) {
                $sqlUpdateSlot = "UPDATE event SET slots = slots + 1 WHERE event_id = :event_id";
                $stmUpdateSlot = $this->pdo->prepare($sqlUpdateSlot);
                $stmUpdateSlot->execute([':event_id' => $eventId]);

                $this->pdo->commit();
                return ['success' => true, 'message' => 'Hủy đăng ký sự kiện thành công!'];
            } else {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Bạn chưa đăng ký hoặc đơn đã bị hủy từ trước!'];
            }
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    public function getEventByID($event_id): ?Event {
        $sql = "SELECT * FROM event WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':event_id' => $event_id]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        
        return $this->mapRowToEvent($row);
    }

    public function insert(Event $event): bool {
        $sql = "INSERT INTO event (club_id, event_name, event_date, start_time, end_time, slots, location, status) 
                VALUES (:club_id, :event_name, :event_date, :start_time, :end_time, :slots, :location, :status)";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_id'    => $event->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date' => $event->getEventDate()->format('Y-m-d'),
            ':start_time' => $event->getStartTime()?->format('Y-m-d H:i:s'),
            ':end_time'   => $event->getEndTime()?->format('Y-m-d H:i:s'),
            ':slots'      => $event->getSlot(),
            ':location'   => $event->getLocation(),
            ':status'     => $event->getStatus()
        ]);
    }

    public function delete($event_id): bool {
        $sql = "DELETE FROM event WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([':event_id' => $event_id]);
    }

    public function update(Event $event): bool {
        $sql = "UPDATE event 
                SET club_id = :club_id, 
                    event_name = :event_name, 
                    event_date = :event_date, 
                    start_time = :start_time, 
                    end_time = :end_time, 
                    slots = :slots, 
                    location = :location, 
                    status = :status 
                WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':event_id'   => $event->getEventId(),
            ':club_id'    => $event->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date' => $event->getEventDate()->format('Y-m-d'),
            ':start_time' => $event->getStartTime()?->format('Y-m-d H:i:s'),
            ':end_time'   => $event->getEndTime()?->format('Y-m-d H:i:s'),
            ':slots'      => $event->getSlot(),
            ':location'   => $event->getLocation(),
            ':status'     => $event->getStatus()
        ]);
    }
    // Lấy danh sách sinh viên đăng ký theo mã sự kiện
    public function getParticipantsByEventId(string $eventId): array {
        $sql = "SELECT re.username, re.register_time, re.register_status,
                       ui.fullname, ui.email, ui.phone
                FROM register_event re
                JOIN userinfo ui ON re.username = ui.username
                WHERE re.event_id = :event_id
                ORDER BY re.register_time DESC";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':event_id' => $eventId]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAttendanceListByEvent(string $eventId): array {
    $sql = "SELECT re.username, re.register_time, re.attendance_status, re.attendance_time,
                   ui.fullname, ui.email, ui.phone, ui.id_number
            FROM register_event re
            JOIN userinfo ui ON re.username = ui.username
            WHERE re.event_id = :event_id AND re.register_status = 'approved'
            ORDER BY ui.fullname ASC";
    $stm = $this->pdo->prepare($sql);
    $stm->execute([':event_id' => $eventId]);
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

// Cập nhật trạng thái điểm danh bằng Fetch API
public function updateAttendanceStatus(string $eventId, string $username, int $status): bool {
    $attendanceTime = ($status === 1) ? date('Y-m-d H:i:s') : null;
    $sql = "UPDATE register_event 
            SET attendance_status = :status, attendance_time = :attendance_time
            WHERE event_id = :event_id AND username = :username AND register_status = 'approved'";
    $stm = $this->pdo->prepare($sql);
    return $stm->execute([
        ':status'          => $status,
        ':attendance_time' => $attendanceTime,
        ':event_id'        => $eventId,
        ':username'        => $username
    ]);
}
}