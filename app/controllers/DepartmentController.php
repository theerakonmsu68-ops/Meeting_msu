<?php


class DepartmentController
{


    private $model;



    public function __construct($model)
    {

        $this->model=$model;

    }





    // ==========================
    // GET
    // ==========================


    public function getDepartmentById($id)
    {


        $data =
        $this->model->getById($id);



        if($data){


            return [

                "status"=>"success",

                "data"=>$data

            ];


        }



        return [

            "status"=>"error",

            "message"=>"ไม่พบข้อมูลภาควิชา"

        ];


    }







    // ==========================
    // CREATE
    // ==========================


    public function createDepartment($data)
    {


        $name =
        trim($data['department_name'] ?? '');



        if($name==""){


            return [

                "status"=>"error",

                "message"=>"กรุณากรอกชื่อภาควิชา"

            ];


        }




        $result =
        $this->model->create($name);




        return [


            "status"=>$result
            ?
            "success"
            :
            "error",


            "message"=>$result

            ?

            "เพิ่มภาควิชาสำเร็จ"

            :

            "ไม่สามารถเพิ่มข้อมูลได้"


        ];


    }







    // ==========================
    // UPDATE
    // ==========================


    public function updateDepartment($data)
    {


        $id =
        $data['department_id'] ?? null;



        $name =
        trim($data['department_name'] ?? '');




        if(!$id || $name==""){


            return [

                "status"=>"error",

                "message"=>"ข้อมูลไม่ครบ"

            ];


        }




        $result =
        $this->model->update(
            $id,
            $name
        );




        return [


            "status"=>$result
            ?
            "success"
            :
            "error",



            "message"=>$result

            ?

            "แก้ไขภาควิชาสำเร็จ"

            :

            "ไม่สามารถแก้ไขข้อมูลได้"


        ];


    }







    // ==========================
    // DELETE
    // ==========================


    public function deleteDepartment($id)
    {



        $result =
        $this->model->delete($id);




        return [


            "status"=>$result
            ?
            "success"
            :
            "error",



            "message"=>$result

            ?

            "ลบภาควิชาสำเร็จ"

            :

            "ไม่สามารถลบข้อมูลได้"


        ];


    }




}