<?php

session_start();

require_once "config/database.php";
require_once "models/User.php";


// Nếu đã đăng nhập thì chuyển về dashboard
if (isset($_SESSION['user'])) {

    switch ($_SESSION['user']['role']) {

        case 'admin':
            header("Location: admin/dashboard.php");
            exit;

        case 'organizer':
            header("Location: organizer/dashboard.php");
            exit;

        case 'member':
            header("Location: member/dashboard.php");
            exit;
    }
}


// Biến thông báo lỗi
$error = "";


// Xử lý form login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // Kiểm tra dữ liệu
    if ($email === "" || $password === "") {

        $error = "Vui lòng nhập đầy đủ email và mật khẩu.";

    } else {

        try {

            // Kết nối database
            $database = new Database();
            $db = $database->getConnection();

            // Tạo User object
            $userModel = new User($db);

            // Tìm user
            $user = $userModel->findByEmail($email);


            // Kiểm tra user
            if (!$user) {

                $error = "Email hoặc mật khẩu không chính xác.";

            } else {

                // Kiểm tra tài khoản có bị khóa không
                if ($user['status'] !== 'active') {

                    $error = "Tài khoản của bạn đã bị khóa.";

                } else {

                    // Kiểm tra password
                    if ($userModel->verifyPassword($password, $user['password'])) {

                        // Tạo session
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'full_name' => $user['full_name'],
                            'msv' => $user['msv'],
                            'class_name' => $user['class_name'],
                            'faculty' => $user['faculty'],
                            'email' => $user['email'],
                            'phone' => $user['phone'],
                            'role' => $user['role'],
                            'avatar' => $user['avatar']
                        ];


                        // Chuyển hướng theo role
                        switch ($user['role']) {

                            case 'admin':
                                header("Location: admin/dashboard.php");
                                exit;

                            case 'organizer':
                                header("Location: organizer/dashboard.php");
                                exit;

                            case 'member':
                                header("Location: member/dashboard.php");
                                exit;

                            default:
                                $error = "Role không hợp lệ.";
                        }

                    } else {

                        $error = "Email hoặc mật khẩu không chính xác.";

                    }
                }
            }

        } catch (PDOException $e) {

            $error = "Có lỗi xảy ra khi kết nối database.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
        }

        .login-container {
            width: 400px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            text-align: center;

            margin-bottom: 30px;
        }

        .login-title h1 {
            color: #2563eb;

            margin-bottom: 8px;
        }

        .login-title p {
            color: #777;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 7px;

            font-weight: bold;
        }

        .form-group input {
            width: 100%;

            padding: 12px;

            border: 1px solid #ddd;

            border-radius: 8px;

            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;

            border-color: #2563eb;
        }

        .btn-login {
            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 8px;

            background: #2563eb;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        .btn-login:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;

            color: #dc2626;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;
        }

        .demo-account {
            margin-top: 25px;

            padding: 15px;

            background: #f8fafc;

            border-radius: 8px;

            font-size: 14px;

            color: #555;
        }

        .demo-account strong {
            color: #333;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-title">

        <h1>CLUB MANAGEMENT</h1>

        <p>Đăng nhập hệ thống</p>

    </div>


    <?php if ($error !== ""): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="">

        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Nhập email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
            >

        </div>


        <div class="form-group">

            <label for="password">
                Mật khẩu
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Nhập mật khẩu"
                required
            >

        </div>


        <button
            type="submit"
            class="btn-login"
        >
            Đăng nhập
        </button>

    </form>


    <div class="demo-account">

        <strong>Tài khoản test:</strong>

        <br><br>

        Admin:
        admin@gmail.com

        <br>

        Organizer:
        binh@gmail.com

        <br>

        Member:
        an@gmail.com

        <br><br>

        Mật khẩu:
        <strong>123456</strong>

    </div>

    <p style="text-align: center; margin-top: 20px;">
        Chưa có tài khoản?
        <a href="register.php">
            Đăng ký ngay
        </a>
    </p>

</div>

</body>

</html>