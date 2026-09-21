<?php
/* ============================================================
 * Admin Sidebar V11
 * เข้ากันกับระบบผู้ใช้งาน ตำแหน่ง คำเชิญ และรายงานประชุมล่าสุด
 * ============================================================ */

$adminSidebarUserId = (int) ($_SESSION['user_id'] ?? 0);
$adminSidebarPage = (string) ($current_page ?? 'dashboard');

$adminSidebarBaseUrl = rtrim((string) PUBLIC_URL, '/') . '/';

$adminSidebarH = static fn($value): string =>
htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$adminSidebarProfile = [
    'name' => (string) ($_SESSION['name'] ?? $_SESSION['fullname'] ?? 'ผู้ดูแลระบบ'),
    'email' => (string) ($_SESSION['email'] ?? ''),
    'picture' => (string) ($_SESSION['picture'] ?? $_SESSION['avatar'] ?? ''),
    'role_name' => (string) ($_SESSION['role_name'] ?? 'แอดมิน'),
    'position_name' => (string) ($_SESSION['position_name'] ?? ''),
    'department_name' => (string) ($_SESSION['department_name'] ?? ''),
];

$adminSidebarStats = [
    'pending_users' => 0,
    'active_meetings' => 0,
    'pending_invitations' => 0,
    'unread_notifications' => 0,
    'pending_agendas' => 0
];

$adminSidebarDb = null;

if (isset($db) && $db instanceof PDO) {
    $adminSidebarDb = $db;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    $adminSidebarDb = $pdo;
}

if ($adminSidebarDb instanceof PDO) {
    try {
        $profileStmt = $adminSidebarDb->prepare(
            "SELECT
                u.name,
                u.email,
                u.picture,
                COALESCE(r.role_name, 'แอดมิน') AS role_name,
                COALESCE(p.position_name, '') AS position_name,
                COALESCE(d.department_name, '') AS department_name
             FROM user u
             LEFT JOIN role r ON r.role_id = u.role_id
             LEFT JOIN positions p ON p.position_id = u.position_id
             LEFT JOIN departments d ON d.department_id = u.department_id
             WHERE u.user_id = ?
             LIMIT 1"
        );
        $profileStmt->execute([$adminSidebarUserId]);
        $profileData = $profileStmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($profileData)) {
            $adminSidebarProfile = array_merge($adminSidebarProfile, $profileData);
        }
    } catch (Throwable $e) {
        // ใช้ข้อมูลจาก Session ต่อ เพื่อไม่ให้ Sidebar ทำให้ทั้งหน้าหยุดทำงาน
    }

    try {
        $adminSidebarStats['pending_users'] = (int) $adminSidebarDb
            ->query("SELECT COUNT(*) FROM user WHERE status = 'pending'")
            ->fetchColumn();
    } catch (Throwable $e) {
        // ฐานข้อมูลบางชุดอาจยังไม่มีสถานะนี้
    }

    try {
        $adminSidebarStats['active_meetings'] = (int) $adminSidebarDb
            ->query(
                "SELECT COUNT(*)
                 FROM meeting
                 WHERE meeting_status IN ('upcoming', 'ongoing')"
            )
            ->fetchColumn();
    } catch (Throwable $e) {
        // ไม่แสดง Badge หากตารางยังไม่พร้อม
    }

    try {
        $adminSidebarStats['pending_invitations'] = (int) $adminSidebarDb
            ->query(
                "SELECT COUNT(*)
                 FROM meeting_attendance
                 WHERE rsvp_status = 'pending'"
            )
            ->fetchColumn();
    } catch (Throwable $e) {
        // ไม่แสดง Badge หากตารางยังไม่พร้อม
    }

    /* ===============================
   นับวาระรออนุมัติ
=============================== */
    try {
        $adminSidebarStats['pending_agendas'] = (int) $adminSidebarDb
            ->query(
                "SELECT COUNT(*)
             FROM agenda
             WHERE admin_status = 'pending'"
            )
            ->fetchColumn();
    } catch (Throwable $e) {
        // ไม่แสดง Badge หากตารางยังไม่พร้อม
    }

    try {
        $notificationStmt = $adminSidebarDb->prepare(
            "SELECT COUNT(*)
             FROM notifications
             WHERE user_id = ?
               AND is_read = 0"
        );
        $notificationStmt->execute([$adminSidebarUserId]);
        $adminSidebarStats['unread_notifications'] = (int) $notificationStmt->fetchColumn();
    } catch (Throwable $e) {
        // ไม่แสดง Badge หากตารางยังไม่พร้อม
    }
}

