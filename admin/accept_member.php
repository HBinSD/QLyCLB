<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";


// =====================================================
// DATABASE
// =====================================================

$database = new Database();
$db = $database->getConnection();


// =====================================================
// KIỂM TRA QUYỀN ADMIN
// =====================================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {

    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}


$clubId = "CLB001";

$keyword = trim($_GET['keyword'] ?? '');


// =====================================================
// FILTER STATUS
// =====================================================

$status = $_GET['status'] ?? 'pending';

$allowedStatus = [
    'pending',
    'approved',
    'rejected'
];

if (!in_array($status, $allowedStatus, true)) {
    $status = 'pending';
}


// =====================================================
// THÔNG BÁO
// =====================================================

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';

unset($_SESSION['success']);
unset($_SESSION['error']);


// =====================================================
// ĐẾM SỐ ĐƠN
// =====================================================

$countSql = "
    SELECT
        SUM( CASE WHEN status = 'pending' THEN 1 ELSE 0 END ) AS pending_count,
        SUM( CASE WHEN status = 'approved' THEN 1 ELSE 0 END ) AS approved_count,
        SUM( CASE WHEN status = 'rejected' THEN 1 ELSE 0 END ) AS rejected_count
    FROM ClubApplication
    WHERE club_id = :club_id
";

$countStmt = $db->prepare($countSql);

$countStmt->execute([
    ':club_id' => $clubId
]);

$countData = $countStmt->fetch(PDO::FETCH_ASSOC);

$pendingCount = (int)($countData['pending_count'] ?? 0);
$approvedCount = (int)($countData['approved_count'] ?? 0);
$rejectedCount = (int)($countData['rejected_count'] ?? 0);


// =====================================================
// LẤY DANH SÁCH ĐƠN
// =====================================================

$sql = "
    SELECT
        ca.application_id,
        ca.username,
        ca.club_id,
        ca.reason,
        ca.expectation,
        ca.skills,
        ca.desired_band,
        ca.status,
        ca.created_at,
        ca.reviewed_by,
        ca.reviewed_at,
        ca.reject_reason,

        ui.fullname,
        ui.email,
        ui.DOB,
        ui.id_number,
        ui.phone,

        cb.band_name,

        reviewerInfo.fullname AS reviewer_name

    FROM ClubApplication AS ca

    INNER JOIN UserInfo AS ui
        ON ui.username = ca.username

    LEFT JOIN ClubBand AS cb
        ON cb.band_id = ca.desired_band
        AND cb.club_id = ca.club_id

    LEFT JOIN UserInfo AS reviewerInfo
        ON reviewerInfo.username = ca.reviewed_by

    WHERE ca.club_id = :club_id
      AND ca.status = :status
";

$params = [
    ':club_id' => $clubId,
    ':status' => $status
];


// =====================================================
// SEARCH
// =====================================================

if ($keyword !== '') {

    $sql .= "
        AND (
            ca.username LIKE :keyword
            OR ui.fullname LIKE :keyword
            OR ui.email LIKE :keyword
            OR ui.id_number LIKE :keyword
            OR cb.band_name LIKE :keyword
        )
    ";

    $params[':keyword'] = '%' . $keyword . '%';
}


$sql .= "
    ORDER BY ca.created_at DESC
";

$stmt = $db->prepare($sql);

$stmt->execute($params);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================================
// HEADER
// =====================================================

require_once "../includes/headers.php";

?>

<link
    rel="stylesheet"
    href="css/accept_member.css"
>


