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


$lvtypequery = "SELECT * from LEAVES_TYPE where lvtype_ID = '$idres'";
$lvtypeexecquery = mysqli_query($conn, $lvtypequery) or die("FAILED TO SEARCH DB " . mysqli_error($conn));
$lvtypearray = mysqli_fetch_array($lvtypeexecquery);

if ($lvtypearray) {

    $currprefixid = $lvtypearray['lvtype_prefix_id'];
    $currlvtypeid = $lvtypearray['lvtype_ID'];
    $currlvtypename = $lvtypearray['lvtype_name'];
    $currlvtypecount = $lvtypearray['lvtype_count'];
} else {
    $_SESSION['delnotif'] = "Leave information not found.";
} /*2nd else end*/



$error = false;




if (isset($_POST['submit_btn'])) {

    $lvtypeid = $_POST['lvid'];
    $lvtypename = $_POST['lvtypename'];
    // $lvtypecount = $_POST['lvcount'];

    $deptnamequery = "SELECT 	lvtype_name FROM 	leaves_type where lvtype_name = '$lvtypename'";
    $deptnameresultqry = mysqli_query($conn, $deptnamequery);
    $deptnamecount = mysqli_num_rows($deptnameresultqry);

    if ($deptnamecount != 0) {
        $error = true;
        $deptnameerror = "Department already exists.";
    }



    if (!$error) {

        $newleavesqry = "UPDATE leaves_type SET lvtype_name = '$lvtypename' where lvtype_ID = '$idres'";
        $newleavesqryresult = mysqli_query($conn, $newleavesqry) or die("FAILED TO CREATE NEW leaves " . mysql_error());
        $activityLog = "Edited leave from $currlvtypename to $lvtypename";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName',  '$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

        if ($newleavesqryresult) {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    swal({
                        //  title: "Good job!",
                        text: "Leave Updated successfully",
                        icon: "success",
                        button: "OK",
                    }).then(function () {
                        window.location.href = 'adminMasterfileLeave.php'; // Replace 'your_new_page.php' with the actual URL
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
</head>

<body>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


        <?php
        include('navbaradmin.php');
        ?>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" enctype="multipart/form-data">
    <div class="container">
        <div class="title pt-4 d-flex justify-content-center">
            <h3>Update Leaves</h3>
        </div>
        <hr>
        <div class="col-lg-8 col-md-10 col-sm-12 card shadow mx-auto my-5 p-4 mt-5">
            <form>
                <div class="form-group row">
                   
                    <label class="control-label">Leave ID:</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" value="<?php echo $currprefixid . $currlvtypeid; ?>" name="lvid" readonly />
                    </div>
                </div>
                <div class="form-group row" style="margin-top:10px;">
                
                    <label class="control-label">Leave Name:</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" value="<?php echo $currlvtypename; ?>" name="lvtypename" />
                    </div>
                </div>
    
                <div class="form-actions d-flex justify-content-center mt-4">
                <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="margin-bottom: 10px; margin-left: 10px; margin-top: 10px;">Update</button>

                </div>
                </div>
            </form>
        </div>
    </div>
</form>


        <div class="row-fluid">
            <!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT
                BIOMETRICS</div> -->
        </div>
        <script src="../js/maruti.dashboard.js"></script>

</body>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>