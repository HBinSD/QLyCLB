<?php
require_once 'includes/functions.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Get user's registrations
$stmt = $conn->prepare("SELECT r.*, e.title, e.start_time, e.end_time, e.location, c.name as club_name
        FROM registrations r
        JOIN events e ON r.event_id = e.id
        JOIN clubs c ON e.club_id = c.id
        WHERE r.user_id = ?
        ORDER BY e.start_time DESC");
$stmt->execute([$userId]);
$registrations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký của tôi - QLyCLB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <h1>Đăng ký của tôi</h1>
        
        <?php if (empty($registrations)): ?>
            <div class="no-data">
                <p>Bạn chưa đăng ký sự kiện nào.</p>
                <a href="index.php" class="btn btn-primary">Xem danh sách sự kiện</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sự kiện</th>
                            <th>CLB</th>
                            <th>Thời gian</th>
                            <th>Địa điểm</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td>
                                    <a href="event_detail.php?id=<?= $reg['event_id'] ?>">
                                        <?= e($reg['title']) ?>
                                    </a>
                                </td>
                                <td><?= e($reg['club_name']) ?></td>
                                <td>
                                    <?= date('H:i d/m/Y', strtotime($reg['start_time'])) ?><br>
                                    <small>Đến: <?= date('H:i d/m/Y', strtotime($reg['end_time'])) ?></small>
                                </td>
                                <td><?= e($reg['location']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $reg['status'] ?>">
                                        <?= ucfirst($reg['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reg['status'] === 'registered' && strtotime($reg['start_time']) > time()): ?>
                                        <form id="cancelForm_<?= $reg['event_id'] ?>" class="inline-form">
                                            <input type="hidden" name="event_id" value="<?= $reg['event_id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                            <button type="submit" class="btn btn-secondary btn-small">Hủy</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Handle cancellation for all forms
        document.querySelectorAll('[id^="cancelForm_"]').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (!confirm('Bạn có chắc muốn hủy đăng ký?')) return;
                
                const formData = new FormData(this);
                
                try {
                    const response = await fetch('api/cancel_registration.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message || 'Có lỗi xảy ra');
                    }
                } catch (error) {
                    alert('Lỗi kết nối. Vui lòng thử lại.');
                }
            });
        });
    </script>
</body>
</html>

</content>