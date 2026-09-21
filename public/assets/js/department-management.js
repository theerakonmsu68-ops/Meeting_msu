
function showWarning(message, title = 'แจ้งเตือน') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'warning',
            title,
            text: message,
            confirmButtonText: 'ตกลง'
        });
    }

    alert(message);
    return Promise.resolve();
}


function showError(message, title = 'เกิดข้อผิดพลาด') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'error',
            title,
            text: message,
            confirmButtonText: 'ตกลง'
        });
    }

    alert(message);
    return Promise.resolve();
}


function showSuccess(message, title = 'สำเร็จ') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'success',
            title,
            text: message,
            confirmButtonText: 'ตกลง'
        });
    }

    alert(message);
    return Promise.resolve();
}


function showConfirm(
    message,
    title = 'ยืนยันการดำเนินการ',
    confirmText = 'ยืนยัน',
    cancelText = 'ยกเลิก'
) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'warning',
            title,
            text: message,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true
        });
    }

    return Promise.resolve({
        isConfirmed: confirm(
            `${title}\n${message}`
        )
    });
}


/* =========================================================
   Loading
========================================================= */

function showLoading(message = 'กำลังดำเนินการ...') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    return null;
}


function closeLoading() {
    if (typeof Swal !== 'undefined') {
        Swal.close();
    }
}


/* =========================================================
   Configuration
========================================================= */

const API_URL = 'api.php';


/* =========================================================
   DOM Elements
========================================================= */

const searchInput =
    document.getElementById('searchInput');

const tableContainer =
    document.getElementById('tableContainer');

const modal =
    document.getElementById('modal');

const modalTitle =
    document.getElementById('modalTitle');

const departmentIdInput =
    document.getElementById('department_id');

const departmentNameInput =
    document.getElementById('department_name');


let isEdit = false;
let searchTimeout = null;


/* =========================================================
   Live Search
========================================================= */

function liveSearch() {
    if (!searchInput || !tableContainer) {
        return;
    }

    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        const keyword = searchInput.value.trim();

        const url =
            `edit_department.php?ajax=1&page=1&search=${encodeURIComponent(keyword)}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(
                        `HTTP ERROR ${response.status}`
                    );
                }

                return response.text();
            })
            .then(html => {
                tableContainer.innerHTML = html;

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error(
                    'Live Search Error:',
                    error
                );

                showError(
                    'ไม่สามารถค้นหาข้อมูลได้'
                );
            });
    }, 300);
}


if (searchInput) {
    searchInput.addEventListener(
        'input',
        liveSearch
    );
}


/* =========================================================
   Department Modal
========================================================= */

function openCreateDepartment() {
    isEdit = false;

    if (modalTitle) {
        modalTitle.innerText = 'เพิ่มภาควิชา';
    }

    if (departmentIdInput) {
        departmentIdInput.value = '';
    }

    if (departmentNameInput) {
        departmentNameInput.value = '';
    }

    if (modal) {
        modal.classList.add('show');
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}


/* =========================================================
   Edit Department
========================================================= */

function editDepartment(id) {
    isEdit = true;

    fetch(
        `${API_URL}?action=department_get&id=${encodeURIComponent(id)}`
    )
        .then(response => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ERROR ${response.status}`
                );
            }

            return response.json();
        })
        .then(res => {
            const data = res.data ?? res;

            if (modalTitle) {
                modalTitle.innerText =
                    'แก้ไขภาควิชา';
            }

            if (departmentIdInput) {
                departmentIdInput.value =
                    data.department_id ?? '';
            }

            if (departmentNameInput) {
                departmentNameInput.value =
                    data.department_name ?? '';
            }

            if (modal) {
                modal.classList.add('show');
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        })
        .catch(error => {
            console.error(
                'Edit Department Error:',
                error
            );

            showError(
                'ไม่สามารถโหลดข้อมูลภาควิชาได้'
            );
        });
}


/* =========================================================
   Save Department
========================================================= */

