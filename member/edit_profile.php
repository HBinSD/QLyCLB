<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$pageTitle = "Chỉnh sửa thông tin cá nhân";
$activeMenu = "profile.php";

$user = $_SESSION['user'] ?? [];

if (empty($user['username'])) {
    header("Location: ../login.php");
    exit;
}
require_once "../includes/headers.php";
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $user['username'];
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $DOB = trim($_POST['DOB'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $avatarLink = trim($_POST['avatar'] ?? '');

    // Validate họ tên
    if ($fullname === '') {
        $error = "Họ và tên không được để trống.";
    } elseif (mb_strlen($fullname) < 2) {
        $error = "Họ và tên phải có ít nhất 2 ký tự.";
    } elseif (mb_strlen($fullname) > 100) {
        $error = "Họ và tên không được vượt quá 100 ký tự.";
    } elseif (!preg_match("/^[\p{L}\s]+$/u", $fullname)) {
        $error = "Họ và tên chỉ được chứa chữ cái và khoảng trắng.";
    }

    // Validate email
    if ($error === "") {
        if ($email === '') {
            $error = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không hợp lệ.";
        } elseif (strlen($email) > 255) {
            $error = "Email không được vượt quá 255 ký tự.";
        }
    }

    // Validate số điện thoại
    if ($error === "" && $phone !== '' && !preg_match('/^0[0-9]{9}$/', $phone)) {
        $error = "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.";
    }

    // Validate giới tính
    if ($error === "" && $gender !== '' && !in_array($gender, ['Nam', 'Nữ', 'Khác'], true)) {
        $error = "Giới tính không hợp lệ.";
    }

    // Validate ngày sinh
    if ($error === "" && $DOB !== '') {
        $date = DateTime::createFromFormat('Y-m-d', $DOB);
        if (!$date || $date->format('Y-m-d') !== $DOB) {
            $error = "Ngày sinh không hợp lệ.";
        } elseif ($DOB > date('Y-m-d')) {
            $error = "Ngày sinh không được lớn hơn ngày hiện tại.";
        }
    }

    // Validate MSSV
    if ($error === "") {
        if ($id_number === '') {
            $error = "Mã số sinh viên không được để trống.";
        } elseif (strlen($id_number) < 5 || strlen($id_number) > 20) {
            $error = "Mã số sinh viên phải có từ 5 đến 20 ký tự.";
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $id_number)) {
            $error = "Mã số sinh viên chỉ được chứa chữ cái và số.";
        }
    }

    // Validate link avatar
    if ($error === "" && $avatarLink !== '') {
        if (!filter_var($avatarLink, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $avatarLink)) {
            $error = "Link ảnh đại diện không hợp lệ (phải bắt đầu bằng http:// hoặc https://).";
        }
    }

    // Upload avatar
    if ($error === "" && isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = "Có lỗi xảy ra khi upload ảnh.";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = "Ảnh không được lớn hơn 5MB.";
        } else {
            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp'
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowedTypes[$mimeType])) {
                $error = "Chỉ được upload ảnh JPG, PNG, GIF hoặc WEBP.";
            } else {
                $uploadDir = "uploads/avatars/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = $username . "_" . time() . "." . $allowedTypes[$mimeType];
                $uploadPath = $uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $avatarLink = "uploads/avatars/" . $newFileName;
                } else {
                    $error = "Không thể lưu ảnh lên server.";
                }
            }
        }
    }

    // Update database
    if ($error === "") {
        try {
            $sql = "UPDATE UserInfo SET
                        fullname = :fullname,
                        email = :email,
                        phone = :phone,
                        gender = :gender,
                        DOB = :DOB,
                        id_number = :id_number,
                        avt_links = :avt_link
                    WHERE username = :username";

            $db = (new Database())->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':fullname'  => $fullname,
                ':email'     => $email,
                ':phone'     => $phone,
                ':gender'    => $gender,
                ':DOB'       => $DOB !== '' ? $DOB : null,
                ':id_number' => $id_number,
                ':avt_link'  => $avatarLink,
                ':username'  => $username
            ]);

            // Update session
            $_SESSION['user']['fullname']  = $fullname;
            $_SESSION['user']['email']     = $email;
            $_SESSION['user']['phone']     = $phone;
            $_SESSION['user']['gender']    = $gender;
            $_SESSION['user']['DOB']       = $DOB;
            $_SESSION['user']['id_number'] = $id_number;
            $_SESSION['user']['avatar']    = $avatarLink;

            echo "<script>
                        alert('Cập nhật thành công!');
                        window.location.href = 'profile.php';
                      </script>";
            exit;
        } catch (PDOException $e) {
            $error = "Không thể cập nhật thông tin.";
        }
    }
}


