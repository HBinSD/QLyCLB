<?php
require_once 'includes/functions.php';

// Get filter parameters
$clubFilter = $_GET['club'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Build query
$sql = "SELECT e.*, c.name as club_name, 
        (e.max_participants - e.current_participants) as available_slots
        FROM events e
        JOIN clubs c ON e.club_id = c.id
        WHERE 1=1";
$params = [];

if ($clubFilter) {
    $sql .= " AND e.club_id = ?";
    $params[] = $clubFilter;
}

if ($dateFilter) {
    $sql .= " AND DATE(e.start_time) = ?";
    $params[] = $dateFilter;
}

if ($statusFilter) {
    $sql .= " AND e.status = ?";
    $params[] = $statusFilter;
} else {
    // Only show open events by default
    $sql .= " AND e.status = 'open'";
}

$sql .= " ORDER BY e.start_time ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Get clubs for filter
$clubs = $conn->query("SELECT id, name FROM clubs ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sự kiện - QLyCLB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <h1>Danh sách sự kiện</h1>
        
        <!-- Filter Form -->
        <div class="filter-box">
            <form method="GET" action="" class="filter-form">
                <select name="club">
                    <option value="">Tất cả CLB</option>
                    <?php foreach ($clubs as $club): ?>
                        <option value="<?= e($club['id']) ?>" <?= $clubFilter == $club['id'] ? 'selected' : '' ?>>
                            <?= e($club['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="date" value="<?= e($dateFilter) ?>">
                
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="open" <?= $statusFilter == 'open' ? 'selected' : '' ?>>Đang mở</option>
                    <option value="closed" <?= $statusFilter == 'closed' ? 'selected' : '' ?>>Đã đóng</option>
                    <option value="full" <?= $statusFilter == 'full' ? 'selected' : '' ?>>Hết chỗ</option>
                </select>
                
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="index.php" class="btn btn-secondary">Xóa bộ lọc</a>
            </form>
        </div>
        
        <!-- Events List -->
        <div class="events-grid">
            <?php if (empty($events)): ?>
                <p class="no-data">Không có sự kiện nào phù hợp.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <span class="club-badge"><?= e($event['club_name']) ?></span>
                            <span class="status-badge status-<?= e($event['status']) ?>">
                                <?= $event['status'] === 'open' ? 'Đang mở' : ($event['status'] === 'full' ? 'Hết chỗ' : 'Đã đóng') ?>
                            </span>
                        </div>
                        
                        <h3><a href="event_detail.php?id=<?= $event['id'] ?>"><?= e($event['title']) ?></a></h3>
                        
                        <div class="event-info">
                            <p>📅 <?= formatDateTime($event['start_time']) ?></p>
                            <p>📍 <?= e($event['location']) ?></p>
                            <p>👥 <?= $event['current_participants'] ?>/<?= $event['max_participants'] ?: '∞' ?> người</p>
                            <?php if ($event['max_participants'] > 0): ?>
                                <p>Còn lại: <?= $event['available_slots'] ?> chỗ</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="event-footer">
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="assets/js/main.js"></script>
</body>
</html>

</content>