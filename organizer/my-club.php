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

// =====================================================
// LẤY THÔNG TIN CLB (Bỏ club_id và created_by)
// =====================================================
$sql = "
    SELECT 
        club_name,
        description,
        rule,
        logo,
        owner_id,
        created_at,
        status
    FROM clubs
    WHERE owner_id = :owner_id
    LIMIT 1
";

$stmt = $db->prepare($sql);
$stmt->execute([':owner_id' => $organizerUsername]);
$club = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Thông tin câu lạc bộ";
require_once "../includes/headers.php";
?>

<div class="container" style="padding: 20px; max-width: 900px; margin: 0 auto;">
    <h2>Thông tin câu lạc bộ</h2>
    <p>Quản lý thông tin chi tiết của câu lạc bộ.</p>

    <?php if ($club): ?>
        <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 20px;">
            <p style="margin-bottom: 15px;">
                <strong>Tên câu lạc bộ:</strong> 
                <?= htmlspecialchars($club['club_name']) ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Mô tả:</strong><br>
                <?= nl2br(htmlspecialchars($club['description'])) ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Quy định:</strong><br>
                <?= nl2br(htmlspecialchars($club['rule'])) ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Logo:</strong> 
                <?= htmlspecialchars($club['logo'] ?: 'Chưa cập nhật') ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Chủ sở hữu (Owner ID):</strong> 
                <?= htmlspecialchars($club['owner_id']) ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Ngày tạo:</strong> 
                <?= !empty($club['created_at']) ? date('d/m/Y H:i', strtotime($club['created_at'])) : '-' ?>
            </p>

            <p style="margin-bottom: 15px;">
                <strong>Trạng thái:</strong> 
                <span style="padding: 4px 10px; border-radius: 4px; color: #fff; background-color: <?= $club['status'] == 1 ? '#28a745' : '#dc3545' ?>;">
                    <?= $club['status'] == 1 ? 'Hoạt động' : 'Đã xóa' ?>
                </span>
            </p>
        </div>
    <?php else: ?>
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: center;">
            <p>Hiện tại tài khoản của bạn chưa quản lý câu lạc bộ nào.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once "../includes/footer.php"; ?>