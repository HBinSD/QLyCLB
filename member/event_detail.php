<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $user['username'];

$eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventId) {
    header("Location: events.php");
    exit;
}

$database = new Database();
$db       = $database->getConnection();

/*
|--------------------------------------------------------------------------
| 1. Lấy thông tin sự kiện
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
    LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':event_id' => $eventId,
]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit;
}

$clubId = $event['club_id'];

/*
|--------------------------------------------------------------------------
| Xử lý Hủy đăng ký (CHỈ CHO PHÉP HỦY KHI TRẠNG THÁI LÀ 'pending')
|--------------------------------------------------------------------------
*/
$alertMessage = "";
$alertType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_registration') {
    // Chỉ cập nhật khi register_status = 'pending'
    $cancelSql = "
        UPDATE Register_event 
        SET register_status = 'cancelled' 
        WHERE username = :username 
          AND event_id = :event_id 
          AND register_status = 'pending'
    ";

    $stmtCancel = $db->prepare($cancelSql);
    $stmtCancel->execute([
        ':username' => $username,
        ':event_id' => $eventId
    ]);

    if ($stmtCancel->rowCount() > 0) {
        $alertMessage = "Hủy đăng ký sự kiện thành công!";
        $alertType = "success";
    } else {
        $alertMessage = "Không thể hủy đăng ký. (Sự kiện đã được duyệt hoặc trạng thái không hợp lệ).";
        $alertType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 2. Đếm số người đã được duyệt
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT COUNT(*) AS approved_count
    FROM Register_event
    WHERE event_id = :event_id
      AND register_status = 'approved'
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':event_id' => $eventId,
]);

$approvedCount = (int) $stmt->fetchColumn();

$slots = (int) $event['slots'];

$remainingSlots = max(0, $slots - $approvedCount);

/*
|--------------------------------------------------------------------------
| 3. Lấy các ban được phép tham gia
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        cb.band_id,
        cb.band_name

    FROM EventBand AS eb

    INNER JOIN ClubBand AS cb
        ON cb.band_id = eb.band_id
        AND cb.club_id = :club_id

    WHERE eb.event_id = :event_id

    ORDER BY cb.band_name ASC
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id'  => $clubId,
    ':event_id' => $eventId,
]);

$requiredBands = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| 4. Kiểm tra user có phải thành viên CLB không
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT 1
    FROM ClubMember
    WHERE username = :username
      AND club_id = :club_id
      AND status = 1
    LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':username' => $username,
    ':club_id'  => $clubId,
]);

$isClubMember = (bool) $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| 5. Kiểm tra user thuộc ban nào
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        cbm.band_id,
        cb.band_name
    FROM ClubBandMember AS cbm
    INNER JOIN ClubBand AS cb
        ON cb.band_id = cbm.band_id
        AND cb.club_id = cbm.club_id
    WHERE cbm.username = :username
      AND cbm.club_id = :club_id
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':username' => $username,
    ':club_id'  => $clubId,
]);

$userBands = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| 6. Kiểm tra user có thuộc ban được yêu cầu không
|--------------------------------------------------------------------------
*/

