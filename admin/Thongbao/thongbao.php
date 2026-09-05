<?php

session_start();

require_once "/../../includes/auth.php";
require_once "/../../database/database.php";
$pageTitle = "Thông báo CLB";
$activeMenu = "notifications.php";

/*
|--------------------------------------------------------------------------
| KẾT NỐI DATABASE
|--------------------------------------------------------------------------
*/

$database = new Database();

$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| LẤY CLB CỦA USER ĐANG ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/

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
| NẾU USER CHƯA THAM GIA CLB
|--------------------------------------------------------------------------
*/

if (!$userClub) {

    require_once "/../../includes/headers.php";

?>

<link rel="stylesheet" href="css/club.css">
<link rel="stylesheet" href="css/notifications.css">

<div class="club-layout">

```
<!-- SIDEBAR -->

<aside class="club-sidebar">

    <div class="club-sidebar-title">

        <span>☰</span>

        <span>QUẢN LÝ CLB</span>

    </div>


    <nav class="club-menu">

        <a href="club.php" class="club-menu-item">

            <span class="menu-icon">
                🏠
            </span>

            <span>
                Giới thiệu CLB
            </span>

        </a>


        <a href="club_member.php" class="club-menu-item">

            <span class="menu-icon">
                👥
            </span>

            <span>
                Danh sách thành viên
            </span>

        </a>


        <a href="events.php" class="club-menu-item">

            <span class="menu-icon">
                📅
            </span>

            <span>
                Sự kiện
            </span>

        </a>


        <a href="registered_events.php" class="club-menu-item">

            <span class="menu-icon">
                ✓
            </span>

            <span>
                Các sự kiện đã đăng ký
            </span>

        </a>


        <a href="thongbao.php" class="club-menu-item active">

            <span class="menu-icon">
                🔔
            </span>

            <span>
                Thông báo CLB
            </span>

        </a>

    </nav>

</aside>


<!-- NỘI DUNG -->

<main class="club-content">

    <div class="members-empty">

        <div class="empty-icon">

            🔔

        </div>


        <h2>
            Thông báo CLB
        </h2>


        <p>
            Bạn chưa tham gia câu lạc bộ nào.
        </p>

    </div>

</main>
```

</div>

<?php

    require_once "/../../includes/footer.php";

    exit;

}


/*
|--------------------------------------------------------------------------
| LẤY CLUB ID
|--------------------------------------------------------------------------
*/

$clubId = $userClub['club_id'];


