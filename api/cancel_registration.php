<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Phương thức không được hỗ trợ');
}

// Validate CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    jsonResponse(false, 'CSRF token không hợp lệ');
}

// Check login
if (!isLoggedIn()) {
    jsonResponse(false, 'Vui lòng đăng nhập');
}

$eventId = (int)($_POST['event_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$eventId) {
    jsonResponse(false, 'ID sự kiện không hợp lệ');
}

try {
    // Check if registration exists and is active
    $stmt = $conn->prepare("SELECT id FROM registrations WHERE event_id = ? AND user_id = ? AND status = 'registered'");
    $stmt->execute([$eventId, $userId]);
    
    if (!$stmt->fetch()) {
        jsonResponse(false, 'Bạn chưa đăng ký sự kiện này hoặc đã hủy trước đó');
    }
    
    // Update registration status to cancelled
    $stmt = $conn->prepare("UPDATE registrations SET status = 'cancelled', updated_at = NOW() WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$eventId, $userId]);
    
    // Decrease participant count
    $stmt = $conn->prepare("UPDATE events SET current_participants = GREATEST(0, current_participants - 1) WHERE id = ?");
    $stmt->execute([$eventId]);
    
    // If event was full, reopen it
    $stmt = $conn->prepare("SELECT max_participants, current_participants, status FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if ($event['status'] === 'full' && $event['current_participants'] < $event['max_participants']) {
        $stmt = $conn->prepare("UPDATE events SET status = 'open' WHERE id = ?");
        $stmt->execute([$eventId]);
    }
    
    jsonResponse(true, 'Hủy đăng ký thành công');
    
} catch (Exception $e) {
    jsonResponse(false, 'Lỗi hệ thống: ' . $e->getMessage());
}

?>