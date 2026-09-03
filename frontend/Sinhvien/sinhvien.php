<?php
require_once "../includes/auth.php";
require_once "../database/database.php";

$database = new Database();
$db = $database->getConnection();

$sql = "
    SELECT 
        cm.username,
        ui.fullname,
        ui.student_code,
        ui.class,
        ui.faculty,
        ui.email,
        ui.phone,
        cm.ban,
        cm.position,
        cm.status
    FROM ClubMember cm
    JOIN UserInfo ui ON cm.username = ui.username
";

$stmt = $db->prepare($sql);
$stmt->execute();

$danhSachSinhVien = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| LẤY ID SINH VIÊN
|--------------------------------------------------------------------------
*/

$id = $_GET['id'] ?? '';

$sinhVien = null;

if ($id !== '') {
    foreach ($danhSachSinhVien as $student) {
        if ($student['student_code'] === $id) {
            $sinhVien = $student;
            break;
        }
    }
}


/*
|--------------------------------------------------------------------------
| XÓA SINH VIÊN
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'])) {

    $username = $_POST['username'];

    $sql = "DELETE FROM ClubMember WHERE username = :username";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username
    ]);

    header("Location: sinhvien.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CẬP NHẬT SINH VIÊN
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $username = $_POST['username'];
    $ban = $_POST['ban'];
    $position = $_POST['position'];
    $status = $_POST['status'];

    $sql = "
        UPDATE ClubMember
        SET 
            ban = :ban,
            position = :position,
            status = :status
        WHERE username = :username
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':ban' => $ban,
        ':position' => $position,
        ':status' => $status,
        ':username' => $username
    ]);

    header("Location: sinhvien.php?id=" . urlencode($_POST['student_code']));
    exit;
}


$keyword = $_GET['keyword'] ?? '';

$ketQuaTimKiem = [];

foreach ($danhSachSinhVien as $student) {

    if (
        $keyword === '' ||
        stripos($student['student_code'], $keyword) !== false ||
        stripos($student['fullname'], $keyword) !== false ||
        stripos($student['class'], $keyword) !== false
    ) {
        $ketQuaTimKiem[] = $student;
    }
}

$danhSachSinhVien = $ketQuaTimKiem;
?>

<?php require_once "../includes/headers.php"; ?>

<?php require_once "../includes/sidebar-admin.php"; ?>

<main class="main-content">

<div class="container">

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

                <?php foreach ($danhSachSinhVien as $student): ?>

                    <tr 
                        class="<?= ($id === $student['student_code']) ? 'selected' : '' ?>" 
                    >

                        <td>
                            <?= $stt++ ?>
                        </td>

                        <td>
                            <a 
                                class="student-link" 
                                href="sinhvien.php?id=<?= urlencode($student['student_code']) ?>"
                            >
                                <?= htmlspecialchars($student['student_code']) ?>
                            </a>
                        </td>

                        <td>
                            <a 
                                class="student-link" 
                                href="sinhvien.php?id=<?= urlencode($student['student_code']) ?>"
                            >
                                <?= htmlspecialchars($student['fullname']) ?>
                            </a>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['class']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['ban']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($student['position']) ?>
                        </td>

                        <td>

                            <?php if ($student['status'] === 'Đang hoạt động'): ?>

                                <span class="status active">
                                    Đang hoạt động
                                </span>

                            <?php elseif ($student['status'] === 'Tạm khóa'): ?>

                                <span class="status locked">
                                    Tạm khóa
                                </span>

                            <?php else: ?>

                                <span class="status">
                                    <?= htmlspecialchars($student['status']) ?>
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


    <?php if ($sinhVien !== null): ?>

    <?php $student = $sinhVien; ?>


        <!-- THÔNG TIN SINH VIÊN ĐƯỢC CHỌN -->

        <div class="student-detail">

            <h2>THÔNG TIN SINH VIÊN</h2>

            <div class="detail-line"></div>


            <div class="detail-grid">

                <div>
                    <strong>MSSV:</strong>
                    <?= htmlspecialchars($student['student_code']) ?>
                </div>

                <div>
                    <strong>Họ tên:</strong>
                    <?= htmlspecialchars($student['fullname']) ?>
                </div>

                <div>
                    <strong>Lớp:</strong>
                    <?= htmlspecialchars($student['class']) ?>
                </div>

                <div>
                    <strong>Khoa:</strong>
                    <?= htmlspecialchars($student['faculty']) ?>
                </div>

                <div>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($student['email']) ?>
                </div>

                <div>
                    <strong>Số điện thoại:</strong>
                    <?= htmlspecialchars($student['phone']) ?>
                </div>

                <div>
                    <strong>Ban:</strong>
                    <?= htmlspecialchars($student['ban']) ?>
                </div>

                <div>
                    <strong>Chức vụ:</strong>
                    <?= htmlspecialchars($student['position']) ?>
                </div>

                <div>
                    <strong>Trạng thái:</strong>
                    <?= htmlspecialchars($student['status']) ?>
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
                        name="username" 
                        value="<?= htmlspecialchars($student['username']) ?>"
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
                        name="username"
                        value="<?= htmlspecialchars($student['username']) ?>"
                    >
                    <input 
                        type="hidden" 
                        name="student_code" 
                        value="<?= htmlspecialchars($student['student_code']) ?>"
                    >

                    <div class="form-row">

                        <label>MSSV</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($student['student_code']) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-row">

                        <label>Họ tên</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($student['fullname']) ?>"
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

                        <select name="position">

                            <option
                                value="Thành viên"
                                <?= $student['position'] === 'Thành viên' ? 'selected' : '' ?>
                            >
                                Thành viên
                            </option>

                            <option
                                value="Cộng tác viên"
                                <?= $student['position'] === 'Cộng tác viên' ? 'selected' : '' ?>
                            >
                                Cộng tác viên
                            </option>

                            <option
                                value="Trưởng ban"
                                <?= $student['position'] === 'Trưởng ban' ? 'selected' : '' ?>
                            >
                                Trưởng ban
                            </option>

                            <option
                                value="Phó ban"
                                <?= $student['position'] === 'Phó ban' ? 'selected' : '' ?>
                            >
                                Phó ban
                            </option>

                            <option
                                value="Chủ nhiệm"
                                <?= $student['position'] === 'Chủ nhiệm' ? 'selected' : '' ?>
                            >
                                Chủ nhiệm
                            </option>

                            <option
                                value="Phó chủ nhiệm"
                                <?= $student['position'] === 'Phó chủ nhiệm' ? 'selected' : '' ?>
                            >
                                Phó chủ nhiệm
                            </option>

                        </select>

                    </div>


                    <div class="form-row">

                        <label>Trạng thái</label>

                        <select name="status">

                            <option
                                value="Đang hoạt động"
                                <?= $student['status'] === 'Đang hoạt động' ? 'selected' : '' ?>
                            >
                                Đang hoạt động
                            </option>

                            <option
                                value="Tạm khóa"
                                <?= $student['status'] === 'Tạm khóa' ? 'selected' : '' ?>
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

</main>

<?php require_once "../includes/footer.php"; ?>
