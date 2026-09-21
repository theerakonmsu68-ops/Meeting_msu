<?php


/* ===============================
AUTH
=============================== */


require_once dirname(__DIR__, 3) . '/app/middleware/AuthMiddleware.php';


AuthMiddleware::allow(1);





require_once __DIR__.'/../../../app/bootstrap.php';

require_once __DIR__.'/../../../app/config/database.php';

require_once __DIR__.'/../../../app/helpers/view_helper.php';


require_once __DIR__.'/../../../app/models/Meeting.php';

require_once __DIR__.'/../../../app/controllers/MeetingController.php';







$db = (new Database())->connect();




$meetingModel = new Meeting($db);


$meetingController = new MeetingController($meetingModel);








/* ===============================
GET ID
=============================== */


if(empty($_GET['id']))
{

    die("ไม่พบรหัสการประชุม");

}



$meeting_id = (int)$_GET['id'];








/* ===============================
UPDATE
=============================== */


if($_SERVER['REQUEST_METHOD']=="POST")
{


    try
    {


        $data = [


            'meeting_title'
            =>
            $_POST['meeting_title'] ?? '',



            'report_header'
            =>
            $_POST['report_header'] ?? null,



            'meeting_number'
            =>
            $_POST['meeting_number'] ?? null,



            'meeting_date'
            =>
            $_POST['meeting_date'] ?? '',



            'meeting_time'
            =>
            $_POST['meeting_time'] ?? '',



            'meeting_location'
            =>
            $_POST['meeting_location'] ?? '',



            'meeting_link'
            =>
            $_POST['meeting_link'] ?? null,



            'meeting_status'
            =>
            $_POST['meeting_status']
            ??
            'upcoming'

        ];






        // update meeting

        $meetingController->update(

            $meeting_id,

            $data

        );







        // update agenda detail

        if(!empty($_POST['agenda_detail']))
        {


            foreach(
                $_POST['agenda_detail']
                as $agenda_id=>$detail
            )
            {


                $meetingController
                ->updateAgenda(
                    $agenda_id,
                    $detail
                );


            }


        }







        /*
        ===============================
        Upload Meeting Documents
        ===============================
        */


        if(!empty($_FILES['meeting_documents']['name'][0]))
        {


            $files=[];



            foreach(
                $_FILES['meeting_documents']['name']
                as $key=>$name
            )
            {


                $files[]=[

                    "name"=>$name,

                    "path"=>"uploads/meetings/".$name,

                    "size"=>
                    $_FILES['meeting_documents']['size'][$key],

                    "type"=>
                    $_FILES['meeting_documents']['type'][$key]

                ];

            }




            $meetingController
            ->createDocuments(
                $meeting_id,
                $files
            );


        }








        /*
        ===============================
        Upload Agenda Documents
        ===============================
        */


        if(!empty($_FILES['agenda_documents']))
        {


            foreach(
                $_FILES['agenda_documents']['name']
                as $agenda_id=>$fileGroup
            )
            {


                $files=[];



                foreach($fileGroup as $key=>$name)
                {


                    if(empty($name))
                    {
                        continue;
                    }



                    $files[]=[


                        "name"=>$name,


                        "path"=>"uploads/agenda/".$name,


                        "size"=>
                        $_FILES['agenda_documents']
                        ['size'][$agenda_id][$key],



                        "type"=>
                        $_FILES['agenda_documents']
                        ['type'][$agenda_id][$key]


                    ];

                }





                if(!empty($files))
                {


                    $meetingController
                    ->createAgendaDocuments(

                        $agenda_id,

                        $files

                    );


                }


            }


        }






        header(
            "Location: edit_meeting.php?id=".$meeting_id
        );


        exit;


    }
    catch(Throwable $e)
    {

        die(
            "เกิดข้อผิดพลาด : ".$e->getMessage()
        );

    }



}









/* ===============================
LOAD DATA
=============================== */


