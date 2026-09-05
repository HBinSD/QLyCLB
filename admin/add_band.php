<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$clubId = "CLB001";

$error = "";


/*
|--------------------------------------------------------------------------
| XỬ LÝ THÊM BAN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bandName = trim($_POST['band_name'] ?? '');

    if ($bandName === '') {

        $error = "Tên ban không được để trống.";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | Kiểm tra trùng tên ban
            |--------------------------------------------------------------------------
            */

            $stmt = $db->prepare("
                SELECT band_id
                FROM ClubBand
                WHERE club_id = :club_id
                  AND band_name = :band_name
                LIMIT 1
            ");

            $stmt->execute([
                ':club_id' => $clubId,
                ':band_name' => $bandName
            ]);

            if ($stmt->fetch()) {

                throw new Exception(
                    "Ban này đã tồn tại trong câu lạc bộ."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Tạo band_id
            |--------------------------------------------------------------------------
            |
            | Lấy band_id lớn nhất của CLB rồi + 1.
            |
            */

            $stmt = $db->prepare("
                SELECT MAX(band_id)
                FROM ClubBand
                WHERE club_id = :club_id
            ");

            $stmt->execute([
                ':club_id' => $clubId
            ]);

            $maxBandId = $stmt->fetchColumn();

            $newBandId = $maxBandId
                ? ((int)$maxBandId + 1)
                : 1;


            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            $stmt = $db->prepare("
                INSERT INTO ClubBand (
                    band_id,
                    club_id,
                    band_name
                )
                VALUES (
                    :band_id,
                    :club_id,
                    :band_name
                )
            ");

            $stmt->execute([
                ':band_id' => $newBandId,
                ':club_id' => $clubId,
                ':band_name' => $bandName
            ]);


            header("Location: club.php");
            exit;

        } catch (Exception $e) {

            $error = $e->getMessage();

        } catch (PDOException $e) {

            $error = "Không thể thêm ban: "
                   . $e->getMessage();
        }
    }
}


$pageTitle = "Thêm ban";
$activeMenu = "club.php";

require_once "../includes/headers.php";
?>

<link
    rel="stylesheet"
    href="css/add_band.css"
>


<div class="add-band-page">

    <div class="page-header">

        <div>

            <a
                href="club.php"
                class="back-link"
            >
                ← Quay lại quản lý CLB
            </a>

            <h1>
                Thêm ban mới
            </h1>

            <p>
                Thêm một ban mới vào câu lạc bộ.
            </p>

        </div>

    </div>


    <?php if ($error !== ''): ?>

        <div class="alert error">
            ⚠️
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="form-card">

        <div class="card-header">

            <h2>
                Thông tin ban
            </h2>

            <p>
                Nhập tên ban muốn thêm vào CLB.
            </p>

        </div>


        <form method="POST">

            <div class="form-body">

                <div class="form-group">

                    <label>
                        Mã CLB
                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars($clubId) ?>"
                        disabled
                    >

                </div>


                <div class="form-group">

                    <label for="band_name">
                        Tên ban
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="band_name"
                        name="band_name"
                        placeholder="Ví dụ: Ban Truyền thông"
                        value="<?= htmlspecialchars(
                            $_POST['band_name'] ?? ''
                        ) ?>"
                        required
                    >

                </div>

            </div>


            <div class="form-actions">

                <a
                    href="club.php"
                    class="btn-cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn-save"
                >
                    + Thêm ban
                </button>

            </div>

        </form>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>