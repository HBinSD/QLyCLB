<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .register-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 520px;
        }
        .register-card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group input:focus, 
        .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .error-msg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #f5c6cb;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            display: block;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-card">
    <h2>Đăng ký tài khoản</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="index.php?page=register" method="POST">
        <div class="form-group">
            <label for="fullname">Họ và tên *</label>
            <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required placeholder="Ví dụ: Nguyễn Văn A">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="username">Tên đăng nhập *</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required placeholder="Tên đăng nhập">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="example@email.com">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="0901234567">
            </div>
            <div class="form-group">
                <label for="id_number">Số CCCD/CMND</label>
                <input type="text" id="id_number" name="id_number" value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>" placeholder="012345678901">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="dob">Ngày sinh</label>
                <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="gender">Giới tính</label>
                <select id="gender" name="gender">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" <?= (($_POST['gender'] ?? '') === 'Nam') ? 'selected' : '' ?>>Nam</option>
                    <option value="Nữ" <?= (($_POST['gender'] ?? '') === 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                    <option value="Khác" <?= (($_POST['gender'] ?? '') === 'Khác') ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-submit">Đăng ký</button>
    </form>

    <a href="index.php?page=login" class="login-link">Đã có tài khoản? Đăng nhập ngay</a>
</div>

</body>
</html>