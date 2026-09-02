<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="form">
        <form action="?page=register" method="POST">
            <h1>Đăng ký</h1>
            <span>Tạo tài khoản mới</span>

            <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="form-area">
                <p>Username (*)</p>
                <input type="text" name="username" placeholder="Nhập username" pattern="[A-Za-z0-9_]+" required>
            </div>
            <div class="form-area">
                <p>Mật khẩu (*)</p>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <div class="form-area">
                <p>Họ và tên (*)</p>
                <input type="text" name="fullname" placeholder="Nhập họ tên" required>
            </div>
            <div class="form-area">
                <p>Email (*)</p>
                <input type="email" name="email" placeholder="Nhập email" required>
            </div>
            <div class="form-area">
                <p>Ngày sinh</p>
                <input type="date" name="dob">
            </div>
            <div class="form-area">
                <p>Số điện thoại</p>
                <input type="text" name="phone">
            </div>
            <div class="form-area">
                <p>Số CCCD/CMND</p>
                <input type="text" name="id_number">
            </div>

            <input type="submit" value="Đăng ký">
            <p style="margin-top: 15px;">Đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a></p>
        </form>
    </div>
</body>
</html>