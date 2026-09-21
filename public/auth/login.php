<?php

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';

header('Cross-Origin-Opener-Policy: same-origin-allow-popups');

/*
|--------------------------------------------------------------------------
| Database / Authentication
|--------------------------------------------------------------------------
*/
$db = (new Database())->connect();

$userModel = new User($db);
$auth = new AuthController($userModel);

$page_title = 'เข้าสู่ระบบ';
$page_js    = 'sweetalert2.all.min.js';

/*
|--------------------------------------------------------------------------
| Redirect URL ตาม Role
|--------------------------------------------------------------------------
*/
function getRedirectUrlByRole(): string
{
    $roleId = (int) ($_SESSION['role_id'] ?? 0);

    switch ($roleId) {
        case 1:
            // Admin
            return '../admin/index.php';

        case 3:
            // Executive
            return '../executives/index.php';

        case 4:
            // Department
            return '../departments/index.php';

        case 2:
            // User
            return '../users/index.php';

        default:
            return './login.php';
    }
}

/*
|--------------------------------------------------------------------------
| Redirect ตาม Role
|--------------------------------------------------------------------------
*/
function redirectByRole(): void
{
    $roleId = (int) ($_SESSION['role_id'] ?? 0);

    switch ($roleId) {
        case 1:
            header('Location: ../admin/index.php');
            break;

        case 3:
            header('Location: ../executives/index.php');
            break;

        case 4:
            header('Location: ../departments/index.php');
            break;

        case 2:
            header('Location: ../users/index.php');
            break;

        default:
            session_unset();
            session_destroy();
            header('Location: ./login.php');
            break;
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| ถ้า Login อยู่แล้ว → Redirect ตาม Role
|--------------------------------------------------------------------------
*/
if (!empty($_SESSION['user_id'])) {
    redirectByRole();
}

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/
$alertScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {

        $loginMessage = 'กรุณากรอกชื่อผู้ใช้งานและรหัสผ่าน';
        $alertMessage = json_encode(
            $loginMessage,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $alertScript = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: {$alertMessage},
            confirmButtonColor: '#1a73e8',
            confirmButtonText: 'ตกลง'
        });
    }

    if (window.history.replaceState) {
        window.history.replaceState(null, document.title, window.location.href);
    }
});
</script>
HTML;

    } else {

        $loginResult = $auth->login($username, $password);

        if (($loginResult['status'] ?? 'error') === 'success') {

            $redirectUrl = getRedirectUrlByRole();
            $loginMessage = $loginResult['message'] ?? 'เข้าสู่ระบบสำเร็จ';

            $alertMessage = json_encode(
                $loginMessage,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            $redirectUrlJs = json_encode(
                $redirectUrl,
                JSON_UNESCAPED_SLASHES
            );

            $alertScript = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'เข้าสู่ระบบสำเร็จ',
            text: {$alertMessage},
            confirmButtonColor: '#1a73e8',
            confirmButtonText: 'เข้าสู่ระบบ',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(function () {
            window.location.href = {$redirectUrlJs};
        });
    } else {
        window.location.href = {$redirectUrlJs};
    }
});
</script>
HTML;

        } else {

            $loginMessage = $loginResult['message'] ?? 'ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง';
            $alertMessage = json_encode(
                $loginMessage,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            $alertScript = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบไม่สำเร็จ',
            text: {$alertMessage},
            confirmButtonColor: '#1a73e8',
            confirmButtonText: 'ตกลง'
        });
    }

    if (window.history.replaceState) {
        window.history.replaceState(null, document.title, window.location.href);
    }
});
</script>
HTML;

        }
    }
}

/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/
include_once __DIR__ . '/../../app/views/layouts/header.php';

/*
|--------------------------------------------------------------------------
| Welcome Popup Component
|--------------------------------------------------------------------------
*/
include_once __DIR__ . '/../../app/views/components/alert_login.php';

/*
|--------------------------------------------------------------------------
| แสดง SweetAlert2
|--------------------------------------------------------------------------
*/
echo $alertScript;

?>

<!-- =========================================================
     LOGIN (MATERIAL DESIGN 3)
========================================================= -->
<div class="login-wrapper">

    <div class="login-card">

        <!-- Logo -->
        <div class="logo">
            <img
    src="<?= htmlspecialchars(ASSET_URL . 'image/logo.svg', ENT_QUOTES, 'UTF-8') ?>"
    width="70"
    height="70"
    alt="โลโก้ระบบงานประชุม"
>
        </div>

        <!-- Title -->
        <h1>ระบบงานประชุม</h1>

        <p class="subtitle">
            กรุณาเข้าสู่ระบบด้วยบัญชีของคุณ
        </p>

        <!-- Login Form -->
        <form
            method="post"
            action=""
            autocomplete="on"
            id="loginForm"
        >

            <!-- Username -->
            <div class="form-group">
                <label for="username">
                    ชื่อผู้ใช้งาน
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="inputu"
                    placeholder="กรอกชื่อผู้ใช้งาน"
                    autocomplete="username"
                    maxlength="100"
                    required
                    autofocus
                >
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">
                    รหัสผ่าน
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="inputp"
                    placeholder="กรอกรหัสผ่าน"
                    autocomplete="current-password"
                    maxlength="255"
                    required
                >
            </div>

            <!-- Forgot Password -->
            <div class="forgot-box">
                <a
                    href="#"
                    class="small-link"
                    onclick="forgotPassword(); return false;"
                >
                    ลืมรหัสผ่าน?
                </a>
            </div>

            <!-- Login Button -->
            <button
                class="buttonlogin"
                type="submit"
                id="loginBtn"
            >
                <div class="spinner"></div>
                <span class="btn-text">
                    เข้าสู่ระบบ
                </span>
            </button>

            <!-- Divider -->
            <div class="divider">
                <span>หรือ</span>
            </div>

            <!-- =====================================================
                 GOOGLE LOGIN (MATERIAL DESIGN 3 STYLED)
            ====================================================== -->
            <div
                id="g_id_onload"
                data-client_id="<?= htmlspecialchars(GOOGLE_CLIENT_ID, ENT_QUOTES, 'UTF-8') ?>"
                data-callback="handleCredentialResponse"
                data-auto_prompt="false"
                data-use_fedcm_for_prompt="false"
                data-use_fedcm_for_button="false"
            >
            </div>

            <div class="google-wrap">
                <div
                    class="g_id_signin"
                    data-type="standard"
                    data-size="large"
                    data-theme="outline"
                    data-text="continue_with"
                    data-shape="pill"
                    data-logo_alignment="left"
                    data-width="370"
                >
                </div>
            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
            // เพิ่มสถานะกำลังโหลดป้องกันการกดซ้ำ
            loginBtn.classList.add('loading');
        });
    }
});
</script>

<?php

/*
|--------------------------------------------------------------------------
| Footer
|--------------------------------------------------------------------------
*/
include_once __DIR__ . '/../../app/views/layouts/footer.php';

?>