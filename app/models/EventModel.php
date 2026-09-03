<?php 
require_once __DIR__ . '/../../core/Database.php';
require_once 'event.php';

class EventModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAllEvent(): array {
        $sql = "SELECT * FROM event";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($rows as $row) {
            $events[] = new Event(
                (string)$row['event_id'],
                (string)$row['club_id'],
                $row['event_name'],
                new DateTime($row['event_date']),
                (int)$row['slots'],
                $row['location'],
                (string)$row['status']
            );
        }
        return $events;
    }

    public function getEventByID($event_id): ?Event {
        $sql = "SELECT * FROM event WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':event_id' => $event_id]);
        
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Event(
            (string)$row['event_id'],
            (string)$row['club_id'],
            $row['event_name'],
            new DateTime($row['event_date']),
            (int)$row['slots'],
            $row['location'],
            (string)$row['status']
        );
    }

    public function insert(Event $event): bool {
        $sql = "INSERT INTO event (club_id, event_name, event_date, slots, location, status) 
                VALUES (:club_id, :event_name, :event_date, :slots, :location, :status)";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':club_id'    => $event->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date' => $event->getEventDate()->format('Y-m-d H:i:s'),
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
                    slots = :slots, 
                    location = :location, 
                    status = :status 
                WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':event_id'   => $event->getEventId(),
            ':club_id'    => $event->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date' => $event->getEventDate()->format('Y-m-d H:i:s'),
            ':slots'      => $event->getSlot(),
            ':location'   => $event->getLocation(),
            ':status'     => $event->getStatus()
        ]);
    }
}
?>