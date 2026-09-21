<template id="aiChatTemplate">
    <div class="ai-chat-widget">

        <button
            type="button"
            class="ai-chat-trigger"
            onclick="toggleChat()"
            aria-label="เปิดผู้ช่วยประชุม"
            title="ผู้ช่วยประชุม"
        >
            <i data-lucide="bot" class="icon-bot"></i>
        </button>

        <div class="ai-chat-window" id="aiChatWindow">

            <div class="ai-chat-header">
                <div class="ai-profile">

                    <div class="ai-avatar">
                        <i data-lucide="bot"></i>
                    </div>

                    <div class="ai-profile-copy">
                        <h4 class="ai-title">ผู้ช่วยประชุม</h4>
                        <span class="ai-subtitle">พร้อมให้บริการตลอด 24 ชม.</span>
                    </div>
                </div>

                <div class="ai-header-actions">

                    <button
                        type="button"
                        class="ai-icon-btn"
                        onclick="clearChat()"
                        title="ล้างประวัติการสนทนา"
                        aria-label="ล้างประวัติการสนทนา"
                    >
                        <i data-lucide="trash-2"></i>
                    </button>

                    <button
                        type="button"
                        class="ai-icon-btn btn-close-chat"
                        onclick="toggleChat()"
                        title="ปิด"
                        aria-label="ปิดหน้าต่างสนทนา"
                    >
                        <i data-lucide="x"></i>
                    </button>

                </div>
            </div>

            <div class="ai-chat-body" id="chatBody">
                <div class="message ai-msg">
                    สวัสดีครับ มีอะไรให้ผมช่วยเหลือเกี่ยวกับวาระการประชุมในวันนี้ไหมครับ?
                </div>
            </div>

            <div class="ai-chat-footer">
                <div class="input-container">

                    <input
                        type="text"
                        id="chatInput"
                        placeholder="พิมพ์ข้อความถาม AI..."
                        onkeypress="handleKeyPress(event)"
                        autocomplete="off"
                    >

                    <button
                        type="button"
                        class="btn-send-chat"
                        onclick="sendMessage()"
                        aria-label="ส่งข้อความ"
                        title="ส่งข้อความ"
                    >
                        <i data-lucide="send"></i>
                    </button>

                </div>
            </div>

        </div>
    </div>
</template>

<style>
/* ==========================================================================
   AI CHAT — GOOGLE CALENDAR / GOOGLE WORKSPACE THEME
   ========================================================================== */

.ai-chat-widget,
.ai-chat-widget * {
    box-sizing: border-box;
}

.ai-chat-widget {
    --ai-blue: #1a73e8;
    --ai-blue-hover: #1765cc;
    --ai-blue-soft: #e8f0fe;

    --ai-text: #202124;
    --ai-text-secondary: #5f6368;

    --ai-border: #dadce0;
    --ai-border-hover: #bdc1c6;

    --ai-bg: #ffffff;
    --ai-soft: #f8f9fa;
    --ai-hover: #f1f3f4;

    --ai-danger: #d93025;
    --ai-danger-bg: #fce8e6;

    position: fixed;
    right: 24px;
    bottom: 24px;

    z-index: 1500;

    font-family:
        'Google Sans',
        'Sarabun',
        'Roboto',
        -apple-system,
        BlinkMacSystemFont,
        sans-serif;
}


/* ==========================================================================
   FLOATING TRIGGER
   ========================================================================== */

.ai-chat-trigger {
    width: 56px;
    height: 56px;
    padding: 0;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--ai-border);
    border-radius: 50%;

    background: var(--ai-blue);
    color: #ffffff;

    cursor: pointer;

    box-shadow:
        0 2px 6px rgba(60, 64, 67, .15),
        0 4px 10px rgba(60, 64, 67, .10);

    transition:
        background-color .18s ease,
        box-shadow .18s ease,
        transform .18s ease;
}

