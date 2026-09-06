<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? [];

if (
    ($user['role'] ?? '') !== 'organizer' &&
    ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}

$clubId = "CLB001";

$eventId = (int)($_GET['id'] ?? $_POST['event_id'] ?? 0);

if ($eventId <= 0) {
    header("Location: events.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();

/*
|--------------------------------------------------------------------------
| Lấy sự kiện
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM Event
    WHERE event_id = :event_id
      AND club_id = :club_id
    LIMIT 1
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':event_id' => $eventId,
    ':club_id' => $clubId
]);

$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Lấy danh sách ban
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        band_id,
        band_name
    FROM ClubBand
    WHERE club_id = :club_id
    ORDER BY band_name
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':club_id' => $clubId
]);

$bands = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Các ban hiện tại của sự kiện
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT band_id
    FROM EventBand
    WHERE event_id = :event_id
";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':event_id' => $eventId
]);

$selectedBands = $stmt->fetchAll(PDO::FETCH_COLUMN);


/*
|--------------------------------------------------------------------------
| XỬ LÝ FORM
|--------------------------------------------------------------------------
*/

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventName = trim($_POST['event_name'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $slots = (int)($_POST['slots'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'upcoming';

    $selectedBands = $_POST['bands'] ?? [];

    /*
     * Chỉ nhận band_id dạng số
     */
    $selectedBands = array_map(
        'intval',
        $selectedBands
    );

    $selectedBands = array_unique(
        $selectedBands
    );


    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    if ($eventName === '') {

        $error = "Vui lòng nhập tên sự kiện.";

    } elseif ($eventDate === '') {

        $error = "Vui lòng chọn ngày tổ chức.";

    } elseif ($startTime === '') {

        $error = "Vui lòng nhập giờ bắt đầu.";

    } elseif ($endTime === '') {

        $error = "Vui lòng nhập giờ kết thúc.";

    } elseif ($startTime >= $endTime) {

        $error = "Giờ kết thúc phải lớn hơn giờ bắt đầu.";

    } elseif ($slots <= 0) {

        $error = "Số lượng người tham gia phải lớn hơn 0.";

    } elseif ($location === '') {

        $error = "Vui lòng nhập địa điểm.";

    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra số người đã duyệt
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $sql = "
            SELECT COUNT(*)
            FROM Register_event
            WHERE event_id = :event_id
              AND register_status = 'approved'
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':event_id' => $eventId
        ]);

        $approvedCount = (int)$stmt->fetchColumn();

        if ($slots < $approvedCount) {

            $error =
                "Số chỗ mới không thể nhỏ hơn số thành viên "
                . "đã được duyệt ($approvedCount người).";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra ban
    |--------------------------------------------------------------------------
    */

    if ($error === '' && !empty($selectedBands)) {

        $placeholders = implode(
            ',',
            array_fill(
                0,
                count($selectedBands),
                '?'
            )
        );

        $sql = "
            SELECT COUNT(*)
            FROM ClubBand
            WHERE club_id = ?
              AND band_id IN ($placeholders)
        ";

        $params = array_merge(
            [$clubId],
            $selectedBands
        );

        $stmt = $db->prepare($sql);

        $stmt->execute($params);

        $validBandCount = (int)$stmt->fetchColumn();

        if ($validBandCount !== count($selectedBands)) {

            $error =
                "Danh sách ban tham gia không hợp lệ.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        try {

            $db->beginTransaction();


            /*
            | Khóa sự kiện
            */

            $sql = "
                SELECT *
                FROM Event
                WHERE event_id = :event_id
                  AND club_id = :club_id
                FOR UPDATE
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':event_id' => $eventId,
                ':club_id' => $clubId
            ]);

            $lockedEvent = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lockedEvent) {

                throw new Exception(
                    "Không tìm thấy sự kiện."
                );
            }


            /*
            | Update Event
            */

            $sql = "
                UPDATE Event
                SET
                    event_name = :event_name,
                    event_date = :event_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    slots = :slots,
                    location = :location,
                    description = :description,
                    status = :status
                WHERE event_id = :event_id
                  AND club_id = :club_id
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':event_name' => $eventName,
                ':event_date' => $eventDate,
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':slots' => $slots,
                ':location' => $location,
                ':description' => $description,
                ':status' => $status,
                ':event_id' => $eventId,
                ':club_id' => $clubId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Xóa các ban cũ
            |--------------------------------------------------------------------------
            */

            $sql = "
                DELETE FROM EventBand
                WHERE event_id = :event_id
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':event_id' => $eventId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Thêm lại các ban
            |--------------------------------------------------------------------------
            */

            if (!empty($selectedBands)) {

                $sql = "
                    INSERT INTO EventBand
                    (
                        event_id,
                        band_id
                    )
                    VALUES
                    (
                        :event_id,
                        :band_id
                    )
                ";

                $stmt = $db->prepare($sql);

                foreach ($selectedBands as $bandId) {

                    $stmt->execute([
                        ':event_id' => $eventId,
                        ':band_id' => $bandId
                    ]);
                }
            }


            $db->commit();


            /*
            | Chuyển về trang chi tiết
            */

            header(
                "Location: event_detail.php?id=" . $eventId
            );

            exit;

        } catch (Exception $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $error =
                "Không thể cập nhật sự kiện: "
                . $e->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Nếu POST lỗi thì giữ dữ liệu người dùng nhập
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event['event_name'] = $_POST['event_name'] ?? $event['event_name'];
    $event['event_date'] = $_POST['event_date'] ?? $event['event_date'];
    $event['start_time'] = $_POST['start_time'] ?? $event['start_time'];
    $event['end_time'] = $_POST['end_time'] ?? $event['end_time'];
    $event['slots'] = $_POST['slots'] ?? $event['slots'];
    $event['location'] = $_POST['location'] ?? $event['location'];
    $event['description'] = $_POST['description'] ?? $event['description'];
    $event['status'] = $_POST['status'] ?? $event['status'];
}


/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/

$pageTitle = "Chỉnh sửa sự kiện";
$activeMenu = "events.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/edit_event.css">


<div class="edit-event-page">

    <div class="page-header">

        <div>

            <a
                href="event_detail.php?id=<?= $eventId ?>"
                class="back-link"
            >
                ← Quay lại chi tiết
            </a>

            <h1>Chỉnh sửa sự kiện</h1>

            <p>
                Cập nhật thông tin sự kiện của câu lạc bộ.
            </p>

        </div>

    </div>


    <?php if ($error !== ''): ?>

        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form
        method="POST"
        class="event-form"
    >

        <input
            type="hidden"
            name="event_id"
            value="<?= $eventId ?>"
        >


        <!-- THÔNG TIN CƠ BẢN -->

        <div class="form-card">

            <h2>Thông tin cơ bản</h2>


            <div class="form-group">

                <label for="event_name">
                    Tên sự kiện
                </label>

                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    value="<?= htmlspecialchars(
                        $event['event_name']
                    ) ?>"
                    required
                >

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label for="event_date">
                        Ngày tổ chức
                    </label>

                    <input
                        type="date"
                        id="event_date"
                        name="event_date"
                        value="<?= htmlspecialchars(
                            $event['event_date']
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="start_time">
                        Giờ bắt đầu
                    </label>

                    <input
                        type="time"
                        id="start_time"
                        name="start_time"
                        value="<?= htmlspecialchars(
                            $event['start_time']
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="end_time">
                        Giờ kết thúc
                    </label>

                    <input
                        type="time"
                        id="end_time"
                        name="end_time"
                        value="<?= htmlspecialchars(
                            $event['end_time']
                        ) ?>"
                        required
                    >

                </div>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label for="location">
                        Địa điểm
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?= htmlspecialchars(
                            $event['location']
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="slots">
                        Số lượng người tham gia
                    </label>

                    <input
                        type="number"
                        id="slots"
                        name="slots"
                        min="1"
                        value="<?= htmlspecialchars(
                            $event['slots']
                        ) ?>"
                        required
                    >

                </div>

            </div>


            <div class="form-group">

                <label for="description">
                    Mô tả sự kiện
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="7"
                ><?= htmlspecialchars(
                    $event['description'] ?? ''
                ) ?></textarea>

            </div>

        </div>


        <!-- BAN -->

        <div class="form-card">

            <h2>Đối tượng tham gia</h2>

            <p class="form-note">
                Nếu không chọn ban nào, tất cả thành viên
                trong câu lạc bộ đều có thể đăng ký.
            </p>


            <div class="band-checkboxes">

                <?php foreach ($bands as $band): ?>

                    <label class="band-checkbox">

                        <input
                            type="checkbox"
                            name="bands[]"
                            value="<?= (int)$band['band_id'] ?>"

                            <?= in_array(
                                (int)$band['band_id'],
                                array_map(
                                    'intval',
                                    $selectedBands
                                ),
                                true
                            )
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $band['band_name']
                            ) ?>
                        </span>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- TRẠNG THÁI -->

        <div class="form-card">

            <h2>Trạng thái sự kiện</h2>

            <div class="form-group">

                <label for="status">
                    Trạng thái
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option
                        value="upcoming"
                        <?= $event['status'] === 'upcoming'
                            ? 'selected'
                            : '' ?>
                    >
                        Sắp diễn ra
                    </option>

                    <option
                        value="ongoing"
                        <?= $event['status'] === 'ongoing'
                            ? 'selected'
                            : '' ?>
                    >
                        Đang diễn ra
                    </option>

                    <option
                        value="completed"
                        <?= $event['status'] === 'completed'
                            ? 'selected'
                            : '' ?>
                    >
                        Đã kết thúc
                    </option>

                    <option
                        value="cancelled"
                        <?= $event['status'] === 'cancelled'
                            ? 'selected'
                            : '' ?>
                    >
                        Đã hủy
                    </option>

                </select>

            </div>

        </div>


        <!-- ACTION -->

        <div class="form-actions">

            <a
                href="event_detail.php?id=<?= $eventId ?>"
                class="btn btn-cancel"
            >
                Hủy
            </a>

            <button
                type="submit"
                class="btn btn-save"
            >
                Lưu thay đổi
            </button>

        </div>

    </form>

</div>

<?php require_once '../includes/footer.php' ?>

</body>