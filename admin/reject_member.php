<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";


// =====================================================
// CHECK ADMIN
// =====================================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {

    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}


// =====================================================
// DATABASE
// =====================================================

$database = new Database();
$db = $database->getConnection();


// =====================================================
// APPLICATION ID
// =====================================================

$applicationId =
    trim($_GET['id'] ?? '');

$clubId = "CLB001";


if ($applicationId === '') {

    header("Location: members.php");

    exit;
}


// =====================================================
// GET APPLICATION
// =====================================================

$sql = "
    SELECT
        ca.*,
        ui.fullname,
        ui.email,
        ui.id_number,
        cb.band_name

    FROM ClubApplication AS ca

    INNER JOIN UserInfo AS ui
        ON ui.username = ca.username

    LEFT JOIN ClubBand AS cb
        ON cb.band_id = ca.desired_band
        AND cb.club_id = ca.club_id

    WHERE ca.application_id = :application_id
      AND ca.club_id = :club_id
    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':application_id' => $applicationId,
    ':club_id' => $clubId
]);

$application =
    $stmt->fetch(PDO::FETCH_ASSOC);


if (!$application) {

    $_SESSION['error'] =
        "Không tìm thấy đơn đăng ký.";

    header("Location: members.php");

    exit;
}


if ($application['status'] !== 'pending') {

    $_SESSION['error'] =
        "Đơn đăng ký này đã được xử lý.";

    header("Location: members.php");

    exit;
}


// =====================================================
// POST
// =====================================================

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reason =
        trim($_POST['reject_reason'] ?? '');


    if ($reason === '') {

        $error =
            "Vui lòng nhập lý do từ chối.";

    } elseif (mb_strlen($reason) > 500) {

        $error =
            "Lý do từ chối không được quá 500 ký tự.";

    } else {

        try {

            $sql = "
                UPDATE ClubApplication
                SET
                    status = 'rejected',
                    reviewed_by = :reviewed_by,
                    reviewed_at = NOW(),
                    reject_reason = :reject_reason
                WHERE application_id = :application_id
                  AND club_id = :club_id
                  AND status = 'pending'
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':reviewed_by' => $user['username'],
                ':reject_reason' => $reason,
                ':application_id' => $applicationId,
                ':club_id' => $clubId
            ]);


            $_SESSION['success'] =
                "Đã từ chối đơn đăng ký.";


            header("Location: members.php");

            exit;


        } catch (PDOException $e) {

            $error =
                "Có lỗi xảy ra khi xử lý đơn.";
        }
    }
}


// =====================================================
// HEADER
// =====================================================

require_once "../includes/headers.php";

?>

<link
    rel="stylesheet"
    href="css/reject_member.css"
>


<div class="reject-page">


    <div class="reject-card">


        <div class="reject-header">

            <h1>
                Từ chối đơn đăng ký
            </h1>

            <p>
                Vui lòng nhập lý do để sinh viên biết lý do đơn bị từ chối.
            </p>

        </div>


        <?php if ($error): ?>

            <div class="alert-error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- MEMBER -->

        <div class="member-preview">


            <div class="avatar">

                <?= strtoupper(
                    mb_substr(
                        $application['fullname']
                        ?: $application['username'],
                        0,
                        1
                    )
                ) ?>

            </div>


            <div>

                <h3>

                    <?= htmlspecialchars(
                        $application['fullname']
                        ?: $application['username']
                    ) ?>

                </h3>

                <p>

                    MSSV:
                    <?= htmlspecialchars(
                        $application['id_number']
                    ) ?>

                </p>

                <p>

                    Ban:

                    <?= htmlspecialchars(
                        $application['band_name']
                        ?: 'Chưa chọn'
                    ) ?>

                </p>

            </div>


        </div>


        <!-- FORM -->

        <form
            method="POST"
        >


            <div class="form-group">

                <label for="reject_reason">

                    Lý do từ chối

                    <span>*</span>

                </label>


                <textarea
                    name="reject_reason"
                    id="reject_reason"
                    rows="6"
                    placeholder="Nhập lý do từ chối đơn đăng ký..."
                    required
                ></textarea>


            </div>


            <div class="button-row">


                <a
                    href="accept_members.php"
                    class="btn-back"
                >
                    Quay lại
                </a>


                <button
                    type="submit"
                    class="btn-reject"
                    onclick="return confirm('Bạn có chắc muốn từ chối đơn đăng ký này?');"
                >
                    Xác nhận từ chối
                </button>


            </div>


        </form>


    </div>

</div>

<?php require_once '../includes/footer.php'; ?>

</body>