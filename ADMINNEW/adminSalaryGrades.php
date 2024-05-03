<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

session_start();

$master = $_SESSION['master'];
?>  

<style>
body{
  font-family: 'Poppins', sans-serif;
}
table.table td .add{
    display:none;
}
</style>

<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- jQuery (necessary for SweetAlert) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- SweetAlert JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        var actions = $("table td:last-child").html();

        $(".add-new").click(function(){
            $(this).attr("disabled","disabled");
            var index = $("table tbody tr:last-child").index();
            var row = '<tr>' +
            '<td><input type="text" class="form-control" name="GradeNumber" id="GradeNumber" required></td>'+
            '<td><input type="text" class="form-control" name="Step1" id="Step1"</td>'+
            '<td><input type="text" class="form-control" name="Step2" id="Step2"</td>'+
            '<td><input type="text" class="form-control" name="Step3" id="Step3"</td>'+
            '<td><input type="text" class="form-control" name="Step4" id="Step4"</td>'+
            '<td><input type="text" class="form-control" name="Step5" id="Step5"</td>'+
            '<td><input type="text" class="form-control" name="Step6" id="Step6"</td>'+
            '<td><input type="text" class="form-control" name="Step7" id="Step7"</td>'+
            '<td><input type="text" class="form-control" name="Step8" id="Step8"</td>'+
            '<td>' + actions + '</td>'+
            '</tr>';
            $("table").append(row);
            $("table tbody tr").eq(index + 1).find(".add,.edit").toggle();
            $('[date-toggle="tooltip"]').tooltip();
        });

        $(document).on("click",".add", function(){
            var empty = false;
            var input = $(this).parents("tr").find('input[type="text"]');
            var txtGrade = $("#GradeNumber").val();
            var txtStep1 = $("#Step1").val();
            var txtStep2 = $("#Step2").val();
            var txtStep3 = $("#Step3").val();
            var txtStep4 = $("#Step4").val();
            var txtStep5 = $("#Step5").val();
            var txtStep6 = $("#Step6").val();
            var txtStep7 = $("#Step7").val();
            var txtStep8 = $("#Step8").val();
            $.post("functions/addsalarygrade.php",{txtGrade:txtGrade, txtStep1:txtStep1, txtStep2:txtStep2, txtStep3:txtStep3,txtStep4:txtStep4, txtStep5:txtStep5, txtStep6:txtStep6, txtStep7:txtStep7, txtStep8:txtStep8},function(data){
                $("#displaymessage").html(data);

            });
            $(this).parents("tr").find(".error").first().focus();
            if(!empty){
                input.each(function(){
                    $(this).parent("td").html($(this).val());
                });
                $(this).parents("tr").find(".add,.edit").toggle();
                $(".add-new").removeAttr("disabled");
            }
        });

        $(document).on("click", ".delete", function(){
    var id = $(this).attr("id");
    var string = id;
    
    // Show confirmation toast before deletion
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true,
        toast: true,
        position: 'center',
        showConfirmButton: true,
        showCancelButton: true,
        cancelButtonColor: '#d33',
        cancelButtonText: 'cancel!',
        confirmButtonColor: '#3085d6',
        timer: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, proceed with deletion
            $(this).parents("tr").remove();
            $(".add-new").removeAttr("disabled");
            $.post("functions/addsalarygrade.php",{string:string},function(data){
                $("#displaymessage").html(data);
                // Show success toast
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The record has been deleted.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // If cancelled, do nothing
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Your record is safe',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
});



        $(document).on("click",".update",function(){
            var id=$("#txtGrade").val();
            var txtStep1 = $("#txtStep1").val();
            var txtStep2 = $("#txtStep2").val();
            var txtStep3 = $("#txtStep3").val();
            var txtStep4 = $("#txtStep4").val();
            var txtStep5 = $("#txtStep5").val();
            var txtStep6 = $("#txtStep6").val();
            var txtStep7 = $("#txtStep7").val();
            var txtStep8 = $("#txtStep8").val();
            $.post("functions/addsalarygrade.php",{id:id, txtStep1:txtStep1, txtStep2:txtStep2, txtStep3:txtStep3, txtStep4:txtStep4, txtStep5:txtStep5, txtStep6:txtStep6, txtStep7:txtStep7, txtStep8:txtStep8}, function(data) {
                $("#displaymessage").html(data);
                reloadTableData();
            });
        });

        $(document).on("click", ".edit", function(){
            $(this).parents("tr").find("td:not(:last-child)").each(function(i){
                if (i === 0){
                    var idname = 'txtGrade';
                } else if (i === 1){
                    var idname = 'txtStep1';
                } else if (i === 2){
                    var idname = 'txtStep2';
                } else if (i === 3){
                    var idname = 'txtStep3';
                } else if (i === 4){
                    var idname = 'txtStep4';
                } else if (i === 5){
                    var idname = 'txtStep5';
                } else if (i === 6){
                    var idname = 'txtStep6';
                } else if (i === 7){
                    var idname = 'txtStep7';
                } else if (i === 8){
                    var idname = 'txtStep8';
                }
                $(this).html('<input type="text" name="updaterec" id="' + idname + '" class="form-control" value="' + $(this).text() + '">');
            });
            $(this).parents("tr").find(".add,.edit").toggle();
            $(".add-new").attr("disabled","disabled");
            $(this).parents("tr").find(".add").removeClass("add").addClass("update");
        });
    });

    function reloadTableData() {
        location.reload();
    }

