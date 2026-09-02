<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="form">
        <form action="?page=login" method="POST">
            <h1>Đăng nhập</h1>
            <span>Đăng nhập hệ thống</span>

            <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="form-area">
                <p>Username</p>
                <input type="text" id="username" name="username" placeholder="Nhập username" minlength="3"
                    maxlength="30" pattern="[A-Za-z0-9_]+" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-area">
                <p>Password</p>
                <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
            </div>

            <input type="submit" value="Đăng nhập">
            <p style="margin-top: 15px;">Chưa có tài khoản? <a href="index.php?page=register">Đăng ký</a></p>
        </form>
    </div>
</body>
</html>