/*
|--------------------------------------------------------------------------
| THÊM - CẬP NHẬT - XÓA THÔNG BÁO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | THÊM THÔNG BÁO
    |--------------------------------------------------------------------------
    */

    if ($action === "create") {

        $tieuDe = trim($_POST["tieuDe"] ?? "");

        $noiDung = trim($_POST["noiDung"] ?? "");

        $ngayDang = $_POST["ngayDang"] ?? "";

        $nguoiDang = trim($_POST["nguoiDang"] ?? "");

        $anh = trim($_POST["anh"] ?? "");


        if (

            $tieuDe !== "" &&

            $noiDung !== "" &&

            $ngayDang !== "" &&

            $nguoiDang !== ""

        ) {

            $sql = "

                INSERT INTO notifications
                (
                    club_id,
                    title,
                    content,
                    posted_date,
                    posted_by,
                    image
                )

                VALUES
                (
                    :club_id,
                    :title,
                    :content,
                    :posted_date,
                    :posted_by,
                    :image
                )

            ";


            $stmt = $db->prepare($sql);


            $stmt->execute([

                ':club_id' => $clubId,

                ':title' => $tieuDe,

                ':content' => $noiDung,

                ':posted_date' => $ngayDang,

                ':posted_by' => $nguoiDang,

                ':image' => $anh !== "" ? $anh : null

            ]);

        }


        header("Location: thongbao.php?success=created");

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT THÔNG BÁO
    |--------------------------------------------------------------------------
    */

    if ($action === "update") {

        $id = (int)($_POST["id"] ?? 0);

        $tieuDe = trim($_POST["tieuDe"] ?? "");

        $noiDung = trim($_POST["noiDung"] ?? "");

        $ngayDang = $_POST["ngayDang"] ?? "";

        $nguoiDang = trim($_POST["nguoiDang"] ?? "");

        $anh = trim($_POST["anh"] ?? "");


        if ($id > 0) {

            $sql = "

                UPDATE notifications

                SET

                    title = :title,

                    content = :content,

                    posted_date = :posted_date,

                    posted_by = :posted_by,

                    image = :image

                WHERE

                    notification_id = :id

                AND

                    club_id = :club_id

            ";


            $stmt = $db->prepare($sql);


            $stmt->execute([

                ':title' => $tieuDe,

                ':content' => $noiDung,

                ':posted_date' => $ngayDang,

                ':posted_by' => $nguoiDang,

                ':image' => $anh !== "" ? $anh : null,

                ':id' => $id,

                ':club_id' => $clubId

            ]);

        }


        header("Location: thongbao.php?success=updated");

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | XÓA THÔNG BÁO
    |--------------------------------------------------------------------------
    */

    if ($action === "delete") {

        $id = (int)($_POST["id"] ?? 0);


        if ($id > 0) {

            $sql = "

                DELETE FROM notifications

                WHERE notification_id = :id

                AND club_id = :club_id

            ";


            $stmt = $db->prepare($sql);


            $stmt->execute([

                ':id' => $id,

                ':club_id' => $clubId

            ]);

        }


        header("Location: thongbao.php?success=deleted");

        exit;

    }

}


/*
|--------------------------------------------------------------------------
| LẤY THÔNG BÁO CẦN CẬP NHẬT
|--------------------------------------------------------------------------
*/

$editId = isset($_GET["edit"])

    ? (int)$_GET["edit"]

    : 0;


$thongBaoSua = null;


if ($editId > 0) {

    $sqlEdit = "

        SELECT *

        FROM notifications

        WHERE notification_id = :id

        AND club_id = :club_id

        LIMIT 1

    ";


    $stmtEdit = $db->prepare($sqlEdit);


    $stmtEdit->execute([

        ':id' => $editId,

        ':club_id' => $clubId

    ]);


    $thongBaoSua = $stmtEdit->fetch(PDO::FETCH_ASSOC);

}


/*
|--------------------------------------------------------------------------
| PHÂN TRANG
|--------------------------------------------------------------------------
*/

$soLuongMoiTrang = 5;


$trangHienTai = isset($_GET["page"])

    ? (int)$_GET["page"]

    : 1;


if ($trangHienTai < 1) {

    $trangHienTai = 1;

}


/*
|--------------------------------------------------------------------------
| ĐẾM TỔNG SỐ THÔNG BÁO
|--------------------------------------------------------------------------
*/

$sqlCount = "

    SELECT COUNT(*)

    FROM notifications

    WHERE club_id = :club_id

";


$stmtCount = $db->prepare($sqlCount);


$stmtCount->execute([

    ':club_id' => $clubId

]);


$tongSoThongBao = (int)$stmtCount->fetchColumn();


$tongSoTrang = ceil(

    $tongSoThongBao / $soLuongMoiTrang

);


if (

    $trangHienTai > $tongSoTrang

    &&

    $tongSoTrang > 0

) {

    $trangHienTai = $tongSoTrang;

}


$viTriBatDau =

    ($trangHienTai - 1)

    *

    $soLuongMoiTrang;


/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH THÔNG BÁO
|--------------------------------------------------------------------------
*/

$sqlThongBao = "

    SELECT *

    FROM notifications

    WHERE club_id = :club_id

    ORDER BY

        posted_date DESC,

        notification_id DESC

    LIMIT :limit

    OFFSET :offset

";


$stmtThongBao = $db->prepare($sqlThongBao);


$stmtThongBao->bindValue(

    ':club_id',

    $clubId,

    PDO::PARAM_STR

);


$stmtThongBao->bindValue(

    ':limit',

    $soLuongMoiTrang,

    PDO::PARAM_INT

);


$stmtThongBao->bindValue(

    ':offset',

    $viTriBatDau,

    PDO::PARAM_INT

);


$stmtThongBao->execute();


$thongBaoHienThi =

    $stmtThongBao->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

require_once "../includes/headers.php";

?>

<link rel="stylesheet" href="css/club.css">

<link rel="stylesheet" href="css/notifications.css">

<div class="club-layout">

```
<!-- =====================================
     SIDEBAR
====================================== -->

<aside class="club-sidebar">


    <div class="club-sidebar-title">

        <span>
            ☰
        </span>

        <span>
            QUẢN LÝ CLB
        </span>

    </div>


    <nav class="club-menu">


        <!-- GIỚI THIỆU -->

        <a
            href="club.php"
            class="club-menu-item"
        >

            <span class="menu-icon">
                🏠
            </span>

            <span>
                Giới thiệu CLB
            </span>

        </a>


        <!-- THÀNH VIÊN -->

        <a
            href="club_member.php"
            class="club-menu-item"
        >

            <span class="menu-icon">
                👥
            </span>

            <span>
                Danh sách thành viên
            </span>

        </a>


        <!-- SỰ KIỆN -->

        <a
            href="events.php"
            class="club-menu-item"
        >

            <span class="menu-icon">
                📅
            </span>

            <span>
                Sự kiện
            </span>

        </a>


        <!-- ĐÃ ĐĂNG KÝ -->

        <a
            href="registered_events.php"
            class="club-menu-item"
        >

            <span class="menu-icon">
                ✓
            </span>

            <span>
                Các sự kiện đã đăng ký
            </span>

        </a>


        <!-- THÔNG BÁO -->

        <a
            href="thongbao.php"
            class="club-menu-item active"
        >

            <span class="menu-icon">
                🔔
            </span>

            <span>
                Thông báo CLB
            </span>

        </a>


    </nav>

</aside>


<!-- =====================================
     CONTENT
====================================== -->

<main class="club-content">


    <div class="container">


        <!-- PAGE TITLE -->

        <div class="page-title">


            <div>

                <h1>
                    Quản lý thông báo
                </h1>


                <p>
                    Quản lý các thông báo và tin tức của câu lạc bộ
                </p>

            </div>


            <button
                class="btn-add"
                onclick="openCreateForm()"
            >

                + Thêm thông báo

            </button>


        </div>


        <!-- =====================================
             THÔNG BÁO THÀNH CÔNG
        ====================================== -->

        <?php if (isset($_GET["success"])): ?>


            <div class="success-message">


                <?php if ($_GET["success"] === "created"): ?>

                    Thêm thông báo thành công!


                <?php elseif ($_GET["success"] === "updated"): ?>

                    Cập nhật thông báo thành công!


                <?php elseif ($_GET["success"] === "deleted"): ?>

                    Xóa thông báo thành công!


                <?php endif; ?>


            </div>


        <?php endif; ?>


        <!-- =====================================
             TÌM KIẾM
        ====================================== -->

        <div class="search-box">


            <input
                type="text"
                id="search"
                placeholder="🔍 Tìm kiếm thông báo..."
                onkeyup="searchNotification()"
            >


        </div>


        <!-- =====================================
             DANH SÁCH THÔNG BÁO
        ====================================== -->

        <div
            class="notification-list"
            id="notificationList"
        >


            <?php if (!empty($thongBaoHienThi)): ?>


                <?php foreach ($thongBaoHienThi as $tb): ?>


                    <div class="notification-card">


                        <!-- ẢNH -->

                        <?php if (!empty($tb["image"])): ?>


                            <img
                                src="<?= htmlspecialchars($tb["image"]) ?>"
                                alt="Ảnh thông báo"
                            >


                        <?php endif; ?>


                        <!-- NỘI DUNG -->

                        <div class="notification-content">


                            <span class="badge">

                                Thông báo

                            </span>


                            <h2>

                                <?= htmlspecialchars($tb["title"]) ?>

                            </h2>


                            <p>

                                <?= htmlspecialchars($tb["content"]) ?>

                            </p>


                            <div class="notification-info">


                                <!-- NGÀY -->

                                <span>

                                    📅

                                    <?= date(
                                        "d/m/Y",
                                        strtotime(
                                            $tb["posted_date"]
                                        )
                                    ) ?>

                                </span>


                                <!-- NGƯỜI ĐĂNG -->

                                <span>

                                    👤

                                    <?= htmlspecialchars(
                                        $tb["posted_by"]
                                    ) ?>

                                </span>


                            </div>


                            <!-- =====================================
                                 ACTION
                            ====================================== -->

                            <div class="actions">


                                <!-- XEM CHI TIẾT -->

                                <button
                                    type="button"
                                    class="btn-detail"
                                    onclick="showDetail(this)"
                                >

                                    Xem chi tiết

                                </button>


                                <!-- CẬP NHẬT -->

                                <a
                                    href="thongbao.php?edit=<?= $tb["notification_id"] ?>"
                                    class="btn-edit"
                                >

                                    Cập nhật

                                </a>


                                <!-- XÓA -->

                                <form
                                    method="POST"
                                    class="delete-form"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này không?')"
                                >


                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >


                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $tb["notification_id"] ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="btn-delete"
                                    >

                                        Xóa

                                    </button>


                                </form>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="empty-notification">

                    <h3>
                        Chưa có thông báo nào
                    </h3>


                    <p>
                        Hiện tại câu lạc bộ chưa có thông báo.
                    </p>

                </div>


            <?php endif; ?>


        </div>


        <!-- =====================================
             PHÂN TRANG
        ====================================== -->

        <?php if ($tongSoTrang > 1): ?>


            <div class="pagination">


                <!-- TRƯỚC -->

                <?php if ($trangHienTai > 1): ?>

                    <a
                        href="thongbao.php?page=<?= $trangHienTai - 1 ?>"
                    >

                        « Trước

                    </a>

                <?php endif; ?>


                <!-- SỐ TRANG -->

                <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>


                    <a
                        href="thongbao.php?page=<?= $i ?>"
                        class="<?= $i == $trangHienTai ? 'active' : '' ?>"
                    >

                        <?= $i ?>

                    </a>


                <?php endfor; ?>


                <!-- SAU -->

                <?php if ($trangHienTai < $tongSoTrang): ?>

                    <a
                        href="thongbao.php?page=<?= $trangHienTai + 1 ?>"
                    >

                        Sau »

                    </a>

                <?php endif; ?>


            </div>


        <?php endif; ?>


    </div>


</main>
```

</div>

<!-- =====================================
     MODAL THÊM THÔNG BÁO
====================================== -->

<div
    class="modal"
    id="createModal"
>

```
<div class="modal-content">


    <div class="modal-header">


        <h2>
            Thêm thông báo
        </h2>


        <span
            onclick="closeCreateForm()"
            class="close"
        >

            &times;

        </span>


    </div>


    <form method="POST">


        <input
            type="hidden"
            name="action"
            value="create"
        >


        <!-- TIÊU ĐỀ -->

        <label>
            Tiêu đề thông báo
        </label>


        <input
            type="text"
            name="tieuDe"
            placeholder="Nhập tiêu đề..."
            required
        >


        <!-- NỘI DUNG -->

        <label>
            Nội dung
        </label>


        <textarea
            name="noiDung"
            rows="5"
            placeholder="Nhập nội dung thông báo..."
            required
        ></textarea>


        <!-- NGÀY -->

        <label>
            Ngày đăng
        </label>


        <input
            type="date"
            name="ngayDang"
            value="<?= date('Y-m-d') ?>"
            required
        >


        <!-- NGƯỜI ĐĂNG -->

        <label>
            Người đăng
        </label>


        <input
            type="text"
            name="nguoiDang"
            value="<?= htmlspecialchars($user['username']) ?>"
            required
        >


        <!-- LINK ẢNH -->

        <label>
            Link ảnh
        </label>


        <input
            type="text"
            name="anh"
            placeholder="Dán link ảnh (không bắt buộc)"
        >


        <div class="form-buttons">


            <button
                type="button"
                class="btn-cancel"
                onclick="closeCreateForm()"
            >

                Hủy

            </button>


            <button
                type="submit"
                class="btn-save"
            >

                Lưu thông báo

            </button>


        </div>


    </form>


</div>
```

</div>

<!-- =====================================
     MODAL CẬP NHẬT
====================================== -->

<?php if ($thongBaoSua !== null): ?>

<div
    class="modal"
    id="editModal"
    style="display: flex;"
>

```
<div class="modal-content">


    <div class="modal-header">


        <h2>
            Cập nhật thông báo
        </h2>


        <a
            href="thongbao.php"
            class="close"
        >

            &times;

        </a>


    </div>


    <form method="POST">


        <input
            type="hidden"
            name="action"
            value="update"
        >


        <input
            type="hidden"
            name="id"
            value="<?= $thongBaoSua["notification_id"] ?>"
        >


        <!-- TIÊU ĐỀ -->

        <label>
            Tiêu đề thông báo
        </label>


        <input
            type="text"
            name="tieuDe"
            value="<?= htmlspecialchars($thongBaoSua["title"]) ?>"
            required
        >


        <!-- NỘI DUNG -->

        <label>
            Nội dung
        </label>


        <textarea
            name="noiDung"
            rows="5"
            required
        ><?= htmlspecialchars($thongBaoSua["content"]) ?></textarea>


        <!-- NGÀY -->

        <label>
            Ngày đăng
        </label>


        <input
            type="date"
            name="ngayDang"
            value="<?= htmlspecialchars($thongBaoSua["posted_date"]) ?>"
            required
        >


        <!-- NGƯỜI ĐĂNG -->

        <label>
            Người đăng
        </label>


        <input
            type="text"
            name="nguoiDang"
            value="<?= htmlspecialchars($thongBaoSua["posted_by"]) ?>"
            required
        >


        <!-- ẢNH -->

        <label>
            Link ảnh
        </label>


        <input
            type="text"
            name="anh"
            value="<?= htmlspecialchars($thongBaoSua["image"] ?? "") ?>"
            placeholder="Dán link ảnh..."
        >


        <div class="form-buttons">


            <a
                href="thongbao.php"
                class="btn-cancel"
            >

                Hủy

            </a>


            <button
                type="submit"
                class="btn-save"
            >

                Cập nhật

            </button>


        </div>


    </form>


</div>
```

</div>

<?php endif; ?>

<!-- =====================================
     MODAL CHI TIẾT
====================================== -->

<div
    class="modal"
    id="detailModal"
>

```
<div class="modal-content detail-content">


    <div class="modal-header">


        <h2>
            Chi tiết thông báo
        </h2>


        <span
            onclick="closeDetail()"
            class="close"
        >

            &times;

        </span>


    </div>


    <div id="detailText"></div>


</div>
```

</div>

<script>


/*
|--------------------------------------------------------------------------
| MỞ FORM THÊM
|--------------------------------------------------------------------------
*/

function openCreateForm() {

    document
        .getElementById("createModal")
        .style
        .display = "flex";

}


/*
|--------------------------------------------------------------------------
| ĐÓNG FORM THÊM
|--------------------------------------------------------------------------
*/

function closeCreateForm() {

    document
        .getElementById("createModal")
        .style
        .display = "none";

}


/*
|--------------------------------------------------------------------------
| TÌM KIẾM
|--------------------------------------------------------------------------
*/

function searchNotification() {

    let keyword =

        document
            .getElementById("search")
            .value
            .toLowerCase();


    let cards =

        document
            .querySelectorAll(
                ".notification-card"
            );


    cards.forEach(function(card) {


        let text =

            card
                .innerText
                .toLowerCase();


        if (text.includes(keyword)) {

            card.style.display = "flex";

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

function showDetail(button) {

    let card =

        button.closest(
            ".notification-card"
        );


    let title =

        card
            .querySelector("h2")
            .innerText;


    let content =

        card
            .querySelector("p")
            .innerText;


    let info =

        card
            .querySelector(".notification-info")
            .innerText;


    document
        .getElementById("detailText")
        .innerHTML =

        "<h3>" + title + "</h3>" +

        "<p>" + content + "</p>" +

        "<p>" + info + "</p>";


    document
        .getElementById("detailModal")
        .style
        .display = "flex";

}


/*
|--------------------------------------------------------------------------
| ĐÓNG CHI TIẾT
|--------------------------------------------------------------------------
*/

function closeDetail() {

    document
        .getElementById("detailModal")
        .style
        .display = "none";

}


</script>

<?php require_once "../includes/footer.php"; ?>
