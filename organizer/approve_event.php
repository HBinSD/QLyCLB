<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Chỉ organizer hoặc admin được duyệt
|--------------------------------------------------------------------------
*/

if (
    ($user['role'] ?? '') !== 'organizer'
    && ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}


/*
|--------------------------------------------------------------------------
| Kiểm tra POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: event_registrations.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$eventId  = filter_input(
    INPUT_POST,
    'event_id',
    FILTER_VALIDATE_INT
);

if ($username === '' || !$eventId) {
    header("Location: event_registrations.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();


try {

    $db->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Khóa Event
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT *
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


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra CLB
    |--------------------------------------------------------------------------
    */

    if ($event['club_id'] !== 'CLB001') {
        throw new Exception(
            "Sự kiện không thuộc câu lạc bộ."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Lấy đăng ký
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT *
        FROM Register_event

        WHERE username = :username
          AND event_id = :event_id

        LIMIT 1

        FOR UPDATE
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':event_id' => $eventId
    ]);

    $registration = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$registration) {

        throw new Exception(
            "Không tìm thấy đăng ký."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Chỉ duyệt đơn pending
    |--------------------------------------------------------------------------
    */

    if (
        $registration['register_status']
        !== 'pending'
    ) {

        throw new Exception(
            "Đăng ký này đã được xử lý trước đó."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra thành viên
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT *
        FROM ClubMember

        WHERE username = :username
          AND club_id = :club_id

        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':club_id' => $event['club_id']
    ]);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$member) {

        throw new Exception(
            "Người đăng ký không còn là thành viên CLB."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra ban
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT eb.band_id

        FROM EventBand AS eb

        WHERE eb.event_id = :event_id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':event_id' => $eventId
    ]);

    $requiredBands = $stmt->fetchAll(
        PDO::FETCH_COLUMN
    );


    /*
    |--------------------------------------------------------------------------
    | Nếu event yêu cầu ban
    |--------------------------------------------------------------------------
    */

    if (!empty($requiredBands)) {

        $placeholders = implode(
            ',',
            array_fill(
                0,
                count($requiredBands),
                '?'
            )
        );


        $sql = "
            SELECT band_id

            FROM ClubBandMember

            WHERE username = ?
              AND club_id = ?

              AND band_id IN ($placeholders)

            LIMIT 1
        ";


        $params = [
            $username,
            $event['club_id']
        ];

        foreach ($requiredBands as $bandId) {
            $params[] = $bandId;
        }


        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $hasPermission = $stmt->fetchColumn();


        if (!$hasPermission) {

            throw new Exception(
                "Thành viên không thuộc ban được yêu cầu của sự kiện."
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Đếm số người đã được duyệt
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


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra số lượng
    |--------------------------------------------------------------------------
    */

    if (
        $event['slots'] !== null
        && $approvedCount >= (int)$event['slots']
    ) {

        throw new Exception(
            "Sự kiện đã đủ số lượng người tham gia."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DUYỆT
    |--------------------------------------------------------------------------
    */

    $sql = "
        UPDATE Register_event

        SET
            register_status = 'approved',
            approved_by = :approved_by,
            approved_time = NOW(),
            reject_reason = NULL

        WHERE username = :username
          AND event_id = :event_id
          AND register_status = 'pending'
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':approved_by' => $user['username'],
        ':username' => $username,
        ':event_id' => $eventId
    ]);


    $db->commit();


    header(
        "Location: event_registrations.php?success=approved"
    );

    exit;


} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }


    header(
        "Location: event_registrations.php?error="
        . urlencode($e->getMessage())
    );

    exit;
}