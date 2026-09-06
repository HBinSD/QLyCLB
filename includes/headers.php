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

    /* CSS tối ưu cho Global Loader */
    #global-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #ffffff; /* Màu nền che trang */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999999;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }
    #global-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
    <div id="global-loader">
    <div class="spinner"></div>
    </div>


    <div class="app">

        <?php require_once __DIR__ . "/navbar.php"; ?>

        <div class="main-wrapper">

            <?php require_once __DIR__ . "/sidebar.php"; ?>

            <main class="main-content">