<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<?php
    session_start();
    require_once(dirname(__FILE__) . "/database/database.php");

    // check nếu đã đăng nhập thì chuyển qua dashboard
    if (isset($_SESSION['user'])) {

        switch ($_SESSION['user']['role']) {

            case 'admin':
                header("Location: admin/dashboard.php");
                exit;

            case 'organizer':
                header("Location: organizer/dashboard.php");
                exit;

            case 'member':
                header("Location: member/dashboard.php");
                exit;
        }
    }
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        // ========================================
        // VALIDATE FORM
        // ========================================

        if ($username === "" || $password === "") {
            $error = "Vui lòng nhập đầy đủ username và mật khẩu.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = "Username không hợp lệ.";
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                $sql = "SELECT
                            us.username,
                            us.password,
                            us.role,
                            us.status,
                            us.created_at,
                            u.*
                        FROM user AS us
                        LEFT JOIN userinfo AS u
                            ON us.username = u.username
                        WHERE us.username = ?";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $username
                ]);
                $user = $stmt->fetch();
                if (!$user) {
                    $error = "Username hoặc mật khẩu không chính xác.";
                } else {
                    if (
                        isset($user['status'])
                        && $user['status'] !== '1'
                    ) {
                        $error = "Tài khoản của bạn không tồn tại.";

                    } else {
                        // ========================================
                        // KIỂM TRA PASSWORD
                        // ========================================
                        
                        if (password_verify($password, $user['password'])) {
                            // ========================================
                            // TẠO SESSION
                            // ========================================

                            $_SESSION['user'] = [
                                'username' => $user['username'],
                                'fullname' => $user['fullname'] ?? '',
                                'email' => $user['email'] ?? '',
                                'DOB' => $user['dob'] ?? '',
                                'phone' => $user['phone'] ?? '',
                                'id_number' => $user['id_number'] ?? '',
                                'gender' => $user['gender'] ?? '',
                                'role' => $user['role'],
                                'ngayTao' => $user['created_at'] ??'',
                                'avatar' => $user['avt_links'] ??'',
                            ];

                            // ========================================
                            // CHUYỂN TRANG THEO ROLE
                            // ========================================

                            switch ($user['role']) {
                                case 'admin':
                                    header("Location: admin/dashboard.php");
                                    exit;
                                case 'organizer':
                                    header("Location: organizer/dashboard.php");
                                    exit;
                                case 'member':
                                    header("Location: member/dashboard.php");
                                    exit;
                                default:
                                    $error = "Role của tài khoản không hợp lệ.";
                            }
                        } else {
                            $error = "Username hoặc mật khẩu không chính xác.";
                        }
                    }
                }
            } catch (PDOException $e) {
                $error = "Có lỗi xảy ra khi kết nối database.";
                echo $e->getMessage();  
            }
        }
    }
?>
<body>
    <div class="form">
        <form action="" method="POST">
            <h1>Đăng nhập</h1>
            <span>Đăng nhập hệ thống</span>


            <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>


            <div class="form-area">
                <p>Username</p>
                <input type="text" id="username" name="username" placeholder="Nhập username" minlength="3"
                    maxlength="30" pattern="[A-Za-z0-9_]+" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required>
            </div>

            <div class="form-area">
                <p>Password</p>
                <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
            </div>

            <input type="submit" value="Đăng nhập">
        </form>
    </div>
</body>

</html>