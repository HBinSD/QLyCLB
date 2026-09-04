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
// CHECK ADMIN
// =====================================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}


// =====================================================
// CLUB
// =====================================================

$clubId = "CLB001";


// =====================================================
// GET APPLICATION ID
// =====================================================

$applicationId = trim($_GET['id'] ?? '');

if ($applicationId === '') {

    $_SESSION['error'] = "Không tìm thấy đơn đăng ký.";

    header("Location: accept_member.php");
    exit;
}


// =====================================================
// GET APPLICATION DETAIL
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
        ui.gender,
        ui.avt_links,

        cb.band_name,

        reviewer.fullname AS reviewer_name

    FROM ClubApplication AS ca

    INNER JOIN UserInfo AS ui
        ON ui.username = ca.username

    LEFT JOIN ClubBand AS cb
        ON cb.band_id = ca.desired_band
        AND cb.club_id = ca.club_id

    LEFT JOIN UserInfo AS reviewer
        ON reviewer.username = ca.reviewed_by

    WHERE ca.application_id = :application_id
      AND ca.club_id = :club_id

    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':application_id' => $applicationId,
    ':club_id' => $clubId
]);

$application = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$application) {

    $_SESSION['error'] = "Không tìm thấy đơn đăng ký.";

    header("Location: accept_member.php");
    exit;
}


// =====================================================
// FORMAT DATA
// =====================================================

$fullname = $application['fullname']
    ?: $application['username'];

$avatar = trim($application['avt_link'] ?? '');


// =====================================================
// HEADER
// =====================================================

require_once "../includes/headers.php";

?>

<link
    rel="stylesheet"
    href="css/member_detail.css"
>


