<?php
session_start();

/* =====================================================
   KHỞI TẠO
   ===================================================== */

$page = $_POST['page'] ?? 'login';
$step = $_POST['step'] ?? 1;

$message = "";
$error = "";


/* =====================================================
   1. NHẤN "ĐĂNG KÍ" TỪ TRANG ĐĂNG NHẬP
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "go_register") {

    $page = "register";
    $step = 1;
}


/* =====================================================
   2. XỬ LÝ ĐĂNG NHẬP
   ===================================================== */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "login"
) {

    $email = trim($_POST['email'] ?? '');
    $matkhau = $_POST['matkhau'] ?? '';

    // Nếu đã có tài khoản
    if (
        isset($_SESSION['email']) &&
        isset($_SESSION['matkhau'])
    ) {

        // Kiểm tra tài khoản
        if (
            $email == $_SESSION['email'] &&
            $matkhau == $_SESSION['matkhau']
        ) {

            $message = "Đăng nhập thành công!";

        } else {

            $error = "Email hoặc mật khẩu không đúng!";
        }

    } else {

        $error = "Bạn chưa có tài khoản. Vui lòng nhấn Đăng kí!";
    }
}


/* =====================================================
   3. BƯỚC 1 - THÔNG TIN CÁ NHÂN
   ===================================================== */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "register" &&
    $step == 1
) {

    $_SESSION['hoten'] =
        trim($_POST['hoten'] ?? '');

    $_SESSION['ngaysinh'] =
        $_POST['ngaysinh'] ?? '';

    $_SESSION['msv'] =
        trim($_POST['msv'] ?? '');

    $_SESSION['lop'] =
        trim($_POST['lop'] ?? '');

    $_SESSION['email'] =
        trim($_POST['email'] ?? '');

    $_SESSION['matkhau'] =
        $_POST['matkhau'] ?? '';

    // Sang bước 2
    $step = 2;
}


/* =====================================================
   4. QUAY LẠI TỪ BƯỚC 2 VỀ BƯỚC 1
   ===================================================== */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "register" &&
    $step == "back"
) {

    $page = "register";
    $step = 1;
}


/* =====================================================
   5. BƯỚC 2 - THÔNG TIN ĐĂNG KÝ CLB
   ===================================================== */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "register" &&
    $step == 2
) {

    $lydo =
        $_POST["lydo"] ?? "";

    $mongmuon =
        $_POST["mongmuon"] ?? "";

    $tainang =
        $_POST["tainang"] ?? "";

    $ban =
        $_POST["ban"] ?? "";


    // Lưu thông tin bước 2

    $_SESSION['lydo'] =
        $lydo;

    $_SESSION['mongmuon'] =
        $mongmuon;

    $_SESSION['tainang'] =
        $tainang;

    $_SESSION['ban'] =
        $ban;


    /*
       KHÔNG chuyển thẳng về login.
       Chuyển sang trang đăng ký thành công.
    */

    $page = "register";
    $step = 3;
}


