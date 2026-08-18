<?php

session_start();

require_once "config/database.php";
require_once "models/User.php";


$error = "";
$success = "";


// =========================
// XỬ LÝ FORM ĐĂNG KÝ
// =========================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $msv = trim($_POST["msv"] ?? "");
    $class_name = trim($_POST["class_name"] ?? "");
    $faculty = trim($_POST["faculty"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // =========================
    // KIỂM TRA DỮ LIỆU
    // =========================

    if (
        $full_name === "" ||
        $msv === "" ||
        $class_name === "" ||
        $faculty === "" ||
        $email === "" ||
        $phone === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $error = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Email không hợp lệ.";

    } elseif (strlen($password) < 6) {

        $error = "Mật khẩu phải có ít nhất 6 ký tự.";

    } elseif ($password !== $confirm_password) {

        $error = "Mật khẩu xác nhận không trùng khớp.";

    } else {

        try {

            // =========================
            // KẾT NỐI DATABASE
            // =========================

            $database = new Database();

            $db = $database->getConnection();

            $userModel = new User($db);


            // =========================
            // KIỂM TRA EMAIL
            // =========================

            $existingEmail = $userModel->findByEmail($email);

            if ($existingEmail) {

                $error = "Email này đã được sử dụng.";

            } else {

                // =========================
                // KIỂM TRA MSV
                // =========================

                $sql = "SELECT id
                        FROM users
                        WHERE msv = :msv
                        LIMIT 1";

                $stmt = $db->prepare($sql);

                $stmt->bindParam(
                    ":msv",
                    $msv
                );

                $stmt->execute();

                $existingMsv = $stmt->fetch();


                if ($existingMsv) {

                    $error = "Mã sinh viên này đã được đăng ký.";

                } else {

                    // =========================
                    // TẠO USER
                    // =========================

                    $data = [
                        "full_name" => $full_name,
                        "msv" => $msv,
                        "class_name" => $class_name,
                        "faculty" => $faculty,
                        "email" => $email,
                        "phone" => $phone,
                        "password" => $password
                    ];


                    if ($userModel->create($data)) {

                        $success = "Đăng ký tài khoản thành công!";

                    } else {

                        $error = "Đăng ký thất bại.";

                    }
                }
            }

        } catch (PDOException $e) {

            $error = "Có lỗi xảy ra với database.";

        }
    }
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đăng ký tài khoản</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family: Arial, sans-serif;

            background: #f1f5f9;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px;
        }


        .register-container {

            width: 650px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 5px 25px
                rgba(0, 0, 0, 0.1);
        }


        .title {

            text-align: center;

            margin-bottom: 30px;
        }


        .title h1 {

            color: #2563eb;

            margin-bottom: 8px;
        }


        .title p {

            color: #777;
        }


        .form-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;
        }


        .form-group {

            display: flex;

            flex-direction: column;
        }


        .form-group.full {

            grid-column: 1 / 3;
        }


        .form-group label {

            margin-bottom: 7px;

            font-weight: bold;

            color: #333;
        }


        .form-group input {

            padding: 12px;

            border: 1px solid #ddd;

            border-radius: 8px;

            font-size: 15px;
        }


        .form-group input:focus {

            outline: none;

            border-color: #2563eb;
        }


        .btn-register {

            width: 100%;

            margin-top: 25px;

            padding: 13px;

            border: none;

            border-radius: 8px;

            background: #2563eb;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }


        .btn-register:hover {

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


        .success {

            background: #dcfce7;

            color: #16a34a;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;
        }


        .login-link {

            text-align: center;

            margin-top: 20px;

            color: #666;
        }


        .login-link a {

            color: #2563eb;

            text-decoration: none;

            font-weight: bold;
        }


        @media (max-width: 600px) {

            .register-container {

                width: 100%;
            }

            .form-grid {

                grid-template-columns: 1fr;
            }

            .form-group.full {

                grid-column: 1;
            }
        }

    </style>

</head>


<body>


<div class="register-container">


    <div class="title">

        <h1>CLUB MANAGEMENT</h1>

        <p>Tạo tài khoản thành viên</p>

    </div>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <?php if ($success !== ""): ?>

        <div class="success">

            <?= htmlspecialchars($success) ?>

            <br><br>

            <a href="login.php">
                Đăng nhập ngay
            </a>

        </div>

    <?php endif; ?>


    <?php if ($success === ""): ?>

        <form method="POST">


            <div class="form-grid">


                <!-- HỌ TÊN -->

                <div class="form-group full">

                    <label for="full_name">
                        Họ và tên
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Nguyễn Văn A"
                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- MSV -->

                <div class="form-group">

                    <label for="msv">
                        Mã sinh viên
                    </label>

                    <input
                        type="text"
                        id="msv"
                        name="msv"
                        placeholder="22110001"
                        value="<?= htmlspecialchars($_POST['msv'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- LỚP -->

                <div class="form-group">

                    <label for="class_name">
                        Lớp
                    </label>

                    <input
                        type="text"
                        id="class_name"
                        name="class_name"
                        placeholder="CNTT01"
                        value="<?= htmlspecialchars($_POST['class_name'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- KHOA -->

                <div class="form-group full">

                    <label for="faculty">
                        Khoa
                    </label>

                    <input
                        type="text"
                        id="faculty"
                        name="faculty"
                        placeholder="Công nghệ thông tin"
                        value="<?= htmlspecialchars($_POST['faculty'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="example@gmail.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- SỐ ĐIỆN THOẠI -->

                <div class="form-group">

                    <label for="phone">
                        Số điện thoại
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="0123456789"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Mật khẩu
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ít nhất 6 ký tự"
                        required
                    >

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirm_password">
                        Xác nhận mật khẩu
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Nhập lại mật khẩu"
                        required
                    >

                </div>


            </div>


            <button
                type="submit"
                class="btn-register"
            >
                Đăng ký tài khoản
            </button>


        </form>


        <div class="login-link">

            Đã có tài khoản?

            <a href="login.php">
                Đăng nhập
            </a>

        </div>

    <?php endif; ?>


</div>


</body>

</html>