<?php

require_once "../includes/auth.php";

$pageTitle = "Trang chủ";

require_once "../includes/header.php";

$user = $_SESSION['user'];

?>

<div class="page-header">

    <h2>Trang chủ</h2>

    <p>
        Tổng quan hoạt động của bạn trong câu lạc bộ.
    </p>

</div>


<!-- WELCOME -->

<div class="welcome-card">

    <div class="welcome-content">

        <h2>
            Xin chào,
            <?= htmlspecialchars($user['full_name']) ?>
        </h2>

        <p>
            Chào mừng bạn đến với hệ thống quản lý câu lạc bộ.
        </p>


        <div class="welcome-info">

            <span class="info-badge">
                MSV: <?= htmlspecialchars($user['msv']) ?>
            </span>

            <span class="info-badge">
                Lớp: <?= htmlspecialchars($user['class_name']) ?>
            </span>

            <span class="info-badge">
                Khoa: <?= htmlspecialchars($user['faculty']) ?>
            </span>

        </div>

    </div>


    <?php if (!empty($user['avatar'])): ?>

        <img
            src="<?= htmlspecialchars($user['avatar']) ?>"
            class="welcome-avatar"
            alt="Avatar"
        >

    <?php else: ?>

        <div class="welcome-avatar default-avatar">

            <?= strtoupper(
                substr($user['full_name'], 0, 1)
            ) ?>

        </div>

    <?php endif; ?>

</div>


<!-- STATISTICS -->

<div class="stats">


    <div class="stat-card">

        <div class="stat-icon">
            🏢
        </div>

        <h3>2</h3>

        <p>
            CLB đang tham gia
        </p>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            📅
        </div>

        <h3>5</h3>

        <p>
            Sự kiện sắp tới
        </p>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            📝
        </div>

        <h3>3</h3>

        <p>
            Sự kiện đã đăng ký
        </p>

    </div>


    <div class="stat-card">

        <div class="stat-icon">
            ✓
        </div>

        <h3>8</h3>

        <p>
            Hoạt động đã tham gia
        </p>

    </div>


</div>


<!-- UPCOMING EVENTS -->

<div class="card">

    <h3 class="card-title">
        Sự kiện sắp tới
    </h3>


    <div class="event-item">

        <strong>
            Workshop PHP cơ bản
        </strong>

        <p>
            📅 25/08/2026 &nbsp; | &nbsp;
            ⏰ 19:00
        </p>

        <p>
            📍 Phòng A101
        </p>

    </div>


    <hr>


    <div class="event-item">

        <strong>
            Lập trình Web với PHP và MySQL
        </strong>

        <p>
            📅 28/08/2026 &nbsp; | &nbsp;
            ⏰ 18:30
        </p>

        <p>
            📍 Phòng A202
        </p>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>