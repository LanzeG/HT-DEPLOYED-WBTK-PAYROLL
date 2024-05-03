<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die("FAILED TO CHECK EMP ID " . mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$_SESSION['editdeptid'] = $_GET['id'];
$idres = $_GET['id'];
$editdeptid = $_SESSION['editdeptid'];
$DELquery = "SELECT * from department WHERE dept_NAME ='$idres'";
$DELselresult = mysqli_query($conn, $DELquery) or die("Failed to search DB. " . mysql_error());
$DELcurr = mysqli_fetch_array($DELselresult);
$DELcount = mysqli_num_rows($DELselresult);

if ($DELcount != 0 && $DELcurr) {

    $currprefixid = $DELcurr['dept_prefix_ID'];
    $currdeptid = $DELcurr['dept_ID'];
    $currdeptname = $DELcurr['dept_NAME'];


} else {
    $updateselecterror = "Department information not found.";
} /*2nd else end*/

$error = false;

if (isset($_POST['submit_btn'])) {

    $deptname = $_POST['deptname'];

    if (empty($deptname)) {

        $error = true;
        $deptnameerror = "Please enter a department name.";

    }

    $deptnamequery = "SELECT dept_NAME FROM department where dept_NAME = '$deptname'";
    $deptnameresultqry = mysqli_query($conn, $deptnamequery);
    $deptnamecount = mysqli_num_rows($deptnameresultqry);

    if ($deptnamecount != 0) {
        $error = true;
        $deptnameerror = "Department already exists.";
    }

    if (!$error) {

        $updateEmployeeQuery = "UPDATE employees SET dept_NAME = '$deptname' WHERE dept_NAME = '$currdeptname'";
        $updateEmployeeResult = mysqli_query($conn, $updateEmployeeQuery) or die("FAILED TO UPDATE EMPLOYEES: " . mysqli_error($conn));

        $newdeptqry = "UPDATE department SET dept_NAME = '$deptname' WHERE dept_ID = '$currdeptid'";
        $newdeptqryresult = mysqli_query($conn, $newdeptqry) or die("FAILED TO CREATE NEW DEPARTMENT " . mysql_error());
        $activityLog = "Edited department from $currdeptname to $deptname";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

        if ($newdeptqryresult) {

            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    swal({
                        //  title: "Good job!",
                        text: "Department updated successfully",
                        icon: "success",
                        button: "OK",
                    }).then(function () {
                        window.location.href = 'adminMasterfileDeptTry.php'; // Replace 'your_new_page.php' with the actual URL
                    });
                });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                swal({
                    // title: "Data ",
                    text: "Something went wrong.",
                    icon: "error",
                    button: "Try Again",
                });
            }); </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</head>
<body>
    <?php
    include('navbarAdmin.php');
    ?>

<form action="adminEDITdepartment.php?id=<?php echo $idres;?>" method="POST" class="form-horizontal" enctype="multipart/form-data">
    <div class="container">
        <div class="title pt-4 d-flex justify-content-center">
            <h3>Update Department</h3>
        </div>
        <hr>
        <div class="col-8 card shadow mx-auto my-5 p-3 mt-5">
            <form action="adminEDITdepartment.php?id=<?php echo $idres;?>" method="POST" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">Department ID:</label>
                    <div class="controls">
                        <input type="text" class="span3 form-control" style=" margin-bottom:10px" value="<?php echo $currprefixid; echo $currdeptid; ?>" name="deptname" readonly />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Department Name:</label>
                    <div class="controls">
                        <input type="text" class="span3 form-control" style=" margin-top:10px; margin-bottom:10px" value="<?php echo $currdeptname; ?>" name="deptname" />
                    </div>
                </div>
                <div class="form-actions d-flex justify-content-center mt-4">
                <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="margin-bottom: 10px; margin-left: 10px; margin-top: 10px;">Update</button>

                </div>
            </form>
        </div>
    </div>
</form>

                    </div>
                </div>

            </div>
        </div>
    </form>

<div class="title text-center">
<h4>Employees in <?php echo $currdeptname;?> Department</h4>

</div>

<div class="table d-flex align-items-center table-responsive">
    <table class="table table-striped">
        <thead class="" style="background-color: #2ff29e; color: #4929aa; text-align: center;">
            <tr> 
                <th style="border-top-left-radius: 10px;">Employee ID</th>
                <th>Name</th>
                <th>Department</th>
                <!-- <th>Shift</th> -->
                <th style="border-top-right-radius: 10px;">Action</th>
            </tr>
        </thead>
        <tbody style=" text-align: center;">
    <?php

$results_perpageDEPT = 20;

if (isset($_GET['page'])){

     $pageDEPT = $_GET['page'];
} else {

     $pageDEPT=1;
}

$start_fromDEPT = ($pageDEPT-1) * $results_perpageDEPT;
$searchqueryDEPT ="SELECT * FROM employees WHERE dept_NAME = '$editdeptid' ORDER BY emp_id ASC LIMIT $start_fromDEPT,".$results_perpageDEPT;

$searchresultDEPT= filterTableDEPT($searchqueryDEPT);

function filterTableDEPT($searchqueryDEPT)
{

     $connDEPT = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
     $filter_ResultDEPT = mysqli_query($connDEPT,$searchqueryDEPT) or die ("failed to query masterfile ".mysql_error());
     return $filter_ResultDEPT;
}

$countdataqryDEPT = "SELECT COUNT(dept_NAME = '$editdeptid') AS total FROM employees";
$countdataqryresultDEPT = mysqli_query($conn,$countdataqryDEPT) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());
$rowDEPT = $countdataqryresultDEPT->fetch_assoc();
$totalpagesDEPT=ceil($rowDEPT['total'] / $results_perpageDEPT);
while($row1DEPT = mysqli_fetch_array($searchresultDEPT)):;
?>
   <tr class="gradeX">
   <td><?php echo $row1DEPT['prefix_ID'],$row1DEPT['emp_id'];?></td>
   <td><?php echo $row1DEPT['last_name'];?>, <?php echo $row1DEPT['first_name'];?> <?php echo $row1DEPT['middle_name'];?></td>
   <td><?php echo $row1DEPT['dept_NAME'];?></td>
   <!-- <td><?php echo $row1DEPT['shift_SCHEDULE'];?></td> -->
   <td><center><a href="adminEDITMasterfile.php?id=<?php echo $row1DEPT['emp_id']?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out btn-mini">
    <span class="icon"><i class="icon-edit"></i></span> Assign to another department
</a>
</center></td>
  
   
     
 </tr>
<?php endwhile;?>


    </tbody>
</table>
</div>
        </div>

       
        
      
      
        <!-- <div class="row-fluid">
            <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT
                BIOMETRICS</div>
        </div> -->
        <?php
        unset($_SESSION['anewdept']);
        ?>

      


        <script src="../js/maruti.dashboard.js"></script>

        </body>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>