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

$database = new Database();
$db = $database->getConnection();

// Đánh dấu tất cả thông báo của sinh viên này thành đã đọc (is_read = 1) khi truy cập trang
$stmtRead = $db->prepare("UPDATE notifications SET is_read = 1 WHERE username = :username AND is_read = 0");
$stmtRead->execute([':username' => $username]);

// Lấy danh sách thông báo của sinh viên, kèm theo tên câu lạc bộ
$sql = "
    SELECT n.*, c.club_name 
    FROM notifications AS n
    LEFT JOIN clubs AS c ON c.club_id = n.club_id
    WHERE n.username = :username
    ORDER BY n.created_at DESC
";
$stmt = $db->prepare($sql);
$stmt->execute([':username' => $username]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Thông báo & Tin tức";
$activeMenu = "news.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/club.css">
<link rel="stylesheet" href="css/register_event.css">

<div class="club-layout">

    <aside class="club-sidebar">
        <div class="club-sidebar-title">
            <span>☰</span>
            <span>QUẢN LÝ CLB</span>
        </div>

        <nav class="club-menu">
            <a href="club.php" class="club-menu-item">
                <span class="menu-icon">🏠</span>
                <span>Giới thiệu CLB</span>
            </a>

            <a href="club_member.php" class="club-menu-item">
                <span class="menu-icon">👥</span>
                <span>Danh sách thành viên</span>
            </a>

            <a href="events.php" class="club-menu-item">
                <span class="menu-icon">📅</span>
                <span>Sự kiện</span>
            </a>

            <a href="registered_events.php" class="club-menu-item">
                <span class="menu-icon">✓</span>
                <span>Các sự kiện đã đăng ký</span>
            </a>

            <a href="notifications.php" class="club-menu-item active">
                <span class="menu-icon">🔔</span>
                <span>Thông báo CLB</span>
            </a>
        </nav>
    </aside>

    <!-- CONTENT -->
    <main class="club-content">
        <div class="register-card" style="max-width: 100%;">
            <h1>Thông báo & Tin tức từ Câu lạc bộ</h1>
            <p>Cập nhật các thông báo mới nhất, lịch sự kiện hoặc thay đổi từ Ban tổ chức câu lạc bộ.</p>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

            <?php if (empty($notifications)): ?>
                <div style="background: #f8f9fa; color: #6c757d; text-align: center; padding: 40px; border-radius: 6px;">
                    📭 Hiện tại bạn không có thông báo nào mới.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($notifications as $item): ?>
                        <div style="background: #fff; border: 1px solid #e0e0e0; border-left: 4px solid #007bff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: bold; color: #007bff; background: #e7f1ff; padding: 3px 8px; border-radius: 4px;">
                                    <?= htmlspecialchars($item['club_name'] ?? 'Câu lạc bộ') ?>
                                </span>
                                <span style="font-size: 12px; color: #888;">
                                    📅 <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                </span>
                            </div>
                            
                            <h3 style="margin: 0 0 10px 0; font-size: 18px; color: #333;">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            
                            <p style="margin: 0; color: #555; line-height: 1.5; white-space: pre-line;">
                                <?= htmlspecialchars($item['message']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</div>

<?php require_once "../includes/footer.php"; ?>