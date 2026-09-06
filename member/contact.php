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

// Lấy thông tin câu lạc bộ sinh viên đang tham gia cùng thông tin người quản lý/chủ nhiệm
$sql = "
    SELECT c.*, ui.fullname AS owner_name, ui.email AS owner_email, ui.phone AS owner_phone
    FROM ClubMember AS cm
    JOIN clubs AS c ON c.club_id = cm.club_id
    LEFT JOIN UserInfo AS ui ON ui.username = c.owner_id
    WHERE cm.username = :username AND cm.status = 1
";
$stmt = $db->prepare($sql);
$stmt->execute([':username' => $username]);
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Liên hệ Câu lạc bộ";
$activeMenu = "contact.php";

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

            <a href="notifications.php" class="club-menu-item">
                <span class="menu-icon">🔔</span>
                <span>Thông báo CLB</span>
            </a>

            <a href="contact.php" class="club-menu-item active">
                <span class="menu-icon">📞</span>
                <span>Liên hệ & Hỗ trợ</span>
            </a>
        </nav>
    </aside>

    <!-- CONTENT -->
    <main class="club-content">
        <div class="register-card" style="max-width: 100%;">
            <h1>Liên hệ & Hỗ trợ Câu lạc bộ</h1>
            <p>Nếu bạn có thắc mắc, cần giải đáp hoặc hỗ trợ về hoạt động câu lạc bộ, vui lòng xem thông tin chi tiết và liên hệ qua các kênh dưới đây:</p>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

            <?php if (empty($clubs)): ?>
                <div style="background: #f8f9fa; color: #6c757d; text-align: center; padding: 40px; border-radius: 6px;">
                    📭 Bạn hiện chưa tham gia câu lạc bộ nào.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($clubs as $club): ?>
                        <div style="background: #fff; border: 1px solid #e0e0e0; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <h2 style="margin-top: 0; color: #007bff; font-size: 20px;">
                                <?= htmlspecialchars($club['club_name']) ?>
                            </h2>
                            
                            <p style="color: #555; line-height: 1.6; margin-bottom: 15px;">
                                <strong>Giới thiệu:</strong> <?= nl2br(htmlspecialchars($club['description'])) ?>
                            </p>

                            <p style="color: #555; line-height: 1.6; margin-bottom: 20px;">
                                <strong>Quy định CLB:</strong> <?= nl2br(htmlspecialchars($club['rule'])) ?>
                            </p>

                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #28a745;">
                                <h4 style="margin: 0 0 10px 0; color: #333;">Thông tin người phụ trách / Chủ nhiệm:</h4>
                                <p style="margin: 5px 0;">👤 <strong>Họ tên:</strong> <?= htmlspecialchars($club['owner_name'] ?? 'Chưa cập nhật') ?></p>
                                <p style="margin: 5px 0;">📧 <strong>Email:</strong> <?= htmlspecialchars($club['owner_email'] ?? 'Chưa cập nhật') ?></p>
                                <p style="margin: 5px 0;">📞 <strong>Số điện thoại:</strong> <?= htmlspecialchars($club['owner_phone'] ?? 'Chưa cập nhật') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</div>

<?php require_once "../includes/footer.php"; ?>