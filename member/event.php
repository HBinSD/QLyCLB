<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Event.php";
require_once "../models/Registration.php";

$pageTitle = "Chi tiết sự kiện";

require_once "../includes/header.php";


if (!isset($_GET['id'])) {

    echo "<div class='card'>Không tìm thấy sự kiện.</div>";

    require_once "../includes/footer.php";

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

    echo "<div class='card'>Sự kiện không tồn tại.</div>";

    require_once "../includes/footer.php";

    exit;
}


$registered = $registrationModel->isRegistered(
    $eventId,
    $userId
);


$registeredCount = $eventModel->countRegistrations(
    $eventId
);


$full = (
    $event['capacity'] > 0 &&
    $registeredCount >= $event['capacity']
);

?>


<div class="page-header">

    <h2>

        <?= htmlspecialchars(
            $event['title']
        ) ?>

    </h2>

    <p>

        <?= htmlspecialchars(
            $event['club_name']
        ) ?>

    </p>

</div>


<div class="card event-detail">


    <h3 class="card-title">

        <?= htmlspecialchars(
            $event['title']
        ) ?>

    </h3>


    <div class="event-detail-info">


        <div>

            <strong>
                Câu lạc bộ
            </strong>

            <p>
                <?= htmlspecialchars(
                    $event['club_name']
                ) ?>
            </p>

        </div>


        <div>

            <strong>
                Thời gian
            </strong>

            <p>

                <?= date(
                    'd/m/Y H:i',
                    strtotime($event['start_time'])
                ) ?>

                -

                <?= date(
                    'H:i',
                    strtotime($event['end_time'])
                ) ?>

            </p>

        </div>


        <div>

            <strong>
                Địa điểm
            </strong>

            <p>

                <?= htmlspecialchars(
                    $event['location']
                ) ?>

            </p>

        </div>


        <div>

            <strong>
                Số lượng
            </strong>

            <p>

                <?= $registeredCount ?>

                /

                <?= $event['capacity'] ?>

                người

            </p>

        </div>


    </div>


    <hr>


    <h3 class="card-title">

        Nội dung

    </h3>


    <p style="
        line-height:1.8;
        color:#64748b;
    ">

        <?= nl2br(
            htmlspecialchars(
                $event['description'] ?? ''
            )
        ) ?>

    </p>


    <div style="margin-top:30px;">


        <?php if ($registered): ?>

            <div class="registered-box">

                ✓ Bạn đã đăng ký sự kiện này.

                <br>

                Bạn có thể quản lý đăng ký trong
                <a href="my-events.php">
                    Sự kiện của tôi
                </a>

            </div>


        <?php elseif ($full): ?>

            <div class="full-box">

                Sự kiện đã đủ số lượng đăng ký.

            </div>


        <?php elseif ($event['status'] === 'cancelled'): ?>

            <div class="full-box">

                Sự kiện đã bị hủy.

            </div>


        <?php elseif ($event['status'] === 'completed'): ?>

            <div class="full-box">

                Sự kiện đã kết thúc.

            </div>


        <?php else: ?>

            <a
                href="register-event.php?id=<?= $eventId ?>"
                class="btn-primary"
            >

                Đăng ký tham gia

            </a>

        <?php endif; ?>


    </div>


</div>


<?php require_once "../includes/footer.php"; ?>