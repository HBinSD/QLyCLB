<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Registration.php";

$pageTitle = "Sự kiện của tôi";

require_once "../includes/header.php";


$userId = $_SESSION['user']['id'];


$database = new Database();

$db = $database->getConnection();


$registrationModel = new Registration($db);

$events = $registrationModel->getMyEvents(
    $userId
);

?>


<div class="page-header">

    <h2>
        Sự kiện của tôi
    </h2>

    <p>
        Quản lý các sự kiện bạn đã đăng ký.
    </p>

</div>


<div class="card">


    <?php if (empty($events)): ?>

        <div style="
            text-align:center;
            padding:40px;
            color:#94a3b8;
        ">

            Bạn chưa đăng ký sự kiện nào.

            <br><br>

            <a
                href="events.php"
                class="btn-primary"
            >
                Xem sự kiện
            </a>

        </div>


    <?php else: ?>


        <div class="my-event-list">


            <?php foreach ($events as $event): ?>


                <div class="my-event-item">


                    <div>

                        <h3>

                            <?= htmlspecialchars(
                                $event['title']
                            ) ?>

                        </h3>


                        <p>

                            🏢

                            <?= htmlspecialchars(
                                $event['club_name']
                            ) ?>

                        </p>


                        <p>

                            📅

                            <?= date(
                                'd/m/Y H:i',
                                strtotime(
                                    $event['start_time']
                                )
                            ) ?>

                        </p>


                        <p>

                            📍

                            <?= htmlspecialchars(
                                $event['location']
                            ) ?>

                        </p>

                    </div>


                    <div class="my-event-right">


                        <?php

                        $status =
                            $event['registration_status'];

                        ?>


                        <?php if ($status === 'registered'): ?>

                            <span class="status registered">
                                Đã đăng ký
                            </span>


                            <form
                                method="POST"
                                action="cancel-registration.php"
                                onsubmit="
                                    return confirm(
                                        'Bạn có chắc muốn hủy đăng ký?'
                                    );
                                "
                            >

                                <input
                                    type="hidden"
                                    name="event_id"
                                    value="<?= $event['event_id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn-danger"
                                >
                                    Hủy đăng ký
                                </button>

                            </form>


                        <?php elseif ($status === 'attended'): ?>

                            <span class="status attended">
                                Đã tham gia
                            </span>


                        <?php elseif ($status === 'absent'): ?>

                            <span class="status absent">
                                Vắng mặt
                            </span>


                        <?php else: ?>

                            <span class="status cancelled">
                                Đã hủy
                            </span>

                        <?php endif; ?>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    <?php endif; ?>


</div>


<?php require_once "../includes/footer.php"; ?>