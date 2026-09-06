<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

$database = new Database();
$db = $database->getConnection();

$username = $user['username'];

$messageSuccess = "";
$messageError = "";

/*
|--------------------------------------------------------------------------
| Các loại nội dung liên hệ
|--------------------------------------------------------------------------
*/

$contactTypes = [
    'Tài khoản & mật khẩu',
    'Vấn đề đăng ký',
    'Đổi ban',
    'Sự kiện & hoạt động',
    'Thông tin cá nhân',
    'Đề xuất / góp ý',
    'Vấn đề khác'
];

/*
|--------------------------------------------------------------------------
| Xử lý gửi Contact
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    /*
    | Kiểm tra loại liên hệ
    */
    if ($subject === '') {

        $messageError = "Vui lòng chọn nội dung liên hệ.";

    } elseif (!in_array($subject, $contactTypes, true)) {

        $messageError = "Nội dung liên hệ không hợp lệ.";

    } elseif ($message === '') {

        $messageError = "Vui lòng nhập nội dung chi tiết.";

    } else {

        try {

            $sql = "
                INSERT INTO Contact (
                    username,
                    subject,
                    message,
                    status,
                    created_at
                )
                VALUES (
                    :username,
                    :subject,
                    :message,
                    'unread',
                    NOW()
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':username' => $username,
                ':subject'  => $subject,
                ':message'  => $message
            ]);

            $messageSuccess = "Gửi liên hệ thành công.";

            // Xóa dữ liệu form sau khi gửi thành công
            $_POST = [];

        } catch (PDOException $e) {

            $messageError = "Không thể gửi liên hệ. Vui lòng thử lại.";
        }
    }
}

$pageTitle = "Liên hệ";
$activeMenu = "contact.php";

require_once "../includes/headers.php";
?>

<div class="contact-container">

    <div class="contact-header">

        <h1>Liên hệ với CLB</h1>

        <p>
            Bạn có thắc mắc hoặc cần hỗ trợ?
            Hãy chọn nội dung liên hệ và gửi thông tin cho ban quản lý.
        </p>

    </div>


    <?php if ($messageSuccess !== ""): ?>

        <div class="alert alert-success">
            ✓ <?= htmlspecialchars($messageSuccess) ?>
        </div>

    <?php endif; ?>


    <?php if ($messageError !== ""): ?>

        <div class="alert alert-error">
            ⚠ <?= htmlspecialchars($messageError) ?>
        </div>

    <?php endif; ?>


    <div class="contact-card">

        <form method="POST">


            <!-- Username -->

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars($username) ?>"
                    readonly
                >

            </div>


            <!-- Loại liên hệ -->

            <div class="form-group">

                <label for="subject">
                    Nội dung liên hệ <span>*</span>
                </label>

                <select
                    id="subject"
                    name="subject"
                    required
                >

                    <option value="">
                        -- Chọn nội dung liên hệ --
                    </option>

                    <?php foreach ($contactTypes as $type): ?>

                        <option
                            value="<?= htmlspecialchars($type) ?>"
                            <?= (($_POST['subject'] ?? '') === $type) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($type) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Nội dung chi tiết -->

            <div class="form-group">

                <label for="message">
                    Nội dung chi tiết <span>*</span>
                </label>

                <textarea
                    id="message"
                    name="message"
                    rows="8"
                    placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..."
                    required
                ><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

            </div>


            <!-- Button -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-submit"
                >
                    📩 Gửi liên hệ
                </button>

            </div>

        </form>

    </div>

</div>

<link rel="stylesheet" href="css/contact.css">

<?php require_once '../includes/footer.php' ?>