$meeting =
$meetingController->get($meeting_id);




if(!$meeting)
{

    die("ไม่พบข้อมูลการประชุม");

}







$agendas =
$meetingController
->getAgendaTree($meeting_id);







$documents =
$meetingController
->getDocuments($meeting_id);








$page_title="Dashboard - Admin";


$page_css="meetings-management.css";


$page_js=[

    "meetings-management.js"

];





include_once __DIR__
.'/../../../app/views/layouts/header.php';





$current_page='meetings';



include_once __DIR__
.'/../../../app/views/layouts/sidebar_admin.php';

?>

<div class="main-content" id="mainContent">



<header class="header">


<div class="header-left">


<button class="toggle-btn" id="toggle-sidebar">

<i data-lucide="menu"></i>

</button>



<h2>

แก้ไขการประชุม

</h2>



</div>


</header>







<main class="content-wrapper">





<form

method="POST"

enctype="multipart/form-data"

id="meetingForm"



>




<div class="form-card">





<h3>

แก้ไขการประชุม

</h3>









<!-- STATUS -->


<div class="status-control-container">



<label>

⚠️ สถานะกระบวนการประชุม

</label>





<select

name="meeting_status"

class="form-control"

>




<option value="upcoming"

<?= $meeting['meeting_status']=="upcoming"?'selected':'' ?>

>

🔵 ยังไม่เริ่มการประชุม (เร็ว ๆ นี้)

</option>





<option value="ongoing"

<?= $meeting['meeting_status']=="ongoing"?'selected':'' ?>

>

🔴 กำลังดำเนินการประชุม (Live)

</option>





<option value="closed"

<?= $meeting['meeting_status']=="closed"?'selected':'' ?>

>

⚫ จบและปิดการประชุม (Closed)

</option>





</select>


</div>









<label>

หัวข้อการประชุม

</label>




<input

type="text"

name="meeting_title"

class="form-control"

value="<?= h($meeting['meeting_title']) ?>"

>









<div class="form-grid-2">





<div>


<label>

วันที่

</label>




<input

type="date"

name="meeting_date"

class="form-control"

value="<?= h($meeting['meeting_date']) ?>"

>



</div>








<div>


<label>

เวลา

</label>




<input

type="time"

name="meeting_time"

class="form-control"

value="<?= h($meeting['meeting_time']) ?>"

>



</div>





</div>









<label>

สถานที่

</label>





<input

type="text"

name="meeting_location"

class="form-control"

value="<?= h($meeting['meeting_location']) ?>"

>









<label>

ลิงก์ห้องประชุมออนไลน์ (ถ้ามี)

</label>





<input

type="url"

name="meeting_link"

class="form-control"

value="<?= h($meeting['meeting_link']) ?>"

>









<div class="form-grid-report">





<div>


<label>

ชื่อคณะกรรมการ/หน่วยงานบนรายงาน

</label>




<input

type="text"

name="report_header"

class="form-control"

value="<?= h($meeting['report_header']) ?>"

>



</div>







<div>


<label>

ครั้งที่

</label>





<input

type="text"

name="meeting_number"

class="form-control"

value="<?= h($meeting['meeting_number']) ?>"

>



</div>





</div>









<!-- ===============================
DOCUMENT
=============================== -->



<label>

เอกสารแนบการประชุม

</label>







<?php if(!empty($documents)): ?>



<div class="old-file-list">



<?php foreach($documents as $doc): ?>



<div class="file-item">



<i data-lucide="file-text"></i>



<a

href="<?= h($doc['file_path']) ?>"

target="_blank"

>


<?= h($doc['document_name']) ?>


</a>



</div>



<?php endforeach; ?>



</div>



<?php endif; ?>









<div class="upload-zone">



<i data-lucide="cloud-upload"></i>



<p>

<b>

คลิกเพื่อเลือกไฟล์ใหม่

</b>

หรือลากไฟล์มาวางที่นี่


