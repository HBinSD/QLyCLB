<?php
$dataFile = __DIR__ . '/data/requests.json';
$requests = [];

if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    $requests = json_decode($content, true) ?: [];
}

// Sắp xếp mới nhất trước
usort($requests, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Đếm theo trạng thái
$countPending  = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
$countApproved = count(array_filter($requests, fn($r) => $r['status'] === 'approved'));
$countRejected = count(array_filter($requests, fn($r) => $r['status'] === 'rejected'));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt yêu cầu gia nhập CLB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container admin-container">
        <header>
            <h1>📋 Quản lý yêu cầu gia nhập</h1>
            <p class="subtitle">Duyệt hoặc từ chối các yêu cầu đăng ký</p>
        </header>

        <div class="stats">
            <div class="stat-card pending">
                <span class="stat-number"><?= $countPending ?></span>
                <span class="stat-label">Chờ duyệt</span>
            </div>
            <div class="stat-card approved">
                <span class="stat-number"><?= $countApproved ?></span>
                <span class="stat-label">Đã duyệt</span>
            </div>
            <div class="stat-card rejected">
                <span class="stat-number"><?= $countRejected ?></span>
                <span class="stat-label">Từ chối</span>
            </div>
        </div>

        <div id="alert" class="alert" style="display: none;"></div>

        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">Tất cả</button>
            <button class="filter-btn" data-filter="pending">Chờ duyệt</button>
            <button class="filter-btn" data-filter="approved">Đã duyệt</button>
            <button class="filter-btn" data-filter="rejected">Từ chối</button>
        </div>

        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <p>Chưa có yêu cầu nào.</p>
                <a href="index.php" class="btn btn-primary">← Quay lại form đăng ký</a>
            </div>
        <?php else: ?>
            <div class="request-list" id="requestList">
                <?php foreach ($requests as $req): ?>
                    <div class="request-card" data-status="<?= htmlspecialchars($req['status']) ?>" data-id="<?= $req['id'] ?>">
                        <div class="request-header">
                            <div class="request-info">
                                <h3><?= htmlspecialchars($req['fullname']) ?></h3>
                                <span class="badge badge-<?= $req['status'] ?>">
                                    <?php
                                    echo match($req['status']) {
                                        'pending'  => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        default    => $req['status']
                                    };
                                    ?>
                                </span>
                            </div>
                            <div class="request-meta">
                                <span>#<?= $req['id'] ?></span>
                                <span><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></span>
                            </div>
                        </div>

                        <div class="request-body">
                            <div class="info-row">
                                <strong>Email:</strong> <?= htmlspecialchars($req['email']) ?>
                            </div>
                            <div class="info-row">
                                <strong>SĐT:</strong> <?= htmlspecialchars($req['phone']) ?>
                            </div>
                            <?php if (!empty($req['student_id'])): ?>
                            <div class="info-row">
                                <strong>Mã SV/CCCD:</strong> <?= htmlspecialchars($req['student_id']) ?>
                            </div>
                            <?php endif; ?>
                            <div class="info-row">
                                <strong>Câu lạc bộ:</strong> <?= htmlspecialchars($req['club']) ?>
                            </div>
                            <div class="info-row">
                                <strong>Lý do:</strong>
                                <p><?= nl2br(htmlspecialchars($req['reason'])) ?></p>
                            </div>
                            <?php if (!empty($req['experience'])): ?>
                            <div class="info-row">
                                <strong>Kinh nghiệm:</strong>
                                <p><?= nl2br(htmlspecialchars($req['experience'])) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($req['note'])): ?>
                            <div class="info-row note">
                                <strong>Ghi chú duyệt:</strong> <?= htmlspecialchars($req['note']) ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($req['status'] === 'pending'): ?>
                        <div class="request-actions">
                            <button class="btn btn-success btn-sm" onclick="reviewRequest(<?= $req['id'] ?>, 'approved')">
                                ✓ Duyệt
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="reviewRequest(<?= $req['id'] ?>, 'rejected')">
                                ✕ Từ chối
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="footer-links">
            <a href="index.php">← Quay lại form đăng ký</a>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
