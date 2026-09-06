<?php

session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";


// =====================================================
// CHECK ADMIN
// =====================================================

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {

    header("Location: ../index.php");
    exit;
}


// =====================================================
// DATABASE
// =====================================================

$database = new Database();
$db = $database->getConnection();


// =====================================================
// ONLY POST
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: accept_member.php");
    exit;
}


// =====================================================
// DATA
// =====================================================

$applicationId = trim(
    $_POST['application_id'] ?? ''
);

$clubId = "CLB001";

$adminUsername = $user['username'];


// =====================================================
// VALIDATE
// =====================================================

if ($applicationId === '') {

    $_SESSION['error'] =
        "Không tìm thấy đơn đăng ký.";

    header("Location: accept_member.php");

    exit;
}


try {

    $db->beginTransaction();


    // =================================================
    // LOCK APPLICATION
    // =================================================

    $sql = "
        SELECT *
        FROM ClubApplication
        WHERE application_id = :application_id
          AND club_id = :club_id
        FOR UPDATE
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':application_id' => $applicationId,
        ':club_id' => $clubId
    ]);

    $application =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$application) {

        throw new Exception(
            "Không tìm thấy đơn đăng ký."
        );
    }


    // =================================================
    // CHECK PENDING
    // =================================================

    if ($application['status'] !== 'pending') {

        throw new Exception(
            "Đơn đăng ký này đã được xử lý trước đó."
        );
    }


    $username =
        $application['username'];

    $bandId =
        $application['desired_band'];


    // =================================================
    // CHECK USER
    // =================================================

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

    if (!$stmt->fetch()) {

        throw new Exception(
            "Tài khoản sinh viên không tồn tại."
        );
    }


    // =================================================
    // CHECK EXISTING CLUB MEMBER
    // =================================================

    $sql = "
        SELECT id
        FROM ClubMember
        WHERE username = :username
          AND club_id = :club_id
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':club_id' => $clubId
    ]);

    if ($stmt->fetch()) {

        throw new Exception(
            "Sinh viên này đã là thành viên CLB."
        );
    }


    // =================================================
    // CHECK BAND
    // =================================================

    if ($bandId !== null && $bandId !== '') {

        $sql = "
            SELECT band_id
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

        if (!$stmt->fetch()) {

            throw new Exception(
                "Ban mà sinh viên đăng ký không tồn tại."
            );
        }
    }


    // =================================================
    // INSERT CLUB MEMBER
    // =================================================

    $defaultRole = 'Thành viên';
    $sql = "
        INSERT INTO ClubMember (
            username,
            club_id,
            joined_at,
            position,
            status
        )
        VALUES (
            :username,
            :club_id,
            NOW(),
            :position,
            1
        )
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':club_id' => $clubId,
        ':position' => $defaultRole
    ]);


    // =================================================
    // INSERT CLUB BAND MEMBER
    // =================================================

    if ($bandId !== null && $bandId !== '') {

        $sql = "
            INSERT INTO ClubBandMember (
                username,
                club_id,
                band_id
            )
            VALUES (
                :username,
                :club_id,
                :band_id
            )
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':club_id' => $clubId,
            ':band_id' => $bandId
        ]);
    }


    // =================================================
    // UPDATE APPLICATION
    // =================================================

    $sql = "
        UPDATE ClubApplication
        SET
            status = 'approved',
            reviewed_by = :reviewed_by,
            reviewed_at = NOW()
        WHERE application_id = :application_id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':reviewed_by' => $adminUsername,
        ':application_id' => $applicationId
    ]);


    // =================================================
    // COMMIT
    // =================================================

    $db->commit();


    $_SESSION['success'] =
        "Đã duyệt thành viên thành công.";


} catch (Exception $e) {

    if ($db->inTransaction()) {

        $db->rollBack();
    }


    $_SESSION['error'] =
        $e->getMessage();
}


// =====================================================
// RETURN
// =====================================================

header("Location: accept_member.php");

exit;