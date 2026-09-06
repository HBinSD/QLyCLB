<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

// =====================================================
// KIỂM TRA QUYỀN ORGANIZER
// =====================================================
$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'organizer') {
    header("Location: ../login.php");
    exit;
}

$organizerUsername = $user['username'];

// =====================================================
// DATABASE
// =====================================================
$database = new Database();
$db = $database->getConnection();

// Lấy club_id mà organizer này đang quản lý
$stmtClub = $db->prepare("SELECT club_id, club_name FROM clubs WHERE owner_id = :owner_id LIMIT 1");
$stmtClub->execute([':owner_id' => $organizerUsername]);
$club = $stmtClub->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    die("Tài khoản của bạn chưa quản lý câu lạc bộ nào.");
}

$clubId = $club['club_id'];
$success = '';
$error = '';

// =====================================================
// XỬ LÝ KHI GỬI THÔNG BÁO
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($title) || empty($message)) {
        $error = "Vui lòng nhập đầy đủ tiêu đề và nội dung thông báo.";
    } else {
        try {
            $db->beginTransaction();

            // 1. Lấy danh sách tất cả thành viên đang hoạt động trong CLB
            $stmtMembers = $db->prepare("SELECT username FROM ClubMember WHERE club_id = :club_id AND status = 1");
            $stmtMembers->execute([':club_id' => $clubId]);
            $members = $stmtMembers->fetchAll(PDO::FETCH_COLUMN);

            if (empty($members)) {
                throw new Exception("Câu lạc bộ hiện chưa có thành viên nào để nhận thông báo.");
            }

            // 2. Chèn thông báo cho từng thành viên
            $stmtInsert = $db->prepare("
                INSERT INTO notifications (club_id, username, title, message, created_at)
                VALUES (:club_id, :username, :title, :message, NOW())
            ");

            foreach ($members as $username) {
                $stmtInsert->execute([
                    ':club_id' => $clubId,
                    ':username' => $username,
                    ':title' => $title,
                    ':message' => $message
                ]);
            }

            $db->commit();
            $success = "Đã gửi thông báo thành công đến toàn bộ thành viên câu lạc bộ!";
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = $e->getMessage();
        }
    }
}

$pageTitle = "Quản lý thông báo CLB";
require_once "../includes/headers.php";
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Gửi thông báo câu lạc bộ</h2>
    <p>Gửi tin tức, lịch sự kiện hoặc thay đổi quan trọng đến tất cả thành viên của <strong><?= htmlspecialchars($club['club_name']) ?></strong>.</p>

    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tiêu đề thông báo:</label>
            <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Ví dụ: Lịch họp triển khai sự kiện mới...">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Nội dung chi tiết:</label>
            <textarea name="message" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Nhập nội dung thông báo tại đây..."></textarea>
        </div>

        <button type="submit"  style="background: #007bff; color: #ccc; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Gửi thông báo cho toàn bộ CLB
        </button>
    </form>
</div>

<?php require_once "../includes/footer.php"; ?>