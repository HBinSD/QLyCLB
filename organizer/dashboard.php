<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];


// ==========================
// KIỂM TRA QUYỀN
// ==========================

if (
    ($user['role'] ?? '') !== 'organizer' &&
    ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}


// ==========================
// KẾT NỐI DATABASE
// ==========================

$database = new Database();
$db = $database->getConnection();


// ==========================
// CẤU HÌNH CLB
// ==========================

$clubId = "CLB001";


// ==========================
// LẤY THÔNG TIN ORGANIZER
// ==========================

$sql = "
    SELECT
        u.username,
        u.role,
        ui.fullname,
        ui.email,
        ui.phone,
        ui.avt_links
    FROM User u
    LEFT JOIN UserInfo ui
        ON ui.username = u.username
    WHERE u.username = :username
    LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':username' => $user['username']
]);

$organizer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$organizer) {
    $organizer = [
        'username' => $user['username'],
        'role' => $user['role'],
        'fullname' => $user['username'],
        'email' => '',
        'phone' => '',
        'avt_link' => ''
    ];
}


// ==========================
// THỐNG KÊ THÀNH VIÊN
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM ClubMember
    WHERE club_id = :club_id
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$totalMembers = (int)$stmt->fetchColumn();


// ==========================
// TỔNG SỐ SỰ KIỆN
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$totalEvents = (int)$stmt->fetchColumn();


// ==========================
// SỰ KIỆN ĐANG DIỄN RA
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
      AND status = 'ongoing'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$ongoingEvents = (int)$stmt->fetchColumn();


// ==========================
// SỰ KIỆN SẮP DIỄN RA
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
      AND status = 'upcoming'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$upcomingEvents = (int)$stmt->fetchColumn();


// ==========================
// SỰ KIỆN ĐÃ HOÀN THÀNH
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
      AND status = 'completed'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$completedEvents = (int)$stmt->fetchColumn();


// ==========================
// ĐƠN ĐĂNG KÝ ĐANG CHỜ DUYỆT
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Register_event r
    INNER JOIN Event e
        ON e.event_id = r.event_id
    WHERE e.club_id = :club_id
      AND r.register_status = 'pending'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$pendingRegistrations = (int)$stmt->fetchColumn();


// ==========================
// TỔNG LƯỢT ĐĂNG KÝ ĐÃ DUYỆT
// ==========================

$sql = "
    SELECT COUNT(*)
    FROM Register_event r
    INNER JOIN Event e
        ON e.event_id = r.event_id
    WHERE e.club_id = :club_id
      AND r.register_status = 'approved'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$approvedRegistrations = (int)$stmt->fetchColumn();


// ==========================
// 5 SỰ KIỆN SẮP DIỄN RA
// ==========================

$sql = "
    SELECT
        e.event_id,
        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.location,
        e.slots,
        e.status,

        (
            SELECT COUNT(*)
            FROM Register_event r
            WHERE r.event_id = e.event_id
              AND r.register_status = 'approved'
        ) AS registered_count

    FROM Event e

    WHERE e.club_id = :club_id
      AND e.status IN ('upcoming', 'ongoing')

    ORDER BY
        e.event_date ASC,
        e.start_time ASC

    LIMIT 5
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$upcomingEventList = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================
// 5 ĐƠN ĐĂNG KÝ MỚI NHẤT
// ==========================

$sql = "
    SELECT
        r.username,
        r.event_id,
        r.register_time,
        r.register_status,

        ui.fullname,
        ui.avt_links,

        e.event_name,
        e.event_date

    FROM Register_event r

    INNER JOIN Event e
        ON e.event_id = r.event_id

    LEFT JOIN UserInfo ui
        ON ui.username = r.username

    WHERE e.club_id = :club_id
      AND r.register_status = 'pending'

    ORDER BY r.register_time DESC

    LIMIT 5
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$pendingList = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================
// HEADER + SIDEBAR
// ==========================

require_once "../includes/headers.php";

?>

<link rel="stylesheet" href="css/dashboard.css">


