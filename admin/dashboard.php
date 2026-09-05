<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

// ===============================
// KIỂM TRA QUYỀN ADMIN
// ===============================
$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// ===============================
// KẾT NỐI DATABASE
// ===============================
$database = new Database();
$db = $database->getConnection();

$clubId = "CLB001";

// ===============================
// THÔNG TIN ADMIN
// ===============================
$adminName = $user['fullname'] ?? $user['username'] ?? 'Admin';

// Nếu session chưa có fullname thì lấy từ DB
if (empty($user['fullname'])) {
    $stmt = $db->prepare("
        SELECT fullname
        FROM UserInfo
        WHERE username = :username
        LIMIT 1
    ");

    $stmt->execute([
        ':username' => $user['username']
    ]);

    $adminInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adminInfo) {
        $adminName = $adminInfo['fullname'];
    }
}

// ===============================
// THỐNG KÊ THÀNH VIÊN
// ===============================

// Tổng thành viên
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM ClubMember
    WHERE club_id = :club_id
");

$stmt->execute([
    ':club_id' => $clubId
]);

$totalMembers = (int)$stmt->fetchColumn();


// Đơn chờ duyệt
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM ClubApplication
    WHERE club_id = :club_id
      AND status = 'pending'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$pendingMembers = (int)$stmt->fetchColumn();


// Đơn đã duyệt
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM ClubApplication
    WHERE club_id = :club_id
      AND status = 'approved'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$approvedApplications = (int)$stmt->fetchColumn();


// Đơn bị từ chối
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM ClubApplication
    WHERE club_id = :clubId
      AND status = 'rejected'
");

$stmt->execute([
    ':clubId' => $clubId
]);

$rejectedApplications = (int)$stmt->fetchColumn();


// ===============================
// THỐNG KÊ SỰ KIỆN
// ===============================

// Tổng số sự kiện
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
");

$stmt->execute([
    ':club_id' => $clubId
]);

$totalEvents = (int)$stmt->fetchColumn();


// Sự kiện sắp diễn ra
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
      AND status = 'upcoming'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$upcomingEvents = (int)$stmt->fetchColumn();


// Sự kiện đang diễn ra
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Event
    WHERE club_id = :club_id
      AND status = 'ongoing'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$ongoingEvents = (int)$stmt->fetchColumn();


// ===============================
// ĐĂNG KÝ SỰ KIỆN
// ===============================

// Đăng ký sự kiện đang chờ
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Register_event r
    INNER JOIN Event e
        ON e.event_id = r.event_id
    WHERE e.club_id = :club_id
      AND r.register_status = 'pending'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$pendingEventRegistrations = (int)$stmt->fetchColumn();


// Đăng ký sự kiện đã duyệt
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Register_event r
    INNER JOIN Event e
        ON e.event_id = r.event_id
    WHERE e.club_id = :club_id
      AND r.register_status = 'approved'
");

$stmt->execute([
    ':club_id' => $clubId
]);

$approvedEventRegistrations = (int)$stmt->fetchColumn();


