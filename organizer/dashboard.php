<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['user']['role'] !== 'organizer') {
    die("Bạn không có quyền truy cập.");
}

?>

<h1>Organizer Dashboard</h1>

<p>
    Xin chào,
    <?= htmlspecialchars($_SESSION['user']['full_name']) ?>
</p>

<p>
    Role:
    <?= htmlspecialchars($_SESSION['user']['role']) ?>
</p>

<a href="../logout.php">
    Đăng xuất
</a>