<div class="organizer-dashboard">

    <!-- =========================
         HEADER DASHBOARD
    ========================== -->

    <div class="dashboard-header">

        <div>
            <h1>
                Xin chào,
                <?= htmlspecialchars($organizer['fullname'] ?: $organizer['username']) ?>
                👋
            </h1>

            <p>
                Chào mừng bạn đến với bảng điều khiển Organizer.
                Hãy kiểm tra hoạt động của câu lạc bộ hôm nay.
            </p>
        </div>

        <a href="create_event.php" class="btn-primary">
            + Tạo sự kiện
        </a>

    </div>


    <!-- =========================
         THỐNG KÊ
    ========================= -->

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-icon blue">
                📅
            </div>

            <div class="stat-content">
                <span>Tổng sự kiện</span>
                <strong><?= $totalEvents ?></strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon green">
                ▶
            </div>

            <div class="stat-content">
                <span>Đang diễn ra</span>
                <strong><?= $ongoingEvents ?></strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon orange">
                🕒
            </div>

            <div class="stat-content">
                <span>Sắp diễn ra</span>
                <strong><?= $upcomingEvents ?></strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon purple">
                ✓
            </div>

            <div class="stat-content">
                <span>Đã hoàn thành</span>
                <strong><?= $completedEvents ?></strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon red">
                👥
            </div>

            <div class="stat-content">
                <span>Thành viên CLB</span>
                <strong><?= $totalMembers ?></strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon yellow">
                📝
            </div>

            <div class="stat-content">
                <span>Chờ duyệt</span>
                <strong><?= $pendingRegistrations ?></strong>
            </div>

        </div>

    </div>


    <!-- =========================
         NỘI DUNG CHÍNH
    ========================= -->

    <div class="dashboard-columns">


        <!-- =====================
             SỰ KIỆN
        ====================== -->

        <div class="dashboard-card">

            <div class="card-header">

                <div>
                    <h2>Sự kiện sắp tới</h2>
                    <p>Những hoạt động gần nhất của CLB</p>
                </div>

                <a href="events.php">
                    Xem tất cả →
                </a>

            </div>


            <?php if (empty($upcomingEventList)): ?>

                <div class="empty-state">
                    <div>📅</div>
                    <p>Chưa có sự kiện sắp diễn ra.</p>

                    <a href="create_event.php" class="btn-small">
                        Tạo sự kiện
                    </a>
                </div>

            <?php else: ?>

                <div class="event-list">

                    <?php foreach ($upcomingEventList as $event): ?>

                        <?php

                        $date = date(
                            'd/m/Y',
                            strtotime($event['event_date'])
                        );

                        $registered = (int)$event['registered_count'];
                        $slots = (int)$event['slots'];

                        $remaining = max(
                            0,
                            $slots - $registered
                        );

                        ?>

                        <div class="event-item">

                            <div class="event-date">

                                <strong>
                                    <?= date('d', strtotime($event['event_date'])) ?>
                                </strong>

                                <span>
                                    <?= date('M', strtotime($event['event_date'])) ?>
                                </span>

                            </div>


                            <div class="event-info">

                                <a
                                    href="event_detail.php?id=<?= (int)$event['event_id'] ?>"
                                    class="event-name"
                                >
                                    <?= htmlspecialchars($event['event_name']) ?>
                                </a>

                                <div class="event-meta">

                                    <span>
                                        📅 <?= $date ?>
                                    </span>

                                    <span>
                                        🕒
                                        <?= htmlspecialchars(substr($event['start_time'], 0, 5)) ?>
                                        -
                                        <?= htmlspecialchars(substr($event['end_time'], 0, 5)) ?>
                                    </span>

                                    <span>
                                        📍 <?= htmlspecialchars($event['location']) ?>
                                    </span>

                                </div>

                                <div class="event-progress">

                                    <div class="progress-text">

                                        <span>
                                            <?= $registered ?>/<?= $slots ?> người đăng ký
                                        </span>

                                        <span>
                                            Còn <?= $remaining ?> chỗ
                                        </span>

                                    </div>

                                    <div class="progress-bar">

                                        <div
                                            class="progress-fill"
                                            style="width: <?= $slots > 0 ? min(100, ($registered / $slots) * 100) : 0 ?>%;"
                                        ></div>

                                    </div>

                                </div>

                            </div>


                            <div class="event-status">

                                <?php if ($event['status'] === 'ongoing'): ?>

                                    <span class="status ongoing">
                                        Đang diễn ra
                                    </span>

                                <?php else: ?>

                                    <span class="status upcoming">
                                        Sắp diễn ra
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>



        <!-- =====================
             ĐĂNG KÝ CHỜ DUYỆT
        ====================== -->

        <div class="dashboard-card">

            <div class="card-header">

                <div>
                    <h2>Đăng ký chờ duyệt</h2>
                    <p>Các yêu cầu mới nhất</p>
                </div>

                <a href="event_registrations.php">
                    Xem tất cả →
                </a>

            </div>


            <?php if (empty($pendingList)): ?>

                <div class="empty-state">

                    <div>✓</div>

                    <p>
                        Không có đơn đăng ký nào đang chờ duyệt.
                    </p>

                </div>

            <?php else: ?>

                <div class="registration-list">

                    <?php foreach ($pendingList as $registration): ?>

                        <div class="registration-item">

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


                            <div class="registration-info">

                                <strong>
                                    <?= htmlspecialchars($fullname) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $registration['event_name']
                                    ) ?>
                                </span>

                                <small>
                                    Đăng ký:
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($registration['register_time'])
                                    ) ?>
                                </small>

                            </div>


                            <a
                                href="event_registrations.php"
                                class="review-btn"
                            >
                                Xử lý
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>



    <!-- =========================
         QUICK ACTIONS
    ========================= -->

    <div class="dashboard-card quick-actions-card">

        <div class="card-header">

            <div>
                <h2>Thao tác nhanh</h2>
                <p>Quản lý câu lạc bộ</p>
            </div>

        </div>


        <div class="quick-actions">

            <a href="create_event.php" class="quick-action">

                <div class="quick-icon">
                    ➕
                </div>

                <div>
                    <strong>Tạo sự kiện</strong>
                    <span>Tạo hoạt động mới cho CLB</span>
                </div>

            </a>


            <a href="events.php" class="quick-action">

                <div class="quick-icon">
                    📅
                </div>

                <div>
                    <strong>Quản lý sự kiện</strong>
                    <span>Xem và chỉnh sửa sự kiện</span>
                </div>

            </a>


            <a
                href="event_registrations.php"
                class="quick-action"
            >

                <div class="quick-icon">
                    📝
                </div>

                <div>
                    <strong>Duyệt đăng ký</strong>

                    <span>
                        <?= $pendingRegistrations ?> yêu cầu đang chờ
                    </span>
                </div>

            </a>


            <a href="approved_events.php" class="quick-action">

                <div class="quick-icon">
                    ✅
                </div>

                <div>
                    <strong>Đăng ký đã duyệt</strong>

                    <span>
                        <?= $approvedRegistrations ?> lượt đã duyệt
                    </span>
                </div>

            </a>


            <a href="../members.php" class="quick-action">

                <div class="quick-icon">
                    👥
                </div>

                <div>
                    <strong>Thành viên</strong>

                    <span>
                        <?= $totalMembers ?> thành viên
                    </span>
                </div>

            </a>

        </div>

    </div>

</div>

<?php
    require_once "../includes/footer.php";
?>
</body>