</script>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Manage Salary Grades</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

    <title>Manage Salary Grades</title>
    <link rel="icon" type="image/png" href="../img/icon1 (3).png">

</head>
<body>
    
<?php
  include('navbarAdmin.php');
  ?>
<div class="title d-flex justify-content-center pt-3">
      <h3>MANAGE SALARY GRADES</h3>
</div>
<div class="row mt-3 mb-1 d-flex justify-content-end  ">

</div>
<div id="tab1">
    <form method="post" action="">
        <div class="row">
            <div class="col-12">
                <div class="control-group">
                    <label class="control-label" for="salaryGrade" id="displaymessage"></label>
                </div>
            </div>
        <div class="control-group text-center">
            <div class="controls mt-3">
                <button type="button" class="add-new inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" name="addSalaryGrade">Add Salary Grade</button>
                    <a href="adminSalaryGrades.php" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out ml-4">
                        <span class="icon"><i class="fas fa-sync-alt"></i></span> Refresh
                    </a>
             </div>
        </div>
    </form>
    <br>
</div> 
</div> 

<div class="row mt-3 mb-1 d-flex justify-content-end">
    <div class="table d-flex align-items-center table-responsive">
        <table class="table table-striped">
            <thead class="table-striped" style="background-color: #2ff29e; color: #4929aa;">
                <tr>
                    <th>Salary Grade</th>
                    <th>Step 1</th>
                    <th>Step 2</th>
                    <th>Step 3</th>
                    <th>Step 4</th>
                    <th>Step 5</th>
                    <th>Step 6</th>
                    <th>Step 7</th>
                    <th>Step 8</th>
                    <th style="border-top-right-radius: 10px; color: #4929aa;">Actions</th>
                </tr>
            </thead>
            <tbody> 
                <?php
                $searchquery ="SELECT * FROM salarygrade";            
                $searchresult= filterTable($searchquery);

               function filterTable($searchquery)
               {
                    $conn = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
                    $filter_Result = mysqli_query($conn,$searchquery) or die ("failed to query Holidays".mysql_error());
                    return $filter_Result;
               }while($row1 = mysqli_fetch_array($searchresult)):;
               ?>
                  <tr class="gradeX">
                  <td><?php echo $row1['GradeNumber'];?></td>
                  <td><?php echo $row1['Step1'];?></td>
                  <td><?php echo $row1['Step2'];?></td>
                  <td><?php echo $row1['Step3'];?></td>
                  <td><?php echo $row1['Step4'];?></td>
                  <td><?php echo $row1['Step5'];?></td>
                  <td><?php echo $row1['Step6'];?></td>
                  <td><?php echo $row1['Step7'];?></td>
                  <td><?php echo $row1['Step8'];?></td>
                  <td class="col-1 text-center">
                    <a class="add" title="Add" ><i class="fa fa-user-plus"></i></a>
                    <a class="edit mr-10" title="Edit" data-toggle="tooltip" id=<?php echo $row1['GradeNumber'];?>><i class="fa fa-pencil"></i></a>
                    <a class="delete mr-10" title="Delete" data-toggle="tooltip" id=<?php echo $row1['GradeNumber'];?>><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                <?php endwhile;?>
            </tbody>
         </table>
</div>
</style>
</html>