$adminSidebarName = trim((string) $adminSidebarProfile['name']) ?: 'ผู้ดูแลระบบ';
$adminSidebarEmail = trim((string) $adminSidebarProfile['email']);
$adminSidebarRole = trim((string) $adminSidebarProfile['role_name']) ?: 'แอดมิน';
$adminSidebarPosition = trim((string) $adminSidebarProfile['position_name']);
$adminSidebarDepartment = trim((string) $adminSidebarProfile['department_name']);

$adminSidebarMeta = implode(
    ' · ',
    array_values(
        array_filter(
            [$adminSidebarPosition, $adminSidebarDepartment],
            static fn($value): bool => trim((string) $value) !== ''
        )
    )
);

if ($adminSidebarMeta === '') {
    $adminSidebarMeta = $adminSidebarEmail !== ''
        ? $adminSidebarEmail
        : 'ผู้ดูแลระบบงานประชุม';
}

$adminSidebarInitial = function_exists('mb_substr')
    ? mb_substr($adminSidebarName, 0, 1, 'UTF-8')
    : substr($adminSidebarName, 0, 1);

$adminSidebarInitialXml = htmlspecialchars(
    $adminSidebarInitial ?: 'A',
    ENT_QUOTES | ENT_XML1,
    'UTF-8'
);

$adminSidebarFallbackSvg =
    '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120">'
    . '<rect width="120" height="120" rx="60" fill="#1d4ed8"/>'
    . '<text x="60" y="67" text-anchor="middle" '
    . 'font-family="Tahoma,Arial,sans-serif" font-size="52" font-weight="700" fill="#ffffff">'
    . $adminSidebarInitialXml
    . '</text></svg>';

$adminSidebarFallbackAvatar =
    'data:image/svg+xml;charset=UTF-8,' . rawurlencode($adminSidebarFallbackSvg);

$adminSidebarPicture = trim((string) ($adminSidebarProfile['picture'] ?? ''));
$adminSidebarAvatar = '';

/*
 * PUBLIC_URL ชี้มายังโฟลเดอร์ public/ ของระบบ
 * ดังนั้นรูป local ต้องต่อเพียง uploads/avatars/ เท่านั้น
 * ห้ามต่อ public/uploads/avatars/ ซ้ำ
 */
if ($adminSidebarPicture !== '') {
    if (filter_var($adminSidebarPicture, FILTER_VALIDATE_URL)) {
        $scheme = strtolower((string) parse_url($adminSidebarPicture, PHP_URL_SCHEME));
        if (in_array($scheme, ['http', 'https'], true)) {
            $adminSidebarAvatar = $adminSidebarPicture;
        }
    } else {
        $normalizedPicture = str_replace('\\', '/', $adminSidebarPicture);
        $pictureFile = basename($normalizedPicture);

        if ($pictureFile !== '' && $pictureFile !== '.' && $pictureFile !== '..') {
            $adminSidebarAvatar =
                $adminSidebarBaseUrl
                . 'uploads/avatars/'
                . rawurlencode($pictureFile)
                . '?v=' . rawurlencode((string) @filemtime(PUBLIC_PATH . '/uploads/avatars/' . $pictureFile));
        }
    }
}

if ($adminSidebarAvatar === '') {
    $adminSidebarAvatar = $adminSidebarFallbackAvatar;
}

$adminSidebarSystemPages = [
    'users',
    'meetings',
    'department',
    'meeting_reports',
    'agendas',
];

$adminSidebarSystemActive = in_array(
    $adminSidebarPage,
    $adminSidebarSystemPages,
    true
);

$adminSidebarIsActive = static function (string $page) use ($adminSidebarPage): string {
    return $adminSidebarPage === $page ? ' active' : '';
};

