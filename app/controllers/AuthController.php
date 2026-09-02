<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Hàm điều hướng theo Role chuẩn mẫu của bạn
    private function redirectByRole(string $role) {
        switch ($role) {
            case 'admin':
                header("Location: index.php?page=admin-dashboard");
                exit;
            case 'organizer':
                header("Location: index.php?page=organizer-dashboard");
                exit;
            case 'member':
                header("Location: index.php?page=event");
                exit;
            default:
                return "Role của tài khoản không hợp lệ.";
        }
    }

    public function login() {
        // Kiểm tra nếu đã đăng nhập thì chuyển trang luôn
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['role']);
        }

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";

            if ($username === "" || $password === "") {
                $error = "Vui lòng nhập đầy đủ username và mật khẩu.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $error = "Username không hợp lệ.";
            } else {
                $user = $this->userModel->getUserByUsernameOrEmail($username);

                if (!$user) {
                    $error = "Username hoặc mật khẩu không chính xác.";
                } elseif (isset($user['status']) && $user['status'] !== '1') {
                    $error = "Tài khoản của bạn không tồn tại hoặc đã bị khóa.";
                } else {
                    if (password_verify($password, $user['password'])) {
                        // Lưu Session đúng chuẩn cấu trúc mẫu
                        $_SESSION['user'] = [
                            'username'  => $user['username'],
                            'fullname'  => $user['fullname'] ?? '',
                            'email'     => $user['email'] ?? '',
                            'DOB'       => $user['DOB'] ?? '',
                            'phone'     => $user['phone'] ?? '',
                            'id_number' => $user['id_number'] ?? '',
                            'role'      => $user['role']
                        ];

                        $redirectError = $this->redirectByRole($user['role']);
                        if ($redirectError) $error = $redirectError;
                    } else {
                        $error = "Username hoặc mật khẩu không chính xác.";
                    }
                }
            }
        }
        require_once __DIR__ . '/../views/login.php';
    }

    public function register() {
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";
            $fullname = trim($_POST["fullname"] ?? "");
            $email    = trim($_POST["email"] ?? "");
            $dob      = $_POST["dob"] ?? null;
            $phone    = trim($_POST["phone"] ?? null);
            $idNumber = trim($_POST["id_number"] ?? null);

            if ($username === "" || $password === "" || $fullname === "" || $email === "") {
                $error = "Vui lòng điền đầy đủ các thông tin bắt buộc.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $error = "Username chỉ gồm chữ cái, số và dấu gạch dưới.";
            } else {
                $success = $this->userModel->register($username, $password, $fullname, $email, $dob, $phone, $idNumber);
                if ($success) {
                    header("Location: index.php?page=login");
                    exit;
                } else {
                    $error = "Username đã tồn tại hoặc có lỗi đăng ký.";
                }
            }
        }
        require_once __DIR__ . '/../views/register.php';
    }

    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
}
?>