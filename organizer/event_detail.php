<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (
    ($user['role'] ?? '') !== 'organizer' &&
    ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}

$eventId = (int)($_GET['id'] ?? 0);

if ($eventId <= 0) {
    header("Location: events.php");
    exit;
}

$clubId = "CLB001";

$database = new Database();
$db = $database->getConnection();
/*
|--------------------------------------------------------------------------
| Lấy thông tin sự kiện
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        e.event_id,
        e.club_id,
        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.slots,
        e.location,
        e.description,
        e.organizer_id,
        e.status,

        ui.fullname AS organizer_name

    FROM Event AS e

    LEFT JOIN UserInfo AS ui
        ON ui.username = e.organizer_id

    WHERE e.event_id = :event_id
      AND e.club_id = :club_id

    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':event_id' => $eventId,
    ':club_id' => $clubId
]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit;
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

$slots = (int)$event['slots'];

$remainingSlots = max(
    0,
    $slots - $approvedCount
);

/*
|--------------------------------------------------------------------------
| Các ban được phép tham gia
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        cb.band_id,
        cb.band_name

    FROM EventBand AS eb

    INNER JOIN ClubBand AS cb
        ON cb.band_id = eb.band_id

    WHERE eb.event_id = :event_id

    ORDER BY cb.band_name
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':event_id' => $eventId
]);

$eventBands = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Danh sách người đăng ký
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        r.username,
        r.register_time,
        r.register_status,
        r.approved_time,

        ui.fullname,
        ui.email,
        ui.phone

    FROM Register_event AS r

    LEFT JOIN UserInfo AS ui
        ON ui.username = r.username

    WHERE r.event_id = :event_id

    ORDER BY
        CASE
            WHEN r.register_status = 'pending' THEN 1
            WHEN r.register_status = 'approved' THEN 2
            ELSE 3
        END,
        r.register_time DESC
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':event_id' => $eventId
]);

$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Trạng thái
|--------------------------------------------------------------------------
*/

$statusText = match ($event['status']) {

    'upcoming' => 'Sắp diễn ra',

    'ongoing' => 'Đang diễn ra',

    'completed' => 'Đã kết thúc',

    'cancelled' => 'Đã hủy',

    default => $event['status']

};


$pageTitle = "Chi tiết sự kiện";
$activeMenu = "events.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/event_detail.css">


<div class="event-detail-page">

    <!-- HEADER -->

    <div class="event-detail-header">

        <div>

            <a href="events.php" class="back-link">
                ← Quay lại danh sách sự kiện
            </a>

            <h1>
                <?= htmlspecialchars($event['event_name']) ?>
            </h1>

            <span class="event-status status-<?= htmlspecialchars($event['status']) ?>">
                <?= htmlspecialchars($statusText) ?>
            </span>

        </div>


        <div class="header-actions">

            <?php if ($event['status'] !== 'completed'): ?>

                <a
                    href="edit_event.php?id=<?= $eventId ?>"
                    class="btn btn-edit"
                >
                    ✏ Chỉnh sửa
                </a>

            <?php endif; ?>


            <a
                href="event_registrations.php?event_id=<?= $eventId ?>"
                class="btn btn-primary"
            >
                👥 Quản lý đăng ký
            </a>

        </div>

    </div>


    <!-- THÔNG TIN CHÍNH -->

    <div class="event-detail-grid">

        <!-- THÔNG TIN SỰ KIỆN -->

        <div class="detail-card">

            <h2>Thông tin sự kiện</h2>

            <div class="detail-list">

                <div class="detail-item">

                    <span class="detail-label">
                        📅 Ngày tổ chức
                    </span>

                    <span class="detail-value">
                        <?= date(
                            'd/m/Y',
                            strtotime($event['event_date'])
                        ) ?>
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        ⏰ Thời gian
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars($event['start_time']) ?>

                        -

                        <?= htmlspecialchars($event['end_time']) ?>

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        📍 Địa điểm
                    </span>

                    <span class="detail-value">
                        <?= htmlspecialchars($event['location']) ?>
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        👤 Người tổ chức
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars(
                            $event['organizer_name']
                            ?? $event['organizer_id']
                        ) ?>

                    </span>

                </div>

            </div>

        </div>


        <!-- THỐNG KÊ -->

        <div class="detail-card">

            <h2>Thống kê đăng ký</h2>

            <div class="statistics">

                <div class="stat-item">

                    <span class="stat-number">
                        <?= $slots ?>
                    </span>

                    <span class="stat-label">
                        Tổng số chỗ
                    </span>

                </div>


                <div class="stat-item">

                    <span class="stat-number">
                        <?= $approvedCount ?>
                    </span>

                    <span class="stat-label">
                        Đã duyệt
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        <?= $remainingSlots ?>
                    </span>
                    <span class="stat-label">
                        Còn trống
                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- MÔ TẢ -->

    <div class="detail-card">

        <h2>Giới thiệu / Mô tả</h2>

        <?php if (!empty($event['description'])): ?>

            <div class="description">
                <?= nl2br(
                    htmlspecialchars($event['description'])
                ) ?>
            </div>

        <?php else: ?>

            <p class="empty-text">
                Sự kiện chưa có mô tả.
            </p>

        <?php endif; ?>

    </div>


    <!-- BAN THAM GIA -->

    <div class="detail-card">

        <h2>Đối tượng / Ban được tham gia</h2>

        <?php if (empty($eventBands)): ?>

            <p class="all-members">
                Tất cả thành viên trong câu lạc bộ
            </p>

        <?php else: ?>

            <div class="band-list">

                <?php foreach ($eventBands as $band): ?>

                    <span class="band-tag">
                        <?= htmlspecialchars($band['band_name']) ?>
                    </span>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>


    <!-- DANH SÁCH ĐĂNG KÝ -->

    <div class="detail-card">

        <div class="card-header">

            <div>

                <h2>Danh sách đăng ký</h2>

                <p>
                    Tổng số:
                    <?= count($registrations) ?>
                    người
                </p>

            </div>


            <a
                href="event_registrations.php?event_id=<?= $eventId ?>"
                class="view-all"
            >
                Xem quản lý đăng ký →
            </a>

        </div>


        <?php if (empty($registrations)): ?>

            <p class="empty-text">
                Chưa có thành viên đăng ký sự kiện này.
            </p>

        <?php else: ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Thành viên</th>

                            <th>Email</th>

                            <th>Số điện thoại</th>

                            <th>Thời gian đăng ký</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($registrations as $registration): ?>

                            <?php

                            $registrationStatus = match (
                                $registration['register_status']
                            ) {

                                'pending' => 'Chờ duyệt',

                                'approved' => 'Đã duyệt',

                                'rejected' => 'Từ chối',

                                default =>
                                    $registration['register_status']

                            };

                            ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $registration['fullname']
                                            ?? $registration['username']
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= htmlspecialchars(
                                            $registration['username']
                                        ) ?>
                                    </small>

                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $registration['email']
                                        ?? ''
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $registration['phone']
                                        ?? ''
                                    ) ?>
                                </td>


                                <td>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $registration['register_time']
                                        )
                                    ) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once '../includes/footer.php' ?>
</body>