<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle  = "Thông tin cá nhân";
$activeMenu = "profile.php";

require_once "../includes/headers.php";

$user = $_SESSION['user'] ?? [];
$avatar = $_SESSION['user']['avatar'] ?? '';
?>

<link rel="stylesheet" href="profile.css">
<div class="profile-page">

    <!-- Tiêu đề -->
    <h1 class="profile-title">
        Thông tin cá nhân
    </h1>

    <div class="profile-container">

        <div class="profile-avatar-section">

        <!-- Avatar -->
        <div class="profile-avatar">
            <?php
            $avatar = $user['avatar'] ?? '';
            ?>

            <?php if (!empty($avatar)): ?>
                <img 
                    src="<?php echo htmlspecialchars($avatar); ?>" 
                    alt="Ảnh đại diện"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

            <?php else: ?>
                <div class="avatar-default">
                    👤
                </div>
            <?php endif; ?>
        </div>

        <!-- Role -->
        <div class="profile-role">
            <?php 
            if ($user['role'] == 'admin') {
                echo 'Nguời quản trị';
            } else if ($user['role'] == 'organizer') {
                echo 'Ban tổ chức';
            } else {
                echo 'Thành viên';
            }

            ?>
        </div>

    </div>

        <!-- Thông tin -->
        <div class="profile-info">

            <div class="info-row">
                <label>Họ và tên</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['fullname'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Tên đăng nhập</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['username'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Email</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Số điện thoại</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['phone'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Giới tính</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['gender'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Ngày sinh</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['DOB'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Mã số SV</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['id_number'] ?? ''); ?>
                </div>
            </div>

            <div class="info-row">
                <label>Ngày tạo tài khoản</label>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['ngayTao'] ?? ''); ?>
                </div>
            </div>

            <!-- Nút chỉnh sửa -->
            <div class="edit-profile">
                <a href="edit_profile.php" class="btn-edit-profile">
                    Chỉnh sửa thông tin cá nhân
                </a>
            </div>

        </div>

</div>

</main>
</div>
</div>

<?php 
    require_once "../includes/footer.php";
?>