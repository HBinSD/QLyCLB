<?php
    session_start();

    require_once "../includes/auth.php";
    require_once "../database/database.php";

    $user = $_SESSION['user'] ?? [];

    if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
    }

    $pageTitle  = "Sự kiện";
    $activeMenu = "club.php";

    $database = new Database();
    $db       = $database->getConnection();

    $keyword = trim($_GET['keyword'] ?? '');

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

        COUNT(
            CASE
                WHEN r.register_status = 'approved'
                THEN 1
            END
        ) AS approved_count

    FROM Event AS e

    LEFT JOIN Register_event AS r
        ON e.event_id = r.event_id

    WHERE e.club_id = :club_id
";

    $params = [
    ':club_id' => 'CLB001',
    ];

    if ($keyword !== '') {
    $sql .= "
        AND (
            e.event_name LIKE :keyword
            OR e.location LIKE :keyword
            OR e.description LIKE :keyword
        )
    ";

    $params[':keyword'] = '%' . $keyword . '%';
    }

    $sql .= "
    GROUP BY
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
        e.status

    ORDER BY e.event_date ASC, e.start_time ASC
";

    $stmt  = $db->prepare($sql);
    $stmt->execute($params);

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/event.css">
<link rel="stylesheet" href="css/club.css">

<div class="club-layout">

    <!-- SIDEBAR CLB -->
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
            <a href="events.php" class="club-menu-item active">
                <span class="menu-icon">📅</span>
                <span>Sự kiện</span>
            </a>

            <!-- Sự kiện đã đăng ký -->
            <a href="registered_events.php" class="club-menu-item">
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
                <h1>Sự kiện</h1>
                <p>
                    Các hoạt động và sự kiện của câu lạc bộ
                </p>
            </div>

        </div>


        <!-- SEARCH -->

        <form method="GET" class="event-search">

            <input
                type="text"
                name="keyword"
                placeholder="Tìm kiếm sự kiện..."
                value="<?php echo htmlspecialchars($keyword) ?>"
            >

            <button type="submit">
                Tìm kiếm
            </button>

        </form>


        <!-- EVENT LIST -->
        <div class="event-list">
            <?php if (empty($events)): ?>
                <div class="empty-event">
                    Không tìm thấy sự kiện nào.
                </div>
            <?php else: ?>

                <?php foreach ($events as $event): ?>

                    <?php
                        $approved = (int) $event['approved_count'];
                        $slots    = (int) $event['slots'];

                        $remaining = max(0, $slots - $approved);
                    ?>

                    <div class="event-card">

                        <div class="event-date">

                            <strong>
                                <?php echo date( 'd', strtotime($event['event_date']) ) ?>
                            </strong>

                            <span>
                                <?php echo date( 'm/Y', strtotime($event['event_date']) ) ?>
                            </span>

                        </div>


                        <div class="event-info">

                            <h2>
                                <?php echo htmlspecialchars( $event['event_name'] ) ?>
                            </h2>

                            <p>
                                🕐
                                <?php echo date( 'H:i', strtotime($event['start_time']) ) ?>
                                -
                                <?php echo date( 'H:i', strtotime($event['end_time']) ) ?>
                            </p>

                            <p>
                                📍
                                <?php echo htmlspecialchars( $event['location'] ) ?>
                            </p>

                            <p> 👥 Còn <strong> <?php echo $remaining ?> </strong> / <?php echo $slots ?> chỗ </p>

                        </div>


                        <div class="event-action">

                            <?php if ($remaining <= 0): ?>

                                <span class="event-full">
                                    Đã đủ chỗ
                                </span>

                            <?php else: ?>

                                <span class="event-open">
                                    Còn chỗ
                                </span>

                            <?php endif; ?>


                            <a
                                href="event_detail.php?id=<?php echo (int)$event['event_id'] ?>"
                                class="btn-detail"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </main>

</div>

<?php require_once "../includes/footer.php"; ?>