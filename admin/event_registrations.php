<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

/*
|--------------------------------------------------------------------------
| Kiểm tra quyền
|--------------------------------------------------------------------------
*/

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

if (
    ($user['role'] ?? '') !== 'organizer'
    && ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo'Bạn không có quyền truy cập';
    exit;
}

$pageTitle = "Duyệt đăng ký sự kiện";
$activeMenu = "event_registrations.php";

$database = new Database();
$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| Lấy danh sách đăng ký
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
        r.reject_reason,

        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.location,
        e.slots,
        e.club_id,

        ui.fullname,
        ui.email,
        ui.avt_links,
        ui.phone

    FROM Register_event AS r

    INNER JOIN Event AS e
        ON e.event_id = r.event_id

    LEFT JOIN UserInfo AS ui
        ON ui.username = r.username

    WHERE e.club_id = 'CLB001'

    ORDER BY
        CASE
            WHEN r.register_status = 'pending' THEN 0
            WHEN r.register_status = 'approved' THEN 1
            WHEN r.register_status = 'rejected' THEN 2
            WHEN r.register_status = 'cancelled' THEN 3
            ELSE 4
        END,
        r.register_time DESC
";

$stmt = $db->prepare($sql);
$stmt->execute();

$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Thống kê
|--------------------------------------------------------------------------
*/

$total = count($registrations);

$pending = 0;
$approved = 0;
$rejected = 0;

foreach ($registrations as $registration) {

    switch ($registration['register_status']) {

        case 'pending':
            $pending++;
            break;

        case 'approved':
            $approved++;
            break;

        case 'rejected':
            $rejected++;
            break;
    }
}


/*
|--------------------------------------------------------------------------
| Hàm trạng thái
|--------------------------------------------------------------------------
*/

function getStatusText($status)
{
    switch ($status) {

        case 'pending':
            return 'Chờ duyệt';

        case 'approved':
            return 'Đã duyệt';

        case 'rejected':
            return 'Từ chối';

        case 'cancelled':
            return 'Đã hủy';

        default:
            return $status;
    }
}

function getStatusClass($status)
{
    switch ($status) {

        case 'pending':
            return 'status-pending';

        case 'approved':
            return 'status-approved';

        case 'rejected':
            return 'status-rejected';

        case 'cancelled':
            return 'status-cancelled';

        default:
            return 'status-default';
    }
}

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/event_registrations.css">


<div class="organizer-layout">


    <!-- CONTENT -->

    <main class="organizer-content">

        <div class="page-header">

            <div>

                <h1>Duyệt đăng ký sự kiện</h1>

                <p>
                    Quản lý và xét duyệt thành viên đăng ký tham gia sự kiện.
                </p>

            </div>

        </div>


        <!-- THỐNG KÊ -->

        <div class="statistics">

            <div class="stat-card">

                <div class="stat-icon">
                    📋
                </div>

                <div>

                    <span>Tổng đăng ký</span>

                    <strong>
                        <?= $total ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card pending">

                <div class="stat-icon">
                    ⏳
                </div>

                <div>

                    <span>Chờ duyệt</span>

                    <strong>
                        <?= $pending ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card approved">

                <div class="stat-icon">
                    ✅
                </div>

                <div>

                    <span>Đã duyệt</span>

                    <strong>
                        <?= $approved ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card rejected">

                <div class="stat-icon">
                    ❌
                </div>

                <div>

                    <span>Từ chối</span>

                    <strong>
                        <?= $rejected ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- DANH SÁCH -->

        <div class="registration-container">

            <div class="table-header">

                <div>

                    <h2>Danh sách đăng ký</h2>

                    <p>
                        Các yêu cầu đăng ký tham gia sự kiện của CLB.
                    </p>

                </div>

            </div>


            <?php if (empty($registrations)): ?>

                <div class="empty-box">

                    <div class="empty-icon">
                        📭
                    </div>

                    <h3>
                        Chưa có đăng ký nào
                    </h3>

                    <p>
                        Hiện tại chưa có thành viên đăng ký sự kiện.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>Thành viên</th>

                                <th>Sự kiện</th>

                                <th>Ngày tổ chức</th>

                                <th>Địa điểm</th>

                                <th>Đăng ký lúc</th>

                                <th>Trạng thái</th>

                                <th>Thao tác</th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($registrations as $registration): ?>

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

                                $registerTime = !empty(
                                    $registration['register_time']
                                )
                                    ? date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $registration['register_time']
                                        )
                                    )
                                    : 'Chưa cập nhật';

                                ?>

                                <tr>

                                    <!-- MEMBER -->

                                    <td>

                                        <div class="member-info">

                                            <div class="member-avatar">

                                            <?php 
                                            $fullname = $registration['username'];
                                            $avt = $registration['avt_links'];
                                            
                                            if (!empty($avt)): ?>
                                                    <img src="<?php echo htmlspecialchars($avt); ?>"
                                                        alt="Ảnh đại diện"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <?php else: ?>
                                                    <div class="avatar-default">
                                                        👤
                                                    </div>
                                                <?php endif; ?> 

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

                                        <span class="date-text">

                                            <?= $eventDate ?>

                                        </span>

                                    </td>


                                    <!-- LOCATION -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $registration['location']
                                            ?? 'Chưa cập nhật'
                                        ) ?>

                                    </td>


                                    <!-- REGISTER TIME -->

                                    <td>

                                        <?= $registerTime ?>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <span
                                            class="status <?= getStatusClass(
                                                $registration['register_status']
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars(
                                                getStatusText(
                                                    $registration['register_status']
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- ACTION -->

                                    <td>

                                        <?php if (
                                            $registration['register_status']
                                            === 'pending'
                                        ): ?>

                                            <div class="action-buttons">

                                                <form
                                                    action="approve_event.php"
                                                    method="POST"
                                                    onsubmit="
                                                        return confirm(
                                                            'Bạn có chắc muốn duyệt đăng ký này?'
                                                        );
                                                    "
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="event_id"
                                                        value="<?= htmlspecialchars(
                                                            $registration['event_id']
                                                        ) ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="username"
                                                        value="<?= htmlspecialchars(
                                                            $registration['username']
                                                        ) ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn-approve"
                                                    >
                                                        ✓ Duyệt
                                                    </button>

                                                </form>


                                                <a
                                                    href="reject_event.php?event_id=<?= urlencode(
                                                        $registration['event_id']
                                                    ) ?>&username=<?= urlencode(
                                                        $registration['username']
                                                    ) ?>"
                                                    class="btn-reject"
                                                >
                                                    ✕ Từ chối
                                                </a>

                                            </div>

                                        <?php elseif (
                                            $registration['register_status']
                                            === 'approved'
                                        ): ?>

                                            <span class="processed approved-text">
                                                ✓ Đã duyệt
                                            </span>

                                        <?php elseif (
                                            $registration['register_status']
                                            === 'rejected'
                                        ): ?>

                                            <span class="processed rejected-text">
                                                ✕ Đã từ chối
                                            </span>

                                        <?php else: ?>

                                            <span class="processed">
                                                —
                                            </span>

                                        <?php endif; ?>

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