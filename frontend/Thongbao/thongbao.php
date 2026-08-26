<?php
session_start();
if (!isset($_SESSION["thongBao"])) {
    $_SESSION["thongBao"] = [
        [
            "id" => 1,
            "tieuDe" => "Tuyển thành viên CLB năm 2026",
            "noiDung" => "CLB mở đơn đăng ký tuyển thành viên mới. Hãy tham gia để cùng học tập và phát triển.",
            "ngayDang" => "2026-08-16",
            "nguoiDang" => "Ban chủ nhiệm CLB",
            "anh" => "https://images.unsplash.com/photo-1523580846011-d3a5bc25702b"
        ],
        [
            "id" => 2,
            "tieuDe" => "Thông báo họp CLB tháng 8",
            "noiDung" => "CLB tổ chức buổi họp định kỳ tháng 8. Thành viên tham gia đầy đủ và đúng giờ.",
            "ngayDang" => "2026-08-15",
            "nguoiDang" => "Ban chủ nhiệm CLB",
            "anh" => "https://images.unsplash.com/photo-1515169067868-5387ec356754"
        ],
        [
            "id" => 3,
            "tieuDe" => "Đăng ký tham gia hoạt động tình nguyện",
            "noiDung" => "CLB tổ chức hoạt động tình nguyện dành cho các thành viên. Hạn đăng ký đến ngày 20/08.",
            "ngayDang" => "2026-08-14",
            "nguoiDang" => "Nguyễn Văn A",
            "anh" => "https://images.unsplash.com/photo-1559027615-cd4628902d4a"
        ]
    ];
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    if ($action === "create") {

        $tieuDe = trim($_POST["tieuDe"] ?? "");
        $noiDung = trim($_POST["noiDung"] ?? "");
        $ngayDang = $_POST["ngayDang"] ?? "";
        $nguoiDang = trim($_POST["nguoiDang"] ?? "");
        $anh = trim($_POST["anh"] ?? "");

        if ($tieuDe !== "" && $noiDung !== "" && $ngayDang !== "" && $nguoiDang !== "" && $anh !== "") {

            $maxId = 0;

           foreach ($_SESSION["thongBao"] as $tb) {
                if ($tb["id"] > $maxId) {
                    $maxId = $tb["id"];
                }
            }

            $thongBaoMoi = [
                "id" => $maxId + 1,
                "tieuDe" => $tieuDe,
                "noiDung" => $noiDung,
                "ngayDang" => $ngayDang,
                "nguoiDang" => $nguoiDang,
                "anh" => $anh
            ];

            $_SESSION["thongBao"][] = $thongBaoMoi;
        }

        header("Location: thongbao.php?success=created");
        exit;
    }


    if ($action === "update") {

        $id = (int)($_POST["id"] ?? 0);

        foreach ($_SESSION["thongBao"] as $key => $tb) {

            if ($tb["id"] === $id) {

                $_SESSION["thongBao"][$key]["tieuDe"] =
                    trim($_POST["tieuDe"] ?? "");

                $_SESSION["thongBao"][$key]["noiDung"] =
                    trim($_POST["noiDung"] ?? "");

                $_SESSION["thongBao"][$key]["ngayDang"] =
                    $_POST["ngayDang"] ?? "";

                $_SESSION["thongBao"][$key]["nguoiDang"] =
                    trim($_POST["nguoiDang"] ?? "");

                $_SESSION["thongBao"][$key]["anh"] =
                    trim($_POST["anh"] ?? "");

                break;
            }
        }

        header("Location: thongbao.php?success=updated");
        exit;
    }

    if ($action === "delete") {

        $id = (int)($_POST["id"] ?? 0);

        foreach ($_SESSION["thongBao"] as $key => $tb) {

            if ($tb["id"] === $id) {

                unset($_SESSION["thongBao"][$key]);

                break;
            }
        }

        $_SESSION["thongBao"] = array_values($_SESSION["thongBao"]);

        header("Location: thongbao.php?success=deleted");
        exit;
    }
}


$editId = isset($_GET["edit"]) ? (int)$_GET["edit"] : 0;

$thongBaoSua = null;

if ($editId > 0) {

    foreach ($_SESSION["thongBao"] as $tb) {

        if ($tb["id"] === $editId) {
            $thongBaoSua = $tb;
            break;
        }
    }
}

