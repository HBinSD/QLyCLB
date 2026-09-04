<?php

session_start();

require_once __DIR__ . "/database/database.php";


// =====================================================
// DATABASE
// =====================================================

$database = new Database();
$db = $database->getConnection();


// =====================================================
// CONFIG
// =====================================================

$clubId = "CLB001";

$errors = [];


// =====================================================
// LẤY DANH SÁCH BAN
// =====================================================

$sql = "
    SELECT
        band_id,
        band_name
    FROM ClubBand
    WHERE club_id = :club_id
    ORDER BY band_id ASC
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':club_id' => $clubId
]);

$bands = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// XỬ LÝ FORM
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =================================================
    // STEP 1
    // =================================================

    $username = trim($_POST["username"] ?? "");
    $fullname = trim($_POST["fullname"] ??"");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $dob = trim($_POST["dob"] ?? "");
    $idNumber = trim($_POST["id_number"] ??"");


    // =================================================
    // STEP 2
    // =================================================

    $reason = trim($_POST["reason"] ?? "");
    $expectation = trim($_POST["expectation"] ?? "");
    $skills = trim($_POST["skills"] ?? "");
    $bandId = trim($_POST["band_id"] ?? "");


    // =================================================
    // VALIDATE USERNAME
    // =================================================

    if ($username === "") {

        $errors[] = "Vui lòng nhập username.";

    } elseif (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {

        $errors[] =
            "Username phải có từ 3-50 ký tự và chỉ chứa chữ, số, dấu chấm, gạch dưới hoặc gạch ngang.";
    }


    // =================================================
    // VALIDATE PASSWORD
    // =================================================

    if ($password === "") {

        $errors[] = "Vui lòng nhập mật khẩu.";

    } elseif (strlen($password) < 6) {

        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }


    // =================================================
    // VALIDATE CONFIRM PASSWORD
    // =================================================

    if ($confirmPassword === "") {

        $errors[] = "Vui lòng nhập lại mật khẩu.";

    } elseif ($password !== $confirmPassword) {

        $errors[] = "Mật khẩu nhập lại không khớp.";
    }


    // =================================================
    // VALIDATE EMAIL
    // =================================================

    if ($email === "") {

        $errors[] = "Vui lòng nhập email.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors[] = "Email không hợp lệ.";
    }


    // =================================================
    // VALIDATE DOB
    // =================================================

    if ($dob === "") {

        $errors[] = "Vui lòng nhập ngày sinh.";

    } else {

        $date = DateTime::createFromFormat(
            "Y-m-d",
            $dob
        );

        $dateErrors =
            DateTime::getLastErrors();

        if (
            !$date ||
            (
                is_array($dateErrors) &&
                (
                    $dateErrors['warning_count'] > 0 ||
                    $dateErrors['error_count'] > 0
                )
            )
        ) {

            $errors[] = "Ngày sinh không hợp lệ.";

        } elseif ($date > new DateTime("today")) {

            $errors[] =
                "Ngày sinh không thể lớn hơn ngày hiện tại.";
        }
    }


    // =================================================
    // CHECK USERNAME TRÙNG
    // =================================================

    if ($username !== "") {

        $sql = "
            SELECT username
            FROM User
            WHERE username = :username
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':username' => $username
        ]);

        if ($stmt->fetch()) {

            $errors[] =
                "Username đã tồn tại. Vui lòng chọn username khác.";
        }
    }


    // =================================================
    // CHECK EMAIL TRÙNG
    // =================================================

    if ($email !== "") {

        $sql = "
            SELECT username
            FROM UserInfo
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        if ($stmt->fetch()) {

            $errors[] =
                "Email đã được sử dụng. Vui lòng sử dụng email khác.";
        }
    }


    // =================================================
    // VALIDATE LÝ DO
    // =================================================

    if ($reason === "") {

        $errors[] =
            "Vui lòng nhập lý do đăng ký CLB.";
    }


    // =================================================
    // VALIDATE MONG MUỐN
    // =================================================

    if ($expectation === "") {

        $errors[] =
            "Vui lòng nhập mong muốn khi tham gia CLB.";
    }


    // =================================================
    // VALIDATE KỸ NĂNG
    // =================================================

    if ($skills === "") {

        $errors[] =
            "Vui lòng nhập kỹ năng của bạn.";
    }


    // =================================================
    // VALIDATE BAN
    // =================================================

    if ($bandId === "") {

        $errors[] =
            "Vui lòng chọn ban muốn tham gia.";

    } else {

        $sql = "
            SELECT
                band_id,
                band_name
            FROM ClubBand
            WHERE band_id = :band_id
              AND club_id = :club_id
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':band_id' => $bandId,
            ':club_id' => $clubId
        ]);

        $selectedBand = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$selectedBand) {

            $errors[] =
                "Ban bạn chọn không tồn tại.";
        }
    }


    // =================================================
    // KIỂM TRA CLB
    // =================================================

    if (empty($errors)) {

        $sql = "
            SELECT club_id
            FROM Clubs
            WHERE club_id = :club_id
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':club_id' => $clubId
        ]);

        if (!$stmt->fetch()) {

            $errors[] =
                "Không tìm thấy câu lạc bộ.";
        }
    }


    // =================================================
    // INSERT DATABASE
    // =================================================

    if (empty($errors)) {

        try {

            $db->beginTransaction();


            // =========================================
            // HASH PASSWORD
            // =========================================

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // =========================================
            // INSERT USER
            // =========================================

            $sql = "
                INSERT INTO User (
                    username,
                    password,
                    role,
                    created_at,
                    status
                )
                VALUES (
                    :username,
                    :password,
                    'member',
                    NOW(),
                    '1'
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword
            ]);


            // =========================================
            // INSERT USER INFO
            // =========================================
            //
            // fullname chưa được nhập ở bước đăng ký.
            // Để trống để sinh viên cập nhật sau.
            //
            // Nếu UserInfo của bạn có các cột khác bắt
            // buộc NOT NULL, cần cho phép NULL hoặc thêm
            // giá trị mặc định.
            // =========================================

            $sql = "
                INSERT INTO UserInfo (
                    username,
                    fullname,
                    email,
                    DOB,
                    id_number
                )
                VALUES (
                    :username,
                    :fullname,
                    :email,
                    :dob,
                    :id_number
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':username' => $username,
                ':fullname' => $fullname,
                ':email' => $email,
                ':dob' => $dob,
                ':id_number' => $idNumber
            ]);


            // =========================================
            // INSERT CLUB APPLICATION
            // =========================================
            //
            // Chưa tạo ClubMember ở đây.
            //
            // desired_band lưu band_id.
            //
            // Ví dụ:
            // band_id = 2
            //
            // Sau này Admin duyệt mới tạo:
            // ClubMember
            // ClubBandMember
            // =========================================

            $sql = "
                INSERT INTO ClubApplication (
                    username,
                    club_id,
                    reason,
                    expectation,
                    skills,
                    desired_band,
                    status,
                    created_at
                )
                VALUES (
                    :username,
                    :club_id,
                    :reason,
                    :expectation,
                    :skills,
                    :desired_band,
                    'pending',
                    NOW()
                )
            ";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':username' => $username,
                ':club_id' => $clubId,
                ':reason' => $reason,
                ':expectation' => $expectation,
                ':skills' => $skills,
                ':desired_band' => $bandId
            ]);


            // =========================================
            // COMMIT
            // =========================================

            $db->commit();


            // =========================================
            // ĐĂNG KÝ THÀNH CÔNG
            // =========================================

            header(
                "Location: register_success.php"
            );

            exit;


        } catch (PDOException $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }


            // =========================================
            // XỬ LÝ TRÙNG KHÓA
            // =========================================

            if ($e->errorInfo[1] ?? null == 1062) {

                $errors[] =
                    "Username hoặc email đã tồn tại.";

            } else {

                $errors[] =
                    "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.";

                // Khi cần debug:
                // $errors[] = $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đăng ký câu lạc bộ</title>

    <link
        rel="stylesheet"
        href="css/register.css"
    >

</head>


<body>


<div class="register-container">


    <!-- =========================================
         TITLE
    ========================================== -->

    <div class="register-title">

        <h1 id="stepTitle">
            ĐĂNG KÝ CÂU LẠC BỘ
        </h1>

        <div class="title-line"></div>

        <p id="stepDescription">
            Bước 1: Vui lòng nhập thông tin tài khoản của bạn.
        </p>

    </div>


    <!-- =========================================
         ERROR
    ========================================== -->

    <?php if (!empty($errors)): ?>

        <div class="error-box">

            <?php foreach ($errors as $error): ?>

                <div>
                    • <?= htmlspecialchars($error) ?>
                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =========================================
         STEP INDICATOR
    ========================================== -->

    <div class="steps">


        <div
            class="step active"
            id="indicator1"
        >

            <span id="indicatorNumber1">
                1
            </span>

            <strong>
                Thông tin tài khoản
            </strong>

        </div>


        <div class="step-line"></div>


        <div
            class="step"
            id="indicator2"
        >

            <span id="indicatorNumber2">
                2
            </span>

            <strong>
                Thông tin đăng ký
            </strong>

        </div>

    </div>


    <!-- =========================================
         FORM
    ========================================== -->

    <form
        method="POST"
        action="register.php"
        id="registerForm"
        novalidate
    >


        <!-- =====================================
             STEP 1
        ====================================== -->

        <div
            class="form-step active"
            id="step1"
        >

            <!-- USERNAME -->

            <div class="form-group full">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    id="username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    autocomplete="username"
                >

                <small class="field-error"></small>

            </div>


            <!-- FULLNAME + ID NUMBER -->

            <div class="form-row">

                <div class="form-group">

                    <label for="fullname">
                        Họ và tên
                    </label>

                    <input
                        type="text"
                        name="fullname"
                        id="fullname"
                        value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                    >

                    <small class="field-error"></small>

                </div>


                <div class="form-group">

                    <label for="id_number">
                        Mã sinh viên
                    </label>

                    <input
                        type="text"
                        name="id_number"
                        id="id_number"
                        value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>"
                    >

                    <small class="field-error"></small>

                </div>

            </div>


            <!-- PASSWORD + CONFIRM PASSWORD -->

            <div class="form-row">

                <div class="form-group">

                    <label for="password">
                        Mật khẩu
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="new-password"
                    >

                    <small class="field-error"></small>

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Nhập lại mật khẩu
                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        autocomplete="new-password"
                    >

                    <small class="field-error"></small>

                </div>

            </div>


            <!-- EMAIL + DOB -->

            <div class="form-row">

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autocomplete="email"
                    >

                    <small class="field-error"></small>

                </div>


                <div class="form-group">

                    <label for="dob">
                        Ngày sinh
                    </label>

                    <input
                        type="date"
                        name="dob"
                        id="dob"
                        value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>"
                    >

                    <small class="field-error"></small>

                </div>

            </div>


            <!-- NEXT -->

            <div class="button-row next-row">

                <button
                    type="button"
                    class="btn-primary"
                    id="nextButton"
                >
                    Tiếp tục

                    <span>→</span>

                </button>

            </div>

        </div>


        <!-- =====================================
             STEP 2
        ====================================== -->

        <div
            class="form-step"
            id="step2"
        >


            <!-- REASON -->

            <div class="form-group full">

                <label for="reason">
                    Lý do đăng ký CLB
                </label>

                <textarea
                    name="reason"
                    id="reason"
                    rows="4"
                ><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>

                <small class="field-error"></small>

            </div>


            <!-- EXPECTATION -->

            <div class="form-group full">

                <label for="expectation">
                    Mong muốn khi tham gia CLB
                </label>

                <textarea
                    name="expectation"
                    id="expectation"
                    rows="4"
                ><?= htmlspecialchars($_POST['expectation'] ?? '') ?></textarea>

                <small class="field-error"></small>

            </div>


            <!-- SKILLS -->

            <div class="form-group full">

                <label for="skills">
                    Tài năng / Kĩ năng của bạn
                </label>

                <textarea
                    name="skills"
                    id="skills"
                    rows="4"
                ><?= htmlspecialchars($_POST['skills'] ?? '') ?></textarea>

                <small class="field-error"></small>

            </div>


            <!-- =================================
                 CHỌN BAN
            ================================== -->

            <div class="form-group full">

                <label for="band_id">
                    Bạn muốn đăng ký vào ban nào?
                </label>


                <select
                    name="band_id"
                    id="band_id"
                >

                    <option value="">
                        -- Chọn ban --
                    </option>


                    <?php foreach ($bands as $band): ?>

                        <option
                            value="<?= htmlspecialchars($band['band_id']) ?>"
                            <?= (
                                ($_POST['band_id'] ?? '') == $band['band_id']
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= htmlspecialchars($band['band_id']) ?>
                            -
                            <?= htmlspecialchars($band['band_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <small class="field-error"></small>

            </div>


            <!-- BUTTON -->

            <div class="button-row">

                <button
                    type="button"
                    class="btn-back"
                    id="backButton"
                >
                    Quay lại
                </button>


                <button
                    type="submit"
                    class="btn-primary"
                >
                    Hoàn tất đăng ký
                </button>

            </div>


        </div>


    </form>

</div>


<script>


// =================================================
// ELEMENT
// =================================================

const step1 =
    document.getElementById("step1");

const step2 =
    document.getElementById("step2");

const indicator1 =
    document.getElementById("indicator1");

const indicator2 =
    document.getElementById("indicator2");

const indicatorNumber1 =
    document.getElementById("indicatorNumber1");

const indicatorNumber2 =
    document.getElementById("indicatorNumber2");

const stepTitle =
    document.getElementById("stepTitle");

const stepDescription =
    document.getElementById("stepDescription");


// =================================================
// STEP 1
// =================================================

function showStep1() {

    step1.classList.add("active");

    step2.classList.remove("active");


    indicator1.classList.add("active");

    indicator1.classList.remove("completed");


    indicator2.classList.remove("active");

    indicator2.classList.remove("completed");


    indicatorNumber1.innerText = "1";

    indicatorNumber2.innerText = "2";


    stepTitle.innerText =
        "ĐĂNG KÝ CÂU LẠC BỘ";


    stepDescription.innerText =
        "Bước 1: Vui lòng nhập thông tin tài khoản của bạn.";
}


// =================================================
// STEP 2
// =================================================

function showStep2() {

    step1.classList.remove("active");

    step2.classList.add("active");


    indicator1.classList.remove("active");

    indicator1.classList.add("completed");


    indicator2.classList.add("active");


    indicatorNumber1.innerText = "✓";

    indicatorNumber2.innerText = "2";


    stepTitle.innerText =
        "THÔNG TIN ĐĂNG KÝ CLB";


    stepDescription.innerText =
        "Bước 2: Hãy chia sẻ thêm thông tin để CLB hiểu rõ hơn về bạn.";
}


// =================================================
// CLEAR ERROR
// =================================================

function clearErrors() {

    document
        .querySelectorAll(".field-error")
        .forEach(function (element) {

            element.innerText = "";

        });


    document
        .querySelectorAll(".input-error")
        .forEach(function (element) {

            element.classList.remove("input-error");

        });


    document
        .querySelectorAll(".select-error")
        .forEach(function (element) {

            element.classList.remove("select-error");

        });
}


// =================================================
// SHOW ERROR
// =================================================

function showError(element, message) {

    const error =
        element.parentElement.querySelector(
            ".field-error"
        );

    if (error) {
        error.innerText = message;
    }


    element.classList.add("input-error");
}


// =================================================
// VALIDATE STEP 1
// =================================================

function validateStep1() {

    clearErrors();

    let valid = true;


    const username =
        document.getElementById("username");

    const password =
        document.getElementById("password");

    const confirmPassword =
        document.getElementById("confirm_password");

    const email =
        document.getElementById("email");

    const dob =
        document.getElementById("dob");


    // USERNAME

    if (username.value.trim() === "") {

        showError(
            username,
            "Vui lòng nhập username."
        );

        valid = false;

    } else if (
        !/^[A-Za-z0-9._-]{3,50}$/.test(
            username.value.trim()
        )
    ) {

        showError(
            username,
            "Username không hợp lệ."
        );

        valid = false;
    }


    // PASSWORD

    if (password.value === "") {

        showError(
            password,
            "Vui lòng nhập mật khẩu."
        );

        valid = false;

    } else if (password.value.length < 6) {

        showError(
            password,
            "Mật khẩu phải có ít nhất 6 ký tự."
        );

        valid = false;
    }


    // CONFIRM PASSWORD

    if (confirmPassword.value === "") {

        showError(
            confirmPassword,
            "Vui lòng nhập lại mật khẩu."
        );

        valid = false;

    } else if (
        password.value !== confirmPassword.value
    ) {

        showError(
            confirmPassword,
            "Mật khẩu nhập lại không khớp."
        );

        valid = false;
    }


    // EMAIL

    if (email.value.trim() === "") {

        showError(
            email,
            "Vui lòng nhập email."
        );

        valid = false;

    } else if (
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
            email.value.trim()
        )
    ) {

        showError(
            email,
            "Email không hợp lệ."
        );

        valid = false;
    }


    // DOB

    if (dob.value === "") {

        showError(
            dob,
            "Vui lòng nhập ngày sinh."
        );

        valid = false;

    } else {

        const selectedDate =
            new Date(dob.value);

        const today =
            new Date();

        today.setHours(0, 0, 0, 0);


        if (selectedDate > today) {

            showError(
                dob,
                "Ngày sinh không hợp lệ."
            );

            valid = false;
        }
    }


    return valid;
}


// =================================================
// VALIDATE STEP 2
// =================================================

function validateStep2() {

    clearErrors();

    let valid = true;


    const reason =
        document.getElementById("reason");

    const expectation =
        document.getElementById("expectation");

    const skills =
        document.getElementById("skills");

    const band =
        document.getElementById("band_id");


    if (reason.value.trim() === "") {

        showError(
            reason,
            "Vui lòng nhập lý do đăng ký."
        );

        valid = false;
    }


    if (expectation.value.trim() === "") {

        showError(
            expectation,
            "Vui lòng nhập mong muốn."
        );

        valid = false;
    }


    if (skills.value.trim() === "") {

        showError(
            skills,
            "Vui lòng nhập kỹ năng."
        );

        valid = false;
    }


    if (band.value === "") {

        showError(
            band,
            "Vui lòng chọn ban."
        );

        valid = false;
    }


    return valid;
}


// =================================================
// NEXT BUTTON
// =================================================

document
    .getElementById("nextButton")
    .addEventListener(
        "click",
        function () {

            if (validateStep1()) {

                showStep2();

            }

        }
    );


// =================================================
// BACK BUTTON
// =================================================

document
    .getElementById("backButton")
    .addEventListener(
        "click",
        function () {

            showStep1();

        }
    );


// =================================================
// SUBMIT
// =================================================

document
    .getElementById("registerForm")
    .addEventListener(
        "submit",
        function (event) {

            if (!validateStep2()) {

                event.preventDefault();

            }

        }
    );


// =================================================
// NẾU SERVER TRẢ LỖI
// =================================================

<?php if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && !empty($errors)
): ?>

showStep2();

<?php endif; ?>


</script>


</body>

</html>