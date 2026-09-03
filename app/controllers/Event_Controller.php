<?php 
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../models/EventModel.php';

class Event_Controller {

    public function index() {
        $eventModel = new EventModel();
        $ds = $eventModel->getAllEvent();
        require_once __DIR__ . '/../views/event_list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new EventModel();
            $event = new Event(
                $_POST['event_id'] ?? '',
                $_POST['club_id'],
                $_POST['event_name'],
                new DateTime($_POST['event_date']),
                (int)$_POST['slots'],
                $_POST['location'],
                (string)$_POST['status']
            );
            $eventModel->insert($event);
            header('Location: index.php?page=event');
            exit();
        }
        require_once __DIR__ . '/../views/event_create.php';
    }

    public function delete() {
        $eventModel = new EventModel();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $eventModel->delete($id);
        }
        header('Location: index.php?page=event');
        exit();
    }

    public function edit() {
        $eventModel = new EventModel();
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event = new Event(
                $_POST['event_id'],
                $_POST['club_id'],
                $_POST['event_name'],
                new DateTime($_POST['event_date']),
                (int)$_POST['slots'],
                $_POST['location'],
                (string)$_POST['status']
            );
            $eventModel->update($event);
            header('Location: index.php?page=event');
            exit();
        }

        $event = $eventModel->getEventByID($id);
        require_once __DIR__ . '/../views/event_edit.php';
    }
}
?>