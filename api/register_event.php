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
    jsonResponse(false, 'Vui lòng đăng nhập để đăng ký sự kiện');
}

$eventId = (int)($_POST['event_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$eventId) {
    jsonResponse(false, 'ID sự kiện không hợp lệ');
}

try {
    // Get event info
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        jsonResponse(false, 'Sự kiện không tồn tại');
    }
    
    // Check if event is open
    if ($event['status'] !== 'open') {
        jsonResponse(false, 'Sự kiện đã đóng hoặc hết chỗ');
    }
    
    // Check deadline
    if (strtotime($event['registration_deadline']) < time()) {
        jsonResponse(false, 'Đã quá hạn đăng ký');
    }
    
    // Check capacity
    if ($event['max_participants'] > 0 && $event['current_participants'] >= $event['max_participants']) {
        jsonResponse(false, 'Sự kiện đã đủ chỗ');
    }
    
    // Check duplicate registration
    $stmt = $conn->prepare("SELECT id FROM registrations WHERE event_id = ? AND user_id = ? AND status != 'cancelled'");
    $stmt->execute([$eventId, $userId]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Bạn đã đăng ký sự kiện này rồi');
    }
    
    // Insert registration
    $stmt = $conn->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
    $stmt->execute([$eventId, $userId]);
    
    // Update participant count
    $stmt = $conn->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE id = ?");
    $stmt->execute([$eventId]);
    
    // Check if full and update status
    $stmt = $conn->prepare("SELECT max_participants, current_participants FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $updatedEvent = $stmt->fetch();
    
    if ($updatedEvent['max_participants'] > 0 && $updatedEvent['current_participants'] >= $updatedEvent['max_participants']) {
        $stmt = $conn->prepare("UPDATE events SET status = 'full' WHERE id = ?");
        $stmt->execute([$eventId]);
    }
    
    // Get updated available slots
    $availableSlots = $updatedEvent['max_participants'] > 0 
        ? $updatedEvent['max_participants'] - $updatedEvent['current_participants'] 
        : null;
    
    jsonResponse(true, 'Đăng ký thành công!', [
        'available_slots' => $availableSlots,
        'current_participants' => $updatedEvent['current_participants']
    ]);
    
} catch (Exception $e) {
    jsonResponse(false, 'Lỗi hệ thống: ' . $e->getMessage());
}

?>