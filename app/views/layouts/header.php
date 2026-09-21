<?php

// ========================================
// โหลด Bootstrap จากโฟลเดอร์ app
// header.php อยู่ที่ app/views/layouts/
// ========================================
require_once dirname(__DIR__, 2) . '/bootstrap.php';


// ========================================
// Middleware
// ========================================
require_once APP_PATH . '/middleware/AuthMiddleware.php';

$csrfToken = AuthMiddleware::csrfToken();

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="<?= htmlspecialchars(
            $csrfToken,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <title>
        <?= htmlspecialchars(
            $page_title ?? 'ระบบงานประชุม',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
        | <?= htmlspecialchars(
            APP_NAME,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>


    <?php
    // ========================================
    // Cache version ของ style.css
    // ใช้ PATH จริงของ Server
    // ========================================

    $styleCssAssetPath =
        PUBLIC_PATH .
        '/assets/css/style.css';

    $styleCssAssetVersion =
        is_file($styleCssAssetPath)
            ? '?v=' . filemtime($styleCssAssetPath)
            : '';
    ?>


    <!-- CSS หลัก -->
    <link
        rel="stylesheet"
        href="<?= ASSET_URL ?>css/style.css<?= $styleCssAssetVersion ?>"
    >

    <!-- Profile Modal -->
    <link
        rel="stylesheet"
        href="<?= ASSET_URL ?>css/modal-ed-profile.css"
    >


    <?php

    $current_page =
        $page_title ?? '';

    ?>


    <?php if (
        $current_page === 'Dashboard - Admin'
    ): ?>

        <link
            rel="stylesheet"
            href="<?= ASSET_URL ?>css/style_ad.css"
        >

    <?php elseif (
        $current_page === 'Dashboard - User'
    ): ?>

        <link
            rel="stylesheet"
            href="<?= ASSET_URL ?>css/style_user.css"
        >

    <?php endif; ?>


    <?php if (
        isset($page_css) &&
        $page_css !== ''
    ): ?>

        <link
            rel="stylesheet"
            href="<?= ASSET_URL ?>css/<?= htmlspecialchars(
                $page_css,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

    <?php endif; ?>


    <!-- Favicon -->
    <link
        rel="icon"
        href="<?= ASSET_URL ?>image/logo.svg"
    >


    <!-- Google Identity -->
    <script
        src="https://accounts.google.com/gsi/client"
        async
        defer
    ></script>


    <!-- Lucide -->
    <script
        src="<?= ASSET_URL ?>js/lucide.min.js"
    ></script>


    <script>
    window.APP_URL = <?= json_encode(
        BASE_URL . 'app/',
        JSON_UNESCAPED_SLASHES
    ) ?>;
    </script>
</head>

<body>

<?php

// ========================================
// Components
// ========================================

require_once APP_PATH .
    '/views/components/profile_modal.php';

require_once APP_PATH .
    '/views/components/ai.php';

?>