$thongBao = $_SESSION["thongBao"];
$soLuongMoiTrang = 5;

$trangHienTai = isset($_GET["page"]) ? (int)$_GET["page"] : 1;

if ($trangHienTai < 1) {
    $trangHienTai = 1;
}

$tongSoThongBao = count($thongBao);
$tongSoTrang = ceil($tongSoThongBao / $soLuongMoiTrang);

if ($trangHienTai > $tongSoTrang && $tongSoTrang > 0) {
    $trangHienTai = $tongSoTrang;
}

$viTriBatDau = ($trangHienTai - 1) * $soLuongMoiTrang;

$thongBaoHienThi = array_slice(
    $thongBao,
    $viTriBatDau,
    $soLuongMoiTrang
);
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý thông báo CLB</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<main class="container">

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


    <!-- ================= TÌM KIẾM ================= -->

    <div class="search-box">

        <input
            type="text"
            id="search"
            placeholder="🔍 Tìm kiếm thông báo..."
            onkeyup="searchNotification()"
        >

    </div>


    <!-- ================= DANH SÁCH ================= -->

    <div
        class="notification-list"
        id="notificationList"
    >

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
                            <?= date("d/m/Y", strtotime($tb["ngayDang"])) ?>
                        </span>

                        <span>
                            👤
                            <?= htmlspecialchars($tb["nguoiDang"]) ?>
                        </span>

                    </div>


                    <!-- ================= ACTION ================= -->

                    <div class="actions">

                        <!-- XEM CHI TIẾT -->

                        <button
                            class="btn-detail"
                            onclick="showDetail(this)"
                        >
                            Xem chi tiết
                        </button>


                        <!-- UPDATE -->

                        <a
                            href="?edit=<?= $tb["id"] ?>"
                            class="btn-edit"
                        >
                            Cập nhật
                        </a>


                        <!-- DELETE -->

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

    </div>


    <div class="pagination">

    <?php if ($trangHienTai > 1): ?>
        <a href="?page=<?= $trangHienTai - 1 ?>">« Trước</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
        <a href="?page=<?= $i ?>"
           class="<?= $i == $trangHienTai ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($trangHienTai < $tongSoTrang): ?>
        <a href="?page=<?= $trangHienTai + 1 ?>">Sau »</a>
    <?php endif; ?>

</div>

</main>

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


            <label>
                Tiêu đề thông báo
            </label>

            <input
                type="text"
                name="tieuDe"
                value="<?= htmlspecialchars($thongBaoSua["tieuDe"]) ?>"
                required
            >


            <label>
                Nội dung
            </label>

            <textarea
                name="noiDung"
                rows="5"
                required
            ><?= htmlspecialchars($thongBaoSua["noiDung"]) ?></textarea>


            <label>
                Ngày đăng
            </label>

            <input
                type="date"
                name="ngayDang"
                value="<?= htmlspecialchars($thongBaoSua["ngayDang"]) ?>"
                required
            >


            <label>
                Người đăng
            </label>

            <input
                type="text"
                name="nguoiDang"
                value="<?= htmlspecialchars($thongBaoSua["nguoiDang"]) ?>"
                required
            >


            <label>
                Link ảnh
            </label>

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

    document.getElementById("createModal").style.display = "flex";

}


function closeCreateForm() {

    document.getElementById("createModal").style.display = "none";

}

function searchNotification() {

    let keyword =
        document
        .getElementById("search")
        .value
        .toLowerCase();

    let cards =
        document.querySelectorAll(
            ".notification-card"
        );

    cards.forEach(function(card) {

        let text =
            card.innerText.toLowerCase();

        if (text.includes(keyword)) {

            card.style.display = "flex";

        } else {

            card.style.display = "none";

        }

    });

}

function showDetail(button) {

    let card = button.closest(".notification-card");

    let actions = card.querySelector(".actions");

    actions.style.display = "none";

    document.getElementById("detailText").innerHTML =
        "<p>" + card.innerText.replace(/\n/g, "<br>") + "</p>";

    actions.style.display = "flex";

    document.getElementById("detailModal").style.display = "flex";
}


function closeDetail() {

    document.getElementById("detailModal").style.display = "none";

}

</script>

</body>

</html>