<?php
/*
|--------------------------------------------------------------------------
| TEMPLATE : เพิ่มการประชุม
|--------------------------------------------------------------------------
| หน้านี้เป็น UI Template เท่านั้น
| ยังไม่มี Database / Model / Controller / POST Processing
|--------------------------------------------------------------------------
*/

$page_title = 'เพิ่มการประชุม';
$page_css = 'meeting-form.css';
$page_js = [
    "sweetalert2.all.min.js",
    "user-management.js"
];

include_once __DIR__ . '/../../../app/views/layouts/header.php';

$current_page = 'meetings';
include_once __DIR__ . '/../../../app/views/layouts/sidebar_admin.php';
?>

<div class="main-content" id="mainContent">

    <header class="header">

        <div class="header-left">

            <button
                class="toggle-btn"
                id="toggle-sidebar"
                type="button"
                aria-label="เปิด/ปิดเมนู"
            >
                <i data-lucide="menu"></i>
            </button>


            <div>
              <div>
                <h2>เพิ่มการประชุม</h2>
                <p class="header-subtitle">
                    เพิ่มข้อมูลการประชุมใหม่และวาระการประชุม
                </p>
            </div>
            </div>

        </div>


        <div class="header-right">

            <a
                href="edit_meetings.php"
                class="btn-back"
            >
                <i data-lucide="arrow-left"></i>
                กลับหน้ารายการ
            </a>

        </div>

    </header>


    <main class="content-wrapper">


        <form id="meetingForm">


            <!-- =====================================================
                 ข้อมูลการประชุม
                 ===================================================== -->
            <section class="form-card">


                <div class="section-header">

                    <div class="section-icon">
                        <i data-lucide="calendar-plus"></i>
                    </div>


                    <div>
                        <h3>
                            ข้อมูลการประชุม
                        </h3>

                        <p>
                            ระบุข้อมูลพื้นฐานของการประชุม
                        </p>
                    </div>

                </div>



                <div class="form-group status-control-container">

                    <label for="meeting_status">
                        สถานะกระบวนการประชุม
                    </label>


                    <select
                        id="meeting_status"
                        name="meeting_status"
                        class="form-control"
                    >

                        <option value="upcoming">
                            ยังไม่เริ่มการประชุม (เร็ว ๆ นี้)
                        </option>

                        <option value="ongoing">
                            กำลังดำเนินการประชุม (Live)
                        </option>

                        <option value="closed">
                            จบและปิดการประชุม (Closed)
                        </option>

                    </select>

                </div>



                <div class="form-group">

                    <label for="meeting_title">
                        หัวข้อการประชุม
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="meeting_title"
                        name="meeting_title"
                        class="form-control"
                        placeholder="ระบุหัวข้อประชุม..."
                    >

                </div>



                <div class="form-grid-2">


                    <div class="form-group">

                        <label for="meeting_date">
                            วันที่
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="meeting_date"
                            name="meeting_date"
                            class="form-control"
                        >

                    </div>



                    <div class="form-group">

                        <label for="meeting_time">
                            เวลา
                            <span class="required">*</span>
                        </label>

                        <input
                            type="time"
                            id="meeting_time"
                            name="meeting_time"
                            class="form-control"
                        >

                    </div>


                </div>



                <div class="form-group">

                    <label for="meeting_location">
                        สถานที่
                    </label>

                    <input
                        type="text"
                        id="meeting_location"
                        name="meeting_location"
                        class="form-control"
                        placeholder="ห้องประชุม หรือ ตึก..."
                    >

                </div>



                <div class="form-group">

                    <label for="meeting_link">
                        ลิงก์ห้องประชุมออนไลน์ (ถ้ามี)
                    </label>

                    <input
                        type="url"
                        id="meeting_link"
                        name="meeting_link"
                        class="form-control"
                        placeholder="https://example.zoom.us/j/..."
                    >

                </div>



                <div class="form-grid-report">


                    <div class="form-group">

                        <label for="report_header">
                            ชื่อคณะกรรมการ/หน่วยงานบนรายงาน
                        </label>

                        <input
                            type="text"
                            id="report_header"
                            name="report_header"
                            class="form-control"
                            placeholder="เช่น คณะกรรมการประจำคณะวิทยาการสารสนเทศ"
                        >

                    </div>



                    <div class="form-group">

                        <label for="meeting_number">
                            ครั้งที่
                        </label>

                        <input
                            type="text"
                            id="meeting_number"
                            name="meeting_number"
                            class="form-control"
                            placeholder="เช่น 9/2569"
                        >

                    </div>


                </div>


            </section>



            <!-- =====================================================
                 เอกสารแนบ
                 ===================================================== -->
            <section class="form-card">


                <div class="section-header">

                    <div class="section-icon">
                        <i data-lucide="paperclip"></i>
                    </div>


                    <div>
                        <h3>
                            เอกสารแนบการประชุม
                        </h3>

                        <p>
                            เพิ่มไฟล์เอกสารที่เกี่ยวข้องกับการประชุม
                        </p>
                    </div>

                </div>



                <label class="upload-zone">


                    <input
                        type="file"
                        name="meeting_documents[]"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                    >


                    <div class="upload-icon">
                        <i data-lucide="cloud-upload"></i>
                    </div>


                    <strong>
                        คลิกเพื่อเลือกไฟล์
                    </strong>


                    <span>
                        หรือลากไฟล์มาวางที่นี่
                    </span>


                    <small>
                        รองรับไฟล์ PDF, Word, Excel และ PowerPoint
                    </small>


                </label>


            </section>



            <!-- =====================================================
                 วาระการประชุม
                 ===================================================== -->
            <section class="form-card">


                <div class="section-header">

                    <div class="section-icon">
                        <i data-lucide="list-tree"></i>
                    </div>


                    <div>
                        <h3>
                            วาระการประชุม
                        </h3>

                        <p>
                            ระบุรายละเอียดและเอกสารประกอบแต่ละวาระ
                        </p>
                    </div>

                </div>



                <div id="agenda-container">


                    <!-- ตัวอย่างวาระ -->
                    <div class="agenda-card">


                        <div class="agenda-card-header">

                            <span class="agenda-number">
                                1
                            </span>


                            <strong>
                                ระเบียบวาระที่ 1
                            </strong>

                        </div>



                        <div class="form-group">

                            <label>
                                รายละเอียดวาระ
                            </label>

                            <textarea
                                name="agenda_detail[]"
                                class="agenda-editor"
                                placeholder="ระบุรายละเอียดวาระ เช่น ที่มา ประเด็นเสนอเพื่อพิจารณา มติ หรือข้อมูลประกอบ..."
                            ></textarea>

                            <small class="editor-help">
                                รองรับการจัดหัวข้อ ตัวหนา ตัวเอียง รายการ ลิงก์ ตาราง และข้อความอ้างอิง
                            </small>

                        </div>



                        <div class="form-group">

                            <label>
                                เอกสารประกอบวาระ
                            </label>

                            <input
                                type="file"
                                name="agenda_documents[]"
                                class="file-control"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                            >

                        </div>


                    </div>


                </div>


            </section>



            <!-- =====================================================
                 ACTIONS
                 ===================================================== -->
            <div class="form-actions">


                <a
                    href="edit_meetings.php"
                    class="btn-cancel"
                >
                    ยกเลิก
                </a>


                <button
                    type="button"
                    class="btn-save"
                >
                    <i data-lucide="save"></i>
                    บันทึกข้อมูล
                </button>


            </div>


        </form>


    </main>

</div>



<!-- =========================================================
     Academic Rich Text Editor : CKEditor 5
     ใช้เฉพาะฝั่ง UI ยังไม่มี PHP บันทึกข้อมูล
     ========================================================= -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
const agendaEditors = [];

document.querySelectorAll('.agenda-editor').forEach((element) => {

    ClassicEditor
        .create(element, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'blockQuote',
                    'insertTable',
                    '|',
                    'undo',
                    'redo'
                ],
                shouldNotGroupWhenFull: true
            },

            heading: {
                options: [
                    {
                        model: 'paragraph',
                        title: 'ข้อความปกติ',
                        class: 'ck-heading_paragraph'
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'หัวข้อหลัก',
                        class: 'ck-heading_heading2'
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'หัวข้อย่อย',
                        class: 'ck-heading_heading3'
                    }
                ]
            },

            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },

            placeholder: 'ระบุรายละเอียดวาระการประชุม...'
        })
        .then((editor) => {
            agendaEditors.push(editor);
        })
        .catch((error) => {
            console.error('CKEditor Error:', error);
        });

});
</script>


<?php
include_once __DIR__ . '/../../../app/views/layouts/footer.php';
?>
