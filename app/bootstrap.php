<?php

// ===============================
// 🔧 CORE SYSTEM BOOTSTRAP
// ===============================


// ===============================
// 📁 SYSTEM PATHS
// ===============================

// Root ของโปรเจกต์
// localhost:
// C:/xampp/htdocs/Meeting_msu
//
// hosting:
// /home/username/public_html
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}


// โฟลเดอร์ app
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}


// โฟลเดอร์ public
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . '/public');
}


// ===============================
// 🌐 BASE URL
// ===============================

if (!defined('BASE_URL')) {

    /*
     * ถ้ามี APP_BASE_URL ใน Hosting
     * จะใช้ค่านั้นก่อน
     *
     * เช่น:
     * APP_BASE_URL=https://meeting.example.ac.th/
     */

    $envBaseUrl = trim((string) getenv('APP_BASE_URL'));

    if ($envBaseUrl !== '') {

        define(
            'BASE_URL',
            rtrim($envBaseUrl, '/') . '/'
        );

    } else {

        /*
         * ตรวจหา path อัตโนมัติจาก Document Root
         *
         * localhost:
         * C:/xampp/htdocs/Meeting_msu
        
         *
         * hosting:
         * /home/user/public_html
         * => /
         */

        $documentRoot = realpath(
            $_SERVER['DOCUMENT_ROOT'] ?? ''
        );

        $projectRoot = realpath(BASE_PATH);

        $baseUrl = '/';

        if ($documentRoot && $projectRoot) {

            // รองรับ Windows path
            $documentRoot = str_replace(
                '\\',
                '/',
                $documentRoot
            );

            $projectRoot = str_replace(
                '\\',
                '/',
                $projectRoot
            );


            if (str_starts_with(
                $projectRoot,
                $documentRoot
            )) {

                $relativePath = substr(
                    $projectRoot,
                    strlen($documentRoot)
                );

                $relativePath = trim(
                    $relativePath,
                    '/'
                );


                if ($relativePath !== '') {

                    $baseUrl =
                        '/' .
                        $relativePath .
                        '/';

                }
            }
        }


        define(
            'BASE_URL',
            $baseUrl
        );
    }
}


// ===============================
// 🌐 PUBLIC URL
// ===============================

if (!defined('PUBLIC_URL')) {
    define(
        'PUBLIC_URL',
        BASE_URL . 'public/'
    );
}


// ===============================
// 🎨 ASSET URL
// ===============================

if (!defined('ASSET_URL')) {
    define(
        'ASSET_URL',
        PUBLIC_URL . 'assets/'
    );
}


// ===============================
// 👤 SESSION
// ===============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ===============================
// 🕒 TIMEZONE
// ===============================

date_default_timezone_set(
    'Asia/Bangkok'
);


// ===============================
// ⚠️ ERROR REPORTING
// ===============================

error_reporting(E_ALL);

ini_set(
    'display_errors',
    getenv('APP_DEBUG') === '1'
        ? '1'
        : '0'
);


// ===============================
// ⚙️ CONFIG
// ===============================

require_once APP_PATH . '/config/config.php';


// ===============================
// 🔐 SECURITY HEADERS
// ===============================

header(
    'X-Frame-Options: SAMEORIGIN'
);

header(
    'X-Content-Type-Options: nosniff'
);

header(
    'X-XSS-Protection: 1; mode=block'
);