<div class="admin-members-page">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="page-header">

        <div>

            <h1>
                Duyệt thành viên
            </h1>

            <p>
                Quản lý và xét duyệt đơn đăng ký vào câu lạc bộ.
            </p>

        </div>

    </div>


    <!-- =================================================
         STATISTICS
    ================================================== -->

    <div class="statistics">


        <div class="stat-card">

            <div class="stat-icon pending-icon">
                ⏳
            </div>

            <div>

                <span>
                    Chờ duyệt
                </span>

                <strong>
                    <?= $pendingCount ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon approved-icon">
                ✓
            </div>

            <div>

                <span>
                    Đã duyệt
                </span>

                <strong>
                    <?= $approvedCount ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon rejected-icon">
                ×
            </div>

            <div>

                <span>
                    Từ chối
                </span>

                <strong>
                    <?= $rejectedCount ?>
                </strong>

            </div>

        </div>


    </div>


    <!-- =================================================
         MESSAGE
    ================================================== -->

    <?php if ($success): ?>

        <div class="alert alert-success">

            ✓
            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert alert-error">

            !
            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         FILTER
    ================================================== -->

    <div class="filter-box">

        <form
            method="GET"
            action="members.php"
        >


            <div class="search-box">

                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm username, họ tên, MSSV, email..."
                    value="<?= htmlspecialchars($keyword) ?>"
                >

                <button
                    type="submit"
                    class="btn-search"
                >
                    Tìm kiếm
                </button>

            </div>


            <div class="status-filter">

                <a
                    href="accept_member.php?status=pending"
                    class="<?= $status === 'pending' ? 'active' : '' ?>"
                >
                    Chờ duyệt
                    <span><?= $pendingCount ?></span>
                </a>

                <a
                    href="accept_member.php?status=approved"
                    class="<?= $status === 'approved' ? 'active' : '' ?>"
                >
                    Đã duyệt
                    <span><?= $approvedCount ?></span>
                </a>

                <a
                    href="accept_member.php?status=rejected"
                    class="<?= $status === 'rejected' ? 'active' : '' ?>"
                >
                    Từ chối
                    <span><?= $rejectedCount ?></span>
                </a>

            </div>

        </form>

    </div>


    <!-- =================================================
         APPLICATION LIST
    ================================================== -->

    <div class="application-card">


        <div class="card-title">

            <div>

                <?php if ($status === 'pending'): ?>

                    <h2>
                        Danh sách đơn chờ duyệt
                    </h2>

                <?php elseif ($status === 'approved'): ?>

                    <h2>
                        Danh sách thành viên đã duyệt
                    </h2>

                <?php elseif ($status === 'rejected'): ?>

                    <h2>
                        Danh sách đơn đã từ chối
                    </h2>

                <?php endif; ?>


                <p>
                    <?= count($applications) ?> đơn đăng ký
                </p>

            </div>

        </div>


        <?php if (empty($applications)): ?>

            <div class="empty-state">

                <div class="empty-icon">
                    📭
                </div>

                <h3>
                    Không có đơn đăng ký
                </h3>

                <p>
                    Hiện tại không có đơn nào trong danh sách này.
                </p>

            </div>


        <?php else: ?>


            <div class="application-list">


                <?php foreach ($applications as $application): ?>


                    <div class="application-item">


                        <!-- =================================
                             USER INFO
                        ================================== -->

                        <div class="member-info">


                            <div class="avatar">

                                <?= strtoupper(
                                    mb_substr(
                                        $application['fullname'] ?: $application['username'],
                                        0,
                                        1
                                    )
                                ) ?>

                            </div>


                            <div class="member-main">

                                <h3>

                                    <?= htmlspecialchars(
                                        $application['fullname']
                                        ?: $application['username']
                                    ) ?>

                                </h3>


                                <div class="member-meta">

                                    <span>
                                        Username:
                                        <b>
                                            <?= htmlspecialchars(
                                                $application['username']
                                            ) ?>
                                        </b>
                                    </span>


                                    <span>
                                        MSSV:
                                        <b>
                                            <?= htmlspecialchars(
                                                $application['id_number']
                                            ) ?>
                                        </b>
                                    </span>


                                    <span>
                                        Email:
                                        <b>
                                            <?= htmlspecialchars(
                                                $application['email']
                                            ) ?>
                                        </b>
                                    </span>

                                </div>

                            </div>


                        </div>


                        <!-- =================================
                             APPLICATION INFO
                        ================================== -->

                        <div class="application-info">

                            <div class="info-row">

                                <span>
                                    Ban đăng ký
                                </span>

                                <strong>

                                    <?php if (!empty($application['band_name'])): ?>

                                        <?= htmlspecialchars(
                                            $application['desired_band']
                                        ) ?>

                                        -

                                        <?= htmlspecialchars(
                                            $application['band_name']
                                        ) ?>

                                    <?php else: ?>

                                        Chưa chọn

                                    <?php endif; ?>

                                </strong>

                            </div>


                            <div class="info-row">

                                <span>
                                    Ngày đăng ký
                                </span>

                                <strong>

                                    <?= !empty($application['created_at'])
                                        ? date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $application['created_at']
                                            )
                                        )
                                        : '-'
                                    ?>

                                </strong>

                            </div>


                            <?php if ($status === 'approved'): ?>

                                <div class="info-row">

                                    <span>
                                        Người duyệt
                                    </span>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $application['reviewer_name']
                                            ?: $application['reviewed_by']
                                            ?: '-'
                                        ) ?>

                                    </strong>

                                </div>


                                <div class="info-row">

                                    <span>
                                        Thời gian duyệt
                                    </span>

                                    <strong>

                                        <?= !empty($application['reviewed_at'])
                                            ? date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $application['reviewed_at']
                                                )
                                            )
                                            : '-'
                                        ?>

                                    </strong>

                                </div>

                            <?php endif; ?>


                            <?php if ($status === 'rejected'): ?>

                                <div class="info-row">

                                    <span>
                                        Người xử lý
                                    </span>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $application['reviewer_name']
                                            ?: $application['reviewed_by']
                                            ?: '-'
                                        ) ?>

                                    </strong>

                                </div>


                                <div class="info-row">

                                    <span>
                                        Thời gian xử lý
                                    </span>

                                    <strong>

                                        <?= !empty($application['reviewed_at'])
                                            ? date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $application['reviewed_at']
                                                )
                                            )
                                            : '-'
                                        ?>

                                    </strong>

                                </div>


                                <?php if (!empty($application['reject_reason'])): ?>

                                    <div class="reject-reason-small">

                                        <span>
                                            Lý do:
                                        </span>

                                        <?= htmlspecialchars(
                                            $application['reject_reason']
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            <?php endif; ?>

                        </div>


                        <!-- =================================
                             ACTION
                        ================================== -->

                        <div class="application-action">

                            <a
                                href="member_detail.php?id=<?= urlencode($application['application_id']) ?>"
                                class="btn-detail"
                            >
                                Xem chi tiết
                            </a>


                            <?php if ($status === 'pending'): ?>

                                <form
                                    method="POST"
                                    action="approve_member.php"
                                    onsubmit="return confirm('Bạn có chắc muốn duyệt thành viên này?');"
                                >

                                    <input
                                        type="hidden"
                                        name="application_id"
                                        value="<?= htmlspecialchars(
                                            $application['application_id']
                                        ) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn-approve"
                                    >
                                        ✓ Duyệt
                                    </button>

                                </form>


                                <a
                                    href="reject_member.php?id=<?= urlencode(
                                        $application['application_id']
                                    ) ?>"
                                    class="btn-reject"
                                >
                                    Từ chối
                                </a>


                            <?php elseif ($status === 'approved'): ?>

                                <span class="status-badge status-approved">
                                    ✓ Đã duyệt
                                </span>


                            <?php elseif ($status === 'rejected'): ?>

                                <span class="status-badge status-rejected">
                                    × Đã từ chối
                                </span>

                            <?php endif; ?>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>


</div>

<?php require_once '../includes/footer.php'; ?>
</body>