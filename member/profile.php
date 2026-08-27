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

<style>

.profile-page {
    width: 100%;
    padding: 0;
}

/* Tiêu đề */
.profile-title {
    font-size: 28px;
    font-weight: 700;
    color: #31578c;
    margin: 0 0 25px 16px;
    text-transform: uppercase;
}

/* Khung chính */
.profile-container {
    display: flex;
    align-items: center;
    gap: 32px;
    width: 100%;
    min-height: 300px;
    background: #ffffff;
    border-radius: 12px;
    padding: 30px 50px;
    box-sizing: border-box;
}

/* Khu vực avatar */
.profile-avatar-section {
    width: 240px;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

/* Avatar */
.profile-avatar {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    background: #d9d9d9;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

/* Ảnh avatar */
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Avatar mặc định nếu không có ảnh */
.avatar-default {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #ffffff;
}

/* Role dưới avatar */
.profile-role {
    margin-top: 12px;
    min-width: 90px;
    padding: 7px 18px;
    text-align: center;
    background: #cee5ff;
    color: #3a70bd;
    border: 1px solid #cbd9ea;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-sizing: border-box;
}
/* Khung thông tin */
.profile-info {
    flex: 1;
    border: 1px solid #c9c9c9;
    border-radius: 13px;
    padding: 20px 28px;
    min-height: 260px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.15);
    box-sizing: border-box;
}

/* Một dòng thông tin */
.info-row {
    display: flex;
    align-items: center;
    min-height: 48px;
    border-bottom: 1px solid #eeeeee;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    width: 150px;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
}

.info-value {
    flex: 1;
    font-size: 14px;
    color: #1e293b;
    padding: 8px 0;
}

/* Responsive */
@media (max-width: 768px) {

    .profile-container {
        flex-direction: column;
        align-items: center;

        padding: 25px;
    }

    .profile-avatar-section {
        width: auto;
    }

    .profile-info {
        width: 100%;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;

        padding: 10px 0;
    }

    .info-row label {
        width: 100%;
        margin-bottom: 4px;
    }
}

.edit-profile {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-edit-profile {
    display: inline-block;
    padding: 10px 20px;
    background: #31578c;
    color: #ffffff;
    border-radius: 7px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s ease;
}

.btn-edit-profile:hover {
    background: #24466f;
    transform: translateY(-1px);
}
</style>


</main>
</div>
</div>
</body>
</html>