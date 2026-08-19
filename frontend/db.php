<?php

$host = "localhost";
$dbname = "student_db";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    var_dump($conn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Kết nối MySQL thành công!";

} catch (PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}