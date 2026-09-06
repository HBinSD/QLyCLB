<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (($user['role'] ?? '') !== 'organizer' && ($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo'Bạn không có quyền truy cập';
    exit;
}

$pageTitle = "Quản lý sự kiện";
$activeMenu = "events.php";

require_once "../includes/headers.php";

$clubId = "CLB001";

$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "
    SELECT
        e.event_id,
        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.slots,
        e.location,
        e.description,
        e.organizer_id,
        e.status,
        ui.fullname AS organizer_name,

        (
            SELECT COUNT(*)
            FROM Register_event r
            WHERE r.event_id = e.event_id
              AND r.register_status = 'approved'
        ) AS registered_count

    FROM Event e

    LEFT JOIN UserInfo ui
        ON ui.username = e.organizer_id

    WHERE e.club_id = :club_id
";

$params = [
    ':club_id' => $clubId
];

if ($keyword !== '') {
    $sql .= "
        AND (
            e.event_name LIKE :keyword
            OR e.location LIKE :keyword
            OR e.description LIKE :keyword
        )
    ";

    $params[':keyword'] = "%{$keyword}%";
}

if ($status !== '') {
    $sql .= " AND e.status = :status ";
    $params[':status'] = $status;
}

$sql .= "
    ORDER BY e.event_date DESC, e.start_time DESC
";

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare($sql);
$stmt->execute($params);

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="css/events.css">

<div class="event-page">

    <div class="page-header">
        <div>
            <h1>Quản lý sự kiện</h1>
            <p>Quản lý các sự kiện của câu lạc bộ.</p>
        </div>

        <a href="create_event.php" class="btn btn-primary">
            + Tạo sự kiện
        </a>
    </div>


    <!-- Tìm kiếm và lọc -->

    <form method="GET" class="filter-box">

        <div class="search-box">
            <input
                type="text"
                name="keyword"
                placeholder="Tìm kiếm sự kiện..."
                value="<?= htmlspecialchars($keyword) ?>"
            >
        </div>

        <select name="status">
            <option value="">Tất cả trạng thái</option>

            <option value="upcoming"
                <?= $status === 'upcoming' ? 'selected' : '' ?>>
                Sắp diễn ra
            </option>

            <option value="ongoing"
                <?= $status === 'ongoing' ? 'selected' : '' ?>>
                Đang diễn ra
            </option>

            <option value="completed"
                <?= $status === 'completed' ? 'selected' : '' ?>>
                Đã kết thúc
            </option>

            <option value="cancelled"
                <?= $status === 'cancelled' ? 'selected' : '' ?>>
                Đã hủy
            </option>
        </select>

        <button type="submit" class="btn btn-filter">
            Lọc
        </button>

        <a href="events.php" class="btn btn-reset">
            Đặt lại
        </a>

    </form>


    <!-- Danh sách sự kiện -->

    <div class="event-list">

        <?php if (empty($events)): ?>

            <div class="empty-state">
                <h3>Không tìm thấy sự kiện</h3>
                <p>Hiện tại chưa có sự kiện phù hợp.</p>
            </div>

        <?php else: ?>

            <?php foreach ($events as $event): ?>

                <?php
                $registered = (int)$event['registered_count'];
                $slots = (int)$event['slots'];
                $remaining = max(0, $slots - $registered);

                $eventStatus = $event['status'];

                $statusText = match ($eventStatus) {
                    'upcoming' => 'Sắp diễn ra',
                    'ongoing' => 'Đang diễn ra',
                    'completed' => 'Đã kết thúc',
                    'cancelled' => 'Đã hủy',
                    default => $eventStatus
                };
                ?>

                <div class="event-card">

                    <div class="event-card-header">

                        <div>
                            <h2>
                                <?= htmlspecialchars($event['event_name']) ?>
                            </h2>

                            <span class="event-status status-<?= htmlspecialchars($eventStatus) ?>">
                                <?= htmlspecialchars($statusText) ?>
                            </span>
                        </div>

                    </div>


                    <div class="event-info">

                        <div class="info-item">
                            <strong>📅 Ngày</strong>
                            <span>
                                <?= date('d/m/Y', strtotime($event['event_date'])) ?>
                            </span>
                        </div>

                        <div class="info-item">
                            <strong>⏰ Thời gian</strong>
                            <span>
                                <?= htmlspecialchars($event['start_time']) ?>
                                -
                                <?= htmlspecialchars($event['end_time']) ?>
                            </span>
                        </div>

                        <div class="info-item">
                            <strong>📍 Địa điểm</strong>
                            <span>
                                <?= htmlspecialchars($event['location']) ?>
                            </span>
                        </div>

                        <div class="info-item">
                            <strong>👥 Đăng ký</strong>
                            <span>
                                <?= $registered ?> / <?= $slots ?>
                            </span>
                        </div>

                    </div>


                    <?php if (!empty($event['description'])): ?>

                        <p class="event-description">
                            <?= htmlspecialchars($event['description']) ?>
                        </p>

                    <?php endif; ?>


                    <div class="event-card-footer">

                        <span class="organizer">
                            Người tổ chức:
                            <?= htmlspecialchars($event['organizer_name'] ?? $event['organizer_id']) ?>
                        </span>


                        <div class="event-actions">

                            <a
                                href="event_detail.php?id=<?= $event['event_id'] ?>"
                                class="btn btn-view"
                            >
                                Xem chi tiết
                            </a>

                            <?php if ($eventStatus === 'upcoming'): ?>

                                <a
                                    href="edit_event.php?id=<?= $event['event_id'] ?>"
                                    class="btn btn-edit"
                                >
                                    Chỉnh sửa
                                </a>

                            <?php endif; ?>


                            <a
                                href="event_registrations.php?event_id=<?= $event['event_id'] ?>"
                                class="btn btn-register"
                            >
                                Đăng ký
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<?php require_once "../includes/footer.php"; ?>
</body>