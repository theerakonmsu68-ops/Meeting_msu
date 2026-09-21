<?php

// ==========================================
// 🔧 BOOTSTRAP / PATH CONSTANTS
// footer.php อยู่ที่ app/views/layouts/
// ==========================================
if (
    !defined('BASE_PATH') ||
    !defined('PUBLIC_PATH') ||
    !defined('PUBLIC_URL') ||
    !defined('ASSET_URL')
) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

?>

<script src="https://unpkg.com/lucide@latest"></script>

<script>
// ==========================================================================
// 🌐 URL ฝั่ง public สำหรับ JavaScript
// รองรับทั้ง localhost และ Hosting ตามค่า PUBLIC_URL
// ==========================================================================
const PUBLIC_URL = <?= json_encode(
    PUBLIC_URL,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) ?>;
const meetingCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const nativeFetch = window.fetch.bind(window);
window.fetch = function (input, init = {}) {
    const method = (init.method || 'GET').toUpperCase();
    if (method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS' && meetingCsrfToken) {
        const headers = new Headers(init.headers || {});
        if (!headers.has('X-CSRF-Token')) {
            headers.set('X-CSRF-Token', meetingCsrfToken);
        }
        init.headers = headers;
    }
    return nativeFetch(input, init);
};

// ==========================================
// 🔒 GOOGLE LOGIN HANDLER
// ==========================================
function handleCredentialResponse(response) {
    fetch(`${PUBLIC_URL}auth/google_login.php`, {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                credential: response.credential
            })

        })

        .then(res => res.json())

        .then(result => {

            /*
            |--------------------------------------------------------------------------
            | LOGIN GOOGLE สำเร็จ
            |--------------------------------------------------------------------------
            */
            if (result.status === "success") {

                let redirectUrl;

                /*
                 * Redirect ตาม Role
                 */
                if (result.role_id == 1) {

                    redirectUrl = `${PUBLIC_URL}admin/index.php`;

                } else if (result.role_id == 3) {

                    redirectUrl = `${PUBLIC_URL}executives/index.php`;

                } else if (result.role_id == 4) {

                    redirectUrl = `${PUBLIC_URL}departments/index.php`;

                } else {

                    redirectUrl = `${PUBLIC_URL}users/index.php`;

                }


                /*
                 |--------------------------------------------------------------------------
                 | SweetAlert2
                 |--------------------------------------------------------------------------
                 */
                if (typeof Swal !== 'undefined') {

                    Swal.fire({

                        icon: 'success',

                        title: 'เข้าสู่ระบบสำเร็จ',

                        text: result.message || 'ยินดีต้อนรับเข้าสู่ระบบ',

                        confirmButtonColor: '#0284c7',

                        confirmButtonText: 'เข้าสู่ระบบ',

                        allowOutsideClick: false,

                        allowEscapeKey: false,

                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },

                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }

                    }).then(function() {

                        /*
                         * กดปุ่มแล้วค่อย Redirect
                         */
                        window.location.href = redirectUrl;

                    });

                } else {

                    /*
                     * กรณี SweetAlert2 โหลดไม่ทัน
                     */
                    window.location.href = redirectUrl;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | ACCOUNT PENDING / BLOCKED
            |--------------------------------------------------------------------------
            */
            else if (
                result.status === "pending" ||
                result.status === "blocked"
            ) {

                if (typeof Swal !== 'undefined') {

                    Swal.fire({

                        icon: result.status === "blocked" ?
                            'error' :
                            'warning',

                        title: result.status === "blocked" ?
                            'ไม่สามารถเข้าสู่ระบบได้' :
                            'รอการอนุมัติ',

                        text: result.message || 'ไม่สามารถเข้าสู่ระบบได้',

                        confirmButtonColor: '#0284c7',

                        confirmButtonText: 'ตกลง'

                    });

                } else {

                    alert(result.message || "ไม่สามารถเข้าสู่ระบบได้");

                }

            }

            /*
            |--------------------------------------------------------------------------
            | LOGIN GOOGLE ไม่สำเร็จ
            |--------------------------------------------------------------------------
            */
            else {

                if (typeof Swal !== 'undefined') {

                    Swal.fire({

                        icon: 'error',

                        title: 'เข้าสู่ระบบไม่สำเร็จ',

                        text: result.message ||
                            'ไม่สามารถเข้าสู่ระบบด้วย Google ได้',

                        confirmButtonColor: '#0284c7',

                        confirmButtonText: 'ตกลง'

                    });

                } else {

                    alert(
                        result.message ||
                        "เข้าสู่ระบบไม่สำเร็จ"
                    );

                }

            }

        })

        /*
        |--------------------------------------------------------------------------
        | CONNECTION ERROR
        |--------------------------------------------------------------------------
        */
        .catch(error => {

            console.error(error);

            if (typeof Swal !== 'undefined') {

                Swal.fire({

                    icon: 'error',

                    title: 'เกิดข้อผิดพลาด',

                    text: 'ไม่สามารถเชื่อมต่อกับระบบได้ กรุณาลองใหม่อีกครั้ง',

                    confirmButtonColor: '#0284c7',

                    confirmButtonText: 'ตกลง'

                });

            } else {

                alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");

            }

        });

}