?>

<link rel="stylesheet" href="css/edit_profile.css">

<div class="profile-page">

    <h1 class="profile-title">
        Chỉnh sửa thông tin cá nhân
    </h1>

    <?php if ($message !== ""): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>


    <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">

        <!-- Avatar -->
        <div class="edit-avatar-section">

            <div class="profile-avatar">

                <?php if (!empty($user['avatar'])): ?>

                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Ảnh đại diện"
                    onerror="this.style.display='none';">

                <?php else: ?>

                <div class="avatar-default">
                    👤
                </div>

                <?php endif; ?>

            </div>

            <div class="profile-role">

                <?php
                if (($user['role'] ?? '') === 'admin') {
                    echo 'Người quản trị';
                } elseif (($user['role'] ?? '') === 'organizer') {
                    echo 'Ban tổ chức';
                } else {
                    echo 'Thành viên';
                }
                ?>

            </div>

        </div>


        <!-- Thông tin -->
        <div class="profile-info">

            <!-- Họ tên -->
            <div class="info-row">

                <label for="fullname">
                    Họ và tên
                </label>

                <input type="text" id="fullname" name="fullname"
                    value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>

            </div>


            <!-- Username -->
            <div class="info-row">

                <label>
                    Tên đăng nhập
                </label>

                <input type="text" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled>

                <small>
                    Tên đăng nhập không thể thay đổi.
                </small>

            </div>


            <!-- Email -->
            <div class="info-row">

                <label for="email">
                    Email
                </label>

                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

            </div>


            <!-- Số điện thoại -->
            <div class="info-row">

                <label for="phone">
                    Số điện thoại
                </label>

                <input type="text" id="phone" name="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

            </div>


            <!-- Giới tính -->
            <div class="info-row">

                <label for="gender">
                    Giới tính
                </label>

                <select id="gender" name="gender">

                    <option value="">
                        -- Chọn giới tính --
                    </option>

                    <option value="Nam" <?php echo (($user['gender'] ?? '') === 'Nam') ? 'selected' : ''; ?>>
                        Nam
                    </option>

                    <option value="Nữ" <?php echo (($user['gender'] ?? '') === 'Nữ') ? 'selected' : ''; ?>>
                        Nữ
                    </option>

                    <option value="Khác" <?php echo (($user['gender'] ?? '') === 'Khác') ? 'selected' : ''; ?>>
                        Khác
                    </option>

                </select>

            </div>


            <!-- Ngày sinh -->
            <div class="info-row">

                <label for="DOB">
                    Ngày sinh
                </label>

                <input type="date" id="DOB" name="DOB" value="<?php
                        echo !empty($user['DOB'])
                            ? htmlspecialchars(date('Y-m-d', strtotime($user['DOB'])))
                            : '';
                    ?>">

            </div>


            <!-- MSSV -->
            <div class="info-row">

                <label for="id_number">
                    Mã số SV
                </label>

                <input type="text" id="id_number" name="id_number"
                    value="<?php echo htmlspecialchars($user['id_number'] ?? ''); ?>">

            </div>


            <!-- Avatar link -->
            <div class="info-row">

                <label for="avatar">
                    Link ảnh đại diện
                </label>

                <input type="url" id="avatar" name="avatar" placeholder="https://example.com/avatar.jpg"
                    value="<?php echo htmlspecialchars($user['avatar'] ?? ''); ?>">

                <small>
                    Nhập URL của ảnh nếu bạn không muốn upload ảnh từ máy.
                </small>

            </div>


            <!-- Upload avatar -->
            <div class="info-row">

                <label for="avatar_file">
                    Hoặc upload ảnh
                </label>

                <input type="file" id="avatar_file" name="avatar_file"
                    accept="image/jpeg,image/png,image/gif,image/webp">

                <small>
                    JPG, PNG, GIF, WEBP — tối đa 5MB.
                </small>

            </div>


            <!-- Nút -->
            <div class="edit-profile-buttons">

                <button type="submit" class="btn-save-profile">
                    Lưu thay đổi
                </button>

                <a href="profile.php" class="btn-cancel-profile">
                    Hủy
                </a>

            </div>

        </div>

    </form>

</div>

</main>
</div>
</div>

<?php
require_once "../includes/footer.php";
?>