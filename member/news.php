<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle  = "Thông báo CLB";
$activeMenu = "news.php";

/*
|--------------------------------------------------------------------------
| USER ĐANG ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/

$user = $_SESSION["user"] ?? null;

if (!$user) {
    header("Location: ../login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| KẾT NỐI DATABASE
|--------------------------------------------------------------------------
*/

$database = new Database();
$db = $database->getConnection();

/*
|--------------------------------------------------------------------------
| CLB
|--------------------------------------------------------------------------
*/

$clubId = "CLB001";

/*
|--------------------------------------------------------------------------
| HÀM ESCAPE HTML
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH THÔNG BÁO
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        notification_id,
        club_id,
        title,
        content,
        posted_date,
        posted_by,
        image
    FROM notifications
    WHERE club_id = :club_id
    ORDER BY posted_date DESC, notification_id DESC
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ":club_id" => $clubId
]);

$notifications = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);


/*
|--------------------------------------------------------------------------
| LẤY TÊN NGƯỜI ĐĂNG
|--------------------------------------------------------------------------
|
| Nếu posted_by chứa username thì lấy fullname từ UserInfo.
|
*/

foreach ($notifications as &$notification) {

    $notification["poster_name"] =
        $notification["posted_by"];

    if (!empty($notification["posted_by"])) {

        $sqlPoster = "
            SELECT fullname
            FROM UserInfo
            WHERE username = :username
            LIMIT 1
        ";

        $stmtPoster = $db->prepare(
            $sqlPoster
        );

        $stmtPoster->execute([
            ":username" =>
                $notification["posted_by"]
        ]);

        $poster = $stmtPoster->fetch(
            PDO::FETCH_ASSOC
        );

        if ($poster && !empty($poster["fullname"])) {

            $notification["poster_name"] =
                $poster["fullname"];
        }
    }
}

unset($notification);


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

require_once "../includes/headers.php";

?>

<style>

/* =========================================================
   PAGE
========================================================= */

.notifications-page {
    padding: 25px;
}


/* =========================================================
   HEADER
========================================================= */

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.notifications-header h1 {
    margin: 0 0 6px;
    color: #1e3a5f;
    font-size: 30px;
}

.notifications-header p {
    margin: 0;
    color: #64748b;
}


/* =========================================================
   SEARCH
========================================================= */

.notification-search {
    background: white;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
}

.notification-search input {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 15px;
    border: 1px solid #dbe3ec;
    border-radius: 9px;
    outline: none;
    font-size: 14px;
}

.notification-search input:focus {
    border-color: #1e3a5f;
}


/* =========================================================
   LIST
========================================================= */

.notification-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}


/* =========================================================
   CARD
========================================================= */

.notification-card {
    display: flex;
    gap: 20px;
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow:
        0 3px 12px rgba(0, 0, 0, 0.06);
    border-left: 4px solid #1e3a5f;
    transition: 0.2s;
}

.notification-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 6px 18px rgba(0, 0, 0, 0.08);
}


/* =========================================================
   IMAGE
========================================================= */

.notification-image {
    width: 180px;
    min-width: 180px;
    height: 120px;

    border-radius: 10px;

    overflow: hidden;

    background: #f1f5f9;

    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.no-image {
    color: #94a3b8;
    font-size: 35px;
}


/* =========================================================
   CONTENT
========================================================= */

.notification-body {
    flex: 1;
}

.notification-title {
    margin: 0 0 10px;

    color: #1e3a5f;

    font-size: 21px;

    line-height: 1.4;
}

.notification-content {
    color: #475569;
    line-height: 1.65;
    overflow: hidden;
    margin: 0 0 12px;
}


/* =========================================================
   INFO
========================================================= */

.notification-info {
    display: flex;

    flex-wrap: wrap;

    gap: 15px;

    color: #64748b;

    font-size: 13px;
}


/* =========================================================
   BUTTON
========================================================= */

.notification-actions {
    margin-top: 14px;
}

.btn-detail {
    border: none;

    padding: 8px 14px;

    border-radius: 8px;

    background: #e0f2fe;

    color: #075985;

    cursor: pointer;

    font-size: 13px;
}

.btn-detail:hover {
    background: #bae6fd;
}


/* =========================================================
   EMPTY
========================================================= */

.empty-notification {
    background: white;

    padding: 60px 20px;

    text-align: center;

    border-radius: 14px;

    box-shadow:
        0 3px 12px rgba(0, 0, 0, 0.05);

    color: #64748b;
}

.empty-notification-icon {
    font-size: 50px;

    margin-bottom: 10px;
}

.empty-notification h3 {
    color: #1e3a5f;

    margin-bottom: 8px;
}


/* =========================================================
   MODAL
========================================================= */

.modal {
    display: none;

    position: fixed;

    inset: 0;

    background: rgba(15, 23, 42, 0.55);

    justify-content: center;

    align-items: center;

    padding: 20px;

    z-index: 9999;
}

.modal.show {
    display: flex;
}

.modal-content {
    width: 100%;

    max-width: 700px;

    max-height: 90vh;

    overflow-y: auto;

    background: white;

    border-radius: 16px;

    padding: 25px;

    box-sizing: border-box;
}


/* =========================================================
   MODAL HEADER
========================================================= */

.modal-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;
}

