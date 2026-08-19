<?php
session_start();

/* ======================================================================
   ĐĂNG KÝ THAM GIA SỰ KIỆN
   Đối tượng dữ liệu: Lượt đăng ký sự kiện (đăng ký / hủy đăng ký)
   ====================================================================== */

/* ----------------------------------------------------------------------
   1) TỔ CHỨC DỮ LIỆU BẰNG MẢNG
   - $dsSuKien: danh sách sự kiện chính thức đang mở đăng ký (dữ liệu mẫu,
     ở buổi sau sẽ thay bằng dữ liệu lấy từ bảng "sự kiện đã duyệt").
   - $_SESSION['dsDangKy']: mảng lưu toàn bộ lượt đăng ký, mỗi phần tử là
     1 mảng kết hợp (associative array) đại diện cho 1 bản ghi.
   ---------------------------------------------------------------------- */
$dsSuKien = [
    "Ngày hội Công nghệ thông tin 2026",
    "Workshop Lập trình Web cơ bản",
    "Cuộc thi Hackathon Sinh viên",
    "Seminar Kỹ năng mềm cho tân sinh viên",
];

if (!isset($_SESSION['dsDangKy'])) {
    $_SESSION['dsDangKy'] = [];
}

$thongBao = "";
$loaiThongBao = ""; // "success" | "error"

/* ----------------------------------------------------------------------
   2) HÀM TỰ ĐỊNH NGHĨA #1 (xử lý nghiệp vụ chính)
   Tìm vị trí (index) của bản ghi đăng ký ứng với 1 sinh viên + 1 sự kiện
   trong mảng danh sách đăng ký. Trả về -1 nếu chưa từng đăng ký.
   ---------------------------------------------------------------------- */
function timViTriDangKy(array $dsDangKy, string $maSV, string $suKien): int
{
    foreach ($dsDangKy as $index => $dk) {
        if ($dk['maSV'] === $maSV && $dk['suKien'] === $suKien) {
            return $index;
        }
    }
    return -1;
}

/* ----------------------------------------------------------------------
   2b) HÀM TỰ ĐỊNH NGHĨA #2
   Đếm số lượt đang ở trạng thái "Đã đăng ký" cho 1 sự kiện cụ thể
   -> dùng để hiển thị số lượng người tham gia hiện tại của mỗi sự kiện.
   ---------------------------------------------------------------------- */
function demSoLuongDangKy(array $dsDangKy, string $suKien): int
{
    $dem = 0;
    foreach ($dsDangKy as $dk) {
        if ($dk['suKien'] === $suKien && $dk['trangThai'] === 'Đã đăng ký') {
            $dem++;
        }
    }
    return $dem;
}

/* ----------------------------------------------------------------------
   3) TIẾP NHẬN VÀ XỬ LÝ DỮ LIỆU NHẬP (khi submit form - POST)
   ---------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $hoTen    = trim($_POST['hoTen'] ?? '');
    $maSV     = trim($_POST['maSV'] ?? '');
    $suKien   = trim($_POST['suKien'] ?? '');
    $hanhDong = trim($_POST['hanhDong'] ?? '');
    $lyDoHuy  = trim($_POST['lyDoHuy'] ?? '');

    /* ----- ĐIỀU KIỆN kiểm tra dữ liệu bắt buộc ----- */
    if ($hoTen === '' || $maSV === '' || $suKien === '' || $hanhDong === '') {
        $thongBao = "Vui lòng nhập đầy đủ các trường bắt buộc (*).";
        $loaiThongBao = "error";

    } elseif (!in_array($suKien, $dsSuKien, true)) {
        $thongBao = "Sự kiện không hợp lệ.";
        $loaiThongBao = "error";

    } elseif ($hanhDong === 'Hủy đăng ký' && $lyDoHuy === '') {
        $thongBao = "Vui lòng nhập lý do khi hủy đăng ký.";
        $loaiThongBao = "error";

    } else {
        // Sử dụng hàm tự định nghĩa để tra cứu bản ghi đã tồn tại chưa
        $viTri = timViTriDangKy($_SESSION['dsDangKy'], $maSV, $suKien);

        /* ----- ĐIỀU KIỆN phân loại theo hành động ----- */
        if ($hanhDong === 'Đăng ký') {

            if ($viTri !== -1 && $_SESSION['dsDangKy'][$viTri]['trangThai'] === 'Đã đăng ký') {
                $thongBao = "Sinh viên $maSV đã đăng ký sự kiện này rồi.";
                $loaiThongBao = "error";

            } elseif ($viTri !== -1) {
                // Đã từng đăng ký nhưng trước đó đã hủy -> đăng ký lại
                $_SESSION['dsDangKy'][$viTri]['trangThai'] = 'Đã đăng ký';
                $_SESSION['dsDangKy'][$viTri]['hoTen']     = $hoTen;
                $_SESSION['dsDangKy'][$viTri]['lyDo']      = '';
                $thongBao = "Đăng ký lại thành công!";
                $loaiThongBao = "success";

            } else {
                // Thêm một phần tử (bản ghi) mới vào mảng danh sách đăng ký
                $_SESSION['dsDangKy'][] = [
                    "hoTen"     => $hoTen,
                    "maSV"      => $maSV,
                    "suKien"    => $suKien,
                    "trangThai" => "Đã đăng ký",
                    "lyDo"      => "",
                ];
                $thongBao = "Đăng ký tham gia sự kiện thành công!";
                $loaiThongBao = "success";
            }

        } else { // $hanhDong === 'Hủy đăng ký'

            if ($viTri === -1) {
                $thongBao = "Không tìm thấy đăng ký của sinh viên $maSV cho sự kiện này.";
                $loaiThongBao = "error";

            } elseif ($_SESSION['dsDangKy'][$viTri]['trangThai'] === 'Đã hủy') {
                $thongBao = "Đăng ký này đã được hủy trước đó.";
                $loaiThongBao = "error";

            } else {
                $_SESSION['dsDangKy'][$viTri]['trangThai'] = 'Đã hủy';
                $_SESSION['dsDangKy'][$viTri]['lyDo']      = $lyDoHuy;
                $thongBao = "Đã hủy đăng ký thành công.";
                $loaiThongBao = "success";
            }
        }
    }
}