// ==========================================
// 🧠 JWT DECODE FUNCTION
// ==========================================
function parseJwt(token) {
    let base64Url = token.split('.')[1];
    let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    let jsonPayload = decodeURIComponent(
        atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join('')
    );
    return JSON.parse(jsonPayload);
}

// ==========================================
// 🔑 FORGOT PASSWORD FUNCTION
// ==========================================
function forgotPassword() {
    alert(
        "หากคุณลืมรหัสผ่าน กรุณาติดต่อผู้ดูแลระบบ\n\n" +
        "📧 admin@msu.ac.th\n" +
        "📞 02-123-4567"
    );
}

// ==========================================
// ⏳ LOGIN LOADING STATE
// ==========================================
const form = document.querySelector("form");
if (form) {
    form.addEventListener("submit", function() {
        const btn = document.getElementById("loginBtn");
        if (btn) {
            const text = btn.querySelector(".btn-text");
            btn.classList.add("loading");
            if (text) {
                text.innerText = "กำลังเข้าสู่ระบบ...";
            }
        }
    });
}
</script>

<!-- Sidebar control removed from footer.
     Sidebar state is managed by each role sidebar component
     to prevent duplicate event listeners.
-->

<?php if(isset($use_chart) && $use_chart === true): ?>

<script src="<?= ASSET_URL ?>js/chart.umd.min.js"></script>

<?php endif; ?>

<?php if (isset($page_js) && !empty($page_js)): ?>

    <?php
    $sweetAlertAssetPath = PUBLIC_PATH . '/assets/js/sweetalert2.all.min.js';
    $sweetAlertAssetVersion = is_file($sweetAlertAssetPath) ? '?v=' . filemtime($sweetAlertAssetPath) : '';
    $commonJsAssetPath = PUBLIC_PATH . '/assets/js/common.js';
    $commonJsAssetVersion = is_file($commonJsAssetPath) ? '?v=' . filemtime($commonJsAssetPath) : '';
    ?>
    <script src="<?= ASSET_URL ?>js/sweetalert2.all.min.js<?= $sweetAlertAssetVersion ?>"></script>
    <script src="<?= ASSET_URL ?>js/common.js<?= $commonJsAssetVersion ?>"></script>

    <?php if (is_array($page_js)): ?>

        <?php foreach ($page_js as $js): ?>

            <?php if (!empty($js)): ?>
                <?php
                if ($js === 'sweetalert2.all.min.js') {
                    continue;
                }
                $pageJsAssetPath = PUBLIC_PATH . '/assets/js/' . $js;
                $pageJsAssetVersion = is_file($pageJsAssetPath) ? '?v=' . filemtime($pageJsAssetPath) : '';
                ?>
                <script src="<?= ASSET_URL ?>js/<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?><?= $pageJsAssetVersion ?>"></script>
            <?php endif; ?>

        <?php endforeach; ?>

    <?php else: ?>

        <?php if ($page_js !== 'sweetalert2.all.min.js'): ?>
            <?php
            $pageJsAssetPath = PUBLIC_PATH . '/assets/js/' . $page_js;
            $pageJsAssetVersion = is_file($pageJsAssetPath) ? '?v=' . filemtime($pageJsAssetPath) : '';
            ?>
            <script src="<?= ASSET_URL ?>js/<?= htmlspecialchars($page_js, ENT_QUOTES, 'UTF-8') ?><?= $pageJsAssetVersion ?>"></script>
        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>

<script>
if (typeof lucide !== "undefined") {
    lucide.createIcons();
}
</script>
<script src="<?= ASSET_URL ?>js/modal-ed-profile.js"></script>
</body>

</html>