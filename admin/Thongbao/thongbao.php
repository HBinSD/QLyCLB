<?php

require_once "../includes/auth.php";
require_once "../database/database.php";

$database = new Database();
$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| THÊM THÔNG BÁO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | CREATE
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
            $nguoiDang !== "" &&
            $anh !== ""
        ) {

            $sql = "
                INSERT INTO ThongBao
                (
                    tieuDe,
                    noiDung,
                    ngayDang,
                    nguoiDang,
                    anh
                )
                VALUES
                (
                    :tieuDe,
                    :noiDung,
                    :ngayDang,
                    :nguoiDang,
                    :anh
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ":tieuDe" => $tieuDe,
                ":noiDung" => $noiDung,
                ":ngayDang" => $ngayDang,
                ":nguoiDang" => $nguoiDang,
                ":anh" => $anh
            ]);
        }

        header("Location: thongbao.php?success=created");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
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
                UPDATE ThongBao
                SET
                    tieuDe = :tieuDe,
                    noiDung = :noiDung,
                    ngayDang = :ngayDang,
                    nguoiDang = :nguoiDang,
                    anh = :anh
                WHERE id = :id
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ":tieuDe" => $tieuDe,
                ":noiDung" => $noiDung,
                ":ngayDang" => $ngayDang,
                ":nguoiDang" => $nguoiDang,
                ":anh" => $anh,
                ":id" => $id
            ]);
        }

        header("Location: thongbao.php?success=updated");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    if ($action === "delete") {

        $id = (int)($_POST["id"] ?? 0);

        if ($id > 0) {

            $sql = "
                DELETE FROM ThongBao
                WHERE id = :id
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ":id" => $id
            ]);
        }

        header("Location: thongbao.php?success=deleted");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| LẤY ID THÔNG BÁO CẦN SỬA
|--------------------------------------------------------------------------
*/

$editId = isset($_GET["edit"])
    ? (int)$_GET["edit"]
    : 0;

$thongBaoSua = null;

if ($editId > 0) {

    $sql = "
        SELECT *
        FROM ThongBao
        WHERE id = :id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ":id" => $editId
    ]);

    $thongBaoSua = $stmt->fetch(PDO::FETCH_ASSOC);
}


/*
|--------------------------------------------------------------------------
| TÌM KIẾM
|--------------------------------------------------------------------------
*/

$keyword = trim($_GET["keyword"] ?? "");


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
    FROM ThongBao
    WHERE
        tieuDe LIKE :keyword
        OR noiDung LIKE :keyword
        OR nguoiDang LIKE :keyword
";

$stmt = $db->prepare($sqlCount);

$stmt->execute([
    ":keyword" => "%" . $keyword . "%"
]);

$tongSoThongBao = $stmt->fetchColumn();

$tongSoTrang = ceil(
    $tongSoThongBao / $soLuongMoiTrang
);

if (
    $tongSoTrang > 0 &&
    $trangHienTai > $tongSoTrang
) {
    $trangHienTai = $tongSoTrang;
}


/*
|--------------------------------------------------------------------------
| LẤY DANH SÁCH THÔNG BÁO
|--------------------------------------------------------------------------
*/

$viTriBatDau =
    ($trangHienTai - 1) * $soLuongMoiTrang;

$sql = "
    SELECT *
    FROM ThongBao
    WHERE
        tieuDe LIKE :keyword
        OR noiDung LIKE :keyword
        OR nguoiDang LIKE :keyword
    ORDER BY ngayDang DESC
    LIMIT :limit
    OFFSET :offset
";

$stmt = $db->prepare($sql);

$stmt->bindValue(
    ":keyword",
    "%" . $keyword . "%",
    PDO::PARAM_STR
);

$stmt->bindValue(
    ":limit",
    $soLuongMoiTrang,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ":offset",
    $viTriBatDau,
    PDO::PARAM_INT
);

$stmt->execute();

$thongBaoHienThi =
    $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<?php require_once "../includes/headers.php"; ?>

<?php require_once "../includes/sidebar-admin.php"; ?>


<main class="main-content">

