<?php
$pageTitle = $pageTitle ?? "Quản lý câu lạc bộ";
$basePath = $basePath ?? "..";
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet"
          href="../public/css/dashboard.css">

</head>

<body>

<div class="app">

    <?php require_once __DIR__ . "/sidebar.php"; ?>

    <div class="main-wrapper">

        <?php require_once __DIR__ . "/navbar.php"; ?>

        <main class="main-content"></main>