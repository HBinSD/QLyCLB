<?php

require_once "../includes/auth.php";

require_once "../config/database.php";
require_once "../models/Club.php";

$pageTitle = "Câu lạc bộ";

require_once "../includes/header.php";


$database = new Database();

$db = $database->getConnection();

$clubModel = new Club($db);

$clubs = $clubModel->getAll();

?>

<div class="page-header">

    <h2>Câu lạc bộ</h2>

    <p>
        Danh sách các câu lạc bộ trong hệ thống.
    </p>

</div>


<div class="club-grid">

    <?php foreach ($clubs as $club): ?>

        <div class="club-card">

            <div class="club-logo">

                <?php if (!empty($club['logo'])): ?>

                    <img
                        src="<?= htmlspecialchars($club['logo']) ?>"
                        alt="Logo"
                    >

                <?php else: ?>

                    🏢

                <?php endif; ?>

            </div>


            <h3>
                <?= htmlspecialchars($club['name']) ?>
            </h3>


            <p>

                <?= htmlspecialchars(
                    mb_substr(
                        $club['description'] ?? '',
                        0,
                        120
                    )
                ) ?>

                ...

            </p>


            <a
                href="club.php?id=<?= $club['id'] ?>"
                class="btn-primary"
            >
                Xem chi tiết
            </a>

        </div>

    <?php endforeach; ?>

</div>


<?php require_once "../includes/footer.php"; ?>

<style>
    .club-grid {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;
}


.club-card {
    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 3px 15px
        rgba(0, 0, 0, 0.05);
}


.club-logo {
    width: 65px;
    height: 65px;

    background: #eff6ff;

    border-radius: 12px;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 30px;

    margin-bottom: 15px;
}


.club-logo img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    border-radius: 12px;
}


.club-card h3 {
    color: #1e293b;

    margin-bottom: 10px;
}


.club-card p {
    color: #64748b;

    font-size: 14px;

    line-height: 1.6;

    min-height: 65px;

    margin-bottom: 20px;
}


.btn-primary {
    display: inline-block;

    background: #2563eb;

    color: white;

    padding: 10px 18px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 14px;
}


.btn-primary:hover {
    background: #1d4ed8;
}


@media (max-width: 900px) {

    .club-grid {
        grid-template-columns: 1fr 1fr;
    }

}


@media (max-width: 600px) {

    .club-grid {
        grid-template-columns: 1fr;
    }

}
</style>