<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

// ======================================================
// KIỂM TRA QUYỀN ADMIN
// ======================================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}


// ======================================================
// DATABASE
// ======================================================

$database = new Database();
$db = $database->getConnection();

$clubId = "CLB001";

$error = "";
$success = "";


// ======================================================
// XỬ LÝ UPDATE THÔNG TIN CLB
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $clubName   = trim($_POST['club_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $rule       = trim($_POST['rule'] ?? '');

    if ($clubName === '') {

        $error = "Tên câu lạc bộ không được để trống.";

    } else {

        try {

            $stmt = $db->prepare("
                UPDATE Clubs
                SET
                    club_name = :club_name,
                    description = :description,
                    rule = :rule
                WHERE club_id = :club_id
            ");

            $stmt->execute([
                ':club_name' => $clubName,
                ':description' => $description !== ''
                    ? $description
                    : null,
                ':rule' => $rule !== ''
                    ? $rule
                    : null,
                ':club_id' => $clubId
            ]);

            $success = "Cập nhật thông tin câu lạc bộ thành công.";

        } catch (PDOException $e) {

            $error = "Không thể cập nhật câu lạc bộ: "
                   . $e->getMessage();
        }
    }
}


// ======================================================
// LẤY THÔNG TIN CLB
// ======================================================

$stmt = $db->prepare("
    SELECT
        club_id,
        club_name,
        description,
        rule,
        created_at,
        created_by,
        status
    FROM Clubs
    WHERE club_id = :club_id
    LIMIT 1
");

$stmt->execute([
    ':club_id' => $clubId
]);

$club = $stmt->fetch(PDO::FETCH_ASSOC);


// ======================================================
// KHÔNG TÌM THẤY CLB
// ======================================================

if (!$club) {

    $error = "Không tìm thấy thông tin câu lạc bộ.";

    $club = [
        'club_id' => $clubId,
        'club_name' => '',
        'description' => '',
        'rule' => '',
        'created_at' => null,
        'created_by' => '',
        'status' => ''
    ];
}


// ======================================================
// LẤY DANH SÁCH BAN
// ======================================================

$stmt = $db->prepare("
    SELECT
        band_id,
        club_id,
        band_name
    FROM ClubBand
    WHERE club_id = :club_id
    ORDER BY band_id ASC
");

$stmt->execute([
    ':club_id' => $clubId
]);

$bands = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ======================================================
// HEADER
// ======================================================

$pageTitle = "Quản lý câu lạc bộ";
$activeMenu = "club.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/clubs.css">


<div class="club-management-page">

    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="page-header">

        <div>

            <h1>
                Quản lý câu lạc bộ
            </h1>

            <p>
                Quản lý thông tin và các ban trong câu lạc bộ.
            </p>

        </div>

    </div>


    <!-- ==================================================
         MESSAGE
    ================================================== -->

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


    <!-- ==================================================
         THÔNG TIN CLB
    ================================================== -->

    <div class="club-card">

        <div class="card-header">

            <div>

                <h2>
                    Thông tin câu lạc bộ
                </h2>

                <p>
                    Cập nhật thông tin cơ bản của CLB.
                </p>

            </div>

            <span class="club-id">
                <?= htmlspecialchars($club['club_id']) ?>
            </span>

        </div>


        <form method="POST">

            <div class="form-body">

                <!-- TÊN CLB -->

                <div class="form-group">

                    <label for="club_name">
                        Tên câu lạc bộ
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="club_name"
                        name="club_name"
                        value="<?= htmlspecialchars(
                            $club['club_name'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <!-- DESCRIPTION -->

                <div class="form-group">

                    <label for="description">
                        Giới thiệu / Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Nhập giới thiệu về câu lạc bộ..."
                    ><?= htmlspecialchars(
                        $club['description'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- RULE -->

                <div class="form-group">

                    <label for="rule">
                        Nội quy câu lạc bộ
                    </label>

                    <textarea
                        id="rule"
                        name="rule"
                        rows="8"
                        placeholder="Nhập nội quy của câu lạc bộ..."
                    ><?= htmlspecialchars(
                        $club['rule'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- INFO -->

                <div class="club-info-grid">

                    <div class="info-item">

                        <span class="info-label">
                            Mã CLB
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $club['club_id']
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Trạng thái
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $club['status'] ?? '--'
                            ) ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Ngày tạo
                        </span>

                        <strong>
                            <?=
                                !empty($club['created_at'])
                                ? date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $club['created_at']
                                    )
                                )
                                : '--'
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Người tạo
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $club['created_by'] ?? '--'
                            ) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-save"
                >
                    💾 Lưu thông tin CLB
                </button>

            </div>

        </form>

    </div>


    <!-- ==================================================
         QUẢN LÝ CÁC BAN
    ================================================== -->

    <div class="club-card">

        <div class="card-header">

            <div>

                <h2>
                    Các ban trong câu lạc bộ
                </h2>

                <p>
                    Quản lý các ban trực thuộc CLB.
                </p>

            </div>


            <a
                href="add_band.php"
                class="btn-add"
            >
                + Thêm ban
            </a>

        </div>


        <div class="bands-container">

            <?php if (empty($bands)): ?>

                <div class="empty-state">

                    <div class="empty-icon">
                        🏢
                    </div>

                    <h3>
                        Chưa có ban nào
                    </h3>

                    <p>
                        Hãy thêm ban đầu tiên cho câu lạc bộ.
                    </p>

                    <a
                        href="add_band.php"
                        class="btn-add"
                    >
                        + Thêm ban
                    </a>

                </div>

            <?php else: ?>

                <div class="band-list">

                    <?php foreach ($bands as $band): ?>

                        <div class="band-item">

                            <div class="band-icon">
                                🏢
                            </div>


                            <div class="band-info">

                                <h3>
                                    <?= htmlspecialchars(
                                        $band['band_name']
                                    ) ?>
                                </h3>

                                <span>
                                    BAND ID:
                                    <?= htmlspecialchars(
                                        $band['band_id']
                                    ) ?>
                                </span>

                            </div>


                            <div class="band-actions">

                                <a
                                    href="delete_band.php?id=<?= urlencode(
                                        $band['band_id']
                                    ) ?>"
                                    class="btn-delete"
                                    onclick="
                                        return confirm(
                                            'Bạn có chắc muốn xóa ban này không?'
                                        );
                                    "
                                >
                                    🗑️ Xóa
                                </a>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>