<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

if (($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}

$database = new Database();
$db = $database->getConnection();

$status = $_GET['status'] ?? 'all';

$allowedStatus = ['all', 'unread', 'read'];

if (!in_array($status, $allowedStatus, true)) {
    $status = 'all';
}

/*
|--------------------------------------------------------------------------
| Đánh dấu đã xem
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['mark_read'])
) {

    $contactId = (int)($_POST['contact_id'] ?? 0);

    if ($contactId > 0) {

        $sql = "
            UPDATE Contact
            SET
                status = 'read',
                viewed_at = NOW()
            WHERE contact_id = :contact_id
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':contact_id' => $contactId
        ]);
    }

    header("Location: contact.php?status=" . urlencode($status));
    exit;
}

/*
|--------------------------------------------------------------------------
| Thống kê
|--------------------------------------------------------------------------
*/

$stmt = $db->query("
    SELECT COUNT(*)
    FROM Contact
    WHERE status = 'unread'
");

$unreadCount = (int)$stmt->fetchColumn();

$stmt = $db->query("
    SELECT COUNT(*)
    FROM Contact
");

$totalCount = (int)$stmt->fetchColumn();

$stmt = $db->query("
    SELECT COUNT(*)
    FROM Contact
    WHERE status = 'read'
");

$readCount = (int)$stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| Danh sách Contact
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        c.contact_id,
        c.username,
        c.subject,
        c.message,
        c.status,
        c.created_at,
        c.viewed_at,

        ui.fullname,
        ui.email,
        ui.phone

    FROM Contact c

    LEFT JOIN UserInfo ui
        ON ui.username = c.username

    WHERE 1 = 1
";

$params = [];

if ($status !== 'all') {
    $sql .= " AND c.status = :status";
    $params[':status'] = $status;
}

$sql .= "
    ORDER BY
        CASE
            WHEN c.status = 'unread' THEN 0
            ELSE 1
        END,
        c.created_at DESC
";

$stmt = $db->prepare($sql);
$stmt->execute($params);

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Quản lý liên hệ";
$activeMenu = "contact.php";

require_once "../includes/headers.php";
?>

<div class="contact-admin-container">

    <div class="page-header">

        <div>
            <h1>Quản lý liên hệ</h1>

            <p>
                Xem các liên hệ được gửi từ thành viên.
            </p>
        </div>

    </div>

    <!-- Thống kê -->

    <div class="contact-stats">

        <div class="stat-card">
            <div class="stat-icon">📩</div>

            <div>
                <span>Tổng liên hệ</span>
                <strong><?= $totalCount ?></strong>
            </div>
        </div>

        <div class="stat-card unread">
            <div class="stat-icon">🔔</div>

            <div>
                <span>Chưa xem</span>
                <strong><?= $unreadCount ?></strong>
            </div>
        </div>

        <div class="stat-card read">
            <div class="stat-icon">✓</div>

            <div>
                <span>Đã xem</span>
                <strong><?= $readCount ?></strong>
            </div>
        </div>

    </div>

    <!-- Bộ lọc -->

    <div class="filter-bar">

        <a
            href="contact.php?status=all"
            class="<?= $status === 'all' ? 'active' : '' ?>"
        >
            Tất cả
        </a>

        <a
            href="contact.php?status=unread"
            class="<?= $status === 'unread' ? 'active' : '' ?>"
        >
            Chưa xem
            <?php if ($unreadCount > 0): ?>
                <span class="badge-count">
                    <?= $unreadCount ?>
                </span>
            <?php endif; ?>
        </a>

        <a
            href="contact.php?status=read"
            class="<?= $status === 'read' ? 'active' : '' ?>"
        >
            Đã xem
        </a>

    </div>

    <!-- Danh sách -->

    <div class="contact-list">

        <?php if (empty($contacts)): ?>

            <div class="empty-state">
                <div>📭</div>
                <h3>Chưa có liên hệ</h3>
                <p>Hiện tại chưa có liên hệ nào.</p>
            </div>

        <?php else: ?>

            <?php foreach ($contacts as $contact): ?>

                <div class="contact-item <?= $contact['status'] === 'unread' ? 'is-unread' : '' ?>">

                    <div class="contact-main">

                        <div class="contact-top">

                            <div class="contact-user">

                                <strong>
                                    <?= htmlspecialchars(
                                        $contact['fullname']
                                        ?: $contact['username']
                                    ) ?>
                                </strong>

                                <span>
                                    @<?= htmlspecialchars($contact['username']) ?>
                                </span>

                            </div>

                            <div class="contact-status">

                                <?php if ($contact['status'] === 'unread'): ?>

                                    <span class="status unread">
                                        Chưa xem
                                    </span>

                                <?php else: ?>

                                    <span class="status read">
                                        Đã xem
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                        <h3 class="contact-subject">
                            <?= htmlspecialchars($contact['subject']) ?>
                        </h3>

                        <p class="contact-message">
                            <?= nl2br(
                                htmlspecialchars($contact['message'])
                            ) ?>
                        </p>

                        <div class="contact-info">

                            <span>
                                📧
                                <?= htmlspecialchars($contact['email'] ?? '') ?>
                            </span>

                            <?php if (!empty($contact['phone'])): ?>

                                <span>
                                    📱
                                    <?= htmlspecialchars($contact['phone']) ?>
                                </span>

                            <?php endif; ?>

                            <span>
                                🕒
                                <?= htmlspecialchars($contact['created_at']) ?>
                            </span>

                        </div>

                    </div>

                    <?php if ($contact['status'] === 'unread'): ?>

                        <div class="contact-action">

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="contact_id"
                                    value="<?= (int)$contact['contact_id'] ?>"
                                >

                                <button
                                    type="submit"
                                    name="mark_read"
                                    class="btn-read"
                                >
                                    ✓ Đánh dấu đã xem
                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<link rel="stylesheet" href="css/contact.css">

<?php require_once '../includes/footer.php' ?>