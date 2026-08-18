<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Event.php";

$pageTitle = "Sự kiện";

require_once "../includes/header.php";


$database = new Database();

$db = $database->getConnection();

$eventModel = new Event($db);

$events = $eventModel->getAll();

?>


<div class="page-header">

    <h2>
        Sự kiện
    </h2>

    <p>
        Danh sách các hoạt động và sự kiện của câu lạc bộ.
    </p>

</div>


<div class="event-grid">

    <?php foreach ($events as $event): ?>

        <div class="event-card">


            <div class="event-status">

                <?= htmlspecialchars(
                    $event['status']
                ) ?>

            </div>


            <h3>

                <?= htmlspecialchars(
                    $event['title']
                ) ?>

            </h3>


            <p class="event-club">

                🏢

                <?= htmlspecialchars(
                    $event['club_name']
                ) ?>

            </p>


            <p>

                📅

                <?= date(
                    'd/m/Y H:i',
                    strtotime($event['start_time'])
                ) ?>

            </p>


            <p>

                📍

                <?= htmlspecialchars(
                    $event['location']
                ) ?>

            </p>


            <p>

                👥

                <?= htmlspecialchars(
                    $event['capacity']
                ) ?>

                người

            </p>


            <a
                href="event.php?id=<?= $event['id'] ?>"
                class="btn-primary"
            >

                Xem chi tiết

            </a>


        </div>

    <?php endforeach; ?>

</div>


<?php require_once "../includes/footer.php"; ?>

<style>
    .event-grid {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;
}


.event-card {
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 3px 15px
        rgba(0, 0, 0, 0.05);
}


.event-card h3 {
    color: #1e293b;

    margin: 15px 0;
}


.event-card p {
    color: #64748b;

    font-size: 14px;

    margin: 10px 0;
}


.event-club {
    color: #2563eb !important;

    font-weight: 600;
}


.event-status {
    display: inline-block;

    background: #dcfce7;

    color: #16a34a;

    padding: 5px 10px;

    border-radius: 20px;

    font-size: 12px;
}


@media (max-width: 900px) {

    .event-grid {
        grid-template-columns: 1fr 1fr;
    }

}


@media (max-width: 600px) {

    .event-grid {
        grid-template-columns: 1fr;
    }

}
</style>