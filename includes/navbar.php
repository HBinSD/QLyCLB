<?php

$user = $_SESSION['user'];

$fullName = $user['full_name'] ?? 'Người dùng';

$avatar = $user['avatar'] ?? '';

?>

<header class="navbar">

    <div class="navbar-left">

        <button class="mobile-menu-btn"
                onclick="toggleSidebar()">

            ☰

        </button>

        <h1>
            QUẢN LÝ CÂU LẠC BỘ
        </h1>

    </div>


    <div class="navbar-right">

        <div class="account">

            <button class="account-btn"
                    onclick="toggleAccountMenu()">

                <?php if (!empty($avatar)): ?>

                    <img
                        src="<?= htmlspecialchars($avatar) ?>"
                        class="navbar-avatar"
                        alt="Avatar"
                    >

                <?php else: ?>

                    <div class="navbar-avatar default-avatar">
                        <?= strtoupper(substr($fullName, 0, 1)) ?>
                    </div>

                <?php endif; ?>


                <span>
                    <?= htmlspecialchars($fullName) ?>
                </span>

                <span class="arrow">
                    ▼
                </span>

            </button>


            <div class="account-menu"
                 id="accountMenu">

                <div class="account-menu-name">

                    <?= htmlspecialchars($fullName) ?>

                </div>


                <a href="../member/profile.php">

                    <span class="menu-icon">
                        👤
                    </span>

                    Xem hồ sơ

                </a>


                <a href="../logout.php">

                    <span class="menu-icon">
                        ⇥
                    </span>

                    Đăng xuất

                </a>

            </div>

        </div>

    </div>

</header>