/* =====================================================
   6. TỪ TRANG ĐĂNG KÝ THÀNH CÔNG
      QUAY VỀ TRANG ĐĂNG NHẬP
   ===================================================== */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "success"
) {

    $page = "login";
    $step = 1;

    $message = "";
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

    <title>Đăng nhập CLB</title>


    <style>

        /* =====================================================
           RESET
           ===================================================== */

        * {

            box-sizing: border-box;

            margin: 0;

            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }


        /* =====================================================
           BODY
           ===================================================== */

        body {

            background: #f4f7fa;

            min-height: 100vh;

            padding: 40px 20px;
        }


        /* =====================================================
           CONTAINER
           ===================================================== */

        .container {

            width: 100%;

            max-width: 1000px;

            margin: auto;

            background: white;

            padding: 35px 45px;

            border-radius: 12px;

            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.08);
        }


        /* =====================================================
           LOGIN BOX
           ===================================================== */

        .login-box {

            max-width: 815px;

            min-height: 580px;

            margin: 40px auto;

            padding: 30px 50px;

            border-radius: 35px;

            background: white;
        }


        /* =====================================================
           TITLE
           ===================================================== */

        .title {

            color: #3d5587;

            font-size: 30px;

            font-weight: 700;

            text-transform: uppercase;

            margin-bottom: 12px;
        }


        /* =====================================================
           LINE
           ===================================================== */

        .line {

            height: 3px;

            background: #3d5587;

            width: 100%;

            margin-bottom: 30px;
        }


        /* =====================================================
           SUBTITLE
           ===================================================== */

        .subtitle {

            color: #666;

            font-size: 16px;

            margin-bottom: 25px;
        }


        /* =====================================================
           FORM GROUP
           ===================================================== */

        .form-group {

            margin-bottom: 25px;
        }


        /* =====================================================
           LABEL
           ===================================================== */

        label {

            display: block;

            font-size: 16px;

            font-weight: bold;

            margin-bottom: 9px;

            color: #222;
        }


        .required {

            color: red;
        }


        /* =====================================================
           INPUT
           ===================================================== */

        input,
        textarea,
        select {

            width: 100%;

            padding: 13px 14px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 15px;

            outline: none;

            background: white;

            transition: 0.2s;
        }


        input:focus,
        textarea:focus,
        select:focus {

            border-color: #3d5587;

            box-shadow:
                0 0 0 2px
                rgba(61, 85, 135, 0.1);
        }


        textarea {

            resize: vertical;

            min-height: 120px;
        }


        /* =====================================================
           ROW
           ===================================================== */

        .row {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;
        }


        /* =====================================================
           TEXT ĐĂNG KÝ
           ===================================================== */

        .register-text {

            margin-top: 25px;

            color: #666;

            font-weight: bold;

            font-size: 16px;
        }


        /* =====================================================
           NÚT LOGIN
           ===================================================== */

        .login-button {

            display: flex;

            justify-content: space-between;

            margin-top: 40px;

            padding: 0 50px;
        }


        /* =====================================================
           BUTTON
           ===================================================== */

        button {

            border: none;

            padding: 13px 35px;

            border-radius: 6px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;
        }


        .btn-primary {

            background: #3d5587;

            color: white;
        }


        .btn-primary:hover {

            background: #2e426d;
        }


        .btn-secondary {

            background: #e8ecef;

            color: #333;
        }


        .btn-secondary:hover {

            background: #d9dee2;
        }


        /* =====================================================
           BUTTON FORM
           ===================================================== */

        .button-area {

            display: flex;

            gap: 12px;

            margin-top: 25px;
        }


        /* =====================================================
           BAN
           ===================================================== */

        .ban-list {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 12px;

            margin-top: 10px;
        }


        .ban-option {

            border: 1px solid #d4dce2;

            border-radius: 7px;

            padding: 14px;

            cursor: pointer;

            transition: 0.2s;

            background: #fafafa;
        }


        .ban-option:hover {

            border-color: #3d5587;

            background: #f0f7fc;
        }


        .ban-option input {

            width: auto;

            margin-right: 8px;
        }


        /* =====================================================
           MESSAGE
           ===================================================== */

        .message {

            background: #dff3e5;

            color: #247a3d;

            padding: 13px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-weight: bold;

            text-align: center;
        }


        /* =====================================================
           ERROR
           ===================================================== */

        .error {

            background: #ffe1e1;

            color: #c62828;

            padding: 13px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        /* =====================================================
           STEPS
           ===================================================== */

        .steps {

            display: flex;

            justify-content: center;

            align-items: center;

            margin-bottom: 35px;
        }


        .step {

            display: flex;

            align-items: center;
        }


        .circle {

            width: 42px;

            height: 42px;

            border-radius: 50%;

            background: #d9e4eb;

            color: #666;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;
        }


        .circle.active {

            background: #3d5587;

            color: white;
        }


        .step-text {

            margin-left: 8px;

            font-weight: bold;

            color: #555;
        }


        .step-text.active {

            color: #3d5587;
        }


        .step-line {

            width: 100px;

            height: 2px;

            background: #d9e4eb;

            margin: 0 15px;
        }


        /* =====================================================
           TRANG THÀNH CÔNG
           ===================================================== */

        .success {

            text-align: center;

            padding: 60px 20px;
        }


        .success-icon {

            width: 80px;

            height: 80px;

            background: #dff3e5;

            color: #2e9b52;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 42px;

            margin: 0 auto 25px;
        }


        .success h2 {

            color: #3d5587;

            font-size: 28px;

            margin-bottom: 15px;
        }


        .success p {

            color: #555;

            line-height: 1.7;

            margin-bottom: 10px;
        }


        .success-button {

            margin-top: 30px;
        }


        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 700px) {

            .container {

                padding: 25px 20px;
            }


            .login-box {

                padding: 25px 20px;

                min-height: auto;
            }


            .row {

                grid-template-columns: 1fr;

                gap: 0;
            }


            .ban-list {

                grid-template-columns: 1fr;
            }


            .step-text {

                display: none;
            }


            .step-line {

                width: 60px;
            }


            .login-button {

                padding: 0;

                gap: 20px;
            }


            .login-button button {

                width: 50%;
            }
        }

    </style>

