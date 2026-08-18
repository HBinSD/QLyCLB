<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Event.php";
require_once "../models/Registration.php";


if (!isset($_GET['id'])) {

    header("Location: events.php");

    exit;
}


$eventId = (int) $_GET['id'];

$userId = $_SESSION['user']['id'];


$database = new Database();

$db = $database->getConnection();


$eventModel = new Event($db);

$registrationModel = new Registration($db);


$event = $eventModel->findById($eventId);


if (!$event) {

    die("Sự kiện không tồn tại.");

}


if ($registrationModel->isRegistered(
    $eventId,
    $userId
)) {

    header(
        "Location: event.php?id=$eventId"
    );

    exit;
}


$count = $eventModel->countRegistrations(
    $eventId
);


if (
    $event['capacity'] > 0 &&
    $count >= $event['capacity']
) {

    die("Sự kiện đã đủ số lượng.");

}


$registrationModel->register(
    $eventId,
    $userId
);


header(
    "Location: event.php?id=$eventId"
);

exit;