.modal-header h2 {
    margin: 0;

    color: #1e3a5f;
}

.close {
    font-size: 30px;

    color: #64748b;

    cursor: pointer;

    line-height: 1;
}

.close:hover {
    color: #1e3a5f;
}


/* =========================================================
   DETAIL
========================================================= */

.detail-image {
    width: 100%;

    max-height: 350px;

    object-fit: cover;

    border-radius: 12px;

    margin-bottom: 20px;
}

/* =========================================================
   DETAIL MODAL
========================================================= */

.detail-content {
    max-width: 480px;
    width: 90%;
}

.detail-title {
    color: #1e3a5f;
    margin: 0 0 15px;
    font-size: 20px;
}

.detail-message {
    color: #475569;
    font-size: 14px;
}

.detail-info {
    background: #f8fafc;
    padding: 10px 12px;
    border-radius: 9px;
    color: #64748b;
    margin-top: 15px;
    font-size: 12px;
}
/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .notifications-page {
        padding: 15px;
    }

    .notifications-header {
        align-items: flex-start;

        flex-direction: column;

        gap: 10px;
    }

    .notification-card {
        flex-direction: column;
    }

    .notification-image {
        width: 100%;
        height: 180px;
    }

}

</style>


<div class="notifications-page">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="notifications-header">

        <div>

            <h1>
                Thông báo CLB
            </h1>

            <p>
                Các thông báo mới nhất từ câu lạc bộ
            </p>

        </div>

    </div>


    <!-- =====================================================
         SEARCH
    ====================================================== -->

    <div class="notification-search">

        <input
            type="text"
            id="notificationSearch"
            placeholder="🔍 Tìm kiếm thông báo..."
            onkeyup="searchNotifications()"
        >

    </div>


    <!-- =====================================================
         DANH SÁCH THÔNG BÁO
    ====================================================== -->

    <div
        class="notification-list"
        id="notificationList"
    >

        <?php if (!empty($notifications)): ?>

            <?php foreach ($notifications as $notification): ?>

                <div
                    class="notification-card"
                    data-title="<?= e($notification["title"]) ?>"
                >

                    <!-- =================================================
                         IMAGE
                    ================================================== -->

                    <?php if (!empty($notification["image"])): ?>

                        <div class="notification-image">

                            <img
                                src="<?= e($notification["image"]) ?>"
                                alt="<?= e($notification["title"]) ?>"
                                onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🖼️</div>';"
                            >

                        </div>

                    <?php else: ?>

                        <div class="notification-image">

                            <div class="no-image">
                                🔔
                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         BODY
                    ================================================== -->

                    <div class="notification-body">

                        <h2 class="notification-title">

                            <?= e($notification["title"]) ?>

                        </h2>


                        <p class="notification-content">

                            <?= e($notification["content"]) ?>

                        </p>


                        <!-- =================================================
                             INFO
                        ================================================== -->

                        <div class="notification-info">

                            <span>
                                👤
                                <?= e(
                                    $notification["poster_name"]
                                ) ?>
                            </span>


                            <span>
                                📅
                                <?= !empty(
                                    $notification["posted_date"]
                                )
                                    ? date(
                                        "d/m/Y H:i",
                                        strtotime(
                                            $notification["posted_date"]
                                        )
                                    )
                                    : ""
                                ?>
                            </span>


                            <span>
                                🏢
                                <?= e(
                                    $notification["club_id"]
                                ) ?>
                            </span>

                        </div>


                        <!-- =================================================
                             DETAIL BUTTON
                        ================================================== -->

                        <div class="notification-actions">

                            <button
                                type="button"
                                class="btn-detail"
                                onclick="showNotificationDetail(this)"
                            >
                                Xem chi tiết
                            </button>

                        </div>


                        <!-- =================================================
                             DATA CHO MODAL
                        ================================================== -->

                        <div
                            class="notification-data"
                            style="display:none;"
                        >

                            <div class="detail-title-data">
                                <?= e(
                                    $notification["title"]
                                ) ?>
                            </div>


                            <div class="detail-content-data">
                                <?= e(
                                    $notification["content"]
                                ) ?>
                            </div>


                            <div class="detail-poster-data">
                                <?= e(
                                    $notification["poster_name"]
                                ) ?>
                            </div>


                            <div class="detail-date-data">

                                <?= !empty(
                                    $notification["posted_date"]
                                )
                                    ? date(
                                        "d/m/Y H:i",
                                        strtotime(
                                            $notification["posted_date"]
                                        )
                                    )
                                    : ""
                                ?>

                            </div>


                            <div class="detail-club-data">
                                <?= e(
                                    $notification["club_id"]
                                ) ?>
                            </div>


                            <?php if (!empty($notification["image"])): ?>

                                <div class="detail-image-data">

                                    <?= e(
                                        $notification["image"]
                                    ) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty-notification">

                <div class="empty-notification-icon">
                    🔔
                </div>

                <h3>
                    Chưa có thông báo
                </h3>

                <p>
                    Hiện tại câu lạc bộ chưa có thông báo nào.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>


