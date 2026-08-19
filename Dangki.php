<?php
session_start();

$dsSuKien = [
    "Ngày hội Công nghệ đại học Thủ Đô",
    "Ngày hội hiến máu tình nguyện",
    "Tuần sinh hoạt công dân",
    "Ngày hội tư vẫn tuyển sinh",
];
if (!isset($_SESSION['dsDangKy'])) {
    $_SESSION['dsDangKy'] = [];
}
$errors = [
    'hoTen'    => '',
    'maSV'     => '',
    'suKien'   => '',
    'hanhDong' => '',
    'lyDoHuy'  => ''
];
$thongBao = "";
$loaiThongBao = "";
function timViTriDangKy(array $dsDangKy, string $maSV, string $suKien): int
{
    foreach ($dsDangKy as $index => $dk) {
        if ($dk['maSV'] === $maSV && $dk['suKien'] === $suKien) {
            return $index;
        }
    }
    return -1;
}
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen    = trim(strip_tags($_POST['hoTen'] ?? ''));
    $maSV     = trim(strip_tags($_POST['maSV'] ?? ''));
    $suKien   = trim(strip_tags($_POST['suKien'] ?? ''));
    $hanhDong = trim(strip_tags($_POST['hanhDong'] ?? ''));
    $lyDoHuy  = trim(strip_tags($_POST['lyDoHuy'] ?? ''));
    if (empty($hoTen)) {
        $errors['hoTen'] = "Vui lòng nhập họ và tên.";
    } else if (mb_strlen($hoTen) < 2 || mb_strlen($hoTen) > 50) {
        $errors['hoTen'] = "Họ và tên phải từ 2 đến 50 ký tự.";
    }
    if (empty($maSV)) {
        $errors['maSV'] = "Vui lòng nhập mã sinh viên.";
    } else if (!preg_match('/^[a-zA-Z0-9]{6,15}$/', $maSV)) {
        $errors['maSV'] = "Mã SV chỉ gồm chữ/số, độ dài 6-15 ký tự.";
    }
    if (empty($suKien)) {
        $errors['suKien'] = "Vui lòng chọn sự kiện.";
    } else if (!in_array($suKien, $dsSuKien, true)) {
        $errors['suKien'] = "Sự kiện không hợp lệ.";
    }
    if (empty($hanhDong)) {
        $errors['hanhDong'] = "Vui lòng chọn hành động.";
    } else if (!in_array($hanhDong, ['Đăng ký', 'Hủy đăng ký'], true)) {
        $errors['hanhDong'] = "Hành động không hợp lệ.";
    }
    if ($hanhDong === 'Hủy đăng ký') {
        if (empty($lyDoHuy)) {
            $errors['lyDoHuy'] = "Vui lòng nhập lý do khi hủy đăng ký.";
        } else if (mb_strlen($lyDoHuy) > 250) {
            $errors['lyDoHuy'] = "Lý do hủy tối đa 250 ký tự.";
        }
    }
    if (!array_filter($errors)) {
        $viTri = timViTriDangKy($_SESSION['dsDangKy'], $maSV, $suKien);
        if ($hanhDong === 'Đăng ký') {
            if ($viTri !== -1 && $_SESSION['dsDangKy'][$viTri]['trangThai'] === 'Đã đăng ký') {
                $thongBao = "Sinh viên $maSV đã đăng ký sự kiện này rồi.";
                $loaiThongBao = "error";
                $errors['maSV'] = "Mã SV đã đăng ký sự kiện này.";
            } else if ($viTri !== -1) {
                $_SESSION['dsDangKy'][$viTri]['trangThai'] = 'Đã đăng ký';
                $_SESSION['dsDangKy'][$viTri]['hoTen']     = $hoTen;
                $_SESSION['dsDangKy'][$viTri]['lyDo']      = '';
                $thongBao = "Đăng ký lại thành công!";
                $loaiThongBao = "success";
            } else {
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
        } else { 
            if ($viTri === -1) {
                $thongBao = "Không tìm thấy đăng ký của sinh viên $maSV cho sự kiện này.";
                $loaiThongBao = "error";
                $errors['maSV'] = "Mã SV chưa đăng ký sự kiện này.";
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
        if ($loaiThongBao === 'success') {
            $_POST = [];
        }
    } else {
        $thongBao = "Dữ liệu nhập vào chưa hợp lệ. Vui lòng kiểm tra các ô báo lỗi!";
        $loaiThongBao = "error";
    }
}
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
    <style>
        .error-message {
            color: #d9534f;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }
        input.has-error, select.has-error, textarea.has-error {
            border-color: #d9534f !important;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>ĐĂNG KÝ THAM GIA SỰ KIỆN</h1>
    <div class="underline"></div>
    <p class="subtitle">Vui lòng nhập thông tin để đăng ký hoặc hủy đăng ký tham gia sự kiện.</p>
    <form method="POST" action="#ketqua">
        <div class="field">
            <label>Họ và tên <span class="required">*</span></label>
            <input type="text" name="hoTen" placeholder="Họ và tên...."
                   class="<?= !empty($errors['hoTen']) ? 'has-error' : '' ?>"
                   value="<?= empty($errors['hoTen']) ? htmlspecialchars($_POST['hoTen'] ?? '') : '' ?>">
            <?php if (!empty($errors['hoTen'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['hoTen']) ?></span>
            <?php endif; ?>
        </div>
        <div class="row-2">
            <div class="field">
                <label>Mã sinh viên <span class="required">*</span></label>
                <input type="text" name="maSV" placeholder="Mã Sinh Viên....."
                       class="<?= !empty($errors['maSV']) ? 'has-error' : '' ?>"
                       value="<?= empty($errors['maSV']) ? htmlspecialchars($_POST['maSV'] ?? '') : '' ?>">
                <?php if (!empty($errors['maSV'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['maSV']) ?></span>
                <?php endif; ?>
            </div>
            <div class="field">
                <label>Sự kiện <span class="required">*</span></label>
                <select name="suKien" id="suKien" class="<?= !empty($errors['suKien']) ? 'has-error' : '' ?>">
                    <option value="">-- Chọn sự kiện --</option>
                    <?php 
                    $selectedSK = empty($errors['suKien']) ? ($_POST['suKien'] ?? '') : '';
                    foreach ($dsSuKien as $sk): 
                    ?>
                        <option value="<?= htmlspecialchars($sk) ?>" <?= ($selectedSK === $sk) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sk) ?>
                            (<?= demSoLuongDangKy($_SESSION['dsDangKy'], $sk) ?> đã đăng ký)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['suKien'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['suKien']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="field">
            <label>Hành động <span class="required">*</span></label>
            <?php $hanhDongVal = $_POST['hanhDong'] ?? 'Đăng ký'; ?>
            <div class="radio-group">
                <label>
                    <input type="radio" name="hanhDong" value="Đăng ký"
                           onclick="document.getElementById('boxLyDo').style.display='none'"
                           <?= ($hanhDongVal === 'Đăng ký') ? 'checked' : '' ?>> Đăng ký
                </label>
                <label>
                    <input type="radio" name="hanhDong" value="Hủy đăng ký"
                           onclick="document.getElementById('boxLyDo').style.display='block'"
                           <?= ($hanhDongVal === 'Hủy đăng ký') ? 'checked' : '' ?>> Hủy đăng ký
                </label>
            </div>
            <?php if (!empty($errors['hanhDong'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['hanhDong']) ?></span>
            <?php endif; ?>
        </div>
        <div class="field" id="boxLyDo"
             style="display: <?= ($hanhDongVal === 'Hủy đăng ký') ? 'block' : 'none' ?>;">
            <label>Lý do hủy <span class="required">*</span></label>
            <textarea name="lyDoHuy" placeholder="Nhập lý do hủy đăng ký..."
                      class="<?= !empty($errors['lyDoHuy']) ? 'has-error' : '' ?>"><?= empty($errors['lyDoHuy']) ? htmlspecialchars($_POST['lyDoHuy'] ?? '') : '' ?></textarea>
            <?php if (!empty($errors['lyDoHuy'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['lyDoHuy']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Xác nhận →</button>
    </form>
    <div id="ketqua">
    <?php if ($thongBao !== ''): ?>
        <div class="thongbao <?= $loaiThongBao ?>">
            <?= htmlspecialchars($thongBao) ?>
        </div>
    <?php endif; ?>
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
    </div>
    <p style="margin-top:24px;">
        <a href="dangky_sukien.php?reset=1" style="color:#8a94a3; font-size:13px;">Xóa toàn bộ dữ liệu (dùng để test lại)</a>
    </p>
</div>
</body>
</html>