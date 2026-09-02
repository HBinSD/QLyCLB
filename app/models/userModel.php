<?php
require_once __DIR__ . '/../../core/Database.php';

class UserModel {
    private PDO $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // 1. ĐĂNG KÝ TÀI KHOẢN MỚI (Lưu vào cả 2 bảng user và userinfo)
public function register($username, $password, $fullname, $email, $phone = null, $idNumber = null, $role = 'USER'): bool {
    try {
        $this->pdo->beginTransaction();

        // 1. Thêm vào bảng user
        $sqlUser = "INSERT INTO user (username, password, role, status) VALUES (:username, :password, :role, 1)";
        $stmUser = $this->pdo->prepare($sqlUser);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmUser->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':role'     => $role
        ]);

        // 2. Thêm vào bảng userinfo (đã bổ sung phone và id_number)
        $sqlInfo = "INSERT INTO userinfo (username, fullname, email, phone, id_number) 
                    VALUES (:username, :fullname, :email, :phone, :id_number)";
        $stmInfo = $this->pdo->prepare($sqlInfo);
        $stmInfo->execute([
            ':username'  => $username,
            ':fullname'  => $fullname,
            ':email'     => $email,
            ':phone'     => $phone,
            ':id_number' => $idNumber
        ]);

        $this->pdo->commit();
        return true;
    } catch (Exception $e) {
        $this->pdo->rollBack();
        return false;
    }
}
// 2. TÌM TÀI KHOẢN THEO USERNAME HOẶC EMAIL (Dùng khi Đăng nhập)
    public function getUserByUsernameOrEmail($account) {
        $sql = "SELECT u.username, u.password, u.role, u.status, ui.fullname, ui.email, ui.phone, ui.id_number 
                FROM user u
                LEFT JOIN userinfo ui ON u.username = ui.username
                WHERE u.username = :account OR ui.email = :account";
        
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':account' => $account]);
        
        return $stm->fetch(PDO::FETCH_ASSOC);
    }
}
?>