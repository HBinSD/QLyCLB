<?php

require_once "../includes/auth.php";

$pageTitle = "Hồ sơ cá nhân";

require_once "../includes/header.php";

$user = $_SESSION['user'];

?>

<div class="page-header">

    <h2>
        Hồ sơ cá nhân
    </h2>

    <p>
        Thông tin cá nhân của tài khoản.
    </p>

</div>


<div class="profile-layout">


    <!-- PROFILE LEFT -->

    <div class="profile-sidebar">


        <?php if (!empty($user['avatar'])): ?>

            <img
                src="<?= htmlspecialchars($user['avatar']) ?>"
                class="profile-avatar"
                alt="Avatar"
            >

        <?php else: ?>

            <div class="profile-default-avatar">

                <?= strtoupper(
                    substr($user['full_name'], 0, 1)
                ) ?>

            </div>

        <?php endif; ?>


        <div class="profile-name">

            <?= htmlspecialchars($user['full_name']) ?>

        </div>


        <div class="role-badge">

            Thành viên

        </div>


        <div class="profile-menu">

            <a href="profile.php"
               class="active">

                👤 &nbsp;
                Thông tin cá nhân

            </a>


            <a href="#">

                🔒 &nbsp;
                Đổi mật khẩu

            </a>


            <a href="#">

                📋 &nbsp;
                Hoạt động của tôi

            </a>

        </div>


    </div>


    <!-- PROFILE RIGHT -->

    <div class="card">


        <h3 class="card-title">

            Thông tin cá nhân

        </h3>


        <p style="
            color:#94a3b8;
            margin-bottom:20px;
            font-size:14px;
        ">

            Thông tin tài khoản của bạn trong hệ thống.

        </p>


        <div class="info-table">


            <!-- HỌ TÊN -->

            <div class="info-row">

                <div class="info-label">
                    Họ và tên
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['full_name']
                    ) ?>

                </div>

            </div>


            <!-- MSV -->

            <div class="info-row">

                <div class="info-label">
                    Mã sinh viên
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['msv']
                    ) ?>

                </div>

            </div>


            <!-- LỚP -->

            <div class="info-row">

                <div class="info-label">
                    Lớp
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['class_name']
                    ) ?>

                </div>

            </div>


            <!-- KHOA -->

            <div class="info-row">

                <div class="info-label">
                    Khoa
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['faculty']
                    ) ?>

                </div>

            </div>


            <!-- EMAIL -->

            <div class="info-row">

                <div class="info-label">
                    Email
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['email']
                    ) ?>

                </div>

            </div>


            <!-- SỐ ĐIỆN THOẠI -->

            <div class="info-row">

                <div class="info-label">
                    Số điện thoại
                </div>

                <div class="info-value">

                    <?= htmlspecialchars(
                        $user['phone']
                    ) ?>

                </div>

            </div>


        </div>

    </div>


</div>


<?php require_once "../includes/footer.php"; ?>