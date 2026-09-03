<?php
require_once __DIR__ . '/../models/userModel.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function redirectByRole(string $role): void {
        switch ($role) {
            case 'admin':
                header("Location: index.php?page=admin-dashboard");
                exit;
            case 'organizer':
                header("Location: index.php?page=organizer-dashboard");
                exit;
            case 'member':
            case 'USER':
                header("Location: index.php?page=event");
                exit;
            default:
                header("Location: index.php?page=login");
                exit;
        }
    }

    public function login(): void {
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
                } elseif (isset($user['status']) && (int)$user['status'] !== 1) {
                    $error = "Tài khoản của bạn đã bị khóa hoặc không tồn tại.";
                } else {
                    if (password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user'] = [
                            'username'  => $user['username'],
                            'fullname'  => $user['fullname'] ?? '',
                            'email'     => $user['email'] ?? '',
                            'DOB'       => $user['dob'] ?? '',
                            'phone'     => $user['phone'] ?? '',
                            'id_number' => $user['id_number'] ?? '',
                            'gender'    => $user['gender'] ?? '',
                            'role'      => $user['role'],
                            'ngayTao'   => $user['created_at'] ?? '',
                            'avatar'    => $user['avt_links'] ?? '',
                        ];
                        $this->redirectByRole($user['role']);
                    } else {
                        $error = "Username hoặc mật khẩu không chính xác.";
                    }
                }
            }
        }
        require_once __DIR__ . '/../views/login.php';
    }

    public function register(): void {
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";
            $fullname = trim($_POST["fullname"] ?? "");
            $email    = trim($_POST["email"] ?? "");
            $phone    = trim($_POST["phone"] ?? null);
            $idNumber = trim($_POST["id_number"] ?? null);
            $gender   = trim($_POST["gender"] ?? "");
            $dob      = trim($_POST["dob"] ?? null);

            if ($username === "" || $password === "" || $fullname === "" || $email === "") {
                $error = "Vui lòng điền các thông tin bắt buộc.";
            } else {
                $success = $this->userModel->register($username, $password, $fullname, $email, $phone, $idNumber, $gender, $dob, 'member');
                if ($success) {
                    header("Location: index.php?page=login");
                    exit;
                } else {
                    $error = "Đăng ký thất bại, tài khoản hoặc email đã tồn tại.";
                }
            }
        }
        require_once __DIR__ . '/../views/register.php';
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
    
}