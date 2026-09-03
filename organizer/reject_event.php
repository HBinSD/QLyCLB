<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

if (
    ($user['role'] ?? '') !== 'organizer'
    && ($user['role'] ?? '') !== 'admin'
) {
    header("Location: ../index.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| XỬ LÝ POST - TỪ CHỐI
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventId = filter_input(
        INPUT_POST,
        'event_id',
        FILTER_VALIDATE_INT
    );

    $username = trim(
        $_POST['username'] ?? ''
    );

    $rejectReason = trim(
        $_POST['reject_reason'] ?? ''
    );


    if (
        !$eventId
        || $username === ''
        || $rejectReason === ''
    ) {

        header(
            "Location: event_registrations.php?error="
            . urlencode("Vui lòng nhập đầy đủ thông tin.")
        );

        exit;
    }


    try {

        $sql = "
            UPDATE Register_event

            SET
                register_status = 'rejected',
                approved_by = :approved_by,
                approved_time = NOW(),
                reject_reason = :reject_reason

            WHERE username = :username
              AND event_id = :event_id
              AND register_status = 'pending'
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':approved_by' => $user['username'],
            ':reject_reason' => $rejectReason,
            ':username' => $username,
            ':event_id' => $eventId
        ]);


        if ($stmt->rowCount() === 0) {

            throw new Exception(
                "Đăng ký không tồn tại hoặc đã được xử lý."
            );

        }


        header(
            "Location: event_registrations.php?success=rejected"
        );

        exit;


    } catch (Exception $e) {

        header(
            "Location: event_registrations.php?error="
            . urlencode($e->getMessage())
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| HIỂN THỊ FORM
|--------------------------------------------------------------------------
*/

$eventId = filter_input(
    INPUT_GET,
    'event_id',
    FILTER_VALIDATE_INT
);

$username = trim(
    $_GET['username'] ?? ''
);


if (!$eventId || $username === '') {
    header("Location: event_registrations.php");
    exit;
}


$sql = "
    SELECT
        r.*,
        e.event_name,
        ui.fullname

    FROM Register_event AS r

    INNER JOIN Event AS e
        ON e.event_id = r.event_id

    LEFT JOIN UserInfo AS ui
        ON ui.username = r.username

    WHERE r.event_id = :event_id
      AND r.username = :username

    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':event_id' => $eventId,
    ':username' => $username
]);

$registration = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$registration) {
    header("Location: event_registrations.php");
    exit;
}


if ($registration['register_status'] !== 'pending') {
    header("Location: event_registrations.php");
    exit;
}


$pageTitle = "Từ chối đăng ký";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/event_registrations.css">


<div class="reject-page">

    <div class="reject-form-card">

        <div class="reject-icon">
            ❌
        </div>

        <h1>
            Từ chối đăng ký
        </h1>

        <p class="reject-description">
            Bạn đang từ chối yêu cầu đăng ký của:
        </p>


        <div class="applicant">

            <strong>
                <?= htmlspecialchars(
                    $registration['fullname']
                    ?? $registration['username']
                ) ?>
            </strong>

            <small>
                @<?= htmlspecialchars(
                    $registration['username']
                ) ?>
            </small>

        </div>


        <div class="event-info-box">

            <span>Sự kiện</span>

            <strong>
                <?= htmlspecialchars(
                    $registration['event_name']
                ) ?>
            </strong>

        </div>


        <form
            action="reject_event.php"
            method="POST"
        >

            <input
                type="hidden"
                name="event_id"
                value="<?= htmlspecialchars($eventId) ?>"
            >

            <input
                type="hidden"
                name="username"
                value="<?= htmlspecialchars($username) ?>"
            >


            <label for="reject_reason">
                Lý do từ chối
            </label>

            <textarea
                id="reject_reason"
                name="reject_reason"
                rows="5"
                placeholder="Nhập lý do từ chối..."
                required
            ></textarea>


            <div class="reject-actions">

                <a
                    href="event_registrations.php"
                    class="btn-cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn-confirm-reject"
                >
                    Xác nhận từ chối
                </button>

            </div>

        </form>

    </div>

</div>


<?php
require_once "../includes/footer.php";
?>