// ===============================
// SỰ KIỆN GẦN ĐÂY
// ===============================
$stmt = $db->prepare("
    SELECT
        e.event_id,
        e.event_name,
        e.event_date,
        e.start_time,
        e.end_time,
        e.location,
        e.status,
        e.slots,

        (
            SELECT COUNT(*)
            FROM Register_event r
            WHERE r.event_id = e.event_id
              AND r.register_status = 'approved'
        ) AS registered_count

    FROM Event e

    WHERE e.club_id = :club_id

    ORDER BY
        e.event_date DESC,
        e.start_time DESC

    LIMIT 5
");

$stmt->execute([
    ':club_id' => $clubId
]);

$recentEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ===============================
// ĐƠN THAM GIA MỚI NHẤT
// ===============================
$stmt = $db->prepare("
    SELECT
        ca.application_id,
        ca.username,
        ca.status,
        ca.created_at,
        ui.fullname,
        ui.email,
        cb.band_name

    FROM ClubApplication ca

    LEFT JOIN UserInfo ui
        ON ui.username = ca.username

    LEFT JOIN ClubBand cb
        ON cb.band_id = ca.desired_band

    WHERE ca.club_id = :club_id

    ORDER BY ca.created_at DESC

    LIMIT 5
");

$stmt->execute([
    ':club_id' => $clubId
]);

$recentApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ===============================
// HEADER + SIDEBAR
// ===============================
$pageTitle = "Dashboard Admin";
$activeMenu = "dashboard.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/dashboard.css">

<div class="admin-dashboard">

    <!-- ========================= -->
    <!-- HEADER DASHBOARD -->
    <!-- ========================= -->

    <div class="dashboard-header">

        <div>
            <h1>Dashboard quản trị</h1>
        </div>

        <div class="dashboard-date">
            <?= date("d/m/Y") ?>
        </div>

    </div>


    <!-- ========================= -->
    <!-- THỐNG KÊ THÀNH VIÊN -->
    <!-- ========================= -->

    <h2 class="section-title">
        Tổng quan thành viên
    </h2>

    <div class="stat-grid">

        <div class="stat-card">

            <div class="stat-icon">
                👥
            </div>

            <div class="stat-content">
                <span>Tổng thành viên</span>

                <strong>
                    <?= $totalMembers ?>
                </strong>
            </div>

        </div>


        <div class="stat-card pending">

            <div class="stat-icon">
                ⏳
            </div>

            <div class="stat-content">
                <span>Chờ duyệt</span>

                <strong>
                    <?= $pendingMembers ?>
                </strong>
            </div>

        </div>


        <div class="stat-card approved">

            <div class="stat-icon">
                ✓
            </div>

            <div class="stat-content">
                <span>Đã duyệt</span>

                <strong>
                    <?= $approvedApplications ?>
                </strong>
            </div>

        </div>


        <div class="stat-card rejected">

            <div class="stat-icon">
                ×
            </div>

            <div class="stat-content">
                <span>Từ chối</span>

                <strong>
                    <?= $rejectedApplications ?>
                </strong>
            </div>

        </div>

    </div>


    <!-- ========================= -->
    <!-- THỐNG KÊ SỰ KIỆN -->
    <!-- ========================= -->

    <h2 class="section-title">
        Tổng quan sự kiện
    </h2>

    <div class="stat-grid">

        <div class="stat-card event">

            <div class="stat-icon">
                📅
            </div>

            <div class="stat-content">
                <span>Tổng sự kiện</span>

                <strong>
                    <?= $totalEvents ?>
                </strong>
            </div>

        </div>


        <div class="stat-card upcoming">

            <div class="stat-icon">
                🔜
            </div>

            <div class="stat-content">
                <span>Sắp diễn ra</span>

                <strong>
                    <?= $upcomingEvents ?>
                </strong>
            </div>

        </div>


        <div class="stat-card ongoing">

            <div class="stat-icon">
                🔴
            </div>

            <div class="stat-content">
                <span>Đang diễn ra</span>

                <strong>
                    <?= $ongoingEvents ?>
                </strong>
            </div>

        </div>


        <div class="stat-card registration">

            <div class="stat-icon">
                📝
            </div>

            <div class="stat-content">
                <span>Đăng ký chờ duyệt</span>

                <strong>
                    <?= $pendingEventRegistrations ?>
                </strong>
            </div>

        </div>

    </div>


    <!-- ========================= -->
    <!-- CONTENT GRID -->
    <!-- ========================= -->

    <div class="dashboard-content">


        <!-- ========================= -->
        <!-- SỰ KIỆN GẦN ĐÂY -->
        <!-- ========================= -->

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>
                    <h2>Sự kiện gần đây</h2>

                    <p>
                        Các sự kiện mới nhất của câu lạc bộ
                    </p>
                </div>

                <a href="events.php">
                    Xem tất cả →
                </a>

            </div>


            <?php if (empty($recentEvents)): ?>

                <div class="empty-state">
                    Chưa có sự kiện nào.
                </div>

            <?php else: ?>

                <div class="event-list">

                    <?php foreach ($recentEvents as $event): ?>

                        <a
                            href="event_detail.php?id=<?= (int)$event['event_id'] ?>"
                            class="event-item"
                        >

                            <div class="event-date">

                                <strong>
                                    <?= date(
                                        "d",
                                        strtotime($event['event_date'])
                                    ) ?>
                                </strong>

                                <span>
                                    <?= date(
                                        "m/Y",
                                        strtotime($event['event_date'])
                                    ) ?>
                                </span>

                            </div>


                            <div class="event-info">

                                <h3>
                                    <?= htmlspecialchars(
                                        $event['event_name']
                                    ) ?>
                                </h3>

                                <p>
                                    📍
                                    <?= htmlspecialchars(
                                        $event['location'] ?? 'Chưa cập nhật'
                                    ) ?>
                                </p>

                                <p>
                                    👥
                                    <?= (int)$event['registered_count'] ?>
                                    /
                                    <?= (int)$event['slots'] ?>
                                    người
                                </p>

                            </div>


                            <div class="event-status">

                                <?php if ($event['status'] === 'upcoming'): ?>

                                    <span class="badge upcoming">
                                        Sắp diễn ra
                                    </span>

                                <?php elseif ($event['status'] === 'ongoing'): ?>

                                    <span class="badge ongoing">
                                        Đang diễn ra
                                    </span>

                                <?php elseif ($event['status'] === 'completed'): ?>

                                    <span class="badge completed">
                                        Đã kết thúc
                                    </span>

                                <?php else: ?>

                                    <span class="badge cancelled">
                                        Đã hủy
                                    </span>

                                <?php endif; ?>

                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>


        <!-- ========================= -->
        <!-- ĐƠN MỚI NHẤT -->
        <!-- ========================= -->

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>
                    <h2>Đơn tham gia mới</h2>

                    <p>
                        Các đơn đăng ký thành viên gần đây
                    </p>
                </div>

                <a href="accept_member.php?status=pending">
                    Xem tất cả →
                </a>

            </div>


            <?php if (empty($recentApplications)): ?>

                <div class="empty-state">
                    Chưa có đơn đăng ký.
                </div>

            <?php else: ?>

                <div class="application-list">

                    <?php foreach ($recentApplications as $application): ?>

                        <div class="application-item">

                            <div class="application-avatar">

                                <?= strtoupper(
                                    mb_substr(
                                        $application['fullname']
                                            ?: $application['username'],
                                        0,
                                        1
                                    )
                                ) ?>

                            </div>


                            <div class="application-info">

                                <strong>
                                    <?= htmlspecialchars(
                                        $application['fullname']
                                            ?: $application['username']
                                    ) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $application['username']
                                    ) ?>
                                </span>

                                <small>
                                    <?= htmlspecialchars(
                                        $application['band_name']
                                            ?? 'Chưa chọn ban'
                                    ) ?>
                                </small>

                            </div>


                            <div class="application-status">

                                <?php if (
                                    $application['status'] === 'pending'
                                ): ?>

                                    <span class="badge pending">
                                        Chờ duyệt
                                    </span>

                                    <a
                                        href="member_detail.php?id=<?= (int)$application['application_id'] ?>"
                                    >
                                        Xem
                                    </a>

                                <?php elseif (
                                    $application['status'] === 'approved'
                                ): ?>

                                    <span class="badge approved">
                                        Đã duyệt
                                    </span>

                                <?php else: ?>

                                    <span class="badge rejected">
                                        Từ chối
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ========================= -->
    <!-- QUICK ACTION -->
    <!-- ========================= -->

    <div class="quick-actions">

        <h2 class="section-title">
            Thao tác nhanh
        </h2>

        <div class="quick-grid">

            <a
                href="members.php?status=pending"
                class="quick-card"
            >
                <span>👥</span>

                <div>
                    <strong>
                        Duyệt thành viên
                    </strong>

                    <small>
                        <?= $pendingMembers ?>
                        đơn đang chờ
                    </small>
                </div>

            </a>


            <a
                href="create_event.php"
                class="quick-card"
            >
                <span>➕</span>

                <div>
                    <strong>
                        Tạo sự kiện
                    </strong>

                    <small>
                        Tạo hoạt động mới
                    </small>
                </div>

            </a>


            <a
                href="events.php"
                class="quick-card"
            >
                <span>📅</span>

                <div>
                    <strong>
                        Quản lý sự kiện
                    </strong>

                    <small>
                        Xem và chỉnh sửa sự kiện
                    </small>
                </div>

            </a>


            <a
                href="event_registrations.php"
                class="quick-card"
            >
                <span>📝</span>

                <div>
                    <strong>
                        Duyệt đăng ký
                    </strong>

                    <small>
                        <?= $pendingEventRegistrations ?>
                        đăng ký chờ duyệt
                    </small>
                </div>

            </a>

        </div>

    </div>

</div>
<?php require_once '../includes/footer.php' ?>
</body>