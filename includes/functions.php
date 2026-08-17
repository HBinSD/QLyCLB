<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Check user role
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    if ($user['role'] === 'admin') return true;
    return $user['role'] === $role;
}

// Check if user is organizer of a club
function isClubOrganizer($clubId) {
    global $conn;
    $user = getCurrentUser();
    if (!$user) return false;
    
    if ($user['role'] === 'admin') return true;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM club_organizers WHERE club_id = ? AND user_id = ?");
    $stmt->execute([$clubId, $_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['count'] > 0;
}

// Send JSON response
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Format datetime for display
function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

// Escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>