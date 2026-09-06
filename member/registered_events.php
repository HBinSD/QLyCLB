<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle  = "Sự kiện đã đăng ký";
$activeMenu = "registered_events.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $user['username'];

$database = new Database();
$db = $database->getConnection();

/*
|--------------------------------------------------------------------------
| Lấy các sự kiện mà user đã đăng ký
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        r.event_id,
        r.username,
        r.register_time,
        r.register_status,
        r.approved_by,
        r.approved_time,
        r.reject_reason,

        e.club_id,
        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.slots,
        e.location,
        e.description,
        e.status AS event_status

    FROM Register_event AS r

    INNER JOIN Event AS e
        ON e.event_id = r.event_id

    WHERE r.username = :username

    ORDER BY
        e.event_date DESC,
        e.start_time DESC
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':username' => $username
]);

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Hàm hiển thị trạng thái đăng ký
|--------------------------------------------------------------------------
*/
function getRegisterStatus($status)
{
    switch ($status) {
        case 'pending':
            return [
                'text'  => 'Chờ duyệt',
                'class' => 'status-pending'
            ];

        case 'approved':
            return [
                'text'  => 'Đã được duyệt',
                'class' => 'status-approved'
            ];

        case 'rejected':
            return [
                'text'  => 'Bị từ chối',
                'class' => 'status-rejected'
            ];

        case 'cancelled':
            return [
                'text'  => 'Đã hủy',
                'class' => 'status-cancelled'
            ];

        default:
            return [
                'text'  => ucfirst($status),
                'class' => 'status-default'
            ];
    }
}

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/registered_event.css">
<link rel="stylesheet" href="css/club.css">

<div class="club-layout">

    <!-- SIDEBAR -->
    <aside class="club-sidebar">

        <div class="club-sidebar-title">
            <span>☰</span>
            <span>QUẢN LÝ CLB</span>
        </div>

        <nav class="club-menu">

            <!-- Trang hiện tại -->
            <a href="club.php" class="club-menu-item">
                <span class="menu-icon">🏠</span>
                <span>Giới thiệu CLB</span>
            </a>

            <!-- Danh sách thành viên -->
            <a href="club_member.php" class="club-menu-item">
                <span class="menu-icon">👥</span>
                <span>Danh sách thành viên</span>
            </a>

            <!-- Sự kiện -->
            <a href="events.php" class="club-menu-item">
                <span class="menu-icon">📅</span>
                <span>Sự kiện</span>
            </a>

            <!-- Sự kiện đã đăng ký -->
            <a href="registered_events.php" class="club-menu-item active">
                <span class="menu-icon">✓</span>
                <span>Các sự kiện đã đăng ký</span>
            </a>

            <!-- Thông báo -->
            <a href="notifications.php" class="club-menu-item">
                <span class="menu-icon">🔔</span>
                <span>Thông báo CLB</span>
            </a>

        </nav>

    </aside>


    <!-- CONTENT -->
    <main class="club-content">

        <div class="page-header">

            <div>
                <h1>Sự kiện đã đăng ký</h1>
                <p>
                    Danh sách các sự kiện bạn đã đăng ký tham gia.
                </p>
            </div>

            <a href="events.php" class="btn-back">
                ← Xem tất cả sự kiện
            </a>

        </div>


        <?php if (empty($events)): ?>

            <div class="empty-box">

                <div class="empty-icon">📅</div>

                <h2>Chưa có sự kiện nào</h2>

                <p>
                    Bạn chưa đăng ký tham gia sự kiện nào của câu lạc bộ.
                </p>

                <a href="events.php" class="btn-primary">
                    Xem sự kiện
                </a>

            </div>

        <?php else: ?>

            <div class="event-list">

                <?php foreach ($events as $event): ?>

                    <?php
                    $status = getRegisterStatus(
                        $event['register_status']
                    );

                    $eventDate = !empty($event['event_date'])
                        ? date('d/m/Y', strtotime($event['event_date']))
                        : 'Chưa cập nhật';

                    $registerTime = !empty($event['register_time'])
                        ? date(
                            'd/m/Y H:i',
                            strtotime($event['register_time'])
                        )
                        : 'Chưa cập nhật';

                    $startTime = !empty($event['start_time'])
                        ? date(
                            'H:i',
                            strtotime($event['start_time'])
                        )
                        : '';

                    $endTime = !empty($event['end_time'])
                        ? date(
                            'H:i',
                            strtotime($event['end_time'])
                        )
                        : '';
                    ?>

                    <div class="event-card">

                        <div class="event-card-header">

                            <div>
                                <h2>
                                    <?= htmlspecialchars(
                                        $event['event_name']
                                    ) ?>
                                </h2>

                                <span class="event-club">
                                    CLB:
                                    <?= htmlspecialchars(
                                        $event['club_id']
                                    ) ?>
                                </span>
                            </div>

                            <span class="register-status <?= $status['class'] ?>">
                                <?= htmlspecialchars($status['text']) ?>
                            </span>

                        </div>


                        <div class="event-info">

                            <div class="info-item">

                                <span class="info-icon">📅</span>

                                <div>
                                    <small>Ngày tổ chức</small>
                                    <strong>
                                        <?= $eventDate ?>
                                    </strong>
                                </div>

                            </div>


                            <div class="info-item">

                                <span class="info-icon">⏰</span>

                                <div>
                                    <small>Thời gian</small>

                                    <strong>
                                        <?= $startTime ?>

                                        <?php if ($endTime): ?>
                                            - <?= $endTime ?>
                                        <?php endif; ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="info-item">

                                <span class="info-icon">📍</span>

                                <div>
                                    <small>Địa điểm</small>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $event['location']
                                            ?? 'Chưa cập nhật'
                                        ) ?>
                                    </strong>
                                </div>

                            </div>


                            <div class="info-item">

                                <span class="info-icon">👥</span>

                                <div>
                                    <small>Số lượng</small>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $event['slots']
                                            ?? 'Không giới hạn'
                                        ) ?>
                                    </strong>
                                </div>

                            </div>

                        </div>


                        <div class="register-info">

                            <span>
                                Đăng ký lúc:
                                <strong>
                                    <?= $registerTime ?>
                                </strong>
                            </span>

                            <?php if (
                                $event['register_status'] === 'approved'
                                && !empty($event['approved_time'])
                            ): ?>

                                <span>
                                    Duyệt lúc:
                                    <strong>
                                        <?= date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $event['approved_time']
                                            )
                                        ) ?>
                                    </strong>
                                </span>

                            <?php endif; ?>

                        </div>


                        <?php if (
                            $event['register_status'] === 'rejected'
                            && !empty($event['reject_reason'])
                        ): ?>

                            <div class="reject-box">

                                <strong>Lý do từ chối:</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $event['reject_reason']
                                    ) ?>
                                </span>

                            </div>

                        <?php endif; ?>


                        <div class="event-card-footer">

                            <a
                                href="event_detail.php?id=<?= urlencode($event['event_id']) ?>"
                                class="btn-detail"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php
require_once "../includes/footer.php";
?>