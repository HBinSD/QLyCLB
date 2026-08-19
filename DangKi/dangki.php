<?php
session_start();

/* =========================================
   XÁC ĐỊNH TRANG HIỆN TẠI
   login     = trang đăng nhập
   register  = trang đăng ký
   ========================================= */

$page = $_POST['page'] ?? 'login';
$step = $_POST['step'] ?? 1;

$message = "";
$error = "";


/* =========================================
   XỬ LÝ ĐĂNG NHẬP
   ========================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "login") {

    $email = trim($_POST['email'] ?? '');
    $matkhau = $_POST['matkhau'] ?? '';

    /*
       Nếu đã có tài khoản đăng ký trong session
       thì kiểm tra email + mật khẩu
    */
    if (
        isset($_SESSION['email']) &&
        isset($_SESSION['matkhau']) &&
        $email == $_SESSION['email'] &&
        $matkhau == $_SESSION['matkhau']
    ) {

        $message = "Đăng nhập thành công!";

    } else {

        $error = "Email hoặc mật khẩu không đúng!";
    }
}


/* =========================================
   NHẤN NÚT ĐĂNG KÝ TỪ TRANG ĐĂNG NHẬP
   ========================================= */

else if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "go_register") {

    $page = "register";
    $step = 1;
}


/* =========================================
   BƯỚC 1: THÔNG TIN CÁ NHÂN
   ========================================= */

else if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "register" && $step == 1) {

    $_SESSION['hoten'] = trim($_POST['hoten'] ?? '');
    $_SESSION['ngaysinh'] = $_POST['ngaysinh'] ?? '';
    $_SESSION['msv'] = trim($_POST['msv'] ?? '');
    $_SESSION['lop'] = trim($_POST['lop'] ?? '');
    $_SESSION['email'] = trim($_POST['email'] ?? '');
    $_SESSION['matkhau'] = $_POST['matkhau'] ?? '';

    $step = 2;
}


/* =========================================
   QUAY LẠI TỪ BƯỚC 2 VỀ BƯỚC 1
   ========================================= */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "register" &&
    $step == "back"
) {

    $page = "register";
    $step = 1;
}


/* =========================================
   BƯỚC 2: THÔNG TIN ĐĂNG KÝ CLB
   ========================================= */

else if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    $page == "register" &&
    $step == 2
) {

    $lydo = $_POST["lydo"] ?? "";
    $mongmuon = $_POST["mongmuon"] ?? "";
    $tainang = $_POST["tainang"] ?? "";
    $ban = $_POST["ban"] ?? "";

    // Lưu thông tin bước 2 vào session
    $_SESSION['lydo'] = $lydo;
    $_SESSION['mongmuon'] = $mongmuon;
    $_SESSION['tainang'] = $tainang;
    $_SESSION['ban'] = $ban;

    /*
       Đăng ký hoàn tất
       Sau khi đăng ký sẽ quay về trang đăng nhập
    */

    $message = "Đăng ký thành công! Vui lòng đăng nhập.";

    $page = "login";
}


