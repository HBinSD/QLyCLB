<?php
// Dữ liệu thông báo mẫu
$thongBao = [
    [
        "id" => 1,
        "tieuDe" => "Tuyển thành viên CLB năm 2026",
        "noiDung" => "CLB mở đơn đăng ký tuyển thành viên mới. Hãy tham gia để cùng học tập và phát triển.",
        "ngayDang" => "16/08/2026",
        "nguoiDang" => "Ban chủ nhiệm CLB",
        "anh" => "https://images.unsplash.com/photo-1523580846011-d3a5bc25702b"
    ],
    [
        "id" => 2,
        "tieuDe" => "Thông báo họp CLB tháng 8",
        "noiDung" => "CLB tổ chức buổi họp định kỳ tháng 8. Thành viên tham gia đầy đủ và đúng giờ.",
        "ngayDang" => "15/08/2026",
        "nguoiDang" => "Ban chủ nhiệm CLB",
        "anh" => "https://images.unsplash.com/photo-1515169067868-5387ec356754"
    ],
    [
        "id" => 3,
        "tieuDe" => "Đăng ký tham gia hoạt động tình nguyện",
        "noiDung" => "CLB tổ chức hoạt động tình nguyện dành cho các thành viên. Hạn đăng ký đến ngày 20/08.",
        "ngayDang" => "14/08/2026",
        "nguoiDang" => "Nguyễn Văn A",
        "anh" => "https://images.unsplash.com/photo-1559027615-cd4628902d4a"
    ]
];

// Xử lý thêm thông báo
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tieuDe = $_POST["tieuDe"];
    $noiDung = $_POST["noiDung"];
    $ngayDang = $_POST["ngayDang"];
    $nguoiDang = $_POST["nguoiDang"];
    $anh = $_POST["anh"];

    // Trong bài chưa dùng database nên chỉ minh họa
    $thongBaoMoi = [
        "id" => count($thongBao) + 1,
        "tieuDe" => $tieuDe,
        "noiDung" => $noiDung,
        "ngayDang" => $ngayDang,
        "nguoiDang" => $nguoiDang,
        "anh" => $anh
    ];

    // Thêm vào mảng
    $thongBao[] = $thongBaoMoi;
}
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

            <button class="btn-add" onclick="openForm()">
                + Thêm thông báo
            </button>

        </div>


        <!-- Thanh tìm kiếm -->
        <div class="search-box">

            <input
                type="text"
                id="search"
                placeholder="🔍 Tìm kiếm thông báo..."
                onkeyup="searchNotification()"
            >

        </div>


        <!-- Danh sách thông báo -->
        <div class="notification-list" id="notificationList">

            <?php foreach ($thongBao as $tb): ?>

                <div class="notification-card">

                    <img
                        src="<?= $tb["anh"] ?>"
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
                                📅 <?= $tb["ngayDang"] ?>
                            </span>

                            <span>
                                👤 <?= htmlspecialchars($tb["nguoiDang"]) ?>
                            </span>

                        </div>

                        <div class="actions">

                            <button class="btn-detail">
                                Xem chi tiết
                            </button>

                            <button class="btn-edit">
                                Sửa
                            </button>

                            <button class="btn-delete">
                                Xóa
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </main>


    <!-- Form thêm thông báo -->
    <div class="modal" id="notificationModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>Thêm thông báo</h2>

                <span onclick="closeForm()" class="close">
                    &times;
                </span>

            </div>


            <form method="POST">

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
                        onclick="closeForm()"
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


    <script>

        // Mở form
        function openForm() {
            document.getElementById("notificationModal").style.display = "flex";
        }


        // Đóng form
        function closeForm() {
            document.getElementById("notificationModal").style.display = "none";
        }


        // Tìm kiếm thông báo
        function searchNotification() {

            let keyword =
                document.getElementById("search").value.toLowerCase();

            let cards =
                document.querySelectorAll(".notification-card");

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

    </script>

</body>

</html>