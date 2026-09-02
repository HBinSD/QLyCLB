<?php
session_start();
require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle  = "Giới thiệu Câu Lạc Bộ";
$activeMenu = "club.php";
require_once "../includes/headers.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

// Lấy thông tin câu lạc bộ theo user đang đăng nhập
$database = new Database();
$db = $database->getConnection();

$sql = "
    select cm.*,
            c.*,
            ui.fullname
    from clubmember as cm
    join clubs as c
    join userinfo as ui
    where cm.username = :username and cm.club_id = c.club_id and c.created_by = ui.username;

";

$stmt = $db->prepare($sql);
$stmt->execute([':username' => $user['username']]);
$club = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    echo '<div style="padding: 20px; color: #856404; background: #fff3cd; border-radius: 8px; margin: 20px;">
            Bạn chưa tham gia câu lạc bộ nào.
          </div>';
    require_once "../includes/footer.php";
    exit;
}

// Format ngày thành lập
$createdAt = !empty($club['created_at'])
    ? date('d/m/Y', strtotime($club['created_at']))
    : 'Chưa cập nhật';
?>

<link rel="stylesheet" href="css/club.css">

<div class="club-layout">

    <!-- ==============================
         SUB SIDEBAR
    =============================== -->
    <aside class="club-sidebar">

        <div class="club-sidebar-title">
            <span>☰</span>
            <span>QUẢN LÝ CLB</span>
        </div>

        <nav class="club-menu">

            <!-- Trang hiện tại -->
            <a href="club.php" class="club-menu-item active">
                <span class="menu-icon">🏠</span>
                <span>Giới thiệu CLB</span>
            </a>

            <!-- Danh sách thành viên -->
            <a href="club_member.php" class="club-menu-item">
                <span class="menu-icon">👥</span>
                <span>Danh sách thành viên</span>
            </a>

            <!-- Sự kiện -->
            <a href="events.php" class="club-menu-item">
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


    <!-- ==============================
         NỘI DUNG CLB
    =============================== -->
    <main class="club-content">

        <div class="club-intro">

            <h2>Giới thiệu Câu Lạc Bộ</h2>

            <div class="club-grid">

                <!-- Tên câu lạc bộ -->
                <div class="club-card">

                    <div class="club-label">
                        Tên câu lạc bộ
                    </div>

                    <div class="club-value">
                        <?= htmlspecialchars(
                            $club['club_name'] ?? 'Chưa cập nhật'
                        ) ?>
                    </div>

                </div>


                <!-- Người thành lập -->
                <div class="club-card">

                    <div class="club-label">
                        Người thành lập
                    </div>

                    <?php if (!empty($club['created_by'])): ?>

                    <b class="club-value">
                        <?= htmlspecialchars(
                                $club['fullname'] ?? 'Chưa cập nhật'
                            ) ?>
                    </b>

                    <?php else: ?>

                    <b class="club-value">
                        Không có chủ nhiệm
                    </b>

                    <?php endif; ?>

                </div>


                <!-- Ngày thành lập -->
                <div class="club-card">

                    <div class="club-label">
                        Ngày thành lập
                    </div>

                    <div class="club-value">
                        <?= $createdAt ?>
                    </div>

                </div>


                <!-- Trạng thái -->
                <div class="club-card">

                    <div class="club-label">
                        Trạng thái
                    </div>

                    <div class="club-value">

                        <?php if ($club['status'] == 1): ?>

                        <span class="club-status active">
                            Đang hoạt động
                        </span>

                        <?php else: ?>

                        <span class="club-status inactive">
                            Ngừng hoạt động
                        </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- Giới thiệu -->
                <div class="club-card full">

                    <div class="club-label">
                        Giới thiệu
                    </div>

                    <div class="club-value normal">
                        <?= nl2br( htmlspecialchars( $club['description'] ?? 'Chưa có giới thiệu' ) ) ?>
                    </div>

                </div>


                <!-- Quy định -->
                <div class="club-card full">

                    <div class="club-label">
                        Quy định
                    </div>

                    <div class="club-value normal">
                        <?= nl2br( htmlspecialchars( $club['rule'] ?? 'Chưa có quy định' ) ) ?>
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


</main>
</div>
</div>

<?php require_once "../includes/footer.php"; ?>



<?php require_once "../includes/footer.php"; ?>