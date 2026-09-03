<?php

$userRole = strtolower((string) ($_SESSION['user']['role'] ?? 'member'));
$allowedRoles = ['admin', 'organizer', 'member'];
if (!in_array($userRole, $allowedRoles, true)) {
    $userRole = 'member';
}

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

// Cho phép trang con tự chỉ định, ví dụ edit-member.php vẫn tô "Quản lý thành viên"
$activeMenu = $activeMenu ?? $currentPage;

$roleTitles = [
    'admin'     => 'TÀI KHOẢN ADMIN',
    'organizer' => 'TÀI KHOẢN BAN TỔ CHỨC',
    'member'    => 'TÀI KHOẢN THÀNH VIÊN',
];
$sidebarTitle = $roleTitles[$userRole];

if (!function_exists('sidebar_active')) {
    function sidebar_active(string $page): string
    {
        global $activeMenu;
        return $activeMenu === $page ? ' active' : '';
    }
}
?>

<style>
    .sidebar {
        width: 260px;
        height: 100vh;
        background: #ffffff;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        padding: 24px 16px;
        box-sizing: border-box;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.06);
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1001;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        text-align: center;
        margin-bottom: 28px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .sidebar-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e3a5f;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 13px 16px;
        background: #dbeafe;
        color: #1e40af;
        border-radius: 12px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .sidebar-item:hover {
        background: #bfdbfe;
        color: #1e3a8a;
    }

    .sidebar-item.active {
        background: #1e3a8a;
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }

    .sidebar-icon {
        font-size: 16px;
        line-height: 1;
        flex-shrink: 0;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        z-index: 850;
    }

    .sidebar-overlay.show {
        display: block;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            z-index: 1100;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .sidebar.open {
            transform: translateX(0);
        }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title">
            <?= htmlspecialchars($sidebarTitle) ?>
        </div>
    </div>

    <nav class="sidebar-menu">
        <?php require __DIR__ . "/sidebar-{$userRole}.php"; ?>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    if (!sidebar || !overlay) return;
    sidebar.classList.toggle("open");
    overlay.classList.toggle("show");
}

function closeSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    if (!sidebar || !overlay) return;
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
}
</script>