<?php
$currentUser = getCurrentUser();
?>
<header class="site-header">
    <div class="container header-content">
        <div class="logo">
            <a href="index.php">🎓 QLyCLB</a>
        </div>
        
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Sự kiện</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="my_registrations.php">Đăng ký của tôi</a></li>
                    <?php if (hasRole('organizer') || hasRole('admin')): ?>
                        <li><a href="admin/dashboard.php">Quản trị</a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <span>Xin chào, <?= e($currentUser['full_name']) ?></span>
                        <a href="logout.php" class="btn btn-small btn-secondary">Đăng xuất</a>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-primary">Đăng nhập</a></li>
                    <li><a href="register.php" class="btn btn-secondary">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

</content>