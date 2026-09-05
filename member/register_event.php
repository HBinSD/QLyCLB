<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];


if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $user['username'];

/*
|--------------------------------------------------------------------------
| Lấy event_id
|--------------------------------------------------------------------------
*/

$eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventId) {
    header("Location: events.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$error = "";
$success = "";

/*
|--------------------------------------------------------------------------
| Bắt đầu transaction
|--------------------------------------------------------------------------
*/
try {
    $db->beginTransaction();
    /*
    |--------------------------------------------------------------------------
    | 1. Lấy thông tin Event
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            event_id,
            club_id,
            event_name,
            event_date,
            start_time,
            end_time,
            slots,
            status
        FROM Event
        WHERE event_id = :event_id
        LIMIT 1
        FOR UPDATE
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':event_id' => $eventId
    ]);

    $event = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$event) {
        throw new Exception(
            "Sự kiện không tồn tại."
        );
    }


    $clubId = $event['club_id'];


    /*
    |--------------------------------------------------------------------------
    | 2. Kiểm tra trạng thái Event & Thời hạn đăng ký
    |--------------------------------------------------------------------------
    */

    if ($event['status'] !== 'upcoming') {
        throw new Exception(
            "Sự kiện hiện không mở đăng ký."
        );
    }

    // KIỂM TRA QUÁ HẠN: So sánh thời gian hiện tại với thời gian bắt đầu sự kiện
    $eventStartTimestamp = strtotime($event['event_date'] . ' ' . $event['start_time']);
    if (time() >= $eventStartTimestamp) {
        throw new Exception(
            "Đã quá hạn đăng ký tham gia sự kiện này."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Kiểm tra User có phải thành viên CLB không
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT 1
        FROM ClubMember
        WHERE username = :username
          AND club_id = :club_id
          AND status = 1
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':club_id' => $clubId
    ]);

    $isMember = $stmt->fetchColumn();


    if (!$isMember) {
        throw new Exception(
            "Bạn chưa là thành viên của câu lạc bộ."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Kiểm tra user đã đăng ký Event chưa
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            register_status
        FROM Register_event
        WHERE username = :username
            AND event_id = :event_id
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':event_id' => $eventId
    ]);

    $existingRegistration = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($existingRegistration) {
        throw new Exception(
            "Bạn đã đăng ký sự kiện này rồi."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Kiểm tra ban được phép tham gia
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT COUNT(*)
        FROM EventBand AS eb
        WHERE eb.event_id = :event_id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':event_id' => $eventId
    ]);

    $requiredBandCount = (int)$stmt->fetchColumn();


    if ($requiredBandCount > 0) {

        $sql = "
            SELECT 1
            FROM ClubBandMember AS cbm
            INNER JOIN EventBand AS eb
                ON eb.band_id = cbm.band_id
            INNER JOIN ClubBand AS cb
                ON cb.band_id = cbm.band_id
                AND cb.club_id = cbm.club_id
            WHERE cbm.username = :username
              AND cbm.club_id = :club_id
              AND eb.event_id = :event_id
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':club_id' => $clubId,
            ':event_id' => $eventId
        ]);

        $allowed = $stmt->fetchColumn();


        if (!$allowed) {
            throw new Exception(
                "Bạn không thuộc ban được yêu cầu của sự kiện này."
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Kiểm tra số slot
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT COUNT(*)
        FROM Register_event
        WHERE event_id = :event_id
          AND register_status = 'approved'
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':event_id' => $eventId
    ]);

    $approvedCount = (int)$stmt->fetchColumn();
    $slots = (int)$event['slots'];


    if ($approvedCount >= $slots) {
        throw new Exception(
            "Sự kiện đã đủ số lượng người tham gia."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Tạo đăng ký (Tự động duyệt luôn theo yêu cầu trước đó)
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO Register_event
        (
            username,
            event_id,
            register_time,
            register_status,
            approved_by,
            approved_time
        )
        VALUES
        (
            :username,
            :event_id,
            NOW(),
            'approved',
            'SYSTEM',
            NOW()
        )
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':event_id' => $eventId
    ]);


    /*
    |--------------------------------------------------------------------------
    | 8. Commit
    |--------------------------------------------------------------------------
    */

    $db->commit();

    $success = "Đăng ký tham gia sự kiện thành công!";

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback nếu có lỗi
    |--------------------------------------------------------------------------
    */

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $error = $e->getMessage();
}


/*
|--------------------------------------------------------------------------
| Lấy lại thông tin Event để hiển thị
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        e.*,
        ui.fullname AS organizer_name
    FROM Event AS e
    LEFT JOIN UserInfo AS ui
        ON ui.username = e.organizer_id
    WHERE e.event_id = :event_id
    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':event_id' => $eventId
]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);


$pageTitle = "Đăng ký sự kiện";
$activeMenu = "events.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/register_event.css">
<link rel="stylesheet" href="css/club.css">


<div class="club-layout">

    <aside class="club-sidebar">
        <!-- Sidebar nội dung giữ nguyên -->
    </aside>


    <!-- CONTENT -->

    <main class="club-content">

        <a
            href="event_detail.php?id=<?= $eventId ?>"
            class="back-link"
        >
            ← Quay lại sự kiện
        </a>


        <div class="register-card">

            <h1>
                Đăng ký tham gia sự kiện
            </h1>


            <?php if ($event): ?>

                <div class="event-summary">

                    <h2>
                        <?= htmlspecialchars($event['event_name']) ?>
                    </h2>


                    <p>
                        📅 <?= date('d/m/Y', strtotime($event['event_date'])) ?>
                    </p>


                    <p>
                        🕐 <?= date('H:i', strtotime($event['start_time'])) ?>
                        -
                        <?= date('H:i', strtotime($event['end_time'])) ?>
                    </p>


                    <p>
                        📍 <?= htmlspecialchars($event['location'] ?? 'Chưa cập nhật') ?>
                    </p>

                </div>


                <?php if ($success): ?>

                    <div class="message success">
                        <?= htmlspecialchars($success) ?>
                    </div>


                    <div class="action-buttons">

                        <a
                            href="event_detail.php?id=<?= $eventId ?>"
                            class="btn-primary"
                        >
                            Quay lại sự kiện
                        </a>

                        <a
                            href="registered_events.php"
                            class="btn-secondary"
                        >
                            Xem đăng ký của tôi
                        </a>

                    </div>


                <?php elseif ($error): ?>

                    <div class="message error">
                        <?= htmlspecialchars($error) ?>
                    </div>


                    <div class="action-buttons">

                        <a
                            href="event_detail.php?id=<?= $eventId ?>"
                            class="btn-primary"
                        >
                            Quay lại
                        </a>

                    </div>


                <?php endif; ?>


            <?php else: ?>

                <div class="message error">
                    Không tìm thấy sự kiện.
                </div>

                <a
                    href="events.php"
                    class="btn-primary"
                >
                    Quay lại danh sách sự kiện
                </a>

            <?php endif; ?>

        </div>

    </main>

</div>


<?php require_once "../includes/footer.php"; ?>