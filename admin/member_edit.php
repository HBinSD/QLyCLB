<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

// ========================================
// KIỂM TRA QUYỀN ADMIN
// ========================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}


// ========================================
// DATABASE
// ========================================

$database = new Database();
$db = $database->getConnection();

$clubId = "CLB001";


// ========================================
// LẤY USERNAME
// ========================================

$username = trim($_GET['username'] ?? '');

if ($username === '') {
    header("Location: members.php");
    exit;
}


// ========================================
// XỬ LÝ FORM
// ========================================

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Username lấy từ hidden input
    $username = trim($_POST['username'] ?? '');

    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['DOB'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $idNumber = trim($_POST['id_number'] ?? '');
    $avtLink = trim($_POST['avt_links'] ?? '');

    $position = trim($_POST['position'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $allowedPositions = ['Thành viên', 'Chủ nhiệm'];


    // ====================================
    // VALIDATE
    // ====================================

    if ($username === '') {

        $error = "Username không hợp lệ.";

    } elseif ($fullname === '') {

        $error = "Họ tên không được để trống.";

    } elseif (
        $email !== ''
        && !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $error = "Email không hợp lệ.";

    } elseif (!in_array($position, $allowedPositions, true)) {

        $error = "Vai trò không hợp lệ.";

    } elseif (
        !in_array(
            $status,
            ['active', 'inactive', '1', '0'],
            true
        )
    ) {

        $error = "Trạng thái không hợp lệ.";

    } else {

        try {

            $db->beginTransaction();


            // ====================================
            // KIỂM TRA THÀNH VIÊN
            // ====================================

            $stmt = $db->prepare("
                SELECT username
                FROM ClubMember
                WHERE username = :username
                  AND club_id = :club_id
                LIMIT 1
            ");

            $stmt->execute([
                ':username' => $username,
                ':club_id' => $clubId
            ]);

            $memberExists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$memberExists) {

                throw new Exception(
                    "Không tìm thấy thành viên trong câu lạc bộ."
                );
            }


            // ====================================
            // KIỂM TRA EMAIL TRÙNG
            // ====================================

            if ($email !== '') {

                $stmt = $db->prepare("
                    SELECT username
                    FROM UserInfo
                    WHERE email = :email
                      AND username <> :username
                    LIMIT 1
                ");

                $stmt->execute([
                    ':email' => $email,
                    ':username' => $username
                ]);

                if ($stmt->fetch()) {

                    throw new Exception(
                        "Email này đã được sử dụng bởi tài khoản khác."
                    );
                }
            }


            // ====================================
            // UPDATE USER INFO
            // ====================================

            $stmt = $db->prepare("
                UPDATE UserInfo
                SET
                    fullname = :fullname,
                    email = :email,
                    DOB = :dob,
                    phone = :phone,
                    id_number = :id_number,
                    avt_links = :avt_link,
                    gender = :gender
                WHERE username = :username
            ");

            $stmt->execute([
                ':fullname' => $fullname,

                ':email' => $email !== ''
                    ? $email
                    : null,

                ':dob' => $dob !== ''
                    ? $dob
                    : null,

                ':phone' => $phone !== ''
                    ? $phone
                    : null,

                ':id_number' => $idNumber !== ''
                    ? $idNumber
                    : null,

                ':avt_link' => $avtLink !== ''
                    ? $avtLink
                    : null,

                ':gender' => $gender !== ''
                    ? $gender
                    : null,

                ':username' => $username
            ]);


            // ====================================
            // UPDATE CLUB MEMBER
            // ====================================

            // Nếu chọn thành viên này làm Chủ nhiệm,
            // Chủ nhiệm cũ của CLB sẽ được chuyển xuống Thành viên.
            if ($position === 'Chủ nhiệm') {

                $stmt = $db->prepare("
                    UPDATE ClubMember
                    SET position = 'Thành viên'
                    WHERE club_id = :club_id
                      AND position = 'Chủ nhiệm'
                      AND username <> :username
                ");

                $stmt->execute([
                    ':club_id' => $clubId,
                    ':username' => $username
                ]);
            }

            $stmt = $db->prepare("
                UPDATE ClubMember
                SET
                    position = :position,
                    status = :status
                WHERE username = :username
                  AND club_id = :club_id
            ");

            $stmt->execute([
                ':position' => $position !== ''
                    ? $position
                    : null,

                ':status' => $status,

                ':username' => $username,

                ':club_id' => $clubId
            ]);


            // ====================================
            // COMMIT
            // ====================================

            $db->commit();

            $success = "Cập nhật thông tin thành viên thành công.";

        } catch (Exception $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $error = $e->getMessage();
        }
    }
}


// ========================================
// LẤY THÔNG TIN THÀNH VIÊN THEO USERNAME
// ========================================

$stmt = $db->prepare("
    SELECT

        cm.username,
        cm.club_id,
        cm.joined_at,
        cm.position,
        cm.status,

        ui.fullname,
        ui.email,
        ui.phone,
        ui.DOB,
        ui.id_number,
        ui.avt_links,
        ui.gender

    FROM ClubMember cm

    INNER JOIN UserInfo ui
        ON ui.username = cm.username

    WHERE cm.username = :username
      AND cm.club_id = :club_id

    LIMIT 1
");

$stmt->execute([
    ':username' => $username,
    ':club_id' => $clubId
]);

$member = $stmt->fetch(PDO::FETCH_ASSOC);


// ========================================
// KHÔNG TÌM THẤY
// ========================================

if (!$member) {

    header("Location: members.php");
    exit;
}


// ========================================
// HEADER
// ========================================

$pageTitle = "Chỉnh sửa thành viên";
$activeMenu = "members.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/member_edit.css">


<div class="member-edit-page">

    <!-- ================================= -->
    <!-- HEADER -->
    <!-- ================================= -->

    <div class="edit-header">

        <div>

            <a href="members.php" class="back-link">
                ← Quay lại danh sách
            </a>

            <h1>
                Chỉnh sửa thành viên
            </h1>

            <p>
                Cập nhật thông tin thành viên trong CLB
            </p>

        </div>

    </div>


    <!-- ================================= -->
    <!-- MESSAGE
    ================================= -->

    <?php if ($error !== ''): ?>

        <div class="alert error">

            ⚠️

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <?php if ($success !== ''): ?>

        <div class="alert success">

            ✓

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <!-- ================================= -->
    <!-- FORM
    ================================= -->

    <form
        method="POST"
        class="edit-form"
    >

        <!--
            Giữ username để xử lý POST
        -->

        <input
            type="hidden"
            name="username"
            value="<?= htmlspecialchars(
                $member['username']
            ) ?>"
        >


        <!-- ================================= -->
        <!-- THÔNG TIN CÁ NHÂN
        ================================= -->

        <div class="form-card">

            <div class="card-title">

                <h2>
                    Thông tin cá nhân
                </h2>

                <p>
                    Thông tin tài khoản của thành viên
                </p>

            </div>


            <div class="form-body">


                <!-- AVATAR -->

                <div class="avatar-section">

                    <?php if (!empty($member['avt_links'])): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $member['avt_links']
                            ) ?>"
                            class="edit-avatar"
                            alt="Avatar"
                            onerror="
                                this.style.display='none';
                                document.getElementById(
                                    'avatarFallback'
                                ).style.display='flex';
                            "
                        >

                        <div
                            id="avatarFallback"
                            class="avatar-fallback"
                            style="display:none;"
                        >
                            👤
                        </div>

                    <?php else: ?>

                        <div class="avatar-fallback">
                            👤
                        </div>

                    <?php endif; ?>

                </div>


                <!-- USERNAME -->

                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars(
                            $member['username']
                        ) ?>"
                        disabled
                    >

                    <small>
                        Username không thể thay đổi.
                    </small>

                </div>


                <!-- FULLNAME -->

                <div class="form-group">

                    <label>
                        Họ và tên
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="fullname"
                        value="<?= htmlspecialchars(
                            $member['fullname'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <!-- ID NUMBER -->

                <div class="form-group">

                    <label>
                        MSSV / CCCD
                    </label>

                    <input
                        type="text"
                        name="id_number"
                        value="<?= htmlspecialchars(
                            $member['id_number'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars(
                            $member['email'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        Số điện thoại
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="<?= htmlspecialchars(
                            $member['phone'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- DOB -->

                <div class="form-group">

                    <label>
                        Ngày sinh
                    </label>

                    <input
                        type="date"
                        name="DOB"
                        value="<?=
                            !empty($member['DOB'])
                                ? date(
                                    'Y-m-d',
                                    strtotime(
                                        $member['DOB']
                                    )
                                )
                                : ''
                        ?>"
                    >

                </div>


                <!-- GENDER -->

                <div class="form-group">

                    <label>
                        Giới tính
                    </label>

                    <select name="gender">

                        <option value="">
                            -- Chọn giới tính --
                        </option>

                        <option
                            value="Nam"
                            <?= ($member['gender'] ?? '') === 'Nam'
                                ? 'selected'
                                : '' ?>
                        >
                            Nam
                        </option>

                        <option
                            value="Nữ"
                            <?= ($member['gender'] ?? '') === 'Nữ'
                                ? 'selected'
                                : '' ?>
                        >
                            Nữ
                        </option>

                        <option
                            value="Khác"
                            <?= ($member['gender'] ?? '') === 'Khác'
                                ? 'selected'
                                : '' ?>
                        >
                            Khác
                        </option>

                    </select>

                </div>


                <!-- AVATAR -->

                <div class="form-group full">

                    <label>
                        Link Avatar
                    </label>

                    <input
                        type="url"
                        name="avt_links"
                        value="<?= htmlspecialchars(
                            $member['avt_links'] ?? ''
                        ) ?>"
                        placeholder="https://..."
                    >

                </div>

            </div>

        </div>


        <!-- ================================= -->
        <!-- THÔNG TIN CLB
        ================================= -->

        <div class="form-card">

            <div class="card-title">

                <h2>
                    Thông tin thành viên CLB
                </h2>

                <p>
                    Thông tin về vị trí và trạng thái
                </p>

            </div>


            <div class="form-body">


                <!-- POSITION -->

                <div class="form-group">

                    <label>
                        Vai trò trong CLB
                    </label>

                    <select name="position" required>

                        <option
                            value="Thành viên"
                            <?= ($member['position'] ?? '') === 'Thành viên'
                                ? 'selected'
                                : '' ?>
                        >
                            Thành viên
                        </option>

                        <option
                            value="Chủ nhiệm"
                            <?= ($member['position'] ?? '') === 'Chủ nhiệm'
                                ? 'selected'
                                : '' ?>
                        >
                            👑 Chủ nhiệm
                        </option>

                    </select>

                    <small>
                        Nếu chọn Chủ nhiệm, Chủ nhiệm hiện tại sẽ tự động
                        chuyển xuống Thành viên.
                    </small>

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Trạng thái
                    </label>

                    <select name="status">

                        <option
                            value="1"
                            <?= (string)$member['status'] === '1'
                                ? 'selected'
                                : '' ?>
                        >
                            Đang hoạt động
                        </option>

                        <option
                            value="0"
                            <?= (string)$member['status'] === '0'
                                ? 'selected'
                                : '' ?>
                        >
                            Không hoạt động
                        </option>

                    </select>


                </div>


                <!-- JOIN DATE -->

                <div class="form-group">

                    <label>
                        Ngày tham gia
                    </label>

                    <input
                        type="text"
                        value="<?=
                            !empty($member['joined_at'])
                                ? date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $member['joined_at']
                                    )
                                )
                                : '--'
                        ?>"
                        disabled
                    >

                </div>

            </div>

        </div>


        <!-- ================================= -->
        <!-- BUTTON
        ================================= -->

        <div class="form-actions">

            <a
                href="members.php"
                class="btn-cancel"
            >
                Hủy
            </a>

            <button
                type="submit"
                class="btn-save"
            >
                💾 Lưu thay đổi
            </button>

        </div>

    </form>

</div>
<?php require_once '../includes/footer.php' ?>
</body>