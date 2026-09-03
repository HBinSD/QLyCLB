<?php
require_once __DIR__ . '/../models/userModel.php';

class UserController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
        $user = $this->userModel->getUserByUsernameOrEmail($_SESSION['user']['username']);
        require_once __DIR__ . '/../views/profile.php';
    }

    public function editProfile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $error = "";
        $message = "";
        $user = $this->userModel->getUserByUsernameOrEmail($_SESSION['user']['username']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username   = $_SESSION['user']['username'];
            $fullname   = trim($_POST['fullname'] ?? '');
            $email      = trim($_POST['email'] ?? '');
            $phone      = trim($_POST['phone'] ?? '');
            $gender     = trim($_POST['gender'] ?? '');
            $dob        = trim($_POST['dob'] ?? '');
            $idNumber   = trim($_POST['id_number'] ?? '');
            $avatarLink = trim($_POST['avatar'] ?? '');

            // Validate họ tên & email
            if (empty($fullname)) {
                $error = "Họ tên không được để trống.";
            } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email không hợp lệ.";
            }

            // Upload ảnh đại diện nếu có
            if ($error === "" && isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['avatar_file'];
                if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= 5 * 1024 * 1024) {
                    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (isset($allowedTypes[$mimeType])) {
                        $uploadDir = __DIR__ . "/../../public/uploads/avatars/";
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $newFileName = $username . "_" . time() . "." . $allowedTypes[$mimeType];
                        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                            $avatarLink = "uploads/avatars/" . $newFileName;
                        }
                    } else {
                        $error = "Chỉ chấp nhận file ảnh JPG, PNG, GIF, WEBP.";
                    }
                } else {
                    $error = "Lỗi upload hoặc kích thước ảnh vượt quá 5MB.";
                }
            }

            if ($error === "") {
                $success = $this->userModel->updateProfile($username, $fullname, $email, $phone, $gender, $dob, $idNumber, $avatarLink ?: null);
                if ($success) {
                    // Cập nhật lại Session
                    $_SESSION['user']['fullname']  = $fullname;
                    $_SESSION['user']['email']     = $email;
                    $_SESSION['user']['phone']     = $phone;
                    $_SESSION['user']['gender']    = $gender;
                    $_SESSION['user']['DOB']       = $dob;
                    $_SESSION['user']['id_number'] = $idNumber;
                    if (!empty($avatarLink)) {
                        $_SESSION['user']['avatar'] = $avatarLink;
                    }

                    header('Location: index.php?page=profile');
                    exit();
                } else {
                    $error = "Không thể cập nhật thông tin.";
                }
            }
        }

        require_once __DIR__ . '/../views/edit_profile.php';
    }
}
?>