<?php 
// 1. Nạp file cấu hình bằng đường dẫn tuyệt đối
require_once __DIR__ . '/../config/database.php';

class Database {
    // Phương thức tĩnh để lấy kết nối CSDL
    public static function getConnection() {
        try {
            if (class_exists('PDO')) {
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", // Dùng utf8mb4 để hỗ trợ đầy đủ Tiếng Việt
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Trả dữ liệu dạng Mảng kết hợp
                );
                
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
                $con = new PDO($dsn, DB_USER, DB_PASS, $options);
                
                return $con;
            }
        } catch (PDOException $ex) {
            die('Lỗi kết nối CSDL: ' . $ex->getMessage());
        }
    }
}
?>