<?php
$hoTen   = "Nguyễn Tùng Dương";
$truong  = "Đại học Thủ Đô Hà Nội";
$nganh   = "Công nghệ Thông tin";
$dienThoai = "0332556674";
$email   = "duong2dayo@gmail.com";
$github  = "https://github.com/Duong1day/repo-9-bu-i.git";
$github1 = "https://github.com/HBinSD/QLyCLB.git";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giới thiệu bản thân</title>
  <style>
     * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
      color: #333;
    }
    header {
      background-color: #ff9800;
      padding: 15px 0;
      text-align: center;
    }
    header h1 {
      color: white;
      font-size: 28px;
    }
    nav {
      background-color: #fff3e0;
      display: flex;
      justify-content: center;
      gap: 40px;
      padding: 10px 0;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    nav a {
      text-decoration: none;
      font-size: 18px;
      color: #333;
      font-weight: bold;
    }
    nav a:hover {
      color: #ff5722;
    }
    .container {
      max-width: 900px;
      margin: 30px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .profile-img {
      display: block;
      margin: 0 auto 20px;
      border-radius: 12px;
      width: 250px;
      height: auto;
    }
    h2 {
      margin-top: 20px;
      color: #ff9800;
    }
    p {
      margin-top: 10px;
      line-height: 1.6;
    }
    ol {
      margin-left: 20px;
    }
    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 18px;
      color: #888;
    }

    @media (max-width: 600px) {
      nav {
        flex-direction: column;
        gap: 10px;
      }
      .container {
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>! Chào mừng mọi người đến với trang giới thiệu bản thân</h1>
  </header>
  <nav></nav>
  <div class="container">
    <p>Xin chào! Mình là <strong><?php echo $hoTen; ?></strong>, sinh viên năm nhất ngành 
    <?php echo $nganh; ?> tại <?php echo $truong; ?>. Đây là website mình tạo để giới thiệu đôi nét về bản thân.</p>
    <ol>
      <li>
        <h2>1. Thông tin cá nhân</h2>
        <p>Mình đam mê công nghệ thông tin và luôn muốn phát triển bản thân để có thể tự lập và hỗ trợ gia đình.</p>
      </li>
      <li>
        <h2>2. Sở thích & Đam mê</h2>
        <p>Mình thích chơi game, thể thao, nghe nhạc và xem phim.</p>
      </li>
      <li>
        <h2>3. Mục tiêu tương lai</h2>
        <p>Mình muốn trở thành một lập trình viên giỏi, không ngừng học hỏi và tích lũy kinh nghiệm qua các dự án thực tế.</p>
      </li>
      <li>
        <h2>4. Liên hệ</h2>
        <p><strong>Điện thoại:</strong> <?php echo $dienThoai; ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
        <p><strong>GitHub:</strong> <a href="<?php echo $github; ?>" target="_blank"><?php echo $github; ?></a></p>
        <p><strong>GitHub:</strong> <a href="<?php echo $github1; ?>" target="_blank"><?php echo $github1; ?></a></p>
      </li>
    </ol>
    <footer>
      Cảm ơn bạn đã ghé thăm trang web của mình!
      <br>
    </footer>
  </div>
</body>
</html>