<!-- =========================================================
     MODAL CHI TIẾT
========================================================== -->

<div
    class="modal"
    id="detailModal"
>

    <div class="modal-content">

        <div class="modal-header">

            <h2>
                Chi tiết thông báo
            </h2>

            <span
                class="close"
                onclick="closeNotificationDetail()"
            >
                &times;
            </span>

        </div>


        <!-- IMAGE -->

        <img
            id="detailImage"
            class="detail-image"
            style="display:none;"
            alt="Ảnh thông báo"
        >


        <!-- TITLE -->

        <h3
            id="detailTitle"
            class="detail-title"
        ></h3>


        <!-- CONTENT -->

        <div
            id="detailContent"
            class="detail-content"
        ></div>


        <!-- INFO -->

        <div
            id="detailInfo"
            class="detail-info"
        ></div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| TÌM KIẾM THÔNG BÁO
|--------------------------------------------------------------------------
*/

function searchNotifications() {

    const keyword =
        document
            .getElementById("notificationSearch")
            .value
            .toLowerCase()
            .trim();

    const cards =
        document.querySelectorAll(
            ".notification-card"
        );

    cards.forEach(function(card) {

        const text =
            card.innerText.toLowerCase();

        if (text.includes(keyword)) {

            card.style.display = "";

        } else {

            card.style.display = "none";

        }

    });
}


/*
|--------------------------------------------------------------------------
| XEM CHI TIẾT
|--------------------------------------------------------------------------
*/

function showNotificationDetail(button) {

    const card =
        button.closest(
            ".notification-card"
        );

    if (!card) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | LẤY DATA
    |--------------------------------------------------------------------------
    */

    const data =
        card.querySelector(
            ".notification-data"
        );


    const title =
        data.querySelector(
            ".detail-title-data"
        ).innerText;


    const content =
        data.querySelector(
            ".detail-content-data"
        ).innerText;


    const poster =
        data.querySelector(
            ".detail-poster-data"
        ).innerText;


    const date =
        data.querySelector(
            ".detail-date-data"
        ).innerText;


    const club =
        data.querySelector(
            ".detail-club-data"
        ).innerText;


    const imageData =
        data.querySelector(
            ".detail-image-data"
        );


    /*
    |--------------------------------------------------------------------------
    | HIỂN THỊ TITLE
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        "detailTitle"
    ).innerText = title;


    /*
    |--------------------------------------------------------------------------
    | HIỂN THỊ CONTENT
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        "detailContent"
    ).innerText = content;


    /*
    |--------------------------------------------------------------------------
    | HIỂN THỊ INFO
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        "detailInfo"
    ).innerHTML =
        "👤 Người đăng: <strong>"
        + escapeHtml(poster)
        + "</strong><br><br>"
        + "📅 Ngày đăng: "
        + escapeHtml(date)
        + "<br><br>"
        + "🏢 CLB: "
        + escapeHtml(club);


    /*
    |--------------------------------------------------------------------------
    | HIỂN THỊ ẢNH
    |--------------------------------------------------------------------------
    */

    const image =
        document.getElementById(
            "detailImage"
        );

    if (
        imageData &&
        imageData.innerText.trim() !== ""
    ) {

        image.src =
            imageData.innerText.trim();

        image.style.display = "block";

    } else {

        image.src = "";

        image.style.display = "none";
    }


    /*
    |--------------------------------------------------------------------------
    | MỞ MODAL
    |--------------------------------------------------------------------------
    */

    document
        .getElementById("detailModal")
        .classList
        .add("show");
}


/*
|--------------------------------------------------------------------------
| ĐÓNG MODAL
|--------------------------------------------------------------------------
*/

function closeNotificationDetail() {

    document
        .getElementById("detailModal")
        .classList
        .remove("show");
}


/*
|--------------------------------------------------------------------------
| CLICK RA NGOÀI MODAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "click",
    function(event) {

        const modal =
            document.getElementById(
                "detailModal"
            );

        if (event.target === modal) {

            closeNotificationDetail();

        }

    }
);


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/



function escapeHtml(text) {

    const div =
        document.createElement("div");

    div.textContent = text;

    return div.innerHTML;
}

</script>


<?php

require_once "../includes/footer.php";

?>