$adminSidebarBadge = static function (int $value): string {
    return $value > 99 ? '99+' : (string) $value;
};

$adminSidebarOwnProfileUrl =
    $adminSidebarBaseUrl
    . 'admin/users/edit_users.php?search='
    . rawurlencode($adminSidebarEmail !== '' ? $adminSidebarEmail : $adminSidebarName);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600&family=Roboto:wght@400;500&family=Sarabun:wght@400;500;600&display=swap');

:root {
    --gcal-blue: #1a73e8;
    --gcal-blue-hover: #1765cc;
    --gcal-blue-active: #d3e3fd;

    --gcal-text: #202124;
    --gcal-text-secondary: #5f6368;
    --gcal-icon: #5f6368;

    --gcal-bg: #ffffff;
    --gcal-hover: #f1f3f4;
    --gcal-border: #dadce0;

    --gcal-red: #d93025;
    --gcal-red-hover: #fce8e6;
}

.sidebar.admin-sidebar,
.sidebar.admin-sidebar * {
    box-sizing: border-box;
    font-family: 'Google Sans', 'Sarabun', 'Roboto', Arial, sans-serif;
}

/* =========================================================
   SIDEBAR
   ========================================================= */
.sidebar.admin-sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 900;

    width: 268px;
    height: 100vh;

    display: flex;
    flex-direction: column;

    overflow: hidden;

    background: var(--gcal-bg) !important;
    border-right: 1px solid var(--gcal-border) !important;
    box-shadow: none !important;

    color: var(--gcal-text);

    transition:
        width .2s cubic-bezier(.4, 0, .2, 1),
        transform .2s cubic-bezier(.4, 0, .2, 1);
}

.sidebar.admin-sidebar.collapsed {
    width: 74px;
}

/* =========================================================
   BRAND HEADER
   ========================================================= */
.admin-sidebar .sidebar-header {
    height: 64px;
    min-height: 64px;
    padding: 0 16px;

    display: flex;
    align-items: center;
    gap: 12px;

    flex: 0 0 auto;

    background: #ffffff;
    border-bottom: 1px solid var(--gcal-border);
}

.admin-sidebar-logo {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;

    display: grid;
    place-items: center;

    border: 0;
    border-radius: 8px;

    background: transparent;
    color: var(--gcal-blue);
}

.admin-sidebar-logo svg {
    width: 24px;
    height: 24px;
    stroke-width: 1.9;
}

.admin-sidebar-brand {
    min-width: 0;
    line-height: 1.25;
    white-space: nowrap;
}

