<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Registration.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: my-events.php");

    exit;
}


$eventId = (int) (
    $_POST['event_id'] ?? 0
);


$userId = $_SESSION['user']['id'];


if ($eventId <= 0) {

    header("Location: my-events.php");

    exit;
}


$database = new Database();

$db = $database->getConnection();


$registrationModel = new Registration($db);


$registrationModel->cancel(
    $eventId,
    $userId
);


header("Location: my-events.php");

exit;