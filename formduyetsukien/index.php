<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký gia nhập Câu lạc bộ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Đăng ký gia nhập Câu lạc bộ</h1>
            <p class="subtitle">Điền thông tin bên dưới để gửi yêu cầu gia nhập</p>
        </header>

        <div id="alert" class="alert" style="display: none;"></div>

        <form id="joinForm" action="submit.php" method="POST" novalidate>
            <div class="form-group">
                <label for="fullname">Họ và tên <span class="required">*</span></label>
                <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" required>
                <span class="error" id="fullname-error"></span>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="email@example.com" required>
                <span class="error" id="email-error"></span>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" placeholder="0912345678" required>
                <span class="error" id="phone-error"></span>
            </div>

            <div class="form-group">
                <label for="student_id">Số CCCD<span class="required">*</span></label>
                <input type="text" id="student_id" name="student_id" placeholder="" required>
            </div>

            <div class="form-group">
                <label for="club">Câu lạc bộ muốn gia nhập <span class="required">*</span></label>
                <select id="club" name="club" required>
                    <option value="">-- Chọn câu lạc bộ --</option>
                    <option value="CNTT">Câu lạc bộ Công nghệ thông tin</option>
                    <option value="Tiếng Anh">Câu lạc bộ Tiếng Anh</option>
                    <option value="Âm nhạc">Câu lạc bộ Âm nhạc</option>
                    <option value="Thể thao">Câu lạc bộ Thể thao</option>
                    <option value="Tình nguyện">Câu lạc bộ Tình nguyện</option>
                    <option value="Khác">Khác</option>
                </select>
                <span class="error" id="club-error"></span>
            </div>

            <div class="form-group">
                <label for="reason">Lý do muốn gia nhập <span class="required">*</span></label>
                <textarea id="reason" name="reason" rows="4" placeholder="Hãy chia sẻ lý do bạn muốn tham gia câu lạc bộ..." required></textarea>
                <span class="error" id="reason-error"></span>
            </div>

            <div class="form-group">
                <label for="experience">Kinh nghiệm / Kỹ năng liên quan (Nếu có)</label>
                <textarea id="experience" name="experience" rows="3" placeholder="Ví dụ: Đã học lập trình 1 năm, biết chơi guitar..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" id="submitBtn" class="btn btn-primary">Gửi yêu cầu</button>
                <button type="reset" class="btn btn-secondary">Làm mới</button>
            </div>
        </form>

        <div class="footer-links">
            <a href="admin.php">Trang duyệt yêu cầu tham gia</a>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>