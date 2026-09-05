<?php
session_start();

if (!isset($_SESSION['sinhvien'])) {
    $_SESSION['sinhvien'] = [
        1 => [
            'msv' => '224001802',
            'hoten' => 'Phạm Thị Hoàng Lan',
            'lop' => 'CNTT D2024',
            'khoa' => 'Công nghệ thông tin',
            'email' => 'lan@example.com',
            'sdt' => '0987654321',
            'ban' => 'Truyền thông',
            'chucvu' => 'Thành viên',
            'trangthai' => 'Đang hoạt động'
        ],

        2 => [
            'msv' => '224001781',
            'hoten' => 'Nguyễn Tùng Dương',
            'lop' => 'CNTT D2024',
            'khoa' => 'Công nghệ thông tin',
            'email' => 'duong@example.com',
            'sdt' => '0977777777',
            'ban' => 'Kỹ thuật',
            'chucvu' => 'Trưởng ban',
            'trangthai' => 'Đang hoạt động'
        ],

        3 => [
            'msv' => '224001798',
            'hoten' => 'Đinh Gia Hưng',
            'lop' => 'CNTT D2024',
            'khoa' => 'Công nghệ thông tin',
            'email' => 'hung@example.com',
            'sdt' => '0966666666',
            'ban' => 'Sự kiện',
            'chucvu' => 'Thành viên',
            'trangthai' => 'Đang hoạt động'
        ],

        4 => [
            'msv' => '224001823',
            'hoten' => 'Vũ Mai Phương',
            'lop' => 'CNTT D2024',
            'khoa' => 'Công nghệ thông tin',
            'email' => 'phuong@example.com',
            'sdt' => '0955555555',
            'ban' => 'Đối ngoại',
            'chucvu' => 'Phó ban',
            'trangthai' => 'Đang hoạt động'
        ]
    ];
}


