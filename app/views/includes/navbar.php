<?php
    $user = $_SESSION['user'] ?? [];

    $fullName = $user['fullname'] ?? 'Người dùng';
    $role = $user['role'] ??'';
    $avatar   = $user['avatar'] ?? '';
?>

<style>
/* =========================
       HEADER / NAVBAR
    ========================= */

.navbar {
    height: 72px;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    box-sizing: border-box;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    box-shadow: 1px 2px 8px #888888;
}

/* =========================
       LEFT
    ========================= */

.navbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.mobile-menu-btn {
    width: 42px;
    height: 42px;

    border: none;
    border-radius: 10px;

    background: #eff6ff;
    color: #1e3a8a;

    font-size: 22px;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: 0.2s ease;
}

.mobile-menu-btn:hover {
    background: #dbeafe;
    transform: translateY(-1px);
}

.navbar-title {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.navbar-title h1 {
    margin: 0;

    font-size: 20px;
    font-weight: 700;

    color: #1e3a5f;

    letter-spacing: 0.3px;
}

.navbar-subtitle {
    font-size: 12px;
    color: #64748b;
}

/* =========================
       RIGHT
    ========================= */

.navbar-right {
    display: flex;
    align-items: center;
}

.account {
    position: relative;
}

.account-btn {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 6px 10px 6px 6px;

    background: transparent;
    border: none;
    border-radius: 12px;

    cursor: pointer;

    transition: 0.2s ease;
}

.account-btn:hover {
    background: #f1f5f9;
}

/* =========================
       AVATAR
    ========================= */

.navbar-avatar {
    width: 42px;
    height: 42px;

    border-radius: 50%;

    object-fit: cover;

    border: 2px solid #dbeafe;
}

.default-avatar {
    display: flex;
    align-items: center;
    justify-content: center;

    background: #1e3a8a;
    color: #ffffff;

    font-size: 16px;
    font-weight: 700;
}

/* =========================
       USER NAME
    ========================= */

.account-name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    max-width: 180px;
}

.account-name strong {
    color: #1e293b;
    font-size: 14px;
    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.account-role {
    color: #64748b;
    font-size: 12px;
    margin-top: 3px;
}

.arrow {
    margin-left: 4px;

    color: #64748b;
    font-size: 11px;

    transition: transform 0.2s ease;
}

.account-btn.active .arrow {
    transform: rotate(180deg);
}

/* =========================
       ACCOUNT DROPDOWN
    ========================= */

.account-menu {
    position: absolute;

    top: calc(100% + 10px);
    right: 0;

    width: 230px;

    background: #ffffff;

    border: 1px solid #e5e7eb;
    border-radius: 14px;

    padding: 8px;

    box-shadow:
        0 10px 25px rgba(15, 23, 42, 0.10),
        0 4px 8px rgba(15, 23, 42, 0.05);

    display: none;
}

.account-menu.show {
    display: block;
    animation: menuShow 0.15s ease;
}

@keyframes menuShow {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.account-menu-name {
    padding: 12px;

    font-size: 14px;
    font-weight: 600;

    color: #1e293b;

    border-bottom: 1px solid #e5e7eb;

    margin-bottom: 5px;
}

.account-menu a {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 11px 12px;

    color: #334155;

    text-decoration: none;

    font-size: 14px;

    border-radius: 9px;

    transition: 0.2s ease;
}

.account-menu a:hover {
    background: #eff6ff;
    color: #1e40af;
}

.menu-icon {
    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eff6ff;

    border-radius: 8px;

    font-size: 14px;
}

/* =========================
       RESPONSIVE
    ========================= */

@media (max-width: 768px) {

    .navbar {
        height: 64px;
        padding: 0 15px;
    }

    .navbar-title h1 {
        font-size: 16px;
    }

    .navbar-subtitle {
        display: none;
    }

    .account-name {
        display: none;
    }

    .account-btn {
        padding: 4px;
    }

    .navbar-avatar {
        width: 38px;
        height: 38px;
    }
}

@media (max-width: 480px) {

    .navbar-left {
        gap: 8px;
    }

    .mobile-menu-btn {
        width: 38px;
        height: 38px;
    }

    .navbar-title h1 {
        font-size: 14px;
    }

    .navbar {
        padding: 0 10px;
    }
}
</style>


<header class="navbar">

    <!-- =========================
         LEFT
    ========================== -->

    <div class="navbar-left">

        <div class="navbar-title">

            <h1>
                QUẢN LÝ CÂU LẠC BỘ
            </h1>

            <span class="navbar-subtitle">
                Hệ thống quản lý câu lạc bộ
            </span>

        </div>

    </div>


    <!-- =========================
         RIGHT
    ========================== -->

    <div class="navbar-right">

        <div class="account">

            <button class="account-btn" id="accountBtn" onclick="toggleAccountMenu()" type="button">

                <?php if (!empty($avatar)): ?>

                <img src="<?= htmlspecialchars($avatar) ?>" class="navbar-avatar" alt="Avatar">

                <?php else: ?>

                <div class="navbar-avatar default-avatar">
                    <?= strtoupper(substr($fullName, 0, 1)) ?>
                </div>

                <?php endif; ?>


                <div class="account-name">

                    <strong>
                        <?= htmlspecialchars($fullName) ?>
                    </strong>

                    <span class="account-role">
                        <?php  if ($role == "admin") {
                            echo 'Người quản trị';
                        } else if ($role == "organizer") {
                            echo 'Người tổ chức';
                        } else { echo 'Thành viên'; } ?>
                    </span>

                </div>


                <span class="arrow">
                    ▼
                </span>

            </button>


            <!-- DROPDOWN -->

            <div class="account-menu" id="accountMenu">

                <div class="account-menu-name">
                    <?= htmlspecialchars($fullName) ?>
                </div>


                <a href="../member/profile.php">

                    <span class="menu-icon">
                        👤
                    </span>

                    <span>
                        Xem hồ sơ
                    </span>

                </a>
                <a href="index.php?page=logout">
                <span class="menu-icon"></span>
                <span>Đăng xuất</span>
            </a>
                </a>

            </div>

        </div>

    </div>

</header>


<script>
function toggleAccountMenu() {

    const menu = document.getElementById("accountMenu");
    const button = document.getElementById("accountBtn");

    menu.classList.toggle("show");
    button.classList.toggle("active");
}


/* Click ra ngoài thì đóng menu */

document.addEventListener("click", function(event) {

    const account = document.querySelector(".account");
    const menu = document.getElementById("accountMenu");
    const button = document.getElementById("accountBtn");

    if (
        account &&
        !account.contains(event.target)
    ) {
        menu.classList.remove("show");
        button.classList.remove("active");
    }

});
</script>