</p>




<span>

รองรับไฟล์ PDF, Word, Excel, PowerPoint

</span>





<input

type="file"

name="meeting_documents[]"

multiple

accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"

>



</div>







<!-- ===============================
AGENDA
=============================== -->



<label class="agenda-title">

วาระการประชุม

</label>






<div id="agenda-container">



<?php if(!empty($agendas)): ?>



<?php foreach($agendas as $agenda): ?>



<div class="agenda-card">





<div class="agenda-main-header"

onclick="toggleAgenda(<?= $agenda['agenda_id'] ?>)"

>



<strong>


<?= h($agenda['order_index']) ?>.

<?= h($agenda['agenda_title']) ?>


</strong>




<span id="icon-<?= $agenda['agenda_id'] ?>">

▼

</span>



</div>







<div

id="agenda-box-<?= $agenda['agenda_id'] ?>"

class="agenda-detail-box"



>







<textarea

name="agenda_detail[<?= $agenda['agenda_id'] ?>]"

class="agenda-editor"

placeholder="รายละเอียดวาระการประชุม"

><?= h($agenda['agenda_detail']) ?></textarea>






<?php if(!empty($agenda['children'])): ?>



<div class="sub-agenda-container">



<h4>

วาระเสนอจากหน่วยงาน

</h4>





<?php foreach($agenda['children'] as $child): ?>



<div class="sub-agenda-card">





<div class="sub-agenda-header">



<strong>

<?= h($child['agenda_title']) ?>

</strong>





<span>


<?php

if($child['proposer_type']=="department")
{
    echo "🏢 ภาควิชา";
}

elseif($child['proposer_type']=="executive")
{
    echo "👤 ผู้บริหาร";
}

else
{
    echo "⚙️ Admin";
}

?>


</span>




</div>







<textarea

name="agenda_detail[<?= $child['agenda_id'] ?>]"

class="agenda-editor"

placeholder="รายละเอียดวาระย่อย"

><?= h($child['agenda_detail']) ?></textarea>







<label>

เอกสารประกอบวาระ

</label>






<input

type="file"

name="agenda_documents[<?= $child['agenda_id'] ?>][]"

multiple

accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"

>




</div>





<?php endforeach; ?>




</div>




<?php endif; ?>






</div>







</div>





<?php endforeach; ?>




<?php else: ?>



<p>

ไม่พบวาระการประชุม

</p>



<?php endif; ?>





</div>





<div class="form-actions">





<a

href="edit_meetings.php"

class="btn-cancel"

>

ปิดหน้าต่าง

</a>







<button

type="submit"

class="btn-save"

>

บันทึกข้อมูล

</button>






</div>








</div>





</form>







</main>







</div>









<!-- ===============================
CKEDITOR
================================ -->



<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>







<script>



let agendaEditors = [];








document.querySelectorAll('.agenda-editor')

.forEach((element)=>{






    ClassicEditor

    .create(element)

    .then(editor=>{






        agendaEditors.push({

            element: element,

            editor: editor

        });





    })



    .catch(error=>{


        console.error(error);


    });







});












/*

================================
ก่อน Submit

เอาค่า CKEditor

กลับเข้า textarea

================================

*/


document

.getElementById('meetingForm')

.addEventListener('submit',function(){






    agendaEditors.forEach((item)=>{





        item.element.value =

        item.editor.getData();






    });






});













/*

================================
ซ่อน / แสดง Agenda

================================

*/



function toggleAgenda(id)

{


    const box = document

    .getElementById(

        "agenda-box-"+id

    );





    const icon = document

    .getElementById(

        "icon-"+id

    );






    if(box.style.display === "none")

    {



        box.style.display="block";


        icon.innerHTML="▼";



    }

    else

    {



        box.style.display="none";


        icon.innerHTML="▶";



    }


}






</script>









<?php


include_once __DIR__ . '/../../../app/views/layouts/footer.php';


?>