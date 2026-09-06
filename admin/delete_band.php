<?php
session_start();

require_once "../includes/auth.php";
require_once "../database/database.php";

$user = $_SESSION['user'] ?? null;

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}


$database = new Database();
$db = $database->getConnection();

$clubId = "CLB001";

$bandId = trim($_GET['id'] ?? '');

if ($bandId === '') {
    header("Location: clubs.php");
    exit;
}


try {

    /*
    |--------------------------------------------------------------------------
    | Kiểm tra ban thuộc CLB
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        SELECT band_id
        FROM ClubBand
        WHERE band_id = :band_id
          AND club_id = :club_id
        LIMIT 1
    ");

    $stmt->execute([
        ':band_id' => $bandId,
        ':club_id' => $clubId
    ]);

    if (!$stmt->fetch()) {

        header("Location: club.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Xóa thành viên khỏi ban trước
    |--------------------------------------------------------------------------
    |
    | Nếu ClubBandMember có FK tới ClubBand thì phải xóa
    | các thành viên thuộc ban trước.
    |
    */

    $db->beginTransaction();


    $stmt = $db->prepare("
        DELETE FROM ClubBandMember
        WHERE band_id = :band_id
          AND club_id = :club_id
    ");

    $stmt->execute([
        ':band_id' => $bandId,
        ':club_id' => $clubId
    ]);


    /*
    |--------------------------------------------------------------------------
    | Xóa ban
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        DELETE FROM ClubBand
        WHERE band_id = :band_id
          AND club_id = :club_id
    ");

    $stmt->execute([
        ':band_id' => $bandId,
        ':club_id' => $clubId
    ]);


    $db->commit();


} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }
}


header("Location: clubs.php");
exit;