/*
|--------------------------------------------------------------------------
| LẤY ID SINH VIÊN
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


/*
|--------------------------------------------------------------------------
| XÓA SINH VIÊN
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'])) {

    $deleteId = (int)$_POST['delete_id'];

    if (isset($_SESSION['sinhvien'][$deleteId])) {
        unset($_SESSION['sinhvien'][$deleteId]);
    }

    header("Location: sinhvien.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CẬP NHẬT SINH VIÊN
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $updateId = (int)$_POST['update_id'];

    if (isset($_SESSION['sinhvien'][$updateId])) {

        $_SESSION['sinhvien'][$updateId]['ban'] =
            $_POST['ban'];

        $_SESSION['sinhvien'][$updateId]['chucvu'] =
            $_POST['chucvu'];

        $_SESSION['sinhvien'][$updateId]['trangthai'] =
            $_POST['trangthai'];
    }

    header("Location: sinhvien.php?id=" . $updateId);
    exit;
}


$keyword = $_GET['keyword'] ?? '';

$danhSachSinhVien = [];

foreach ($_SESSION['sinhvien'] as $studentId => $student) {

    if (
        $keyword === '' ||
        stripos($student['msv'], $keyword) !== false ||
        stripos($student['hoten'], $keyword) !== false ||
        stripos($student['lop'], $keyword) !== false
    ) {
        $danhSachSinhVien[$studentId] = $student;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý sinh viên CLB</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<div class="container">

    <!-- TIÊU ĐỀ -->

    <h1>QUẢN LÝ SINH VIÊN</h1>

    <div class="line"></div>


    <!-- TÌM KIẾM -->

    <form method="GET" class="search-form">

        <label>Tìm kiếm:</label>

        <input
            type="text"
            name="keyword"
            placeholder="Nhập MSSV, họ tên hoặc lớp..."
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <button type="submit" class="btn btn-blue">
            Tìm kiếm
        </button>

        <?php if ($keyword !== ''): ?>

            <a href="sinhvien.php" class="btn btn-gray">
                Xóa tìm kiếm
            </a>

        <?php endif; ?>

    </form>


    <!-- DANH SÁCH -->

    <h2>DANH SÁCH SINH VIÊN</h2>

    <div class="line"></div>


    <div class="table-container">

        <table>

            <thead>

                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Lớp</th>
                    <th>Ban</th>
                    <th>Chức vụ</th>
                    <th>Trạng thái</th>
                </tr>

            </thead>

            <tbody>

            <?php if (count($danhSachSinhVien) > 0): ?>

                <?php $stt = 1; ?>

                <?php foreach ($danhSachSinhVien as $studentId => $student): ?>

                    <tr
                        class="<?= ($id === $studentId) ? 'selected' : '' ?>"
                    >

                        <td>
                            <?= $stt++ ?>
                        </td>

                        <td>
                            <a
                                class="student-link"
                                href="sinhvien.php?id=<?= $studentId ?>"
                            >
                                <?= htmlspecialchars($student['msv']) ?>
                            </a>
                        </td>

                        <td>
                            <a
                                class="student-link"
                                href="sinhvien.php?id=<?= $studentId ?>"
                            >
                                <?= htmlspecialchars($student['hoten']) ?>
                            </a>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['lop']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['ban']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['chucvu']) ?>
                        </td>

                        <td>

                            <?php if ($student['trangthai'] === 'Đang hoạt động'): ?>

                                <span class="status active">
                                    Đang hoạt động
                                </span>

                            <?php elseif ($student['trangthai'] === 'Tạm khóa'): ?>

                                <span class="status locked">
                                    Tạm khóa
                                </span>

                            <?php else: ?>

                                <span class="status">
                                    <?= htmlspecialchars($student['trangthai']) ?>
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="7" class="empty">
                        Không tìm thấy sinh viên.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


    <?php if ($id > 0 && isset($_SESSION['sinhvien'][$id])): ?>

        <?php $student = $_SESSION['sinhvien'][$id]; ?>


        <!-- THÔNG TIN SINH VIÊN ĐƯỢC CHỌN -->

        <div class="student-detail">

            <h2>THÔNG TIN SINH VIÊN</h2>

            <div class="detail-line"></div>


            <div class="detail-grid">

                <div>
                    <strong>MSSV:</strong>
                    <?= htmlspecialchars($student['msv']) ?>
                </div>

                <div>
                    <strong>Họ tên:</strong>
                    <?= htmlspecialchars($student['hoten']) ?>
                </div>

                <div>
                    <strong>Lớp:</strong>
                    <?= htmlspecialchars($student['lop']) ?>
                </div>

                <div>
                    <strong>Khoa:</strong>
                    <?= htmlspecialchars($student['khoa']) ?>
                </div>

                <div>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($student['email']) ?>
                </div>

                <div>
                    <strong>Số điện thoại:</strong>
                    <?= htmlspecialchars($student['sdt']) ?>
                </div>

                <div>
                    <strong>Ban:</strong>
                    <?= htmlspecialchars($student['ban']) ?>
                </div>

                <div>
                    <strong>Chức vụ:</strong>
                    <?= htmlspecialchars($student['chucvu']) ?>
                </div>

                <div>
                    <strong>Trạng thái:</strong>
                    <?= htmlspecialchars($student['trangthai']) ?>
                </div>

            </div>


            <!-- LỰA CHỌN -->

            <div class="action-box">

                <a
                    href="sinhvien.php?id=<?= $id ?>&action=edit"
                    class="btn btn-blue"
                >
                    Cập nhật
                </a>


                <form method="POST" class="delete-form">

                    <input
                        type="hidden"
                        name="delete_id"
                        value="<?= $id ?>"
                    >

                    <button
                        type="submit"
                        name="delete"
                        class="btn btn-red"
                        onclick="return confirm('Bạn có chắc muốn xóa sinh viên này không?')"
                    >
                        Xóa sinh viên
                    </button>

                </form>

            </div>

        </div>


        <?php if (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>


            <!-- FORM CẬP NHẬT -->

            <div class="update-box">

                <h2>CẬP NHẬT THÔNG TIN SINH VIÊN</h2>

                <div class="detail-line"></div>


                <form method="POST">

                    <input
                        type="hidden"
                        name="update_id"
                        value="<?= $id ?>"
                    >


                    <div class="form-row">

                        <label>MSSV</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($student['msv']) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-row">

                        <label>Họ tên</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($student['hoten']) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-row">

                        <label>Ban</label>

                        <select name="ban">

                            <option
                                value="Truyền thông"
                                <?= $student['ban'] === 'Truyền thông' ? 'selected' : '' ?>
                            >
                                Truyền thông
                            </option>

                            <option
                                value="Kỹ thuật"
                                <?= $student['ban'] === 'Kỹ thuật' ? 'selected' : '' ?>
                            >
                                Kỹ thuật
                            </option>

                            <option
                                value="Sự kiện"
                                <?= $student['ban'] === 'Sự kiện' ? 'selected' : '' ?>
                            >
                                Sự kiện
                            </option>

                            <option
                                value="Đối ngoại"
                                <?= $student['ban'] === 'Đối ngoại' ? 'selected' : '' ?>
                            >
                                Đối ngoại
                            </option>

                        </select>

                    </div>


                    <div class="form-row">

                        <label>Chức vụ</label>

                        <select name="chucvu">

                            <option
                                value="Thành viên"
                                <?= $student['chucvu'] === 'Thành viên' ? 'selected' : '' ?>
                            >
                                Thành viên
                            </option>

                            <option
                                value="Cộng tác viên"
                                <?= $student['chucvu'] === 'Cộng tác viên' ? 'selected' : '' ?>
                            >
                                Cộng tác viên
                            </option>

                            <option
                                value="Trưởng ban"
                                <?= $student['chucvu'] === 'Trưởng ban' ? 'selected' : '' ?>
                            >
                                Trưởng ban
                            </option>

                            <option
                                value="Phó ban"
                                <?= $student['chucvu'] === 'Phó ban' ? 'selected' : '' ?>
                            >
                                Phó ban
                            </option>

                            <option
                                value="Chủ nhiệm"
                                <?= $student['chucvu'] === 'Chủ nhiệm' ? 'selected' : '' ?>
                            >
                                Chủ nhiệm
                            </option>

                            <option
                                value="Phó chủ nhiệm"
                                <?= $student['chucvu'] === 'Phó chủ nhiệm' ? 'selected' : '' ?>
                            >
                                Phó chủ nhiệm
                            </option>

                        </select>

                    </div>


                    <div class="form-row">

                        <label>Trạng thái</label>

                        <select name="trangthai">

                            <option
                                value="Đang hoạt động"
                                <?= $student['trangthai'] === 'Đang hoạt động' ? 'selected' : '' ?>
                            >
                                Đang hoạt động
                            </option>

                            <option
                                value="Tạm khóa"
                                <?= $student['trangthai'] === 'Tạm khóa' ? 'selected' : '' ?>
                            >
                                Tạm khóa
                            </option>

                        </select>

                    </div>


                    <div class="form-buttons">

                        <button
                            type="submit"
                            name="update"
                            class="btn btn-blue"
                        >
                            Cập nhật
                        </button>


                        <a
                            href="sinhvien.php?id=<?= $id ?>"
                            class="btn btn-gray"
                        >
                            Hủy bỏ
                        </a>

                    </div>

                </form>

            </div>

        <?php endif; ?>


    <?php endif; ?>

</div>

</body>
</html>