function saveDepartment() {
    if (!departmentNameInput) {
        showError(
            'ไม่พบช่องกรอกชื่อภาควิชา'
        );
        return;
    }

    const id =
        departmentIdInput
            ? departmentIdInput.value.trim()
            : '';

    const name =
        departmentNameInput.value.trim();


    if (!name) {
        showWarning(
            'กรุณากรอกชื่อภาควิชา',
            'ข้อมูลไม่ครบ'
        );
        return;
    }


    const formData = new FormData();

    formData.append(
        'department_name',
        name
    );


    if (isEdit && id) {
        formData.append(
            'action',
            'department_update'
        );

        formData.append(
            'department_id',
            id
        );
    } else {
        formData.append(
            'action',
            'department_create'
        );
    }


    showLoading(
        isEdit
            ? 'กำลังแก้ไขข้อมูล...'
            : 'กำลังเพิ่มภาควิชา...'
    );


    fetch(API_URL, {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ERROR ${response.status}`
                );
            }

            return response.json();
        })
        .then(res => {
            closeLoading();

            if (res.status === 'success') {
                showSuccess(
                    res.message,
                    'สำเร็จ'
                ).then(() => {
                    closeModal();
                    window.location.reload();
                });
            } else {
                showError(
                    res.message ||
                    'ไม่สามารถบันทึกข้อมูลได้'
                );
            }
        })
        .catch(error => {
            console.error(
                'Save Department Error:',
                error
            );

            closeLoading();

            showError(
                'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์'
            );
        });
}


/* =========================================================
   Delete Department
========================================================= */

function deleteDepartment(id) {
    showConfirm(
        'คุณแน่ใจหรือไม่ว่าต้องการลบภาควิชานี้?',
        'ยืนยันการลบ',
        'ลบ',
        'ยกเลิก'
    )
        .then(result => {
            if (!result.isConfirmed) {
                return;
            }

            const formData = new FormData();

            formData.append(
                'action',
                'department_delete'
            );

            formData.append(
                'id',
                id
            );


            showLoading(
                'กำลังลบข้อมูล...'
            );


            return fetch(API_URL, {
                method: 'POST',
                body: formData
            });
        })
        .then(response => {
            if (!response) {
                return null;
            }

            if (!response.ok) {
                throw new Error(
                    `HTTP ERROR ${response.status}`
                );
            }

            return response.json();
        })
        .then(res => {
            if (!res) {
                return;
            }

            closeLoading();

            if (res.status === 'success') {
                showSuccess(
                    res.message,
                    'ลบสำเร็จ'
                ).then(() => {
                    window.location.reload();
                });
            } else {
                showError(
                    res.message ||
                    'ไม่สามารถลบข้อมูลได้'
                );
            }
        })
        .catch(error => {
            console.error(
                'Delete Department Error:',
                error
            );

            closeLoading();

            showError(
                'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์'
            );
        });
}


/* =========================================================
   Close Modal
========================================================= */

function closeModal() {
    if (modal) {
        modal.classList.remove('show');
    }
}


/* =========================================================
   Sidebar Toggle
========================================================= */

function initSidebar() {
    const sidebar =
        document.getElementById('sidebar');

    const toggleButton =
        document.getElementById(
            'toggle-sidebar'
        );

    const mainContent =
        document.getElementById('mainContent') ||
        document.getElementById('main-content');


    if (!sidebar) {
        console.warn(
            'ไม่พบ #sidebar'
        );
        return;
    }


    if (!toggleButton) {
        console.warn(
            'ไม่พบ #toggle-sidebar'
        );
        return;
    }


    function isMobile() {
        return window.matchMedia(
            '(max-width: 768px)'
        ).matches;
    }


    function syncMainContent() {
        if (!mainContent) {
            return;
        }

        if (isMobile()) {
            mainContent.classList.remove(
                'expanded'
            );
        } else {
            mainContent.classList.toggle(
                'expanded',
                sidebar.classList.contains(
                    'collapsed'
                )
            );
        }
    }


    toggleButton.addEventListener(
        'click',
        event => {
            event.preventDefault();

            sidebar.classList.toggle(
                'collapsed'
            );

            syncMainContent();
        }
    );


    window.addEventListener(
        'resize',
        syncMainContent
    );


    syncMainContent();
}


/* =========================================================
   Initialize
========================================================= */

if (
    document.readyState === 'loading'
) {
    document.addEventListener(
        'DOMContentLoaded',
        () => {
            initSidebar();
        }
    );
} else {
    initSidebar();
}
