<?php
require_once 'includes/functions.php';

$eventId = (int)($_GET['id'] ?? 0);
if (!$eventId) {
    header('Location: index.php');
    exit;
}

// Get event details
$stmt = $conn->prepare("SELECT e.*, c.name as club_name, 
        (e.max_participants - e.current_participants) as available_slots
        FROM events e
        JOIN clubs c ON e.club_id = c.id
        WHERE e.id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: index.php');
    exit;
}

// Check if current user is registered
$isRegistered = false;
$registrationStatus = null;
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT status FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$eventId, $_SESSION['user_id']]);
    $reg = $stmt->fetch();
    if ($reg) {
        $isRegistered = true;
        $registrationStatus = $reg['status'];
    }
}

// Check if user can register
$canRegister = false;
$registerMessage = '';
if (isLoggedIn() && !$isRegistered) {
    if ($event['status'] === 'open') {
        if (strtotime($event['registration_deadline']) >= time()) {
            if ($event['max_participants'] == 0 || $event['available_slots'] > 0) {
                $canRegister = true;
            } else {
                $registerMessage = 'Sự kiện đã hết chỗ';
            }
        } else {
            $registerMessage = 'Đã quá hạn đăng ký';
        }
    } else {
        $registerMessage = 'Sự kiện đã đóng đăng ký';
    }
} elseif (!isLoggedIn()) {
    $registerMessage = 'Vui lòng đăng nhập để đăng ký';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($event['title']) ?> - QLyCLB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="event-detail">
            <div class="event-header">
                <h1><?= e($event['title']) ?></h1>
                <span class="badge badge-<?= $event['status'] ?>"><?= ucfirst($event['status']) ?></span>
            </div>
            
            <div class="event-meta">
                <div class="meta-item">
                    <strong>📅 Thời gian:</strong>
                    <?= date('H:i d/m/Y', strtotime($event['start_time'])) ?> - 
                    <?= date('H:i d/m/Y', strtotime($event['end_time'])) ?>
                </div>
                <div class="meta-item">
                    <strong>📍 Địa điểm:</strong> <?= e($event['location']) ?>
                </div>
                <div class="meta-item">
                    <strong>🏢 CLB:</strong> <?= e($event['club_name']) ?>
                </div>
                <div class="meta-item">
                    <strong>👥 Số lượng:</strong> 
                    <?= $event['current_participants'] ?>/<?= $event['max_participants'] ?: '∞' ?>
                    <?php if ($event['max_participants'] > 0): ?>
                        (Còn <?= $event['available_slots'] ?> chỗ)
                    <?php endif; ?>
                </div>
                <div class="meta-item">
                    <strong>⏰ Hạn đăng ký:</strong> 
                    <?= date('H:i d/m/Y', strtotime($event['registration_deadline'])) ?>
                </div>
            </div>
            
            <div class="event-description">
                <h3>Mô tả sự kiện</h3>
                <p><?= nl2br(e($event['description'])) ?></p>
            </div>
            
            <div class="event-actions">
                <?php if ($canRegister): ?>
                    <form id="registerForm" class="inline-form">
                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <button type="submit" class="btn btn-primary btn-large">Đăng ký tham gia</button>
                    </form>
                <?php elseif ($isRegistered && $registrationStatus === 'registered'): ?>
                    <div class="alert alert-info">
                        ✅ Bạn đã đăng ký sự kiện này
                        <form id="cancelForm" class="inline-form" style="margin-top: 10px;">
                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-secondary btn-small">Hủy đăng ký</button>
                        </form>
                    </div>
                <?php elseif ($registerMessage): ?>
                    <div class="alert alert-warning"><?= e($registerMessage) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Handle registration
        document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/register_event.php', {
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
        
        // Handle cancellation
        document.getElementById('cancelForm')?.addEventListener('submit', async function(e) {
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
    </script>
</body>
</html>

</content>