<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("../../BASICLOGININFO.PHP");

if(isset($_POST['string'])){

    $id=$_POST['string'];
    $sql = "DELETE from payrollinfo WHERE emp_id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "Record deleted successfully" . $id;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}else if(isset($_POST['id'])){

    $id = $_POST['id'];
    echo "New record updated successfully";

    $txtStep1 = $_POST['txtStep1'];
    $txtStep2 = $_POST['txtStep2'];
    $txtStep3 = $_POST['txtStep3'];
    $txtStep4 = $_POST['txtStep4'];
    $txtStep5 = $_POST['txtStep5'];
    $txtStep6 = $_POST['txtStep6'];
    $txtStep7 = $_POST['txtStep7'];
    $txtStep8 = $_POST['txtStep8'];
    $txtStep9 = $_POST['txtStep9'];
    $txtStep10 = $_POST['txtStep10'];
        
    $sql = "UPDATE payrollinfo SET base_pay='$txtStep1', daily_rate='$txtStep2', hourly_rate='$txtStep3', refsalary='$txtStep4', gsis='$txtStep5', philhealth='$txtStep6', pagibig='$txtStep7', wtax='$txtStep8', disallowance='$txtStep9', current_disallowance='$txtStep10' WHERE emp_id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "New record updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
   
}else{
    echo "New record updated successfully";

}



?>