<div class="container">


    <div class="page-title">

        <div>

            <h1>Quản lý thông báo</h1>

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


    <!-- THÔNG BÁO THÀNH CÔNG -->

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


    <!-- TÌM KIẾM -->

    <form
        method="GET"
        class="search-box"
    >

        <input
            type="text"
            name="keyword"
            placeholder="🔍 Tìm kiếm thông báo..."
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <button
            type="submit"
            class="btn-search"
        >
            Tìm kiếm
        </button>


        <?php if ($keyword !== ""): ?>

            <a
                href="thongbao.php"
                class="btn-cancel"
            >
                Xóa tìm kiếm
            </a>

        <?php endif; ?>

    </form>


    <!-- DANH SÁCH THÔNG BÁO -->

    <div
        class="notification-list"
        id="notificationList"
    >


        <?php if (count($thongBaoHienThi) > 0): ?>


            <?php foreach ($thongBaoHienThi as $tb): ?>


                <div class="notification-card">


                    <img
                        src="<?= htmlspecialchars($tb["anh"]) ?>"
                        alt="Ảnh thông báo"
                    >


                    <div class="notification-content">


                        <span class="badge">

                            Thông báo

                        </span>


                        <h2>

                            <?= htmlspecialchars($tb["tieuDe"]) ?>

                        </h2>


                        <p>

                            <?= htmlspecialchars($tb["noiDung"]) ?>

                        </p>


                        <div class="notification-info">


                            <span>

                                📅

                                <?= date(
                                    "d/m/Y",
                                    strtotime($tb["ngayDang"])
                                ) ?>

                            </span>


                            <span>

                                👤

                                <?= htmlspecialchars(
                                    $tb["nguoiDang"]
                                ) ?>

                            </span>


                        </div>


                        <!-- ACTION -->

                        <div class="actions">


                            <button
                                class="btn-detail"
                                onclick="showDetail(this)"
                            >

                                Xem chi tiết

                            </button>


                            <!-- CẬP NHẬT -->

                            <a
                                href="thongbao.php?edit=<?= $tb["id"] ?>"
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
                                    value="<?= $tb["id"] ?>"
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


            <p class="empty">

                Không tìm thấy thông báo.

            </p>


        <?php endif; ?>


    </div>


    <!-- PHÂN TRANG -->

    <?php if ($tongSoTrang > 1): ?>

        <div class="pagination">


            <?php if ($trangHienTai > 1): ?>

                <a
                    href="?page=<?= $trangHienTai - 1 ?>&keyword=<?= urlencode($keyword) ?>"
                >

                    « Trước

                </a>

            <?php endif; ?>


            <?php for (
                $i = 1;
                $i <= $tongSoTrang;
                $i++
            ): ?>


                <a
                    href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>"
                    class="<?= $i == $trangHienTai ? 'active' : '' ?>"
                >

                    <?= $i ?>

                </a>


            <?php endfor; ?>


            <?php if ($trangHienTai < $tongSoTrang): ?>

                <a
                    href="?page=<?= $trangHienTai + 1 ?>&keyword=<?= urlencode($keyword) ?>"
                >

                    Sau »

                </a>

            <?php endif; ?>


        </div>

    <?php endif; ?>


</div>

</main>


<!-- FORM THÊM -->

<div
    class="modal"
    id="createModal"
>


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


            <label>

                Tiêu đề thông báo

            </label>


            <input
                type="text"
                name="tieuDe"
                placeholder="Nhập tiêu đề..."
                required
            >


            <label>

                Nội dung

            </label>


            <textarea
                name="noiDung"
                rows="5"
                placeholder="Nhập nội dung thông báo..."
                required
            ></textarea>


            <label>

                Ngày đăng

            </label>


            <input
                type="date"
                name="ngayDang"
                required
            >


            <label>

                Người đăng

            </label>


            <input
                type="text"
                name="nguoiDang"
                placeholder="Nhập người đăng..."
                required
            >


            <label>

                Link ảnh

            </label>


            <input
                type="text"
                name="anh"
                placeholder="Dán link ảnh..."
                required
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


</div>


<!-- FORM CẬP NHẬT -->

<?php if ($thongBaoSua !== null): ?>


<div
    class="modal"
    id="editModal"
    style="display: flex;"
>


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
                value="<?= $thongBaoSua["id"] ?>"
            >


            <label>Tiêu đề thông báo</label>


            <input
                type="text"
                name="tieuDe"
                value="<?= htmlspecialchars($thongBaoSua["tieuDe"]) ?>"
                required
            >


            <label>Nội dung</label>


            <textarea
                name="noiDung"
                rows="5"
                required
            ><?= htmlspecialchars($thongBaoSua["noiDung"]) ?></textarea>


            <label>Ngày đăng</label>


            <input
                type="date"
                name="ngayDang"
                value="<?= htmlspecialchars($thongBaoSua["ngayDang"]) ?>"
                required
            >


            <label>Người đăng</label>


            <input
                type="text"
                name="nguoiDang"
                value="<?= htmlspecialchars($thongBaoSua["nguoiDang"]) ?>"
                required
            >


            <label>Link ảnh</label>


            <input
                type="text"
                name="anh"
                value="<?= htmlspecialchars($thongBaoSua["anh"]) ?>"
                required
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


</div>


<?php endif; ?>


<!-- XEM CHI TIẾT -->

<div
    class="modal"
    id="detailModal"
>


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


</div>


<script>


function openCreateForm() {

    document
        .getElementById("createModal")
        .style
        .display = "flex";

}


function closeCreateForm() {

    document
        .getElementById("createModal")
        .style
        .display = "none";

}


function showDetail(button) {

    let card =
        button.closest(".notification-card");

    let title =
        card.querySelector("h2").innerText;

    let content =
        card.querySelector("p").innerText;

    let info =
        card.querySelector(".notification-info")
        .innerText;


    document
        .getElementById("detailText")
        .innerHTML =
            "<h3>" + title + "</h3>" +
            "<p>" + content + "</p>" +
            "<p>" + info.replace(/\n/g, "<br>") + "</p>";


    document
        .getElementById("detailModal")
        .style
        .display = "flex";

}


function closeDetail() {

    document
        .getElementById("detailModal")
        .style
        .display = "none";

}


</script>


<?php require_once "../includes/footer.php"; ?>