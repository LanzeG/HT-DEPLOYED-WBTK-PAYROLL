<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php

include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();
$flag=0;
$adminId = $_SESSION['adminId'];

$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
if(isset($_POST['computeAll'])){
    $generatedQuery ="SELECT * FROM employees ORDER BY employees.emp_id ASC";

}else if (isset($_POST['computeDisplayed'])){
    $generatedQuery =   $_SESSION['printdisplayed13thmonth'];
}


$getEmployeeIDsQuery = mysqli_query($conn, $generatedQuery) or die("FAILED TO GET EMPLOYEE IDs " . mysqli_error($conn));

// Initialize an array to store employee IDs
$employeeIDs = array();

while ($row = mysqli_fetch_assoc($getEmployeeIDsQuery)) {
    $employeeIDs[] = $row['emp_id'];
}
foreach($employeeIDs as $payID){
    $getdrateqry = "SELECT daily_rate FROM PAYROLLINFO WHERE emp_id = '$payID'";
    $getdrateexecqry = mysqli_query($conn,$getdrateqry) or die ("Failed to get drate ".mysqli_error($conn));
    $dratearray = mysqli_fetch_array($getdrateexecqry);
    if($dratearray){

        $drate = $dratearray['daily_rate'];
    
    
    $amount13th = $drate * 26;
    $total13th = number_format((float)$amount13th,2,'.','');
    
    $date = strtotime("now");
    $year13th = date("Y",$date);

    $check13th = "SELECT * FROM 13thmonth WHERE emp_id = '$payID' AND 13th_year = '$year13th'";
    $check13thexec = mysqli_query($conn,$check13th) or die ("FAILED TO CHECK IF COMPUTED ".mysqli_error($conn));
    $check13throws = mysqli_num_rows($check13thexec); 

    $empname = "SELECT first_name, last_name FROM employees where emp_id = '$id13th'";
    $empnameexecqry = mysqli_query($conn, $empname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
    $empData = mysqli_fetch_assoc($empnameexecqry);

    $empFullName = $empData['first_name'] . " " . $empData['last_name'];
   

    if ($check13throws != 0){
        $flag +=1;
        continue;
        
       
        // $_SESSION['13thmonth'] = "13th month has already been computed.";
        // echo "<script>alert('{$_SESSION['13thmonth']}');</script>";
        // header("Location: admin13thmonth.php");

    }else{

        $flag +=1;
        // continue;
        $in13thqry = "INSERT INTO 13thmonth (emp_id, 13th_amount, 13th_year) VALUES ('$payID','$total13th','$year13th')";
        $in13thexecqry = mysqli_query($conn,$in13thqry) or die ("FAILED TO INSERT 13TH MONTH ".mysqli_error($conn));
        if ($in13thexecqry){
            $activityLog = "13th Month Computed for $empFullName ($year13th)";
            $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
            $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
                
        }


    }
}
    
}
if ($flag > 0 || $flag==0) {
    ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            swal({
                text: "13th month has already been computed.",
                icon: "success",
                button: "OK",
            }).then(function () {
                history.back();
            });
        });
    </script>
    <?php
}
?>