<!-- <?php
/*
|--------------------------------------------------------------------------
| TEMPLATE : จัดการการประชุม
|--------------------------------------------------------------------------
| หน้านี้เป็น UI Template เท่านั้น
| ยังไม่มี Database / Model / Controller / Query / PHP ดึงข้อมูล
|--------------------------------------------------------------------------
*/

$page_title = 'จัดการการประชุม';
$page_css = 'meetings-management.css';
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

            <button class="toggle-btn" id="toggle-sidebar">
                <i data-lucide="menu"></i>
            </button>

            <div>
                <h2>จัดการการประชุม</h2>
                <p class="header-subtitle">
                    ค้นหาและจัดการรายการประชุม
                </p>
            </div>

        </div>


        <div class="header-right">

            <a href="create_meeting.php" class="btn-add">
                <i data-lucide="plus"></i>
                เพิ่มการประชุม
            </a>

        </div>
    </header>


    <main class="content-wrapper">

        <!-- =====================================================
             FILTER
             ===================================================== -->
        <section class="filter-card">

            <h3>
                <i data-lucide="filter"></i>
                ค้นหาและตัวกรองการประชุม
            </h3>


            <form class="filter-form">

                <div class="form-group search-group">

                    <label for="search">
                        คำค้นหา
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        placeholder="หัวข้อการประชุม, สถานที่, ครั้งที่"
                    >

                </div>


                <div class="form-group">

                    <label for="meeting_date">
                        วันที่ประชุม
                    </label>

                    <input
                        type="date"
                        id="meeting_date"
                        name="meeting_date"
                        class="form-control"
                    >

                </div>


                <div class="form-group">

                    <label for="meeting_status">
                        สถานะ
                    </label>

                    <select
                        id="meeting_status"
                        name="meeting_status"
                        class="form-control"
                    >
                        <option value="">
                            -- ทุกสถานะ --
                        </option>

                        <option value="upcoming">
                            ยังไม่เริ่มการประชุม
                        </option>

                        <option value="ongoing">
                            กำลังดำเนินการประชุม
                        </option>

                        <option value="closed">
                            จบและปิดการประชุม
                        </option>
                    </select>

                </div>


                <div class="filter-actions">

                    <button
                        type="button"
                        class="btn-search"
                    >
                        <i data-lucide="search"></i>
                        ค้นหา
                    </button>


                    <button
                        type="reset"
                        class="btn-clear"
                    >
                        <i data-lucide="rotate-ccw"></i>
                        ล้างค่า
                    </button>

                </div>

            </form>

        </section>



        <!-- =====================================================
             TABLE
             ===================================================== -->
        <section class="table-card">

            <div class="table-responsive">

                <table>

                    <thead>
                        <tr>
                            <th class="col-no">
                                ลำดับ
                            </th>

                            <th>
                                หัวข้อการประชุม
                            </th>

                            <th>
                                ครั้งที่
                            </th>

                            <th>
                                วันที่
                            </th>

                            <th>
                                เวลา
                            </th>

                            <th>
                                สถานที่
                            </th>

                            <th>
                                สถานะ
                            </th>

                            <th class="col-action">
                                จัดการ
                            </th>
                        </tr>
                    </thead>


                    <tbody>

                        <!--
                            ตัวอย่าง Row
                            เมื่อเชื่อม Backend ค่อยนำ Loop มาใส่ตรงนี้
                        -->

                        <tr>

                            <td>
                                1
                            </td>


                            <td>

                                <div class="meeting-title">

                                    <span class="meeting-icon">
                                        <i data-lucide="calendar-days"></i>
                                    </span>

                                    <div>
                                        <strong>
                                            ตัวอย่างการประชุม
                                        </strong>

                                        <small>
                                            ตัวอย่างหน่วยงาน
                                        </small>
                                    </div>

                                </div>

                            </td>


                            <td>
                                1/2569
                            </td>


                            <td>
                                21/09/2569
                            </td>


                            <td>
                                09:00
                            </td>


                            <td>
                                ห้องประชุม
                            </td>


                            <td>

                                <span class="status-badge status-upcoming">
                                    ยังไม่เริ่มการประชุม
                                </span>

                            </td>


                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="update_meeting.php"
                                        class="btn-edit"
                                        title="แก้ไข"
                                    >
                                        <i data-lucide="pencil"></i>
                                    </a>


                                    <button
                                        type="button"
                                        class="btn-delete"
                                        title="ลบ"
                                    >
                                        <i data-lucide="trash-2"></i>
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>


<?php
include_once __DIR__ . '/../../../app/views/components/profile_modal.php';
include_once __DIR__ . '/../../../app/views/layouts/footer.php';
?> -->
