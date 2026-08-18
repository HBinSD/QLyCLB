<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Club.php";

$pageTitle = "Chi tiết câu lạc bộ";

require_once "../includes/header.php";


if (!isset($_GET['id'])) {

    echo "<div class='card'>Không tìm thấy CLB.</div>";

    require_once "../includes/footer.php";

    exit;
}


$clubId = (int) $_GET['id'];


$database = new Database();

$db = $database->getConnection();

$clubModel = new Club($db);

$club = $clubModel->findById($clubId);

$members = $clubModel->getMembers($clubId);


if (!$club) {

    echo "<div class='card'>CLB không tồn tại.</div>";

    require_once "../includes/footer.php";

    exit;
}

?>


<div class="page-header">

    <h2>
        <?= htmlspecialchars($club['name']) ?>
    </h2>

    <p>
        Thông tin chi tiết câu lạc bộ.
    </p>

</div>


<div class="card">

    <h3 class="card-title">
        Giới thiệu CLB
    </h3>

    <p style="line-height:1.8; color:#64748b;">

        <?= nl2br(
            htmlspecialchars(
                $club['description'] ?? ''
            )
        ) ?>

    </p>

</div>


<div class="card">

    <h3 class="card-title">
        Ban chủ nhiệm và thành viên
    </h3>


    <div class="member-table">

        <?php foreach ($members as $member): ?>

            <div class="member-row">

                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $member['full_name']
                        ) ?>
                    </strong>

                    <br>

                    <small>
                        MSV:
                        <?= htmlspecialchars(
                            $member['msv']
                        ) ?>
                    </small>

                </div>


                <span class="position-badge">

                    <?= htmlspecialchars(
                        $member['position']
                    ) ?>

                </span>

            </div>

        <?php endforeach; ?>

    </div>

</div>


<div class="card">

    <h3 class="card-title">
        Nội quy CLB
    </h3>

    <p style="line-height:1.8; color:#64748b;">

        <?= nl2br(
            htmlspecialchars(
                $club['rules'] ?? 'Chưa có nội quy.'
            )
        ) ?>

    </p>

</div>


<?php require_once "../includes/footer.php"; ?>

<style>
    .member-row {
    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 15px;

    border-bottom: 1px solid #e5e7eb;
}


.member-row:last-child {
    border-bottom: none;
}


.member-row strong {
    color: #1e293b;
}


.member-row small {
    color: #94a3b8;
}


.position-badge {
    background: #eff6ff;

    color: #2563eb;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;
}
</style>