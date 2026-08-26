<?php
$pageTitle = $pageTitle ?? "Quản lý câu lạc bộ";
?>

<style>
    * {
        margin: 0;
        padding: 0;
    }
</style>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet"
          href="dashboard.css">

</head>

<body>

<div class="app">

    <?php require_once __DIR__ . "/navbar.php"; ?>

    <div class="main-wrapper">

        <?php require_once __DIR__ . "/sidebar.php"; ?>

        <main class="main-content"></main>