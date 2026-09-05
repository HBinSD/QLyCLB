<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

/*
|--------------------------------------------------------------------------
| Kết nối database
|--------------------------------------------------------------------------
*/

$database = new Database();
$db = $database->getConnection();


/*
|--------------------------------------------------------------------------
| Kiểm tra quyền
|--------------------------------------------------------------------------
*/

$user = $_SESSION['user'] ?? [];

if (
    ($user['role'] ?? '') !== 'organizer' &&
    ($user['role'] ?? '') !== 'admin'
) {
    http_response_code(403);
    echo 'Bạn không có quyền truy cập';
    exit;
}


/*
|--------------------------------------------------------------------------
| Cấu hình
|--------------------------------------------------------------------------
*/

$clubId = "CLB001";

$error = "";


/*
|--------------------------------------------------------------------------
| Lấy danh sách các ban của CLB
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
| Xử lý form
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventName = trim($_POST['event_name'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $startTime = trim($_POST['start_time'] ?? '');
    $endTime = trim($_POST['end_time'] ?? '');
    $slots = (int)($_POST['slots'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    /*
     * Danh sách ban được chọn
     */
    $selectedBands = $_POST['bands'] ?? [];

    if (!is_array($selectedBands)) {
        $selectedBands = [];
    }

    /*
     * Chuyển band_id về integer
     */
    $selectedBands = array_map(
        'intval',
        $selectedBands
    );

    /*
     * Loại bỏ ID trùng
     */
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
    | Kiểm tra ngày hợp lệ
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $dateObject = DateTime::createFromFormat(
            'Y-m-d',
            $eventDate
        );

        if (
            !$dateObject ||
            $dateObject->format('Y-m-d') !== $eventDate
        ) {
            $error = "Ngày tổ chức không hợp lệ.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Không cho tạo sự kiện trong quá khứ
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $today = date('Y-m-d');

        if ($eventDate < $today) {
            $error = "Không thể tạo sự kiện trong quá khứ.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra các band có thuộc CLB không
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

        if (
            $validBandCount !== count($selectedBands)
        ) {
            $error = "Danh sách ban tham gia không hợp lệ.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Tạo sự kiện
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        try {

            $db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Thêm Event
            |--------------------------------------------------------------------------
            */

            $sql = "
                INSERT INTO Event
                (
                    club_id,
                    event_name,
                    event_date,
                    start_time,
                    end_time,
                    slots,
                    location,
                    description,
                    organizer_id,
                    status
                )
                VALUES
                (
                    :club_id,
                    :event_name,
                    :event_date,
                    :start_time,
                    :end_time,
                    :slots,
                    :location,
                    :description,
                    :organizer_id,
                    'upcoming'
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':club_id' => $clubId,
                ':event_name' => $eventName,
                ':event_date' => $eventDate,
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':slots' => $slots,
                ':location' => $location,
                ':description' => $description,
                ':organizer_id' => $user['username']
            ]);


            /*
            |--------------------------------------------------------------------------
            | Lấy event_id vừa tạo
            |--------------------------------------------------------------------------
            */

            $eventId = (int)$db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Thêm các ban vào EventBand
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


            /*
            |--------------------------------------------------------------------------
            | Hoàn tất
            |--------------------------------------------------------------------------
            */

            $db->commit();


            /*
            | Chuyển sang trang chi tiết
            */

            header(
                "Location: event_detail.php?id=" . $eventId
            );

            exit;


        } catch (PDOException $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $error = "Không thể tạo sự kiện. Vui lòng thử lại.";

            /*
             * Khi debug có thể dùng:
             *
             * $error = $e->getMessage();
             */
        }
    }
}


/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/

$pageTitle = "Tạo sự kiện";
$activeMenu = "events.php";

require_once "../includes/headers.php";
?>

<link rel="stylesheet" href="css/create_event.css">


<div class="create-event-page">

    <div class="page-header">

        <div>

            <a href="events.php" class="back-link">
                ← Quay lại danh sách sự kiện
            </a>

            <h1>Tạo sự kiện</h1>

            <p>
                Tạo một sự kiện mới cho câu lạc bộ.
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

        <!-- THÔNG TIN CƠ BẢN -->

        <div class="form-card">

            <h2>Thông tin cơ bản</h2>


            <div class="form-group">

                <label for="event_name">
                    Tên sự kiện
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    placeholder="Nhập tên sự kiện"
                    value="<?= htmlspecialchars(
                        $_POST['event_name'] ?? ''
                    ) ?>"
                    required
                >

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label for="event_date">
                        Ngày tổ chức
                        <span class="required">*</span>
                    </label>

                    <input
                        type="date"
                        id="event_date"
                        name="event_date"
                        min="<?= date('Y-m-d') ?>"
                        value="<?= htmlspecialchars(
                            $_POST['event_date'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="start_time">
                        Giờ bắt đầu
                        <span class="required">*</span>
                    </label>

                    <input
                        type="time"
                        id="start_time"
                        name="start_time"
                        value="<?= htmlspecialchars(
                            $_POST['start_time'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="end_time">
                        Giờ kết thúc
                        <span class="required">*</span>
                    </label>

                    <input
                        type="time"
                        id="end_time"
                        name="end_time"
                        value="<?= htmlspecialchars(
                            $_POST['end_time'] ?? ''
                        ) ?>"
                        required
                    >

                </div>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label for="location">
                        Địa điểm
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        placeholder="Ví dụ: Phòng A101"
                        value="<?= htmlspecialchars(
                            $_POST['location'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="slots">
                        Số lượng người tham gia
                        <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="slots"
                        name="slots"
                        min="1"
                        placeholder="Ví dụ: 30"
                        value="<?= htmlspecialchars(
                            $_POST['slots'] ?? ''
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
                    placeholder="Nhập nội dung giới thiệu về sự kiện..."
                ><?= htmlspecialchars(
                    $_POST['description'] ?? ''
                ) ?></textarea>

            </div>

        </div>


        <!-- BAN THAM GIA -->

        <div class="form-card">

            <h2>Đối tượng tham gia</h2>

            <p class="form-note">
                Chọn các ban được phép tham gia sự kiện.
                Nếu không chọn ban nào, tất cả thành viên
                của câu lạc bộ đều có thể đăng ký.
            </p>


            <?php if (empty($bands)): ?>

                <div class="empty-band">
                    Câu lạc bộ hiện chưa có ban nào.
                    Sự kiện sẽ được mở cho tất cả thành viên.
                </div>

            <?php else: ?>

                <div class="band-checkboxes">

                    <?php foreach ($bands as $band): ?>
                        <label class="band-item">
                            <input
                                type="checkbox"
                                name="bands[]"
                                value="<?= htmlspecialchars($band['band_id']) ?>"
                            >

                            <span>
                                <?= htmlspecialchars($band['band_name']) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>


        <!-- NGƯỜI TẠO -->

        <div class="form-card">

            <h2>Thông tin người tổ chức</h2>

            <div class="organizer-info">

                <div>

                    <span class="info-label">
                        Người tạo sự kiện
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $user['fullname']
                            ?? $user['username']
                        ) ?>
                    </strong>

                </div>


                <div>

                    <span class="info-label">
                        Tài khoản
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $user['username']
                        ) ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- BUTTON -->

        <div class="form-actions">

            <a
                href="events.php"
                class="btn btn-cancel"
            >
                Hủy
            </a>

            <button
                type="submit"
                class="btn btn-save"
            >
                + Tạo sự kiện
            </button>

        </div>

    </form>

</div>

<?php require_once '../includes/footer.php' ?>

</body>