<div class="member-detail-page">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="page-header">

        <div>

            <div class="breadcrumb">

                <a href="accept_member.php">
                    Duyệt thành viên
                </a>

                <span> / </span>

                <span>Chi tiết đơn đăng ký</span>

            </div>

            <h1>
                Chi tiết đơn đăng ký
            </h1>

            <p>
                Xem thông tin sinh viên và nội dung đăng ký vào câu lạc bộ.
            </p>

        </div>


        <a
            href="accept_member.php"
            class="btn-back"
        >
            ← Quay lại
        </a>

    </div>


    <!-- =================================================
         STATUS
    ================================================== -->

    <div class="status-banner">

        <div class="status-left">

            <span class="status-label">
                Trạng thái đơn:
            </span>


            <?php if ($application['status'] === 'pending'): ?>

                <span class="status status-pending">
                    ⏳ Chờ duyệt
                </span>

            <?php elseif ($application['status'] === 'approved'): ?>

                <span class="status status-approved">
                    ✓ Đã duyệt
                </span>

            <?php elseif ($application['status'] === 'rejected'): ?>

                <span class="status status-rejected">
                    × Đã từ chối
                </span>

            <?php endif; ?>

        </div>


        <div class="application-date">

            Ngày đăng ký:

            <strong>

                <?= !empty($application['created_at'])
                    ? date(
                        'd/m/Y H:i',
                        strtotime($application['created_at'])
                    )
                    : '-'
                ?>

            </strong>

        </div>

    </div>


    <!-- =================================================
         MAIN GRID
    ================================================== -->

    <div class="detail-grid">


        <!-- =================================================
             LEFT
        ================================================== -->

        <div class="left-column">


            <!-- =============================================
                 PERSONAL INFORMATION
            ============================================== -->

            <div class="detail-card">


                <div class="card-header">

                    <div>

                        <h2>
                            Thông tin sinh viên
                        </h2>

                        <p>
                            Thông tin tài khoản và cá nhân
                        </p>

                    </div>

                </div>


                <div class="profile-section">


                    <div class="profile-avatar">

                        <?php if ($avatar !== ''): ?>

                            <img
                                src="<?= htmlspecialchars($avatar) ?>"
                                alt="Avatar"
                            >

                        <?php else: ?>

                            <?= strtoupper(
                                mb_substr(
                                    $fullname,
                                    0,
                                    1
                                )
                            ) ?>

                        <?php endif; ?>

                    </div>


                    <div class="profile-name">

                        <h3>
                            <?= htmlspecialchars($fullname) ?>
                        </h3>

                        <span>
                            <?= htmlspecialchars(
                                $application['username']
                            ) ?>
                        </span>

                    </div>

                </div>


                <div class="info-grid">


                    <div class="info-item">

                        <span class="info-label">
                            Họ và tên
                        </span>

                        <strong>
                            <?= htmlspecialchars($fullname) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Mã sinh viên
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $application['id_number'] ?: '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Username
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $application['username']
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Email
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $application['email'] ?: '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Ngày sinh
                        </span>

                        <strong>

                            <?php if (!empty($application['DOB'])): ?>

                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $application['DOB']
                                    )
                                ) ?>

                            <?php else: ?>

                                -

                            <?php endif; ?>

                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Số điện thoại
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $application['phone'] ?: '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Giới tính
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $application['gender'] ?: '-'
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Câu lạc bộ
                        </span>

                        <strong>
                            <?= htmlspecialchars($clubId) ?>
                        </strong>

                    </div>


                </div>


            </div>


            <!-- =============================================
                 APPLICATION CONTENT
            ============================================== -->

            <div class="detail-card">


                <div class="card-header">

                    <div>

                        <h2>
                            Nội dung đăng ký
                        </h2>

                        <p>
                            Thông tin sinh viên cung cấp khi đăng ký
                        </p>

                    </div>

                </div>


                <div class="application-content">


                    <div class="content-item">

                        <label>
                            Lý do đăng ký CLB
                        </label>

                        <div class="content-box">

                            <?= nl2br(
                                htmlspecialchars(
                                    $application['reason'] ?: 'Chưa cung cấp'
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="content-item">

                        <label>
                            Mong muốn khi tham gia CLB
                        </label>

                        <div class="content-box">

                            <?= nl2br(
                                htmlspecialchars(
                                    $application['expectation']
                                    ?: 'Chưa cung cấp'
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="content-item">

                        <label>
                            Tài năng / Kỹ năng
                        </label>

                        <div class="content-box">

                            <?= nl2br(
                                htmlspecialchars(
                                    $application['skills']
                                    ?: 'Chưa cung cấp'
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="content-item">

                        <label>
                            Ban muốn tham gia
                        </label>

                        <div class="band-box">

                            <?php if (!empty($application['band_name'])): ?>

                                <span class="band-code">

                                    <?= htmlspecialchars(
                                        $application['desired_band']
                                    ) ?>

                                </span>

                                <span>
                                    -
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $application['band_name']
                                    ) ?>

                                </strong>

                            <?php else: ?>

                                <span>
                                    Chưa chọn ban
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>


                </div>


            </div>


            <!-- =============================================
                 REVIEW INFORMATION
            ============================================== -->

            <?php if ($application['status'] !== 'pending'): ?>

                <div class="detail-card">


                    <div class="card-header">

                        <div>

                            <h2>
                                Thông tin xử lý
                            </h2>

                            <p>
                                Thông tin về quá trình xét duyệt
                            </p>

                        </div>

                    </div>


                    <div class="info-grid">


                        <div class="info-item">

                            <span class="info-label">
                                Trạng thái
                            </span>

                            <strong>

                                <?php if ($application['status'] === 'approved'): ?>

                                    <span class="text-approved">
                                        Đã duyệt
                                    </span>

                                <?php else: ?>

                                    <span class="text-rejected">
                                        Đã từ chối
                                    </span>

                                <?php endif; ?>

                            </strong>

                        </div>


                        <div class="info-item">

                            <span class="info-label">
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


                        <div class="info-item">

                            <span class="info-label">
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


                    </div>


                    <?php if (
                        $application['status'] === 'rejected'
                        && !empty($application['reject_reason'])
                    ): ?>

                        <div class="reject-reason">

                            <span>
                                Lý do từ chối
                            </span>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $application['reject_reason']
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                </div>

            <?php endif; ?>


        </div>


        <!-- =================================================
             RIGHT
        ================================================== -->

        <div class="right-column">


            <!-- =============================================
                 APPLICATION SUMMARY
            ============================================== -->

            <div class="detail-card summary-card">


                <h2>
                    Tổng quan đơn đăng ký
                </h2>


                <div class="summary-item">

                    <span>
                        Mã đơn
                    </span>

                    <strong>
                        #<?= htmlspecialchars(
                            $application['application_id']
                        ) ?>
                    </strong>

                </div>


                <div class="summary-item">

                    <span>
                        Username
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $application['username']
                        ) ?>
                    </strong>

                </div>


                <div class="summary-item">

                    <span>
                        Ban đăng ký
                    </span>

                    <strong>

                        <?php if ($application['band_name']): ?>

                            <?= htmlspecialchars(
                                $application['band_name']
                            ) ?>

                        <?php else: ?>

                            Chưa chọn

                        <?php endif; ?>

                    </strong>

                </div>


                <div class="summary-item">

                    <span>
                        Ngày đăng ký
                    </span>

                    <strong>

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $application['created_at']
                            )
                        ) ?>

                    </strong>

                </div>


            </div>


            <!-- =============================================
                 ACTION
            ============================================== -->

            <?php if ($application['status'] === 'pending'): ?>

                <div class="action-card">


                    <h2>
                        Xử lý đơn đăng ký
                    </h2>


                    <p>
                        Sau khi duyệt, sinh viên sẽ trở thành thành viên chính thức của CLB.
                    </p>


                    <form
                        method="POST"
                        action="approve_member.php"
                        onsubmit="return confirm('Bạn có chắc chắn muốn duyệt sinh viên này?');"
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
                            ✓ Duyệt thành viên
                        </button>

                    </form>


                    <a
                        href="reject_member.php?id=<?= urlencode(
                            $application['application_id']
                        ) ?>"
                        class="btn-reject"
                    >
                        × Từ chối đơn
                    </a>


                </div>

            <?php elseif ($application['status'] === 'approved'): ?>

                <div class="action-card approved-card">

                    <div class="action-icon">
                        ✓
                    </div>

                    <h2>
                        Đã trở thành thành viên
                    </h2>

                    <p>
                        Sinh viên này đã được duyệt vào câu lạc bộ.
                    </p>

                </div>


            <?php elseif ($application['status'] === 'rejected'): ?>

                <div class="action-card rejected-card">

                    <div class="action-icon">
                        ×
                    </div>

                    <h2>
                        Đơn đã bị từ chối
                    </h2>

                    <p>
                        Đơn đăng ký này đã được xử lý và từ chối.
                    </p>

                </div>

            <?php endif; ?>


        </div>


    </div>


</div>
<?php require_once '../includes/footer.php'; ?>
</body>