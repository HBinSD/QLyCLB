<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle  = "Danh sách thành viên";
$activeMenu = "club-members.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}

if (
    ($user['role'] ?? '') !== 'organizer' &&
    ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}

$database = new Database();
$db = $database->getConnection();

$sqlClub = "
    SELECT club_id
    FROM clubmember
    WHERE username = :username
    LIMIT 1
";

$stmtClub = $db->prepare($sqlClub);

$stmtClub->execute([
    ':username' => $user['username']
]);

$userClub = $stmtClub->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Nếu user chưa tham gia CLB
|--------------------------------------------------------------------------
*/

if (!$userClub) {

    require_once "../includes/headers.php";
    ?>

<link rel="stylesheet" href="css/club.css">
<link rel="stylesheet" href="css/members.css">

<div class="club-layout">

    <!-- Content -->
    <main class="club-content">

        <div class="members-empty">
            <div class="empty-icon">👥</div>

            <h2>Danh sách thành viên</h2>

            <p>
                Bạn chưa tham gia câu lạc bộ nào.
            </p>

        </div>

    </main>

</div>

</main>
</div>
</div>

<?php
    require_once "../includes/footer.php";
    exit;
}


$clubId = $userClub['club_id'];

/*
|--------------------------------------------------------------------------
| Lấy danh sách thành viên
|--------------------------------------------------------------------------
*/

$sql = "
     select
        cm.username,
        cm.club_id,
        cm.joined_at,
        cm.position,
        cm.status,
        us.fullname,
        us.avt_links,
        cb.band_name
    FROM ClubMember AS cm
    JOIN UserInfo AS us
        ON us.username = cm.username
    left join clubbandmember as cbm
    	on cbm.username = cm.username
    left join clubband as cb
    	on cb.band_id = cbm.band_id
    WHERE cm.club_id = :club_id
    ORDER BY
        CASE
            WHEN cm.position IS NULL OR cm.position = '' THEN 1
            ELSE 0
        END,
        cm.joined_at ASC
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':club_id' => $clubId
]);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Thông tin CLB
|--------------------------------------------------------------------------
*/

$sqlClubInfo = "
    SELECT club_name
    FROM Clubs
    WHERE club_id = :club_id
    LIMIT 1
";

$stmtClubInfo = $db->prepare($sqlClubInfo);

$stmtClubInfo->execute([
    ':club_id' => $clubId
]);

$clubInfo = $stmtClubInfo->fetch(PDO::FETCH_ASSOC);


require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/club.css">
<link rel="stylesheet" href="css/club_member.css">


<div class="club-layout">

    <!-- =====================================
         CONTENT
    ====================================== -->

    <main class="club-content">

        <div class="members-page">

            <!-- Header -->
            <div class="members-header">

                <div>

                    <h2>
                        Danh sách thành viên
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $clubInfo['club_name']
                            ?? 'Câu lạc bộ'
                        ) ?>
                    </p>

                </div>


                <div class="member-count">

                    <strong>
                        <?= count($members) ?>
                    </strong>

                    <span>
                        thành viên
                    </span>

                </div>

            </div>


            <!-- Search -->
            <div class="members-toolbar">

                <div class="member-search">

                    <span>🔍</span>

                    <input type="text" id="memberSearch" placeholder="Tìm kiếm thành viên..." onkeyup="searchMembers()">

                </div>

            </div>


            <!-- Table -->
            <div class="members-table-wrapper">

                <table class="members-table">

                    <thead>

                        <tr>

                            <th> STT </th> 
                            <th> Họ tên </th> 
                            <th> Chức vụ </th> 
                            <th> Ngày tham gia </th>
                            <th> Thuộc ban </th> 
                            <th> Trạng thái </th>

                        </tr>

                    </thead>


                    <tbody id="membersTable">

                        <?php if (!empty($members)): ?>

                        <?php foreach ($members as $index => $member): ?>

                        <tr>

                            <!-- STT -->
                            <td class="member-number">
                                <?= $index + 1 ?>
                            </td>


                            <!-- Username -->
                            <td>

                                <div class="member-username">

                                    <div class="member-avatar">

                                        <?php if (!empty($member['avt_links'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['avt_links']); ?>"
                                            alt="Ảnh đại diện"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                        <?php else: ?>
                                        <div class="avatar-default">
                                            👤
                                        </div>
                                        <?php endif; ?> 
                                    </div>

                                    <span>
                                        <?= htmlspecialchars(
                                                    $member['fullname']
                                                ) ?? ['Unknown'] ?>
                                    </span>

                                </div>

                            </td>


                            <!-- Position -->
                            <td>

                                <?php $position = trim( $member['position'] ?? '' ); 
                                
                                if ($position === ''): ?>

                                    <span class="position-badge member">
                                        Thành viên
                                    </span>

                                <?php else: ?>

                                <span class="position-badge">
                                    <?= htmlspecialchars( $position ) ?>
                                </span>

                                <?php endif; ?>

                            </td>


                            <!-- Joined date -->
                            <td>

                                <?php

                                    if (!empty($member['joined_at'])) {
                                        echo htmlspecialchars( date('d/m/Y',strtotime( $member['joined_at'])));
                                    } else {
                                        echo 'Chưa cập nhật';
                                    }

                                ?>

                            </td>

                            <!-- Status -->
                            <td>

                                <?php if (!empty($member['band_name'])): ?>

                                <span class="status-badge active">
                                    <?= htmlspecialchars($member['band_name']) ?>
                                </span>

                                <?php else: ?>

                                <span class="status-badge inactive">
                                    <?php echo 'Chưa vào ban' ?>
                                </span>

                                <?php endif; ?>

                            </td>


                            <!-- Status -->
                            <td>

                                <?php if ((int)$member['status'] === 1): ?>

                                <span class="status-badge active">
                                    Đang hoạt động
                                </span>

                                <?php else: ?>

                                <span class="status-badge inactive">
                                    Không hoạt động
                                </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5" class="no-members">
                                Chưa có thành viên nào.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<script>
function searchMembers() {

    const input =
        document
        .getElementById("memberSearch")
        .value
        .toLowerCase();

    const rows =
        document
        .querySelectorAll("#membersTable tr");

    rows.forEach(function(row) {

        const text =
            row.textContent.toLowerCase();

        if (text.includes(input)) {

            row.style.display = "";

        } else {

            row.style.display = "none";

        }

    });

}
</script>


</main>
</div>
</div>

<?php require_once "../includes/footer.php"; ?>