.admin-sidebar-brand strong,
.admin-sidebar-brand span {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-sidebar-brand strong {
    color: var(--gcal-text);
    font-size: 16px;
    font-weight: 500;
    letter-spacing: 0;
}

.admin-sidebar-brand span {
    margin-top: 2px;
    color: var(--gcal-text-secondary);
    font-size: 11px;
    font-weight: 400;
}

.admin-sidebar-mobile-close {
    width: 36px;
    height: 36px;
    margin-left: auto;

    display: none;
    place-items: center;

    flex: 0 0 auto;

    border: 0;
    border-radius: 50%;

    background: transparent;
    color: var(--gcal-icon);

    cursor: pointer;
    box-shadow: none !important;
}

.admin-sidebar-mobile-close:hover {
    background: var(--gcal-hover);
}

/* =========================================================
   PROFILE
   ========================================================= */
.admin-profile-card {
    width: auto !important;
    min-height: 58px;

    margin: 8px 8px 4px;
    padding: 7px 12px;

    display: flex;
    align-items: center;
    gap: 10px;

    overflow: hidden;

    border: 0 !important;
    border-radius: 28px;

    background: transparent !important;
    color: inherit;

    text-align: left;
    text-decoration: none;

    cursor: pointer;
    box-shadow: none !important;

    transition: background-color .15s ease;
}

.admin-profile-card:hover {
    background: var(--gcal-hover) !important;
}

.admin-avatar-wrap {
    position: relative;
    flex: 0 0 auto;
}

.admin-avatar {
    width: 36px;
    height: 36px;

    display: block;
    object-fit: cover;

    border: 0;
    border-radius: 50%;

    background: var(--gcal-hover);
}

.admin-notification-dot {
    position: absolute;
    top: -2px;
    right: -3px;

    min-width: 15px;
    height: 15px;
    padding: 0 4px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: 2px solid #fff;
    border-radius: 999px;

    background: var(--gcal-red);
    color: #fff;

    font-size: 8px;
    font-weight: 600;
    line-height: 1;
}

.admin-profile-copy {
    min-width: 0;
    flex: 1;
}

.admin-profile-name,
.admin-profile-meta {
    display: block;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.admin-profile-name {
    color: var(--gcal-text);
    font-size: 13px;
    font-weight: 500;
}

.admin-profile-meta {
    margin-top: 1px;
    color: var(--gcal-text-secondary);
    font-size: 11px;
    font-weight: 400;
}

.admin-role-chip {
    display: block;
    max-width: 100%;

    margin-top: 1px;
    padding: 0;

    overflow: hidden;

    background: transparent;
    color: var(--gcal-text-secondary);

    border: 0;
    border-radius: 0;

    font-size: 10px;
    font-weight: 400;

    white-space: nowrap;
    text-overflow: ellipsis;
}

/* =========================================================
   MENU
   ========================================================= */
.admin-sidebar .sidebar-menu {
    min-height: 0;
    flex: 1;

    padding: 6px 0 10px;

    overflow-x: hidden;
    overflow-y: auto;

    scrollbar-width: thin;
    scrollbar-color: #c7c9cc transparent;
}

.admin-sidebar .sidebar-menu::-webkit-scrollbar {
    width: 6px;
}

.admin-sidebar .sidebar-menu::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: #c7c9cc;
}

.admin-menu-category {
    padding: 14px 24px 5px;

    color: var(--gcal-text-secondary);

    font-size: 11px;
    font-weight: 500;
    letter-spacing: .02em;

    white-space: nowrap;
}

.admin-sidebar .menu-item,
.admin-sidebar .submenu-item {
    position: relative;

    width: calc(100% - 12px);
    min-height: 44px;

    margin: 1px 12px 1px 0;
    padding: 0 18px 0 24px;

    display: flex;
    align-items: center;
    gap: 14px;

    border: 0;
    border-radius: 0 22px 22px 0 !important;

    background: transparent;
    color: var(--gcal-text-secondary) !important;

    font-size: 14px;
    font-weight: 400;
    line-height: 1.25;

    text-align: left;
    text-decoration: none;
    white-space: nowrap;

    cursor: pointer;
    box-shadow: none !important;

    transition:
        background-color .15s ease,
        color .15s ease;
}

.admin-sidebar .submenu-item {
    min-height: 40px;
    padding-left: 20px;
    font-size: 13px;
}

.admin-sidebar .menu-item:hover,
.admin-sidebar .submenu-item:hover {
    background: var(--gcal-hover) !important;
    color: var(--gcal-text) !important;
}

.admin-sidebar .menu-item.active,
.admin-sidebar .submenu-item.active {
    background: var(--gcal-blue-active) !important;
    color: #041e49 !important;
    font-weight: 500 !important;
}

.admin-sidebar .menu-item.active::before,
.admin-sidebar .submenu-item.active::before {
    display: none !important;
}

.admin-sidebar .menu-item > svg,
.admin-sidebar .menu-item > i,
.admin-sidebar .submenu-item > svg,
.admin-sidebar .submenu-item > i {
    width: 20px;
    height: 20px;
    flex: 0 0 20px;

    color: var(--gcal-icon);
    stroke-width: 1.9;
}

.admin-sidebar .menu-item.active > svg,
.admin-sidebar .menu-item.active > i,
.admin-sidebar .submenu-item.active > svg,
.admin-sidebar .submenu-item.active > i {
    color: #041e49 !important;
}

.admin-menu-text {
    min-width: 0;
    flex: 1;

    overflow: hidden;
    text-overflow: ellipsis;
}

/* =========================================================
   BADGES
   ========================================================= */
.admin-menu-badge {
    min-width: 20px;
    height: 20px;
    padding: 0 6px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 auto;

    border-radius: 10px;

    background: var(--gcal-hover);
    color: var(--gcal-text-secondary);

    font-size: 10px;
    font-weight: 500;
    line-height: 1;
}

.admin-menu-badge.info {
    background: #e8f0fe;
    color: #174ea6;
}

.admin-menu-badge.danger {
    background: #fce8e6;
    color: var(--gcal-red);
}

/* =========================================================
   DROPDOWN
   ========================================================= */
.admin-dropdown {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.admin-dropdown-button {
    justify-content: space-between;
}

.admin-dropdown-button .admin-button-content {
    min-width: 0;
    flex: 1;

    display: flex;
    align-items: center;
    gap: 14px;
}

.admin-dropdown-arrow {
    width: 18px !important;
    height: 18px !important;
    flex: 0 0 18px;

    color: var(--gcal-icon);

    transition: transform .18s ease;
}

.admin-dropdown.open .admin-dropdown-arrow {
    transform: rotate(180deg);
}

.admin-submenu {
    max-height: 0;

    margin: 0;
    padding: 0;

    display: flex;
    flex-direction: column;

    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;

    transform: translateY(-2px);

    transition:
        max-height .2s cubic-bezier(.4, 0, .2, 1),
        opacity .15s ease,
        transform .15s ease;
}

.admin-dropdown.open .admin-submenu {
    max-height: 280px;

    margin: 2px 0 4px 28px;

    opacity: 1;
    visibility: visible;
    pointer-events: auto;

    transform: translateY(0);
}

.admin-dropdown.open .admin-submenu .submenu-item {
    width: calc(100% - 12px);
    margin-right: 12px;
}

/* =========================================================
   SYSTEM SUMMARY
   ========================================================= */
.admin-system-summary {
    display: none !important;
}

/* =========================================================
   FOOTER / LOGOUT
   ========================================================= */
.admin-sidebar-footer {
    padding: 6px 0 10px;

    flex: 0 0 auto;

    background: #fff;
    border-top: 1px solid var(--gcal-border);
}

.admin-sidebar .logout-item {
    color: var(--gcal-red) !important;
}

.admin-sidebar .logout-item > svg,
.admin-sidebar .logout-item > i {
    color: var(--gcal-red) !important;
}

.admin-sidebar .logout-item:hover {
    background: var(--gcal-red-hover) !important;
    color: var(--gcal-red) !important;
}

/* =========================================================
   COLLAPSED - DESKTOP
   ========================================================= */
.admin-sidebar.collapsed .admin-sidebar-brand,
.admin-sidebar.collapsed .admin-profile-copy,
.admin-sidebar.collapsed .admin-menu-category,
.admin-sidebar.collapsed .admin-menu-text,
.admin-sidebar.collapsed .admin-menu-badge,
.admin-sidebar.collapsed .admin-dropdown-arrow,
.admin-sidebar.collapsed .admin-submenu,
.admin-sidebar.collapsed .admin-system-summary {
    display: none !important;
}

.admin-sidebar.collapsed .sidebar-header {
    padding: 0;
    justify-content: center;
}

.admin-sidebar.collapsed .admin-sidebar-logo {
    width: 40px;
    height: 40px;
    flex-basis: 40px;
}

.admin-sidebar.collapsed .admin-profile-card {
    width: 44px !important;
    height: 44px;
    min-height: 44px;

    margin: 8px auto 4px;
    padding: 4px;

    justify-content: center;

    border-radius: 50%;
}

.admin-sidebar.collapsed .admin-avatar {
    width: 36px;
    height: 36px;
}

.admin-sidebar.collapsed .menu-item {
    width: 44px;
    min-height: 44px;
    height: 44px;

    margin: 2px auto;
    padding: 0;

    justify-content: center;

    border-radius: 50% !important;
}

.admin-sidebar.collapsed .menu-item > svg,
.admin-sidebar.collapsed .menu-item > i {
    margin: 0;
}

/* =========================================================
   OVERLAY
   ========================================================= */
.admin-sidebar-overlay {
    position: fixed;
    inset: 0;
    z-index: 999;

    display: none;

    background: rgba(32, 33, 36, .32);
}

/* =========================================================
   MAIN CONTENT COMPATIBILITY
   ========================================================= */
body:has(.sidebar.admin-sidebar:not(.collapsed)) .main-content {
    margin-left: 268px;
    width: calc(100% - 268px);
}

body:has(.sidebar.admin-sidebar.collapsed) .main-content {
    margin-left: 74px;
    width: calc(100% - 74px);
}

/* =========================================================
   ACCESSIBILITY
   ========================================================= */
.admin-sidebar .menu-item:focus-visible,
.admin-sidebar .submenu-item:focus-visible,
.admin-sidebar-mobile-close:focus-visible,
.admin-profile-card:focus-visible {
    outline: 2px solid var(--gcal-blue);
    outline-offset: -2px;
}

/* =========================================================
   MOBILE DRAWER
   ========================================================= */
@media (max-width: 768px) {

    body:has(.sidebar.admin-sidebar) .main-content,
    body:has(.sidebar.admin-sidebar.collapsed) .main-content,
    body:has(.sidebar.admin-sidebar:not(.collapsed)) .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }

    .sidebar.admin-sidebar {
        width: min(88vw, 288px);

        z-index: 1000;

        border-right: 0 !important;

        transform: translateX(-105%);

        box-shadow:
            0 8px 10px rgba(60, 64, 67, .14),
            0 3px 14px rgba(60, 64, 67, .12) !important;
    }

    .sidebar.admin-sidebar.collapsed {
        width: min(88vw, 288px);
        transform: translateX(0);
    }

    .admin-sidebar-mobile-close {
        display: grid;
    }

    .admin-sidebar.collapsed .sidebar-header {
        padding: 0 12px 0 16px;
        justify-content: flex-start;
    }

    .admin-sidebar.collapsed .admin-sidebar-brand,
    .admin-sidebar.collapsed .admin-profile-copy,
    .admin-sidebar.collapsed .admin-menu-category,
    .admin-sidebar.collapsed .admin-menu-text {
        display: block !important;
    }

    .admin-sidebar.collapsed .admin-dropdown-arrow,
    .admin-sidebar.collapsed .admin-menu-badge {
        display: inline-flex !important;
    }

    .admin-sidebar.collapsed .admin-profile-card {
        width: auto !important;
        height: auto;
        min-height: 58px;

        margin: 8px 8px 4px;
        padding: 7px 12px;

        justify-content: flex-start;

        border-radius: 28px;
    }

    .admin-sidebar.collapsed .menu-item {
        width: calc(100% - 12px);
        min-height: 44px;
        height: auto;

        margin: 1px 12px 1px 0;
        padding: 0 18px 0 24px;

        justify-content: flex-start;

        border-radius: 0 22px 22px 0 !important;
    }

    .admin-sidebar.collapsed .admin-submenu {
        display: flex !important;
    }

    .admin-sidebar-overlay.visible {
        display: block;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sidebar.admin-sidebar,
    .admin-sidebar .menu-item,
    .admin-sidebar .submenu-item,
    .admin-submenu {
        transition: none;
    }
}
</style>

<aside class="sidebar admin-sidebar" id="sidebar" aria-label="เมนูผู้ดูแลระบบ">
    <div class="sidebar-header">
        <span class="admin-sidebar-logo" aria-hidden="true">
            <i data-lucide="shield-check"></i>
        </span>

        <span class="admin-sidebar-brand">
            <strong>ระบบงานประชุมคณะ</strong>
            <span>ศูนย์ควบคุมผู้ดูแลระบบ</span>
        </span>

        <button type="button" class="admin-sidebar-mobile-close" id="adminSidebarClose" aria-label="ปิดเมนู"
            title="ปิดเมนู">
            <i data-lucide="x"></i>
        </button>
    </div>

    <button type="button" class="admin-profile-card"
        onclick="if (typeof openProfileModal === 'function') { openProfileModal(); }" title="เปิดข้อมูลโปรไฟล์"
        aria-label="เปิดโปรไฟล์ของ <?= $adminSidebarH($adminSidebarName) ?>"
        style="width:auto;text-align:left;cursor:pointer;">
        <span class="admin-avatar-wrap">
            <img src="<?= $adminSidebarH($adminSidebarAvatar) ?>"
                alt="รูปโปรไฟล์ของ <?= $adminSidebarH($adminSidebarName) ?>" class="admin-avatar"
                referrerpolicy="no-referrer"
                onerror="this.onerror=null;this.src='<?= $adminSidebarH($adminSidebarFallbackAvatar) ?>';">
        </span>

        <span class="admin-profile-copy">
            <span class="admin-profile-name" title="<?= $adminSidebarH($adminSidebarName) ?>">
                <?= $adminSidebarH($adminSidebarName) ?>
            </span>
            <span class="admin-profile-meta" title="<?= $adminSidebarH($adminSidebarMeta) ?>">
                <?= $adminSidebarH($adminSidebarMeta) ?>
            </span>
            <span class="admin-role-chip">
                <?= $adminSidebarH($adminSidebarRole) ?>
            </span>
        </span>
    </button>

    <nav class="sidebar-menu" aria-label="เมนูหลัก">
        <div class="admin-menu-category">ภาพรวม</div>

        <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'admin/index.php') ?>"
            class="menu-item<?= $adminSidebarIsActive('dashboard') ?>"
            <?= $adminSidebarPage === 'dashboard' ? 'aria-current="page"' : '' ?> title="หน้าแรกผู้ดูแลระบบ">
            <i data-lucide="layout-dashboard"></i>
            <span class="admin-menu-text">แดชบอร์ด</span>
        </a>

        <div class="admin-menu-category">จัดการระบบ</div>

        <div class="admin-dropdown<?= $adminSidebarSystemActive ? ' open' : '' ?>">
            <button type="button" class="menu-item admin-dropdown-button" data-admin-dropdown-button
                aria-expanded="<?= $adminSidebarSystemActive ? 'true' : 'false' ?>">
                <span class="admin-button-content">
                    <i data-lucide="settings-2"></i>
                    <span class="admin-menu-text">จัดการระบบ</span>
                </span>

                <i data-lucide="chevron-down" class="admin-dropdown-arrow"></i>
            </button>

            <div class="admin-submenu">
                <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'admin/users/edit_users.php') ?>"
                    class="submenu-item<?= $adminSidebarIsActive('users') ?>"
                    <?= $adminSidebarPage === 'users' ? 'aria-current="page"' : '' ?>
                    title="จัดการผู้ใช้งาน">
                    <i data-lucide="users-round"></i>
                    <span class="admin-menu-text">จัดการผู้ใช้งาน</span>

                    <?php if ($adminSidebarStats['pending_users'] > 0): ?>
                    <span class="admin-menu-badge"
                        title="มีผู้ใช้รออนุมัติ <?= (int) $adminSidebarStats['pending_users'] ?> รายการ">
                        <?= $adminSidebarH($adminSidebarBadge($adminSidebarStats['pending_users'])) ?>
                    </span>
                    <?php endif; ?>
                </a>


                 <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'admin/department/edit_department.php') ?>"
                    class="submenu-item<?= $adminSidebarIsActive('department') ?>"
                    <?= $adminSidebarPage === 'department' ? 'aria-current="page"' : '' ?>
                    title="จัดการภาควิชา">
                    <i data-lucide="building"></i>
                    <span class="admin-menu-text">จัดการภาควิชา</span>
                </a>

                    <?php if ($adminSidebarStats['pending_users'] > 0): ?>
                    <span class="admin-menu-badge"
                        title="มีผู้ใช้รออนุมัติ <?= (int) $adminSidebarStats['pending_users'] ?> รายการ">
                        <?= $adminSidebarH($adminSidebarBadge($adminSidebarStats['pending_users'])) ?>
                    </span>
                    <?php endif; ?>
                </a>

                <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'admin/meetings/edit_meetings.php') ?>"
                    class="submenu-item<?= $adminSidebarIsActive('meetings') ?>"
                    <?= $adminSidebarPage === 'meetings' ? 'aria-current="page"' : '' ?>
                    title="สร้าง แก้ไข เชิญสมาชิก และจัดทำรายงานประชุม">
                    <i data-lucide="calendar-cog"></i>
                    <span class="admin-menu-text">จัดการการประชุม</span>

                
                </a>

                <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'admin/agenda/agendas.php') ?>"
                    class="submenu-item<?= $adminSidebarIsActive('agendas') ?>"
                    <?= $adminSidebarPage === 'agendas' ? 'aria-current="page"' : '' ?>
                    title="จัดการวาระการประชุมที่เสนอเข้ามา">
                    <i data-lucide="file-text"></i>
                    <span class="admin-menu-text">จัดการวาระ</span>

                    <?php if ($adminSidebarStats['pending_agendas'] > 0): ?>
                    <span class="admin-menu-badge"
                        title="มีวาระรอตรวจสอบ <?= (int) $adminSidebarStats['pending_agendas'] ?> รายการ">
                        <?= $adminSidebarH($adminSidebarBadge($adminSidebarStats['pending_agendas'])) ?>
                    </span>
                    <?php endif; ?>

                </a>
            </div>
        </div>

        <div class="admin-system-summary">
            <div class="admin-system-summary-title">
                <i data-lucide="activity"></i>
                <span>สถานะระบบประชุม</span>
            </div>

            <div class="admin-summary-row">
                <span>ผู้ใช้รออนุมัติ</span>
                <strong><?= (int) $adminSidebarStats['pending_users'] ?></strong>
            </div>

            <div class="admin-summary-row">
                <span>ประชุมที่ยังไม่ปิด</span>
                <strong><?= (int) $adminSidebarStats['active_meetings'] ?></strong>
            </div>

            <div class="admin-summary-row">
                <span>คำเชิญรอตอบรับ</span>
                <strong><?= (int) $adminSidebarStats['pending_invitations'] ?></strong>
            </div>
        </div>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="<?= $adminSidebarH($adminSidebarBaseUrl . 'auth/logout.php') ?>" class="menu-item logout-item"
            onclick="return handleLogout(event)" title="ออกจากระบบ">
            <i data-lucide="log-out"></i>
            <span class="admin-menu-text">ออกจากระบบ</span>
        </a>
    </div>
