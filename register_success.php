<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đăng ký thành công</title>

    <meta
        http-equiv="refresh"
        content="5;url=login.php"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #f1f3f6;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        .success-card {
            width: 90%;
            max-width: 500px;

            background: white;

            border-radius: 25px;

            padding: 45px 35px;

            text-align: center;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.05);
        }

        .success-icon {
            width: 70px;
            height: 70px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #e8f7ee;

            color: #1d8a4a;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 36px;

            font-weight: bold;
        }

        h1 {
            color: #2f568c;

            font-size: 23px;

            margin-bottom: 12px;
        }

        p {
            color: #64748b;

            font-size: 14px;

            line-height: 1.7;

            margin: 7px 0;
        }

        .countdown {
            margin-top: 25px;

            color: #2f568c;

            font-weight: 600;
        }

        .login-link {
            display: inline-block;

            margin-top: 20px;

            padding: 10px 20px;

            background: #2f568c;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            font-size: 13px;
        }

        .login-link:hover {
            background: #244774;
        }

    </style>

</head>


<body>


<div class="success-card">

    <div class="success-icon">
        ✓
    </div>


    <h1>
        ĐĂNG KÝ THÀNH CÔNG
    </h1>


    <p>
        Tài khoản của bạn đã được tạo thành công.
    </p>


    <p>
        Đơn đăng ký tham gia câu lạc bộ
        đang chờ Admin xét duyệt.
    </p>


    <p>
        Bạn vẫn có thể đăng nhập vào hệ thống
        trong thời gian chờ xét duyệt.
    </p>


    <div class="countdown">

        Tự động chuyển về trang đăng nhập sau
        <span id="countdown">5</span> giây...

    </div>


    <a
        href="login.php"
        class="login-link"
    >
        Đăng nhập ngay
    </a>

</div>


<script>

let seconds = 5;

const countdown =
    document.getElementById("countdown");

const timer = setInterval(function () {

    seconds--;

    countdown.innerText = seconds;

    if (seconds <= 0) {

        clearInterval(timer);

        window.location.href = "login.php";

    }

}, 1000);

</script>


</body>

</html>