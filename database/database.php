<?php
    function db(): PDO {
        $dsn = 'mysql:host=localhost;dbname=qlyclb;charset=utf8mb4';
        return new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO:: FETCH_ASSOC,
        ]);
    }

?>