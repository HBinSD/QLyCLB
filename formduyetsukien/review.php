<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$id     = intval($input['id'] ?? 0);
$status = $input['status'] ?? '';
$note   = trim($input['note'] ?? '');

if ($id <= 0 || !in_array($status, ['approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$dataFile = __DIR__ . '/data/requests.json';

if (!file_exists($dataFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy dữ liệu']);
    exit;
}

$requests = json_decode(file_get_contents($dataFile), true) ?: [];
$found = false;

foreach ($requests as &$req) {
    if ($req['id'] === $id) {
        if ($req['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu này đã được xử lý rồi']);
            exit;
        }
        $req['status'] = $status;
        $req['reviewed_at'] = date('Y-m-d H:i:s');
        $req['note'] = htmlspecialchars($note, ENT_QUOTES, 'UTF-8');
        $found = true;
        break;
    }
}
unset($req);

if (!$found) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu']);
    exit;
}

if (file_put_contents($dataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    $msg = $status === 'approved' ? 'Đã duyệt yêu cầu thành công!' : 'Đã từ chối yêu cầu.';
    echo json_encode(['success' => true, 'message' => $msg]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật dữ liệu']);
}
