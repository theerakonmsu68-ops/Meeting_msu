<?php

require_once dirname(__DIR__, 3) . '/app/middleware/AuthMiddleware.php';
AuthMiddleware::allow(1);

require_once __DIR__ . '/../../../app/bootstrap.php';
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/view_helper.php';

$db = (new Database())->connect();

$limit = 10;

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int) $_GET['page']
    : 1;

$page = max(1, $page);
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');

$whereClause = '';
$params = [];

if ($search !== '') {
    $whereClause = 'WHERE department_name LIKE :search';
    $params[':search'] = "%{$search}%";
}

/* =========================================================
   AJAX TABLE
========================================================= */

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {

    $countSql = "
        SELECT COUNT(*)
        FROM departments
        {$whereClause}
    ";

    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);

    $totalRows = (int) $countStmt->fetchColumn();

    $sql = "
        SELECT department_id, department_name
        FROM departments
        {$whereClause}
        ORDER BY department_id ASC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $db->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table>
        <thead>
            <tr>
                <th width="80">ลำดับ</th>
                <th>ชื่อภาควิชา</th>
                <th>จัดการ</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($departments)): ?>

                <?php $number = $offset + 1; ?>

                <?php foreach ($departments as $department): ?>

                    <tr>
                        <td><?= $number++ ?></td>

                        <td>
                            <b>
                                <?= h($department['department_name']) ?>
                            </b>
                        </td>

                        <td>
                            <div class="action-buttons">

                                <button type="button" class="btn-edit"
                                    onclick="editDepartment(<?= (int) $department['department_id'] ?>)">
                                    แก้ไข
                                </button>

                                <button type="button" class="btn-delete"
                                    onclick="deleteDepartment(<?= (int) $department['department_id'] ?>)">
                                    ลบ
                                </button>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="3" style="text-align:center;padding:30px;color:#94a3b8">
                        ไม่พบข้อมูลภาควิชา
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

    <?php
    exit;
}

/* =========================================================
   NORMAL LOAD
========================================================= */

$countSql = "
    SELECT COUNT(*)
    FROM departments
    {$whereClause}
";

$countStmt = $db->prepare($countSql);
$countStmt->execute($params);

$totalRows = (int) $countStmt->fetchColumn();

$totalPages = max(
    1,
    (int) ceil($totalRows / $limit)
);

$sql = "
    SELECT department_id, department_name
    FROM departments
    {$whereClause}
    ORDER BY department_id ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $db->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   PAGE CONFIG
========================================================= */

$page_title = 'Dashboard - Admin';
$page_css = 'department-management.css';

$page_js = [
    'sweetalert2.all.min.js',
    'department-management.js'
];

include_once __DIR__ . '/../../../app/views/layouts/header.php';

$current_page = 'department';

include_once __DIR__ . '/../../../app/views/layouts/sidebar_admin.php';

?>

<div class="main-content" id="mainContent">

    <header class="header">

        <div class="header-left">

            <button type="button" class="toggle-btn" id="toggle-sidebar">
                <i data-lucide="menu"></i>
            </button>

            <div class="header-title">
                <h2>จัดการภาควิชา</h2>
                <p class="header-subtitle">
                    ค้นหาและจัดการข้อมูลภาควิชาในระบบ
                </p>
            </div>
        </div>

        <div class="header-right">

            <button type="button" class="btn-add" onclick="openCreateDepartment()">
                <i data-lucide="plus"></i>
                เพิ่มภาควิชา
            </button>

        </div>

    </header>

    <main class="content-wrapper">

        <div class="filter-card">

            <h3>
                <i data-lucide="search"></i>
                ค้นหาภาควิชา
            </h3>

            <form method="GET" class="filter-form">

                <div class="form-group search-group">

                    <label for="searchInput">
                        ชื่อภาควิชา
                    </label>

                    <input type="text" id="searchInput" name="search" class="form-control" value="<?= h($search) ?>"
                        placeholder="ค้นหาชื่อภาควิชา..." autocomplete="off">

                </div>

                <div class="filter-actions">

                    <button type="submit" class="btn-search">
                        <i data-lucide="search"></i>
                        ค้นหา
                    </button>

                    <a href="edit_department.php" class="btn-clear">
                        <i data-lucide="rotate-ccw"></i>
                        ล้างค่า
                    </a>

                </div>

            </form>

        </div>

        <div class="table-card" id="tableContainer">

            <table>

                <thead>
                    <tr>
                        <th width="80">ลำดับ</th>
                        <th>ชื่อภาควิชา</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($departments)): ?>

                        <?php $number = $offset + 1; ?>

                        <?php foreach ($departments as $department): ?>

                            <tr>

                                <td>
                                    <?= $number++ ?>
                                </td>

                                <td>
                                    <b>
                                        <?= h($department['department_name']) ?>
                                    </b>
                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <button type="button" class="btn-edit"
                                            onclick="editDepartment(<?= (int) $department['department_id'] ?>)">
                                            แก้ไข
                                        </button>

                                        <button type="button" class="btn-delete"
                                            onclick="deleteDepartment(<?= (int) $department['department_id'] ?>)">
                                            ลบ
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="3" style="text-align:center;padding:30px;color:#94a3b8">
                                ไม่พบข้อมูลภาควิชา
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

            <?php if ($totalPages > 1): ?>

                <div class="pagination-container">

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                            class="pagination-link <?= $page === $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>

                </div>

            <?php endif; ?>

        </div>

    </main>

</div>

<!-- =========================================================
     DEPARTMENT MODAL
========================================================= -->

<div id="modal" class="modal">

    <div class="modal-box">

        <h3 id="modalTitle">เพิ่มภาควิชา</h3>

        <input type="hidden" id="department_id">

        <div class="modal-form-body">

            <div class="form-group">

                <label for="department_name">
                    ชื่อภาควิชา
                </label>

                <input type="text" id="department_name" class="form-control" placeholder="กรอกชื่อภาควิชา"
                    maxlength="255" autocomplete="off">

            </div>

        </div>

        <div class="modal-actions">

            <button type="button" class="btn-cancel" onclick="closeModal()">
                <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                ยกเลิก
            </button>

            <button type="button" class="btn-save" onclick="saveDepartment()">
                <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                บันทึกข้อมูล
            </button>

        </div>

    </div>

</div>

<?php

include_once __DIR__ . '/../../../app/views/components/profile_modal.php';
include_once __DIR__ . '/../../../app/views/layouts/footer.php';

?>