
<style>
    .sidebar {
        width: 260px;
        height: 100vh;
        background: #fff;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        padding: 20px 16px;
        box-sizing: border-box;
        box-shadow: 1px 2px 8px #888888;
        position: fixed !important;
    }

    .sidebar-header {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .sidebar-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e3a5f;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        justify-content: center;        
        gap: 8px;
        padding: 12px 16px;
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
    }

    .sidebar-item.active {
        background: #1e3a8a;              /* xanh đậm như ảnh */
        color: #ffffff;
    }

    .sidebar-icon {
        font-size: 16px;
        line-height: 1;
    }

/* Nếu muốn icon chỉ hiện ở Trang chủ thì giữ nguyên,
   các item khác không có .sidebar-icon sẽ tự căn giữa text */
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title">
            TÀI KHOẢN THÀNH VIÊN
        </div>
    </div>

    <nav class="sidebar-menu">
        <!-- TRANG CHỦ -->
        <a href="dashboard.php" class="sidebar-item active" >
            <span class="sidebar-text">Trang chủ</span>
        </a>

        <!-- GIỚI THIỆU -->
        <a href="#" class="sidebar-item">
            <span class="sidebar-text">Giới thiệu</span>
        </a>

        <!-- TIN TỨC -->
        <a href="#" class="sidebar-item">
            <span class="sidebar-text">Tin tức</span>
        </a>

        <!-- SINH HOẠT -->
        <a href="#" class="sidebar-item">
            <span class="sidebar-text">Sinh hoạt</span>
        </a>

        <!-- HOẠT ĐỘNG -->
        <a href="#" class="sidebar-item">
            <span class="sidebar-text">Hoạt động</span>
        </a>

        <!-- LIÊN HỆ -->
        <a href="#" class="sidebar-item">
            <span class="sidebar-text">Liên hệ</span>
        </a>

        <!-- THÔNG TIN CÁ NHÂN (active) -->
        <a href="profile.php" class="sidebar-item">
            <span class="sidebar-text">Thông tin cá nhân</span>
        </a>
    </nav>
</aside>