.ai-chat-trigger:hover {
    background: var(--ai-blue-hover);

    box-shadow:
        0 4px 10px rgba(60, 64, 67, .18),
        0 6px 14px rgba(60, 64, 67, .12);

    transform: translateY(-1px);
}

.ai-chat-trigger:active {
    transform: scale(.97);
}

.ai-chat-trigger .icon-bot {
    width: 24px;
    height: 24px;
    stroke-width: 2;
}


/* ==========================================================================
   CHAT WINDOW
   ========================================================================== */

.ai-chat-window {
    display: none;

    position: absolute;
    right: 0;
    bottom: 68px;

    width: 380px;
    height: 540px;
    max-height: calc(100vh - 120px);

    flex-direction: column;

    overflow: hidden;

    border: 1px solid var(--ai-border);
    border-radius: 18px;

    background: var(--ai-bg);

    box-shadow:
        0 8px 24px rgba(60, 64, 67, .18),
        0 2px 6px rgba(60, 64, 67, .10);

    animation: aiChatOpen .18s cubic-bezier(.4, 0, .2, 1);
}

@keyframes aiChatOpen {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* ==========================================================================
   HEADER
   ========================================================================== */

.ai-chat-header {
    min-height: 64px;
    padding: 10px 12px 10px 16px;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;

    flex: 0 0 auto;

    background: #ffffff;
    border-bottom: 1px solid var(--ai-border);
}

.ai-profile {
    min-width: 0;

    display: flex;
    align-items: center;
    gap: 10px;
}

.ai-avatar {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;

    display: grid;
    place-items: center;

    border-radius: 50%;

    background: var(--ai-blue-soft);
    color: var(--ai-blue);
}

.ai-avatar svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
}

.ai-profile-copy {
    min-width: 0;
}