/* ----------------------------------------------------------------------
   4) XÓA TOÀN BỘ DỮ LIỆU (nút phụ, tiện để test lại từ đầu)
   ---------------------------------------------------------------------- */
if (isset($_GET['reset'])) {
    $_SESSION['dsDangKy'] = [];
    header("Location: dangky_sukien.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tham gia sự kiện</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

    <h1>ĐĂNG KÝ THAM GIA SỰ KIỆN</h1>
    <div class="underline"></div>
    <p class="subtitle">Vui lòng nhập thông tin để đăng ký hoặc hủy đăng ký tham gia sự kiện.</p>

    <?php if ($thongBao !== ''): ?>
        <div class="thongbao <?= $loaiThongBao ?>">
            <?= htmlspecialchars($thongBao) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="dangky_sukien.php">

        <div class="field">
            <label>Họ và tên <span class="required">*</span></label>
            <input type="text" name="hoTen" placeholder="Nguyễn Văn A"
                   value="<?= htmlspecialchars($_POST['hoTen'] ?? '') ?>">
        </div>

        <div class="row-2">
            <div class="field">
                <label>Mã sinh viên <span class="required">*</span></label>
                <input type="text" name="maSV" placeholder="224001823"
                       value="<?= htmlspecialchars($_POST['maSV'] ?? '') ?>">
            </div>

            <div class="field">
                <label>Sự kiện <span class="required">*</span></label>
                <select name="suKien" id="suKien">
                    <option value="">-- Chọn sự kiện --</option>
                    <?php foreach ($dsSuKien as $sk): ?>
                        <option value="<?= htmlspecialchars($sk) ?>"
                            <?= (($_POST['suKien'] ?? '') === $sk) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sk) ?>
                            (<?= demSoLuongDangKy($_SESSION['dsDangKy'], $sk) ?> đã đăng ký)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label>Hành động <span class="required">*</span></label>
            <div class="radio-group">
                <label><input type="radio" name="hanhDong" value="Đăng ký"
                        onclick="document.getElementById('boxLyDo').style.display='none'"
                        <?= (($_POST['hanhDong'] ?? 'Đăng ký') === 'Đăng ký') ? 'checked' : '' ?>> Đăng ký</label>
                <label><input type="radio" name="hanhDong" value="Hủy đăng ký"
                        onclick="document.getElementById('boxLyDo').style.display='block'"
                        <?= (($_POST['hanhDong'] ?? '') === 'Hủy đăng ký') ? 'checked' : '' ?>> Hủy đăng ký</label>
            </div>
        </div>

        <div class="field" id="boxLyDo"
             style="display: <?= (($_POST['hanhDong'] ?? '') === 'Hủy đăng ký') ? 'block' : 'none' ?>;">
            <label>Lý do hủy <span class="required">*</span></label>
            <textarea name="lyDoHuy" placeholder="Nhập lý do hủy đăng ký..."><?= htmlspecialchars($_POST['lyDoHuy'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn">Xác nhận →</button>
    </form>

    <h2 class="section-title">Danh sách đăng ký</h2>

    <?php if (count($_SESSION['dsDangKy']) === 0): ?>
        <p class="empty-note">Chưa có lượt đăng ký nào.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>#</th>
                <th>Họ và tên</th>
                <th>Mã SV</th>
                <th>Sự kiện</th>
                <th>Trạng thái</th>
                <th>Lý do hủy</th>
            </tr>
            <?php
            // VÒNG LẶP duyệt mảng danh sách đăng ký để hiển thị dạng bảng
            $stt = 1;
            foreach ($_SESSION['dsDangKy'] as $dk):
            ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><?= htmlspecialchars($dk['hoTen']) ?></td>
                    <td><?= htmlspecialchars($dk['maSV']) ?></td>
                    <td><?= htmlspecialchars($dk['suKien']) ?></td>
                    <td>
                        <?php if ($dk['trangThai'] === 'Đã đăng ký'): ?>
                            <span class="badge dangky">Đã đăng ký</span>
                        <?php else: ?>
                            <span class="badge huy">Đã hủy</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $dk['lyDo'] !== '' ? htmlspecialchars($dk['lyDo']) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p style="margin-top:24px;">
        <a href="dangky_sukien.php?reset=1" style="color:#8a94a3; font-size:13px;">Xóa toàn bộ dữ liệu (dùng để test lại)</a>
    </p>

</div>
</body>
</html>