</head>


<body>


<div class="container">


<?php

/* =====================================================
   TRANG 1: ĐĂNG NHẬP
   ===================================================== */

if ($page == "login"):

?>

    <div class="login-box">

        <h1 class="title">

            ĐĂNG NHẬP CÂU LẠC BỘ

        </h1>


        <div class="line"></div>


        <?php if ($message != ""): ?>

            <div class="message">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if ($error != ""): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <input
                type="hidden"
                name="page"
                value="login"
            >


            <!-- EMAIL -->

            <div class="form-group">

                <label>
                    Email
                </label>

                <!-- KHÔNG required -->

                <input
                    type="email"
                    name="email"
                    placeholder="Nhập email"
                >

            </div>


            <!-- MẬT KHẨU -->

            <div class="form-group">

                <label>
                    Mật khẩu
                </label>

                <!-- KHÔNG required -->

                <input
                    type="password"
                    name="matkhau"
                    placeholder="Nhập mật khẩu"
                >

            </div>


            <p class="register-text">

                Bạn chưa có tài khoản?
                Hãy nhấn vào mục đăng kí

            </p>


            <div class="login-button">


                <!-- NÚT ĐĂNG KÍ -->

                <button
                    type="submit"
                    name="page"
                    value="go_register"
                    class="btn-primary"
                >

                    Đăng kí

                </button>


                <!-- NÚT ĐĂNG NHẬP -->

                <button
                    type="submit"
                    name="page"
                    value="login"
                    class="btn-primary"
                >

                    Đăng nhập

                </button>


            </div>


        </form>

    </div>


<?php

/* =====================================================
   TRANG 2: ĐĂNG KÝ BƯỚC 1
   ===================================================== */

elseif (
    $page == "register" &&
    $step == 1
):

?>

    <h1 class="title">

        ĐĂNG KÝ THAM GIA CÂU LẠC BỘ

    </h1>


    <div class="line"></div>


    <p class="subtitle">

        Bước 1:
        Vui lòng nhập đầy đủ thông tin cá nhân của bạn.

    </p>


    <div class="steps">


        <div class="step">

            <div class="circle active">
                1
            </div>

            <span class="step-text active">
                Thông tin cá nhân
            </span>

        </div>


        <div class="step-line"></div>


        <div class="step">

            <div class="circle">
                2
            </div>

            <span class="step-text">
                Thông tin đăng ký
            </span>

        </div>


    </div>


    <form method="POST">


        <input
            type="hidden"
            name="page"
            value="register"
        >

        <input
            type="hidden"
            name="step"
            value="1"
        >


        <!-- HỌ TÊN -->

        <div class="form-group">

            <label>

                Họ và tên
                <span class="required">*</span>

            </label>

            <input
                type="text"
                name="hoten"
                placeholder="VD: Nguyễn Văn An"
                required
            >

        </div>


        <!-- NGÀY SINH + MSV -->

        <div class="row">


            <div class="form-group">

                <label>

                    Ngày sinh
                    <span class="required">*</span>

                </label>

                <input
                    type="date"
                    name="ngaysinh"
                    required
                >

            </div>


            <div class="form-group">

                <label>

                    Mã sinh viên
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    name="msv"
                    placeholder="VD: 22A100001"
                    required
                >

            </div>


        </div>


        <!-- LỚP + EMAIL -->

        <div class="row">


            <div class="form-group">

                <label>

                    Lớp
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    name="lop"
                    placeholder="VD: CNTT D2024"
                    required
                >

            </div>


            <div class="form-group">

                <label>

                    Email
                    <span class="required">*</span>

                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="VD: sinhvien@gmail.com"
                    required
                >

            </div>


        </div>


        <!-- MẬT KHẨU -->

        <div class="form-group">

            <label>

                Mật khẩu
                <span class="required">*</span>

            </label>

            <input
                type="password"
                name="matkhau"
                placeholder="Nhập mật khẩu"
                minlength="6"
                required
            >

        </div>


        <div class="button-area">

            <button
                type="submit"
                class="btn-primary"
            >

                Tiếp tục →

            </button>

        </div>


    </form>


