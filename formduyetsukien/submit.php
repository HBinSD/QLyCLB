<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit;
}

// Lấy và làm sạch dữ liệu
$fullname     = trim($_POST['fullname'] ?? '');
$email       = trim($_POST['email'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$student_id  = trim($_POST['student_id'] ?? '');
$club        = trim($_POST['club'] ?? '');
$reason      = trim($_POST['reason'] ?? '');
$experience  = trim($_POST['experience'] ?? '');

// Validate
$errors = [];

if (empty($fullname) || strlen($fullname) < 2) {
    $errors[] = 'Họ và tên không hợp lệ';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không hợp lệ';
}

if (empty($phone) || !preg_match('/^[0-9]{9,11}$/', $phone)) {
    $errors[] = 'Số điện thoại phải gồm 9-11 chữ số';
}

if (empty($club)) {
    $errors[] = 'Vui lòng chọn câu lạc bộ';
}

if (empty($reason) || strlen($reason) < 10) {
    $errors[] = 'Lý do gia nhập phải có ít nhất 10 ký tự';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

// Đường dẫn file lưu dữ liệu
$dataFile = __DIR__ . '/data/requests.json';

// Tạo thư mục data nếu chưa có
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Đọc dữ liệu hiện có
$requests = [];
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    $requests = json_decode($content, true) ?: [];
}

// Tạo ID mới
$newId = empty($requests) ? 1 : max(array_column($requests, 'id')) + 1;

// Tạo yêu cầu mới
$newRequest = [
    'id'          => $newId,
    'fullname'    => htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'),
    'email'       => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
    'phone'       => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
    'student_id'  => htmlspecialchars($student_id, ENT_QUOTES, 'UTF-8'),
    'club'        => htmlspecialchars($club, ENT_QUOTES, 'UTF-8'),
    'reason'      => htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'),
    'experience'  => htmlspecialchars($experience, ENT_QUOTES, 'UTF-8'),
    'status'      => 'pending', // pending | approved | rejected
    'created_at'  => date('Y-m-d H:i:s'),
    'reviewed_at' => null,
    'note'        => ''
];

$requests[] = $newRequest;

// Lưu lại
if (file_put_contents($dataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Gửi yêu cầu thành công! Yêu cầu của bạn đang chờ duyệt.',
        'id'      => $newId
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể lưu dữ liệu. Vui lòng thử lại.']);
}