</aside>

<div class="admin-sidebar-overlay" id="adminSidebarOverlay" aria-hidden="true"></div>

<script>
(function() {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    const closeButton = document.getElementById('adminSidebarClose');
    const dropdownButtons = document.querySelectorAll('[data-admin-dropdown-button]');

    if (!sidebar || !overlay) {
        return;
    }

    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    function syncOverlay() {
        const visible = isMobile() && sidebar.classList.contains('collapsed');

        overlay.classList.toggle('visible', visible);
        overlay.setAttribute('aria-hidden', visible ? 'false' : 'true');
        document.body.style.overflow = visible ? 'hidden' : '';
    }

    function closeMobileSidebar() {
        if (!isMobile()) {
            return;
        }

        sidebar.classList.remove('collapsed');

        document.getElementById('main-content')?.classList.remove('expanded');
        document.getElementById('mainContent')?.classList.remove('expanded');

        syncOverlay();
    }

    function toggleDropdown(button) {
        const dropdown = button.closest('.admin-dropdown');

        if (!dropdown) {
            return;
        }

        const isOpen = dropdown.classList.toggle('open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    dropdownButtons.forEach((button) => {
        button.addEventListener('click', () => toggleDropdown(button));
    });

    /*
     * คงชื่อฟังก์ชันเดิมไว้ เผื่อหน้าเก่ายังเรียกใช้งานผ่าน onclick
     */
    window.toggleSidebarDropdown = function(button, event) {
        event?.preventDefault();

        const target = button?.matches?.('[data-admin-dropdown-button]') ?
            button :
            button?.closest?.('[data-admin-dropdown-button]');

        if (target) {
            toggleDropdown(target);
        }
    };

    overlay.addEventListener('click', closeMobileSidebar);
    closeButton?.addEventListener('click', closeMobileSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    document.querySelectorAll('.admin-sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobile()) {
                setTimeout(closeMobileSidebar, 50);
            }
        });
    });

    const observer = new MutationObserver(syncOverlay);
    observer.observe(sidebar, {
        attributes: true,
        attributeFilter: ['class']
    });

    window.addEventListener('resize', syncOverlay);

    syncOverlay();

    if (window.lucide) {
        window.lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
        syncOverlay();

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
})();
</script>