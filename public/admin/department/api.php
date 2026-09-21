<?php

/* ===============================
🔐 AUTH & SECURITY
=============================== */

require_once dirname(__DIR__, 3) . '/app/middleware/AuthMiddleware.php';

AuthMiddleware::allow(1);



ini_set('display_errors', 0);

error_reporting(0);



ob_start();



require_once __DIR__ . '/../../../app/bootstrap.php';

require_once __DIR__ . '/../../../app/config/database.php';


require_once __DIR__ . '/../../../app/models/Department.php';

require_once __DIR__ . '/../../../app/controllers/DepartmentController.php';



header('Content-Type: application/json; charset=utf-8');




/* ===============================
CSRF CHECK
=============================== */


if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !AuthMiddleware::verifyCsrf()) {


    http_response_code(419);


    echo json_encode([

        "status"=>"error",

        "message"=>"โทเคนความปลอดภัยไม่ถูกต้อง"

    ]);


    exit;

}





/* ===============================
DATABASE
=============================== */


$db = (new Database())->connect();



$departmentModel = new Department($db);


$departmentController =
new DepartmentController($departmentModel);





$action =
$_POST['action']
??
$_GET['action']
??
null;



$response=[];





switch($action){



/* =================================
GET ONE
================================= */


case "department_get":


    $id=$_GET['id'] ?? null;



    if(!$id){


        $response=[

            "status"=>"error",

            "message"=>"ไม่พบรหัสภาควิชา"

        ];


    }else{


        $response =
        $departmentController
        ->getDepartmentById($id);


    }


break;





/* =================================
CREATE
================================= */


case "department_create":



    $response =
    $departmentController
    ->createDepartment($_POST);



break;







/* =================================
UPDATE
================================= */


case "department_update":



    $response =
    $departmentController
    ->updateDepartment($_POST);



break;








/* =================================
DELETE
================================= */


case "department_delete":



    $id=$_POST['id'] ?? null;



    if(!$id){


        $response=[

            "status"=>"error",

            "message"=>"ไม่พบรหัสภาควิชา"

        ];


    }else{


        $response =
        $departmentController
        ->deleteDepartment($id);


    }



break;








default:


    $response=[

        "status"=>"error",

        "message"=>"Invalid action"

    ];



}





ob_clean();



echo json_encode($response);



exit;