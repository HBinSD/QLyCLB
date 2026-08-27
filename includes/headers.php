<?php
$pageTitle = $pageTitle ?? "Quản lý câu lạc bộ";
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: #f1f5f9;
    }

    .app {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .navbar {
        margin-left: 260px;
        width: calc(100% - 260px);
    }

    .main-wrapper {
        display: flex;
        flex: 1;
        margin-left: 260px;
        min-height: calc(100vh - 72px);
    }

    .main-content {
        flex: 1;
        padding: 24px;
        background: #f1f5f9;
        min-height: calc(100vh - 72px);
        width: 100%;
    }

    @media (max-width: 768px) {
        .navbar {
            margin-left: 0;
            width: 100%;
        }

        .main-wrapper {
            margin-left: 0;
        }

        .main-content {
            min-height: calc(100vh - 64px);
        }
    }
</style>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body>
<div class="app">

    <?php require_once __DIR__ . "/navbar.php"; ?>

    <div class="main-wrapper">

        <?php require_once __DIR__ . "/sidebar.php"; ?>

        <main class="main-content">