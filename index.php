<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

try {
    // Khởi tạo kết nối PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    
    // Thiết lập chế độ báo lỗi ngoại lệ (Exception)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Kết nối cơ sở dữ liệu thành công!";
} catch(PDOException $e) {
    // Thông báo lỗi nếu kết nối thất bại
    echo "Kết nối thất bại: " . $e->getMessage();
}
?>