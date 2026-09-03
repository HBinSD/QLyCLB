<?php
require_once __DIR__ . '/../../core/Database.php';

class UserModel {
    private PDO $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function getUserByUsernameOrEmail(string $account): ?array {
        $sql = "SELECT u.username, u.password, u.role, u.status, u.created_at,
                       ui.fullname, ui.email, ui.phone, ui.id_number, ui.gender, ui.dob, ui.avt_links
                FROM user AS u
                LEFT JOIN userinfo ui ON u.username = ui.username
                WHERE u.username = :account OR ui.email = :account";
        
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':account' => $account]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function register(string $username, string $password, string $fullname, string $email, ?string $phone = null, ?string $idNumber = null, string $gender = '', ?string $dob = null, string $role = 'member'): bool {
        try {
            $this->pdo->beginTransaction();

            $sqlUser = "INSERT INTO user (username, password, role, status) VALUES (:username, :password, :role, 1)";
            $stmUser = $this->pdo->prepare($sqlUser);
            $stmUser->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':role'     => $role
            ]);

            $sqlInfo = "INSERT INTO userinfo (username, fullname, email, phone, id_number, gender, dob)
                        VALUES (:username, :fullname, :email, :phone, :id_number, :gender, :dob)";
            $stmInfo = $this->pdo->prepare($sqlInfo);
            $stmInfo->execute([
                ':username'  => $username,
                ':fullname'  => $fullname,
                ':email'     => $email,
                ':phone'     => $phone,
                ':id_number' => $idNumber,
                ':gender'    => $gender,
                ':dob'       => $dob
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    // Cập nhật thông tin hồ sơ cá nhân
public function updateProfile($username, $fullname, $email, $phone, $gender, $dob, $idNumber, $avatar = null): bool {
    $sql = "UPDATE userinfo 
            SET fullname = :fullname, 
                email = :email, 
                phone = :phone, 
                gender = :gender, 
                dob = :dob, 
                id_number = :id_number,
                avt_links = COALESCE(:avatar, avt_links)
            WHERE username = :username";
            
    $stm = $this->pdo->prepare($sql);
    return $stm->execute([
        ':fullname'  => $fullname,
        ':email'     => $email,
        ':phone'     => $phone,
        ':gender'    => $gender,
        ':dob'       => $dob ?: null,
        ':id_number' => $idNumber,
        ':avatar'    => $avatar,
        ':username'  => $username
    ]);
}
}