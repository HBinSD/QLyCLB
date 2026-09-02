<?php 
    require_once __DIR__ . '/../../core/Database.php';
    require_once 'event.php';
    require_once __DIR__ . '/../dao/DAOInterface.php';
    class EventModel{
        private PDO $pdo;
        public function __construct()
        {
            $database = new Database();
            $this->pdo =$database->getConnection(); // Lấy đối tượng kết nối
        }
    public function getAllEvent():array{
        // Chuẩn bị câu lệnh sql
        $sql = "SELECT * FROM event ";// trong mysql thì dùng dấu nháy đơn để ngoài tên bảng
        // Bảo vệ câu lệnh sql
        $stm = $this->pdo->prepare($sql);
        // Thực thi câu lệnh sql
        $stm -> execute();
        // Hàm để trả về mảng kết hợp
        $rows = $stm -> fetchAll(PDO::FETCH_ASSOC);
        $events =[];
        foreach ($rows as $row) {
            $events[] = new Event(
                $row['event_id'],
                $row['club_id'],
                $row['event_name'],
                new DateTime($row['event_date']),
                (int)$row['slots'],
                $row['location'],
                $row['status']
            );
        }
        return $events;
    }
    public function getEventByID($event_id){
        // Chuẩn bị câu lệnh sql
        $sql = "SELECT * FROM event WHERE event_id = :event_id";
        // Bảo vệ câu lệnh
        $stm = $this->pdo->prepare($sql);
        // Thực thi câu lệnh
        $stm -> execute($event_id);// nếu có dữ liệu cần truyền vào
        //Hàm trả về mảng kết hợp nếu trả về 1 hàm duy nhất chỉ dùng fetch()
        $row = $stm ->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        return new Event(
                $row['event_id'],
                $row['club_id'],
                $row['event_name'],
                new DateTime($row['event_date']),
                (int)$row['slots'],
                $row['location'],
                $row['status']
        );

    }
    Public function insert(Event $event):bool{

        $sql = "INSERT INTO event (event_id, club_id,event_name,event_date,slots,location,status) VALUE (:event_id, :club_id,:event_name,:event_date,:slots,:location,:status)";
        $stm = $this->pdo->prepare($sql);
        $stm ->execute([
            ':event_id' => $event->getEventId(),
            ':club_id' => $event ->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date'=> $event->getEventDate(),
            ':slots' => $event->getSlot(),
            ':location' => $event->getLocation(),
            ':status' => $event ->getStatus()
        ]);
        return 1;
    }
    public function delete($event_id):bool{
        $sql = "DELETE FROM event WHERE event_id = :event_id";
        $stm = $this->pdo->prepare($sql);
        $stm ->execute([':event_id' => $event_id]);
        return 1;
    }        
    public function update(Event $event):bool{
        $sql = "UPDATE event SET club_id=:club_id,event_date=:event_date,slots=:slots,location=:location,status=:status WHERE event_id=:event_id";
        $stm = $this->pdo->prepare($sql);
        $stm ->execute([
            ':event_id' => $event->getEventId(),
            ':club_id' => $event ->getClubId(),
            ':event_name' => $event->getEventName(),
            ':event_date'=> $event->getEventDate(),
            ':slot' => $event->getSlot(),
            ':location' => $event->getLocation(),
            ':status' => $event ->getStatus()
        ]);
        return 1;
    }
    }
?>