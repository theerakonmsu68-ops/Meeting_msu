<?php

class Department
{

    private $conn;
    private $table = "departments";


    public function __construct($db)
    {
        $this->conn = $db;
    }




    // ==========================
    // GET BY ID
    // ==========================

    public function getById($id)
    {

        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE department_id = :id
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->execute([
            ':id'=>$id
        ]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }





    // ==========================
    // CREATE
    // ==========================

    public function create($department_name)
    {

        $sql = "
            INSERT INTO {$this->table}
            (
                department_name
            )
            VALUES
            (
                :name
            )
        ";


        $stmt=$this->conn->prepare($sql);



        return $stmt->execute([

            ':name'=>$department_name

        ]);

    }





    // ==========================
    // UPDATE
    // ==========================

    public function update($id,$department_name)
    {


        $sql="
            UPDATE {$this->table}

            SET

            department_name = :name

            WHERE department_id = :id
        ";



        $stmt=$this->conn->prepare($sql);



        return $stmt->execute([


            ':name'=>$department_name,


            ':id'=>$id


        ]);


    }





    // ==========================
    // DELETE
    // ==========================

    public function delete($id)
    {


        $sql="
            DELETE FROM {$this->table}

            WHERE department_id = :id
        ";



        $stmt=$this->conn->prepare($sql);



        return $stmt->execute([

            ':id'=>$id

        ]);

    }





}