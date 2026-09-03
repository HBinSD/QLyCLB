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

$pageTitle = "Đăng ký đã duyệt";
$activeMenu = "approved_events.php";

$database = new Database();
$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| TÌM KIẾM
|--------------------------------------------------------------------------
*/

$keyword = trim($_GET['keyword'] ?? '');
$eventId = trim($_GET['event_id'] ?? '');


/*
|--------------------------------------------------------------------------
| DANH SÁCH EVENT
|--------------------------------------------------------------------------
*/

$sqlEvents = "
    SELECT
        event_id,
        event_name
    FROM Event
    WHERE club_id = 'CLB001'
    ORDER BY event_date DESC
";

$stmtEvents = $db->prepare($sqlEvents);
$stmtEvents->execute();

$events = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| LẤY NGƯỜI ĐÃ ĐƯỢC DUYỆT
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        r.username,
        r.event_id,
        r.register_time,
        r.register_status,
        r.approved_by,
        r.approved_time,

        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.location,
        e.slots,
        e.club_id,

        ui.fullname,
        ui.email,
        ui.phone

    FROM Register_event AS r

    INNER JOIN Event AS e
        ON e.event_id = r.event_id

    LEFT JOIN UserInfo AS ui
        ON ui.username = r.username

    WHERE e.club_id = 'CLB001'
      AND r.register_status = 'approved'
";


$params = [];


/*
|--------------------------------------------------------------------------
| TÌM KIẾM
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

    $sql .= "
        AND (
            r.username LIKE :keyword
            OR ui.fullname LIKE :keyword
            OR ui.email LIKE :keyword
            OR e.event_name LIKE :keyword
        )
    ";

    $params[':keyword'] = '%' . $keyword . '%';
}


/*
|--------------------------------------------------------------------------
| LỌC EVENT
|--------------------------------------------------------------------------
*/

if ($eventId !== '') {

    $sql .= "
        AND r.event_id = :event_id
    ";

    $params[':event_id'] = $eventId;
}


$sql .= "
    ORDER BY
        e.event_date DESC,
        r.approved_time DESC
";


$stmt = $db->prepare($sql);
$stmt->execute($params);

$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| ĐẾM
|--------------------------------------------------------------------------
*/

$totalApproved = count($registrations);


require_once "../includes/headers.php";
?>

<link
    rel="stylesheet"
    href="css/event_registrations.css"
>


<div class="organizer-layout">

    <!-- SIDEBAR -->

    <aside class="organizer-sidebar">

        <div class="sidebar-title">
            <span>⚙️</span>
            <span>Quản lý CLB</span>
        </div>


        <a href="dashboard.php">
            🏠
            <span>Tổng quan</span>
        </a>


        <a href="events.php">
            📅
            <span>Quản lý sự kiện</span>
        </a>


        <a href="event_registrations.php">
            📝
            <span>Duyệt đăng ký</span>
        </a>


        <a
            href="approved_events.php"
            class="active"
        >
            ✅
            <span>Đã duyệt</span>
        </a>


        <a href="members.php">
            👥
            <span>Thành viên</span>
        </a>

    </aside>


    <!-- CONTENT -->

    <main class="organizer-content">


        <div class="page-header">

            <div>

                <h1>
                    Thành viên đã được duyệt
                </h1>

                <p>
                    Danh sách thành viên đã được chấp nhận tham gia sự kiện.
                </p>

            </div>

        </div>


        <!-- FILTER -->

        <div class="filter-box">

            <form method="GET">

                <div class="filter-row">


                    <div class="filter-item search-item">

                        <label>
                            Tìm kiếm
                        </label>

                        <input
                            type="text"
                            name="keyword"
                            value="<?= htmlspecialchars(
                                $keyword
                            ) ?>"
                            placeholder="Tên thành viên, username, email, sự kiện..."
                        >

                    </div>


                    <div class="filter-item">

                        <label>
                            Sự kiện
                        </label>

                        <select name="event_id">

                            <option value="">
                                Tất cả sự kiện
                            </option>

                            <?php foreach ($events as $event): ?>

                                <option
                                    value="<?= htmlspecialchars(
                                        $event['event_id']
                                    ) ?>"
                                    <?= $eventId == $event['event_id']
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $event['event_name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="filter-actions">

                        <button
                            type="submit"
                            class="btn-filter"
                        >
                            🔍 Tìm kiếm
                        </button>


                        <a
                            href="approved_events.php"
                            class="btn-reset"
                        >
                            ↻ Đặt lại
                        </a>

                    </div>

                </div>

            </form>

        </div>


        <!-- TABLE -->

        <div class="registration-container">

            <div class="table-header">

                <h2>
                    Danh sách đã duyệt
                </h2>

                <p>

                    Có
                    <strong>
                        <?= $totalApproved ?>
                    </strong>
                    thành viên được duyệt.

                </p>

            </div>


            <?php if (empty($registrations)): ?>

                <div class="empty-box">

                    <div class="empty-icon">
                        📭
                    </div>

                    <h3>
                        Không tìm thấy dữ liệu
                    </h3>

                    <p>
                        Chưa có thành viên nào được duyệt hoặc không phù hợp với bộ lọc.
                    </p>

                </div>

            <?php else: ?>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Thành viên
                                </th>

                                <th>
                                    Sự kiện
                                </th>

                                <th>
                                    Ngày tổ chức
                                </th>

                                <th>
                                    Địa điểm
                                </th>

                                <th>
                                    Được duyệt lúc
                                </th>

                                <th>
                                    Người duyệt
                                </th>

                                <th>
                                    Trạng thái
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $registrations
                                as $registration
                            ): ?>

                                <?php

                                $eventDate = !empty(
                                    $registration['event_date']
                                )
                                    ? date(
                                        'd/m/Y',
                                        strtotime(
                                            $registration['event_date']
                                        )
                                    )
                                    : 'Chưa cập nhật';

                                $approvedTime = !empty(
                                    $registration['approved_time']
                                )
                                    ? date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $registration['approved_time']
                                        )
                                    )
                                    : 'Chưa cập nhật';

                                ?>


                                <tr>


                                    <!-- MEMBER -->

                                    <td>

                                        <div class="member-info">

                                            <div class="member-avatar">

                                                <?= strtoupper(
                                                    mb_substr(
                                                        $registration['fullname']
                                                        ?? $registration['username'],
                                                        0,
                                                        1
                                                    )
                                                ) ?>

                                            </div>


                                            <div>

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        $registration['fullname']
                                                        ?? 'Chưa cập nhật'
                                                    ) ?>

                                                </strong>

                                                <small>

                                                    @<?= htmlspecialchars(
                                                        $registration['username']
                                                    ) ?>

                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- EVENT -->

                                    <td>

                                        <div class="event-name">

                                            <?= htmlspecialchars(
                                                $registration['event_name']
                                            ) ?>

                                        </div>

                                    </td>


                                    <!-- DATE -->

                                    <td>

                                        <?= $eventDate ?>

                                    </td>


                                    <!-- LOCATION -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $registration['location']
                                            ?? 'Chưa cập nhật'
                                        ) ?>

                                    </td>


                                    <!-- APPROVED TIME -->

                                    <td>

                                        <?= $approvedTime ?>

                                    </td>


                                    <!-- APPROVED BY -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $registration['approved_by']
                                            ?? '—'
                                        ) ?>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <span class="status status-approved">

                                            ✓ Đã duyệt

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </main>

</div>


<?php
require_once "../includes/footer.php";
?>