<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("../../BASICLOGININFO.PHP");

if(isset($_POST['txtGrade'])){
$txtGrade=$_POST['txtGrade'];
$txtStep1=$_POST['txtStep1'];
$txtStep2=$_POST['txtStep2'];
$txtStep3=$_POST['txtStep3'];
$txtStep4=$_POST['txtStep4'];
$txtStep5=$_POST['txtStep5'];
$txtStep6=$_POST['txtStep6'];
$txtStep7=$_POST['txtStep7'];
$txtStep8=$_POST['txtStep8'];

$sql = "INSERT INTO salarygrade (GradeNumber, Step1, Step2, Step3, Step4, Step5, Step6, Step7, Step8)
        VALUES ('$txtGrade', '$txtStep1', '$txtStep2', '$txtStep3', '$txtStep4', '$txtStep5', '$txtStep6', '$txtStep7', '$txtStep8')";

if (mysqli_query($conn, $sql)) {
    echo "New record inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
}else if(isset($_POST['string'])){


$id=$_POST['string'];
$sql = "DELETE from salarygrade where GradeNumber='$id'";
if (mysqli_query($conn, $sql)) {
    echo "Record deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

}else if(isset($_POST['id'])){

 $id = $_POST['id'];
$txtStep1 = $_POST['txtStep1'];
$txtStep2 = $_POST['txtStep2'];
$txtStep3 = $_POST['txtStep3'];
$txtStep4 = $_POST['txtStep4'];
$txtStep5 = $_POST['txtStep5'];
$txtStep6 = $_POST['txtStep6'];
$txtStep7 = $_POST['txtStep7'];
$txtStep8 = $_POST['txtStep8'];
    
$sql = "UPDATE salarygrade SET Step1='$txtStep1', Step2='$txtStep2', Step3='$txtStep3', Step4='$txtStep4', Step5='$txtStep5', Step6='$txtStep6', Step7='$txtStep7', Step8='$txtStep8' WHERE GradeNumber='$id'";
if (mysqli_query($conn, $sql)) {
    echo "New record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

}

mysqli_close($conn);
?>