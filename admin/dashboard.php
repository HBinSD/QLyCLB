<?php
require_once '../includes/functions.php';

// Require organizer or admin role
if (!isLoggedIn() || !(hasRole('organizer') || hasRole('admin'))) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Get statistics based on role
if ($userRole === 'admin') {
    // Admin sees all stats
    $stmt = $conn->query("SELECT COUNT(*) as total_events FROM events");
    $totalEvents = $stmt->fetch()['total_events'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
    $totalUsers = $stmt->fetch()['total_users'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total_registrations FROM registrations WHERE status = 'registered'");
    $totalRegistrations = $stmt->fetch()['total_registrations'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total_clubs FROM clubs");
    $totalClubs = $stmt->fetch()['total_clubs'];
} else {
    // Organizer sees only their club's stats
    $stmt = $conn->prepare("SELECT club_id FROM club_organizers WHERE user_id = ?");
    $stmt->execute([$userId]);
    $clubIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($clubIds)) {
        $totalEvents = $totalRegistrations = 0;
    } else {
        $placeholders = str_repeat('?,', count($clubIds) - 1) . '?';
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total_events FROM events WHERE club_id IN ($placeholders)");
        $stmt->execute($clubIds);
        $totalEvents = $stmt->fetch()['total_events'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total_registrations FROM registrations r 
                JOIN events e ON r.event_id = e.id 
                WHERE e.club_id IN ($placeholders) AND r.status = 'registered'");
        $stmt->execute($clubIds);
        $totalRegistrations = $stmt->fetch()['total_registrations'];
    }
    $totalUsers = $totalClubs = '-';
}

// Get recent events
if ($userRole === 'admin') {
    $stmt = $conn->query("SELECT e.*, c.name as club_name 
            FROM events e 
            JOIN clubs c ON e.club_id = c.id 
            ORDER BY e.created_at DESC LIMIT 5");
} else {
    $placeholders = str_repeat('?,', count($clubIds) - 1) . '?';
    $stmt = $conn->prepare("SELECT e.*, c.name as club_name 
            FROM events e 
            JOIN clubs c ON e.club_id = c.id 
            WHERE e.club_id IN ($placeholders)
            ORDER BY e.created_at DESC LIMIT 5");
    $stmt->execute($clubIds);
}
$recentEvents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển - QLyCLB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h1>Bảng điều khiển</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $totalEvents ?></div>
                <div class="stat-label">Sự kiện</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalRegistrations ?></div>
                <div class="stat-label">Đăng ký</div>
            </div>
            <?php if ($userRole === 'admin'): ?>
                <div class="stat-card">
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-label">Người dùng</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $totalClubs ?></div>
                    <div class="stat-label">CLB</div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-section">
            <h2>Sự kiện gần đây</h2>
            <?php if (empty($recentEvents)): ?>
                <p class="no-data">Chưa có sự kiện nào.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên sự kiện</th>
                            <th>CLB</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Tham gia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $event): ?>
                            <tr>
                                <td>
                                    <a href="../event_detail.php?id=<?= $event['id'] ?>">
                                        <?= e($event['title']) ?>
                                    </a>
                                </td>
                                <td><?= e($event['club_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $event['status'] ?>">
                                        <?= ucfirst($event['status']) ?>
                                    </span>
                                </td>
                                <td><?= $event['current_participants'] ?>/<?= $event['max_participants'] ?: '∞' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-actions">
            <h2>Thao tác nhanh</h2>
            <div class="action-buttons">
                <?php if ($userRole === 'admin'): ?>
                    <a href="manage_users.php" class="btn btn-primary">Quản lý người dùng</a>
                    <a href="manage_clubs.php" class="btn btn-primary">Quản lý CLB</a>
                <?php endif; ?>
                <a href="manage_events.php" class="btn btn-primary">Quản lý sự kiện</a>
                <a href="../index.php" class="btn btn-secondary">Xem trang chủ</a>
            </div>
        </div>
    </main>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

</content>