$isAllowedBand = false;
if (empty($requiredBands)) {
    $isAllowedBand = true;
} else {
    $requiredBandIds = array_column($requiredBands, 'band_id');
    $userBandIds     = array_column($userBands, 'band_id');

    foreach ($userBandIds as $bandId) {
        if (in_array($bandId, $requiredBandIds)) {
            $isAllowedBand = true;
            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| 7. Kiểm tra user đã đăng ký chưa
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        register_status,
        register_time,
        reject_reason
    FROM Register_event
    WHERE username = :username
      AND event_id = :event_id
    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':username' => $username,
    ':event_id' => $eventId,
]);

$registration = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| 8. Xác định có thể đăng ký hay không
|--------------------------------------------------------------------------
*/

$canRegister     = true;
$registerMessage = '';

if (!$isClubMember) {
    $canRegister = false;
    $registerMessage = 'Bạn chưa là thành viên của câu lạc bộ.';
} elseif ($event['status'] !== 'upcoming') {
    $canRegister = false;
    $registerMessage = 'Sự kiện hiện không mở đăng ký.';
} elseif ($remainingSlots <= 0) {
    $canRegister = false;
    $registerMessage = 'Sự kiện đã đủ số lượng người tham gia.';
} elseif (!$isAllowedBand) {
    $canRegister = false;
    $registerMessage = 'Bạn không thuộc ban được yêu cầu cho sự kiện này.';
} elseif ($registration) {

    switch ($registration['register_status']) {
        case 'pending':
            $canRegister = false;
            $registerMessage = 'Bạn đã đăng ký và đang chờ organizer duyệt.';
            break;

        case 'approved':
            $canRegister = false;
            $registerMessage = 'Bạn đã được duyệt tham gia sự kiện.';
            break;

        case 'rejected':
            $canRegister = false;
            $registerMessage = 'Đăng ký của bạn đã bị từ chối.';
            break;

        case 'cancelled':
            // Đã hủy -> MỞ CHO PHÉP ĐĂNG KÝ LẠI
            $canRegister = true;
            break;

        default:
            $canRegister = false;
            $registerMessage = 'Bạn đã đăng ký sự kiện này.';
    }
}

$pageTitle  = $event['event_name'];
$activeMenu = "events.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/event_detail.css">
<link rel="stylesheet" href="css/club.css">

<div class="club-layout">

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
            <a href="news.php" class="club-menu-item">
                <span class="menu-icon">🔔</span>
                <span>Thông báo CLB</span>
            </a>

        </nav>

    </aside>


    <!-- CONTENT -->

    <main class="club-content">

        <!-- BACK -->

        <a href="events.php" class="back-link">
            ← Quay lại danh sách sự kiện
        </a>


        <!-- EVENT DETAIL -->

        <div class="event-detail-card">

            <div class="event-detail-header">

                <div>

                    <span class="event-status">
                        <?php echo htmlspecialchars($event['status']) ?>
                    </span>

                    <h1>
                        <?php echo htmlspecialchars($event['event_name']) ?>
                    </h1>

                </div>

            </div>


            <!-- EVENT INFO -->

            <div class="event-info-grid">

                <div class="info-item">

                    <span class="info-label">
                        Ngày
                    </span>

                    <strong>
                        <?php echo date('d/m/Y', strtotime($event['event_date'])) ?>
                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Thời gian
                    </span>

                    <strong>
                        <?php echo date('H:i', strtotime($event['start_time'])) ?>
                        -
                        <?php echo date('H:i', strtotime($event['end_time'])) ?>
                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Địa điểm
                    </span>

                    <strong>
                        <?php echo htmlspecialchars($event['location'] ?? 'Chưa cập nhật') ?>
                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Người tổ chức
                    </span>

                    <strong>
                        <?php echo htmlspecialchars($event['organizer_name'] ?? $event['organizer_id']) ?>
                    </strong>

                </div>

            </div>


            <!-- SLOT -->

            <div class="slot-box">

                <div>

                    <span>
                        Số lượng tham gia
                    </span>

                    <strong>
                        <?php echo $approvedCount ?>
                        /
                        <?php echo $slots ?>
                    </strong>

                </div>


                <div class="slot-progress">

                    <?php
                    $percent = $slots > 0
                        ? min(100, ($approvedCount / $slots) * 100)
                        : 100;
                    ?>

                    <div
                        class="slot-progress-bar"
                        style="width: <?php echo $percent ?>%;"
                    ></div>

                </div>


                <p>
                    Còn
                    <strong><?php echo $remainingSlots ?></strong>
                    chỗ trống
                </p>

            </div>


            <!-- DESCRIPTION -->

            <section class="detail-section">

                <h2>
                    Mô tả sự kiện
                </h2>

                <p>
                    <?php echo nl2br(htmlspecialchars($event['description'] ?? 'Chưa có mô tả.')) ?>
                </p>

            </section>


            <!-- REQUIRED BANDS -->

            <section class="detail-section">

                <h2>
                    Ban được tham gia
                </h2>

                <?php if (empty($requiredBands)): ?>

                    <p class="no-requirement">
                        Tất cả thành viên trong câu lạc bộ
                        đều có thể tham gia.
                    </p>

                <?php else: ?>

                    <div class="band-list">

                        <?php foreach ($requiredBands as $band): ?>

                            <span class="band-tag">
                                <?php echo htmlspecialchars($band['band_name']) ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>


            <!-- USER BANDS -->

            <section class="detail-section">

                <h2>
                    Ban của bạn
                </h2>

                <?php if (empty($userBands)): ?>

                    <p class="warning-text">
                        Bạn chưa được phân vào ban nào.
                    </p>

                <?php else: ?>

                    <div class="band-list">

                        <?php foreach ($userBands as $band): ?>

                            <span class="user-band-tag">
                                <?php echo htmlspecialchars($band['band_name']) ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>


            <!-- REGISTER & CANCEL AREA -->

            <div class="register-area">

                <?php if (!empty($alertMessage)): ?>
                    <div class="alert alert-<?= $alertType ?>">
                        <?= htmlspecialchars($alertMessage) ?>
                    </div>
                <?php endif; ?>

                <?php if ($canRegister): ?>

                    <?php $isReRegister = ($registration && $registration['register_status'] === 'cancelled'); ?>

                    <a
                        href="register_event.php?id=<?php echo $eventId ?>"
                        class="btn-register"
                    >
                        <?= $isReRegister ? '🔄 Đăng ký lại tham gia' : 'Đăng ký tham gia' ?>
                    </a>

                    <?php if ($isReRegister): ?>
                        <span style="font-size: 13px; color: #64748b;">
                            (Bạn đã hủy đăng ký sự kiện này trước đó)
                        </span>
                    <?php endif; ?>

                <?php else: ?>

                    <div class="register-message">
                        <?php echo htmlspecialchars($registerMessage) ?>
                    </div>

                    <?php if ($registration): ?>

                        <div class="action-buttons">

                            <!-- Chỉ cho phép hủy khi đang Chờ duyệt (pending) -->
                            <?php if ($registration['register_status'] === 'pending'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đăng ký tham gia sự kiện này?');">
                                    <input type="hidden" name="action" value="cancel_registration">
                                    <button type="submit" class="btn-cancel">
                                        Hủy đăng ký
                                    </button>
                                </form>

                            <!-- Khi đã được Duyệt (approved) thì nhắc nhở liên hệ -->
                            <?php elseif ($registration['register_status'] === 'approved'): ?>
                                <div class="contact-note">
                                    ℹ️ Đơn đăng ký đã được duyệt. Nếu muốn hủy tham gia, vui lòng liên hệ trực tiếp với Ban tổ chức hoặc Ban quản lý CLB.
                                </div>
                            <?php endif; ?>

                            <a
                                href="registered_events.php"
                                class="btn-secondary"
                            >
                                Xem đăng ký của tôi
                            </a>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

<?php require_once "../includes/footer.php"; ?>