<?php

/* =====================================================
   TRANG 3: ĐĂNG KÝ BƯỚC 2
   ===================================================== */

elseif (
    $page == "register" &&
    $step == 2
):

?>

    <h1 class="title">

        THÔNG TIN ĐĂNG KÝ CLB

    </h1>


    <div class="line"></div>


    <p class="subtitle">

        Bước 2:
        Hãy chia sẻ thêm thông tin để CLB hiểu rõ hơn về bạn.

    </p>


    <div class="steps">


        <div class="step">

            <div class="circle active">
                ✓
            </div>

            <span class="step-text active">
                Thông tin cá nhân
            </span>

        </div>


        <div class="step-line"></div>


        <div class="step">

            <div class="circle active">
                2
            </div>

            <span class="step-text active">
                Thông tin đăng ký
            </span>

        </div>


    </div>


    <form method="POST">


        <input
            type="hidden"
            name="page"
            value="register"
        >

        <input
            type="hidden"
            name="step"
            value="2"
        >


        <!-- LÝ DO -->

        <div class="form-group">

            <label>

                Lý do đăng ký tham gia CLB
                <span class="required">*</span>

            </label>

            <textarea
                name="lydo"
                placeholder="Hãy chia sẻ lý do bạn muốn tham gia CLB..."
                required
            ></textarea>

        </div>


        <!-- MONG MUỐN -->

        <div class="form-group">

            <label>

                Mong muốn khi tham gia CLB
                <span class="required">*</span>

            </label>

            <textarea
                name="mongmuon"
                placeholder="Bạn mong muốn học hỏi, trải nghiệm hoặc đạt được điều gì khi tham gia CLB?"
                required
            ></textarea>

        </div>


        <!-- TÀI NĂNG -->

        <div class="form-group">

            <label>

                Tài năng / Kỹ năng của bạn
                <span class="required">*</span>

            </label>

            <textarea
                name="tainang"
                placeholder="VD: Thiết kế, Photoshop, Illustrator, lập trình, truyền thông, tổ chức sự kiện..."
                required
            ></textarea>

        </div>


        <!-- CHỌN BAN -->

        <div class="form-group">

            <label>

                Bạn muốn đăng ký vào ban nào?
                <span class="required">*</span>

            </label>


            <div class="ban-list">


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Truyền thông"
                        required
                    >

                    Ban Truyền thông

                </label>


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Sự kiện"
                    >

                    Ban Sự kiện

                </label>


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Nội dung"
                    >

                    Ban Nội dung

                </label>


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Đối ngoại"
                    >

                    Ban Đối ngoại

                </label>


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Kỹ thuật"
                    >

                    Ban Kỹ thuật

                </label>


                <label class="ban-option">

                    <input
                        type="radio"
                        name="ban"
                        value="Ban Hậu cần"
                    >

                    Ban Hậu cần

                </label>


            </div>

        </div>


        <div class="button-area">


            <!-- QUAY LẠI BƯỚC 1 -->

            <button
                type="submit"
                name="step"
                value="back"
                class="btn-secondary"
            >

                ← Quay lại

            </button>


            <!-- HOÀN TẤT -->

            <button
                type="submit"
                class="btn-primary"
            >

                Hoàn tất đăng ký ✓

            </button>


        </div>


    </form>


<?php

/* =====================================================
   TRANG 4: ĐĂNG KÝ THÀNH CÔNG
   ===================================================== */

elseif (
    $page == "register" &&
    $step == 3
):

?>


    <div class="success">


        <!-- ICON -->

        <div class="success-icon">

            ✓

        </div>


        <!-- TIÊU ĐỀ -->

        <h2>

            ĐĂNG KÝ THÀNH CÔNG!

        </h2>


        <!-- NỘI DUNG -->

        <p>

            Cảm ơn bạn đã đăng ký tham gia câu lạc bộ.

        </p>


        <p>

            Thông tin đăng ký của bạn đã được ghi nhận.

        </p>


        <p>

            Ban quản trị CLB sẽ xem xét
            và phản hồi trong thời gian sớm nhất.

        </p>


        <!-- NÚT QUAY VỀ ĐĂNG NHẬP -->

        <div class="success-button">


            <form method="POST">


                <button
                    type="submit"
                    name="page"
                    value="success"
                    class="btn-primary"
                >

                    ← Quay về đăng nhập

                </button>


            </form>


        </div>


    </div>


<?php endif; ?>


</div>


</body>

</html>