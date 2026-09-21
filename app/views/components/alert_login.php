<style>
/* ==========================================================================
   WELCOME POPUP — GOOGLE CALENDAR / GOOGLE WORKSPACE THEME
   ========================================================================== */

.welcome-popup,
.welcome-popup * {
    box-sizing: border-box;
}

.welcome-popup {
    --google-blue: #1a73e8;
    --google-blue-hover: #1765cc;
    --google-blue-light: #e8f0fe;

    --google-text: #202124;
    --google-text-secondary: #5f6368;

    --google-border: #dadce0;
    --google-border-hover: #bdc1c6;

    --google-surface: #ffffff;
    --google-surface-soft: #f8f9fa;
    --google-hover: #f1f3f4;

    position: fixed;
    inset: 0;

    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;

    z-index: 999999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 32px;

    overflow-x: hidden;
    overflow-y: auto;

    background: var(--google-surface);

    opacity: 1;
    visibility: visible;

    transition:
        opacity .22s cubic-bezier(.4, 0, .2, 1),
        visibility .22s ease;
}


/* ==========================================================================
   CLOSING STATE
   ========================================================================== */

.welcome-popup.is-closing {
    opacity: 0;
    visibility: hidden;
}


/* ==========================================================================
   BACKGROUND
   เรียบแบบ Google Calendar — ไม่มี gradient / element ตกแต่งหนัก
   ========================================================================== */

.welcome-bg {
    position: absolute;
    inset: 0;

    pointer-events: none;

    background: #ffffff;
}

/* เส้นแบ่งบางด้านบน ให้ mood เหมือน Google Workspace */
.welcome-bg::before {
    content: "";

    position: absolute;
    top: 0;
    left: 0;
    right: 0;

    height: 4px;

    background: var(--google-blue);
}


/* ==========================================================================
   CONTENT
   ========================================================================== */

.welcome-container {
    position: relative;
    z-index: 2;

    width: min(620px, 100%);

    margin: auto;

    display: flex;
    flex-direction: column;
    align-items: center;

    text-align: center;

    animation: welcomeContentIn .32s cubic-bezier(.4, 0, .2, 1) both;
}


/* ==========================================================================
   LOGO
   ========================================================================== */

.welcome-logo {
    width: 132px;
    height: 132px;

    margin-bottom: 24px;
    padding: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 0;

    background: transparent;

    box-shadow: none !important;

    animation: logoIn .38s cubic-bezier(.4, 0, .2, 1) .04s both;
}

.welcome-logo img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: contain;
}


/* ==========================================================================
   TITLE
   ========================================================================== */

.welcome-container h1 {
    margin: 0;

    color: var(--google-text);

    font-size: clamp(34px, 5vw, 48px);
    font-weight: 500;
    line-height: 1.18;
    letter-spacing: -.5px;
}

.welcome-container h2 {
    margin: 8px 0 16px;

    color: var(--google-blue);

    font-size: clamp(19px, 2.5vw, 24px);
    font-weight: 500;
    line-height: 1.3;
}


/* ==========================================================================
   DESCRIPTION
   ========================================================================== */

.welcome-container p {
    max-width: 500px;

    margin: 0 auto 28px;

    color: var(--google-text-secondary);

    font-size: 15px;
    font-weight: 400;
    line-height: 1.7;
}


/* ==========================================================================
   BUTTON
   ========================================================================== */

.welcome-button {
    min-width: 190px;
    height: 44px;

    padding: 0 20px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;

    border: none;
    border-radius: 22px;

    background: var(--google-blue);
    color: #ffffff;

    font-family: inherit;
    font-size: 14px;
    font-weight: 500;

    cursor: pointer;

    box-shadow: none !important;

    transition:
        background-color .15s ease,
        transform .1s ease;
}

.welcome-button:hover {
    background: var(--google-blue-hover);
}

.welcome-button:active {
    transform: scale(.98);
}

.welcome-button b {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 18px;
    font-weight: 400;
    line-height: 1;

    transition: transform .15s ease;
}

.welcome-button:hover b {
    transform: translateX(2px);
}


/* ==========================================================================
   FOOTER
   ========================================================================== */

.welcome-footer {
    margin-top: 18px;

    color: #80868b;

    font-size: 11px;
    font-weight: 400;
}


/* ==========================================================================
   ANIMATION
   ========================================================================== */

