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
$error = false;

if (isset($_POST['submit_btn'])) {

    $deptname = $_POST['deptname'];

    if (empty($deptname)) {

        $error = true;
        $deptnameerror = "Please enter a department name.";

    }

    $deptnamequery = "SELECT dept_NAME FROM DEPARTMENT where dept_NAME = '$deptname'";
    $deptnameresultqry = mysqli_query($conn, $deptnamequery);
    $deptnamecount = mysqli_num_rows($deptnameresultqry);

    if ($deptnamecount != 0) {
        $error = true;
        $deptnameerror = "Department already exists.";
    }


    if (!$error) {
        $newdeptqry = "INSERT INTO DEPARTMENT (dept_NAME) VALUES ('$deptname')";
        $newdeptqryresult = mysqli_query($conn, $newdeptqry) or die("FAILED TO CREATE NEW DEPARTMENT " . mysqli_error($conn));
        $activityLog = "Added a new department ($deptname)";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

        if ($newdeptqryresult) {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    swal({
                        //  title: "Good job!",
                        text: "Department inserted successfully",
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <?php
    include('navbaradmin.php');
    ?>
<!-- <<<<<<< admin
    
<form action="adminADDprofile.php" method="POST" class="form-horizontal" enctype="multipart/form-data"> -->

<!--     <div class="content">
        <?php
        if (isset($errMSG)) {
            ?>
            <div class="form-group">
                <div class="alert alert=<?php echo ($errType == "success") ? "success" : $errType; ?>">
                    <font color="green" size="3px"><span class="glyphicon glyphicon-info-sign"></span>
                        <?php echo $errMSG; ?>
                    </font>
                </div>
            </div>
            <?php
        }
        ?> -->
       <form action="adminADDdepartment.php" method="POST" class="form-horizontal" enctype="multipart/form-data">
    <div class="container">
        <div class="title pt-4 d-flex justify-content-center">
            <h3>Add Department</h3>
        </div>
        <hr>
        <div class="col-8 card shadow mx-auto my-5 p-3 mt-5">
            <form action="adminADDdepartment.php" method="POST" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">Department Name:</label>
                    <div class="controls">
                        <input type="text" class="span3 form-control" placeholder="Department Name" name="deptname" required />
                    </div>
                </div>
                <div class="form-actions d-flex justify-content-center">
                    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="margin-top: 20px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</form>


                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="row-fluid">
                <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT
                    BIOMETRICS</div>
            </div> -->


            <script src="../js/maruti.dashboard.js"></script>

            </body>
<style>
   body{
  font-family: 'Poppins', sans-serif;
  background-image: linear-gradient(190deg, #FFFFFF, #DCF6FF);
background-repeat: no-repeat;
background-image: linear-gradient(190deg, #FFFFFF, #DCF6FF 100vh, #DCF6FF);
height: auto;
}
</style>
</html>