/* =========================================
   HTML
   ========================================= */
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>CLB Sinh viên</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {

            background: #f4f7fa;

            min-height: 100vh;

            padding: 40px 20px;
        }

        .container {

            width: 100%;

            max-width: 1000px;

            margin: auto;

            background: white;

            padding: 35px 45px;

            border-radius: 12px;

            box-shadow:
                0 4px 18px rgba(0,0,0,0.08);
        }


        /* =========================
           TIÊU ĐỀ
           ========================= */

        .title {

            color: #1261a0;

            font-size: 28px;

            font-weight: 700;

            text-transform: uppercase;

            margin-bottom: 10px;
        }

        .line {

            height: 2px;

            background: #176b87;

            width: 100%;

            margin-bottom: 30px;
        }

        .subtitle {

            color: #555;

            font-size: 15px;

            margin-bottom: 25px;
        }


        /* =========================
           FORM
           ========================= */

        .form-group {

            margin-bottom: 20px;
        }

        label {

            display: block;

            font-size: 16px;

            font-weight: bold;

            margin-bottom: 8px;

            color: #222;
        }

        .required {

            color: red;
        }

        input,
        textarea,
        select {

            width: 100%;

            padding: 13px 14px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 15px;

            outline: none;

            transition: 0.2s;

            background: white;
        }

        input:focus,
        textarea:focus,
        select:focus {

            border-color: #1261a0;

            box-shadow:
                0 0 0 2px rgba(18,97,160,0.1);
        }

        textarea {

            resize: vertical;

            min-height: 120px;
        }


        /* =========================
           CHIA 2 CỘT
           ========================= */

        .row {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;
        }


        /* =========================
           NÚT
           ========================= */

        .button-area {

            display: flex;

            gap: 12px;

            margin-top: 25px;

            justify-content: space-between;
        }

        button {

            border: none;

            padding: 13px 24px;

            border-radius: 6px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;
        }

        .btn-primary {

            background: #1261a0;

            color: white;
        }

        .btn-primary:hover {

            background: #0e4f84;
        }

        .btn-secondary {

            background: #e8ecef;

            color: #333;
        }

        .btn-secondary:hover {

            background: #d9dee2;
        }


        /* =========================
           ĐĂNG NHẬP
           ========================= */

        .login-box {

            max-width: 650px;

            margin: 50px auto;

            padding: 35px 50px;

            border-radius: 30px;

            background: white;
        }

        .login-box .title {

            font-size: 30px;

            color: #3d5587;
        }

        .login-button {

            display: flex;

            justify-content: space-between;

            margin-top: 30px;
        }

        .login-button button {

            width: 140px;
        }

        .register-text {

            margin-top: 30px;

            color: #666;

            font-weight: bold;

            font-size: 16px;
        }


        /* =========================
           BAN
           ========================= */

        .ban-list {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

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

            border-color: #1261a0;

            background: #f0f7fc;
        }

        .ban-option input {

            width: auto;

            margin-right: 8px;
        }


        /* =========================
           THÔNG BÁO
           ========================= */

        .message {

            background: #dff3e5;

            color: #247a3d;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-weight: bold;
        }

        .error {

            background: #ffe1e1;

            color: #c62828;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        /* =========================
           STEPS
           ========================= */

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

            background: #1261a0;

            color: white;
        }

        .step-text {

            margin-left: 8px;

            font-weight: bold;

            color: #555;
        }

        .step-text.active {

            color: #1261a0;
        }

        .step-line {

            width: 100px;

            height: 2px;

            background: #d9e4eb;

            margin: 0 15px;
        }


        /* =========================
           RESPONSIVE
           ========================= */

        @media (max-width: 700px) {

            .container {

                padding: 25px 20px;
            }

            .login-box {

                padding: 25px 20px;
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
        }

    </style>

</head>


<body>

<div class="container">


<?php

/* =====================================================
   TRANG ĐĂNG NHẬP
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

            <input type="hidden"
                   name="page"
                   value="login">


            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Nhập email"
                    required
                >

            </div>


            <div class="form-group">

                <label>Mật khẩu</label>

                <input
                    type="password"
                    name="matkhau"
                    placeholder="Nhập mật khẩu"
                    required
                >

            </div>


            <p class="register-text">
                Bạn chưa có tài khoản? Hãy nhấn vào mục đăng kí
            </p>


            <div class="login-button">

                <!-- NÚT ĐĂNG KÝ -->

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
   ĐĂNG KÝ - BƯỚC 1
   ===================================================== */

elseif ($page == "register" && $step == 1):

?>

    <h1 class="title">
        ĐĂNG KÝ THAM GIA CÂU LẠC BỘ
    </h1>

    <div class="line"></div>

    <p class="subtitle">
        Bước 1: Vui lòng nhập đầy đủ thông tin cá nhân của bạn.
    </p>


    <!-- THANH TIẾN TRÌNH -->

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
   ĐĂNG KÝ - BƯỚC 2
   ===================================================== */

elseif ($page == "register" && $step == 2):

?>

    <h1 class="title">
        THÔNG TIN ĐĂNG KÝ CLB
    </h1>

    <div class="line"></div>

    <p class="subtitle">
        Bước 2: Hãy chia sẻ thêm thông tin để CLB hiểu rõ hơn về bạn.
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

            <button
                type="submit"
                name="step"
                value="back"
                class="btn-secondary"
            >
                ← Quay lại
            </button>


            <button
                type="submit"
                class="btn-primary"
            >
                Hoàn tất đăng ký ✓
            </button>

        </div>

    </form>


<?php endif; ?>


</div>

</body>

</html>