@keyframes welcomeContentIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes logoIn {
    from {
        opacity: 0;
        transform: scale(.96);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}


/* ==========================================================================
   TABLET
   ========================================================================== */

@media (max-width: 768px) {

    .welcome-popup {
        padding: 24px;
    }

    .welcome-close {
        top: 16px;
        right: 16px;
    }

    .welcome-container {
        width: min(560px, 100%);
    }

    .welcome-logo {
        width: 116px;
        height: 116px;

        margin-bottom: 20px;
        padding: 0;
        border-radius: 0;
    }

    .welcome-container h1 {
        font-size: 36px;
    }

    .welcome-container h2 {
        font-size: 20px;
    }

    .welcome-container p {
        max-width: 460px;

        margin-bottom: 24px;

        font-size: 14px;
    }
}


/* ==========================================================================
   MOBILE
   ========================================================================== */

@media (max-width: 520px) {

    .welcome-popup {
        padding: 20px 16px;
    }

    .welcome-close {
        top: 12px;
        right: 12px;

        width: 40px;
        height: 40px;
    }

    .welcome-container {
        width: 100%;
    }

    .welcome-logo {
        width: 92px;
        height: 92px;

        margin-bottom: 18px;
        padding: 0;
        border-radius: 0;
    }

    .welcome-container h1 {
        font-size: 30px;
    }

    .welcome-container h2 {
        margin-top: 6px;
        margin-bottom: 14px;

        font-size: 18px;
    }

    .welcome-container p {
        max-width: 340px;

        margin-bottom: 22px;

        font-size: 13px;
        line-height: 1.65;
    }

    .welcome-button {
        width: min(100%, 320px);
        min-width: 0;
        height: 44px;
    }

    .welcome-footer {
        margin-top: 16px;
        font-size: 10px;
    }
}


/* ==========================================================================
   SMALL MOBILE
   ========================================================================== */

@media (max-width: 360px) {

    .welcome-popup {
        padding: 16px 12px;
    }

    .welcome-logo {
        width: 78px;
        height: 78px;

        margin-bottom: 14px;
        padding: 0;
        border-radius: 0;
    }

    .welcome-container h1 {
        font-size: 27px;
    }

    .welcome-container h2 {
        font-size: 16px;
    }

    .welcome-container p {
        font-size: 12px;
        line-height: 1.6;
    }

    .welcome-button {
        height: 42px;

        font-size: 13px;
    }
}


/* ==========================================================================
   SHORT HEIGHT SCREENS
   ========================================================================== */

@media (max-height: 600px) and (min-width: 521px) {

    .welcome-popup {
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .welcome-logo {
        width: 88px;
        height: 88px;

        margin-bottom: 14px;
        padding: 0;
        border-radius: 0;
    }

    .welcome-container h1 {
        font-size: 32px;
    }

    .welcome-container h2 {
        margin: 5px 0 10px;

        font-size: 18px;
    }

    .welcome-container p {
        margin-bottom: 18px;

        font-size: 13px;
        line-height: 1.55;
    }

    .welcome-footer {
        margin-top: 12px;
    }
}


@media (prefers-reduced-motion: reduce) {

    .welcome-popup,
    .welcome-container,
    .welcome-logo,
    .welcome-button,
    .welcome-button b {
        animation: none;
        transition: none;
    }
}
</style>

<!-- =========================================================
     FULLSCREEN CLEAN WELCOME POPUP
========================================================= -->
<div
    id="sg-popup-builder-content"
    class="welcome-popup"
    style="display: none;"
>

    <div class="welcome-bg"></div>

    <div class="welcome-container">
<!-- Logo -->
        <div class="welcome-logo">

            <img
    src="<?= htmlspecialchars(
        ASSET_URL . 'image/logo.svg',
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    alt="ระบบงานประชุม"
>

        </div>

        <!-- Title -->
        <h1>
            ยินดีต้อนรับ
        </h1>

        <h2>
            สู่ระบบงานประชุม
        </h2>

        <p>
            ระบบบริหารจัดการงานประชุม
            เพื่อสนับสนุนการทำงานให้สะดวก รวดเร็ว
            และเป็นระบบ
        </p>

        <!-- Button -->
        <button
            type="button"
            class="welcome-button"
            onclick="closeWelcomePopup()"
        >
            <span>เข้าสู่ระบบ</span>
            <b>→</b>
        </button>

        <div class="welcome-footer">
            กรุณากดเข้าสู่ระบบเพื่อเริ่มใช้งาน
        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function() {

    const popup = document.getElementById(
        'sg-popup-builder-content'
    );

    /*
     * ถ้าไม่มี Popup
     * ไม่ต้องทำอะไร
     */
    if (!popup) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | ตรวจสอบ Request
    |--------------------------------------------------------------------------
    |
    | GET  = เข้าหน้า Login / Refresh
    | POST = กด Login ด้วย Username + Password
    |
    */
    const isLoginSubmit =
        <?= $_SERVER['REQUEST_METHOD'] === 'POST' ? 'true' : 'false' ?>;


    /*
    |--------------------------------------------------------------------------
    | ถ้าเป็น POST
    |--------------------------------------------------------------------------
    |
    | ไม่แสดง Welcome Popup
    | เพราะอาจมี SweetAlert2 แสดงอยู่
    |
    */
    if (isLoginSubmit) {

        popup.style.display = 'none';

        popup.classList.remove('is-closing');

        document.body.style.overflow = '';

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | ถ้าเป็น GET
    |--------------------------------------------------------------------------
    |
    | แสดง Welcome Popup
    |
    */
    popup.style.display = 'flex';

    /*
     * เอา Animation ปิดออก
     * เผื่อผู้ใช้เคยเปิด/ปิด Popup
     */
    popup.classList.remove('is-closing');

    /*
     * ป้องกัน Scroll ด้านหลัง
     */
    document.body.style.overflow = 'hidden';

});


/*
|--------------------------------------------------------------------------
| ปิด Welcome Popup
|--------------------------------------------------------------------------
*/
function closeWelcomePopup() {

    const popup = document.getElementById(
        'sg-popup-builder-content'
    );

    /*
     * ถ้าไม่มี Popup
     */
    if (!popup) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | ป้องกันการกดซ้ำ
    |--------------------------------------------------------------------------
    */
    if (popup.classList.contains('is-closing')) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | เริ่ม Animation ปิด
    |--------------------------------------------------------------------------
    */
    popup.classList.add('is-closing');


    /*
    |--------------------------------------------------------------------------
    | เปิด Scroll กลับ
    |--------------------------------------------------------------------------
    */
    document.body.style.overflow = '';


    /*
    |--------------------------------------------------------------------------
    | รอ Animation จบ
    |--------------------------------------------------------------------------
    */
    setTimeout(function() {

        popup.style.display = 'none';

        /*
         * ลบ Class ไว้สำหรับการเปิดครั้งถัดไป
         */
        popup.classList.remove('is-closing');

    }, 550);

}
</script>