.ai-title {
    margin: 0;

    color: var(--ai-text);

    font-size: 15px;
    font-weight: 500;
    line-height: 1.25;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ai-subtitle {
    display: block;

    margin-top: 2px;

    color: var(--ai-text-secondary);

    font-size: 11px;
    font-weight: 400;
    line-height: 1.3;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ai-header-actions {
    display: flex;
    align-items: center;
    gap: 2px;

    flex: 0 0 auto;
}

.ai-icon-btn {
    width: 36px;
    height: 36px;
    padding: 0;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: transparent;
    color: var(--ai-text-secondary);

    cursor: pointer;

    box-shadow: none !important;

    transition:
        background-color .15s ease,
        color .15s ease;
}

.ai-icon-btn:hover {
    background: var(--ai-hover);
    color: var(--ai-text);
}

.ai-icon-btn svg {
    width: 18px;
    height: 18px;
    stroke-width: 2;
}


/* ==========================================================================
   CHAT BODY
   ========================================================================== */

.ai-chat-body {
    min-height: 0;
    flex: 1;

    padding: 18px;

    display: flex;
    flex-direction: column;
    gap: 10px;

    overflow-x: hidden;
    overflow-y: auto;

    background: #ffffff;

    scrollbar-width: thin;
    scrollbar-color: #c7c9cc transparent;
}

.ai-chat-body::-webkit-scrollbar {
    width: 7px;
}

.ai-chat-body::-webkit-scrollbar-thumb {
    border: 2px solid #ffffff;
    border-radius: 999px;
    background: #c7c9cc;
}


/* ==========================================================================
   MESSAGES
   ========================================================================== */

.message {
    max-width: 82%;
    padding: 10px 13px;

    border-radius: 16px;

    font-size: 13px;
    font-weight: 400;
    line-height: 1.55;

    word-wrap: break-word;
    overflow-wrap: anywhere;

    box-shadow: none !important;
}

.ai-msg {
    align-self: flex-start;

    border: 1px solid var(--ai-border);
    border-bottom-left-radius: 5px;

    background: #ffffff;
    color: var(--ai-text);
}

.user-msg {
    align-self: flex-end;

    border: 1px solid transparent;
    border-bottom-right-radius: 5px;

    background: var(--ai-blue-soft);
    color: #174ea6;
}


/* ==========================================================================
   FOOTER
   ========================================================================== */

.ai-chat-footer {
    padding: 12px 14px 14px;

    flex: 0 0 auto;

    border-top: 1px solid var(--ai-border);
    background: #ffffff;
}

.input-container {
    min-height: 44px;

    padding: 3px 4px 3px 14px;

    display: flex;
    align-items: center;
    gap: 8px;

    border: 1px solid var(--ai-border);
    border-radius: 22px;

    background: #ffffff;

    box-shadow: none !important;

    transition: border-color .15s ease;
}

.input-container:hover {
    border-color: var(--ai-border-hover);
}

.input-container:focus-within {
    border-color: var(--ai-border-hover);
    box-shadow: none !important;
}

.input-container input {
    min-width: 0;
    flex: 1;

    padding: 7px 0 !important;

    border: 0 !important;
    border-radius: 0 !important;
    outline: none !important;

    background: transparent !important;
    color: var(--ai-text);

    font-family: inherit;
    font-size: 13px;
    line-height: 1.4;

    box-shadow: none !important;
    appearance: none;
    -webkit-appearance: none;
}

/* ป้องกัน CSS input/focus จากหน้าอื่นมาสร้าง pill สีฟ้าซ้อนด้านใน */
.input-container input:hover,
.input-container input:focus,
.input-container input:focus-visible,
.input-container input:active,
#chatInput:hover,
#chatInput:focus,
#chatInput:focus-visible,
#chatInput:active {
    border: 0 !important;
    border-color: transparent !important;
    border-radius: 0 !important;
    outline: none !important;
    background: transparent !important;
    box-shadow: none !important;
}

.input-container input::placeholder {
    color: #9aa0a6;
}

.btn-send-chat {
    width: 36px;
    height: 36px;
    padding: 0;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 36px;

    border: none;
    border-radius: 50%;

    background: transparent;
    color: var(--ai-blue);

    cursor: pointer;

    box-shadow: none !important;

    transition:
        background-color .15s ease,
        color .15s ease,
        transform .1s ease;
}

.btn-send-chat:hover {
    background: var(--ai-blue-soft);
    color: var(--ai-blue-hover);
}

.btn-send-chat:active {
    transform: scale(.95);
}

.btn-send-chat svg {
    width: 17px;
    height: 17px;
    stroke-width: 2;
}


/* ==========================================================================
   TABLET
   ========================================================================== */

@media (max-width: 768px) {

    .ai-chat-widget {
        right: 16px;
        bottom: 16px;
    }

    .ai-chat-trigger {
        width: 52px;
        height: 52px;
    }

    .ai-chat-window {
        right: 0;
        bottom: 64px;

        width: min(380px, calc(100vw - 32px));
        height: min(540px, calc(100dvh - 112px));
        max-height: none;
    }
}


/* ==========================================================================
   MOBILE
   ========================================================================== */

@media (max-width: 480px) {

    .ai-chat-widget {
        right: 12px;
        bottom: 12px;
    }

    .ai-chat-trigger {
        width: 50px;
        height: 50px;
    }

    .ai-chat-window {
        position: fixed;

        left: 8px;
        right: 8px;
        bottom: 70px;

        width: auto;
        height: min(560px, calc(100dvh - 88px));

        border-radius: 16px;
    }

    .ai-chat-header {
        min-height: 60px;
        padding: 8px 8px 8px 12px;
    }

    .ai-avatar {
        width: 32px;
        height: 32px;
        flex-basis: 32px;
    }

    .ai-title {
        font-size: 14px;
    }

    .ai-subtitle {
        font-size: 10px;
    }

    .ai-chat-body {
        padding: 14px 12px;
    }

    .message {
        max-width: 88%;
        padding: 9px 12px;

        font-size: 12.5px;
    }

    .ai-chat-footer {
        padding: 10px 10px 12px;
    }
}


/* ==========================================================================
   VERY SMALL MOBILE
   ========================================================================== */

@media (max-width: 360px) {

    .ai-chat-window {
        left: 0;
        right: 0;
        bottom: 0;

        width: 100%;
        height: 100dvh;

        border: 0;
        border-radius: 0;

        box-shadow: none;
    }

    .ai-chat-trigger {
        width: 48px;
        height: 48px;
    }

    .ai-subtitle {
        display: none;
    }
}


@media (prefers-reduced-motion: reduce) {

    .ai-chat-trigger,
    .ai-chat-window,
    .ai-icon-btn,
    .btn-send-chat {
        animation: none;
        transition: none;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const template = document.getElementById('aiChatTemplate');

    if (template) {

        const clone = template.content.cloneNode(true);

        document.body.appendChild(clone);

        if (window.lucide) {
            lucide.createIcons();
        }
    }
});


function toggleChat() {

    const chatWindow = document.getElementById('aiChatWindow');

    if (chatWindow) {

        chatWindow.style.display =
            (chatWindow.style.display === 'none' || chatWindow.style.display === '')
                ? 'flex'
                : 'none';

        if (window.lucide) {
            lucide.createIcons();
        }

        if (chatWindow.style.display === 'flex') {
            setTimeout(() => {
                document.getElementById('chatInput')?.focus();
            }, 50);
        }
    }
}


function handleKeyPress(e) {

    if (e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
    }
}


function clearChat() {

    if (confirm('คุณต้องการล้างประวัติการสนทนานี้ใช่หรือไม่?')) {

        const chatBody = document.getElementById('chatBody');

        const clearUrl =
            window.APP_URL + 'controllers/ChatAiController.php?clear=1';

        fetch(clearUrl)
            .then(res => res.json())
            .then(data => {
                console.log('AI Memory:', data.message);
            })
            .catch(err => console.error('Clear memory error:', err));

        if (chatBody) {

            chatBody.innerHTML =
                `<div class="message ai-msg">สวัสดีครับ มีอะไรให้ผมช่วยเหลือเกี่ยวกับวาระการประชุมในวันนี้ไหมครับ?</div>`;
        }
    }
}


function sendMessage() {

    const input = document.getElementById('chatInput');

    if (!input) return;

    const msgText = input.value.trim();

    if (!msgText) return;

    const chatBody = document.getElementById('chatBody');

    if (!chatBody) return;


    const userMessage = document.createElement('div');

    userMessage.className = 'message user-msg';
    userMessage.textContent = msgText;

    chatBody.appendChild(userMessage);

    input.value = '';

    chatBody.scrollTop = chatBody.scrollHeight;


    const targetUrl =
        window.APP_URL + 'controllers/ChatAiController.php';


    fetch(targetUrl, {

        method: 'POST',

        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },

        body: 'message=' + encodeURIComponent(msgText)

    })

    .then(res => {

        if (!res.ok) {
            throw new Error('Network response was not ok');
        }

        return res.json();
    })

    .then(data => {

        const aiMessage = document.createElement('div');

        aiMessage.className = 'message ai-msg';
        aiMessage.textContent = data.reply || '';

        chatBody.appendChild(aiMessage);

        chatBody.scrollTop = chatBody.scrollHeight;
    })

    .catch(err => {

        console.error('Chat Error:', err);

        const errorMessage = document.createElement('div');

        errorMessage.className = 'message ai-msg';
        errorMessage.style.color = '#d93025';
        errorMessage.style.borderColor = '#fad2cf';
        errorMessage.style.background = '#fce8e6';

        errorMessage.textContent =
            'ขออภัย ระบบเชื่อมต่อ AI ขัดข้องในขณะนี้';

        chatBody.appendChild(errorMessage);

        chatBody.scrollTop = chatBody.scrollHeight;
    });
}
</script>
