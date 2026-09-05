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
// TÌM KIẾM
// ========================================

$keyword = trim($_GET['keyword'] ?? '');


// ========================================
// LẤY DANH SÁCH THÀNH VIÊN
// ========================================

$sql = "
    SELECT
        cm.username,
        cm.club_id,
        cm.joined_at,
        cm.position,
        cm.status,

        ui.fullname,
        ui.email,
        ui.phone,
        ui.id_number,
        ui.DOB,
        ui.gender,
        ui.avt_links

    FROM ClubMember cm

    INNER JOIN UserInfo ui
        ON ui.username = cm.username

    WHERE cm.club_id = :club_id
";

$params = [
    ':club_id' => $clubId
];


// ========================================
// SEARCH
// ========================================

if ($keyword !== '') {

    $sql .= "
        AND (
            cm.username LIKE :keyword
            OR ui.fullname LIKE :keyword
            OR ui.email LIKE :keyword
            OR ui.phone LIKE :keyword
            OR ui.id_number LIKE :keyword
        )
    ";

    $params[':keyword'] = '%' . $keyword . '%';
}


// ========================================
// SORT
// ========================================

$sql .= "
    ORDER BY
        CASE
            WHEN cm.position IS NULL
                 OR cm.position = ''
            THEN 1
            ELSE 0
        END,
        cm.joined_at ASC
";

$stmt = $db->prepare($sql);
$stmt->execute($params);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ========================================
// THỐNG KÊ
// ========================================

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM ClubMember
    WHERE club_id = :club_id
");

$stmt->execute([
    ':club_id' => $clubId
]);

$totalMembers = (int)$stmt->fetchColumn();


// ========================================
// HEADER
// ========================================

$pageTitle = "Quản lý thành viên";
$activeMenu = "members.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/members.css">


<div class="admin-members">

    <!-- ================================= -->
    <!-- PAGE HEADER -->
    <!-- ================================= -->

    <div class="page-header">

        <div>
            <h1>Quản lý thành viên</h1>

            <p>
                Danh sách thành viên của câu lạc bộ
            </p>
        </div>

        <div class="member-total">

            <span>Tổng thành viên</span>

            <strong>
                <?= $totalMembers ?>
            </strong>

        </div>

    </div>


    <!-- ================================= -->
    <!-- SEARCH -->
    <!-- ================================= -->

    <div class="search-box">

        <form method="GET">

            <div class="search-input">

                <span>🔍</span>

                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm theo tên, username, email, số điện thoại..."
                    value="<?= htmlspecialchars($keyword) ?>"
                >

            </div>

            <button type="submit">
                Tìm kiếm
            </button>

            <?php if ($keyword !== ''): ?>

                <a href="members.php" class="reset-btn">
                    Xóa lọc
                </a>

            <?php endif; ?>

        </form>

    </div>


    <!-- ================================= -->
    <!-- TABLE -->
    <!-- ================================= -->

    <div class="member-table-container">

        <?php if (empty($members)): ?>

            <div class="empty-state">

                <div class="empty-icon">
                    👥
                </div>

                <h3>
                    Không tìm thấy thành viên
                </h3>

                <p>
                    Không có thành viên phù hợp với từ khóa tìm kiếm.
                </p>

            </div>

        <?php else: ?>

            <div class="table-wrapper">

                <table class="member-table">

                    <thead>

                        <tr>

                            <th>Thành viên</th>

                            <th>Username</th>

                            <th>Email</th>

                            <th>Số điện thoại</th>

                            <th>Chức vụ</th>

                            <th>Ngày tham gia</th>

                            <th>Trạng thái</th>

                            <th>Thao tác</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($members as $member): ?>

                        <tr>

                            <!-- ================= -->
                            <!-- MEMBER -->
                            <!-- ================= -->

                            <td>

                                <div class="member-info">

                                    <?php if (!empty($member['avt_links'])): ?>

                                        <img
                                            src="<?= htmlspecialchars($member['avt_links']) ?>"
                                            class="member-avatar"
                                            alt="Avatar"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        <div
                                            class="member-avatar-fallback"
                                            style="display:none;"
                                        >
                                            <?= strtoupper(
                                                mb_substr(
                                                    $member['fullname']
                                                        ?: $member['username'],
                                                    0,
                                                    1
                                                )
                                            ) ?>
                                        </div>

                                    <?php else: ?>

                                        <div class="member-avatar-fallback">

                                            <?= strtoupper(
                                                mb_substr(
                                                    $member['fullname']
                                                        ?: $member['username'],
                                                    0,
                                                    1
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                    <div class="member-name">

                                        <strong>
                                            <?= htmlspecialchars(
                                                $member['fullname']
                                                    ?: 'Chưa cập nhật'
                                            ) ?>
                                        </strong>

                                        <span>
                                            <?= htmlspecialchars(
                                                $member['id_number']
                                                    ?: 'Chưa có MSSV/CCCD'
                                            ) ?>
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <!-- ================= -->
                            <!-- USERNAME -->
                            <!-- ================= -->

                            <td>

                                <span class="username">
                                    <?= htmlspecialchars(
                                        $member['username']
                                    ) ?>
                                </span>

                            </td>


                            <!-- ================= -->
                            <!-- EMAIL -->
                            <!-- ================= -->

                            <td>

                                <?= htmlspecialchars(
                                    $member['email']
                                        ?: 'Chưa cập nhật'
                                ) ?>

                            </td>


                            <!-- ================= -->
                            <!-- PHONE -->
                            <!-- ================= -->

                            <td>

                                <?= htmlspecialchars(
                                    $member['phone']
                                        ?: 'Chưa cập nhật'
                                ) ?>

                            </td>


                            <!-- ================= -->
                            <!-- POSITION -->
                            <!-- ================= -->

                            <td>

                                <?php if (
                                    !empty($member['position'])
                                ): ?>

                                    <span class="position-badge">
                                        <?= htmlspecialchars(
                                            $member['position']
                                        ) ?>
                                    </span>

                                <?php else: ?>

                                    <span class="no-position">
                                        Thành viên
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ================= -->
                            <!-- JOIN DATE -->
                            <!-- ================= -->

                            <td>

                                <?php if (!empty($member['joined_at'])): ?>

                                    <?= date(
                                        "d/m/Y",
                                        strtotime($member['joined_at'])
                                    ) ?>

                                <?php else: ?>

                                    --

                                <?php endif; ?>

                            </td>


                            <!-- ================= -->
                            <!-- STATUS -->
                            <!-- ================= -->

                            <td>

                                <?php
                                $status = strtolower(
                                    (string)$member['status']
                                );
                                ?>

                                <?php if (
                                    $status === 'active'
                                    || $status === '1'
                                    || $status === 'approved'
                                ): ?>

                                    <span class="status active">
                                        Đang hoạt động
                                    </span>

                                <?php else: ?>

                                    <span class="status inactive">
                                        Không hoạt động
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ================= -->
                            <!-- ACTION -->
                            <!-- ================= -->

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="member_detail.php?username=<?= urldecode($member['username']) ?>"
                                        class="btn-view"
                                        title="Xem thông tin"
                                    >
                                        👁
                                    </a>


                                    <a
                                        href="member_edit.php?username=<?= urldecode($member['username']) ?>"
                                        class="btn-edit"
                                        title="Chỉnh sửa"
                                    >
                                        ✏️
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>
<?php require_once '../includes/footer.php' ?>
</body>