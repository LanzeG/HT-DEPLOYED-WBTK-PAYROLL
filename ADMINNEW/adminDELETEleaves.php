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

$idres = $_GET['id'];
// $DELquery = "SELECT * from SHIFT WHERE shift_ID ='$idres'";
$DELquery = "SELECT * FROM leaves_type WHERE lvtype_ID ='$idres'";
$DELselresult = mysqli_query($conn, $DELquery) or die("Failed to search DB. " . mysql_error());
$DELcurr = mysqli_fetch_array($DELselresult);
$DELcount = mysqli_num_rows($DELselresult);

if ($DELcount != 0 && $DELcurr) {

    $currprefixid = $DELcurr['lvtype_prefix_id'];
    $currleaveid = $DELcurr['lvtype_ID'];
    $currleavename = $DELcurr['lvtype_name'];
    $currleavecount = $DELcurr['lvtype_count'];
} else {
    $_SESSION['delnotif'] = "Leave information not found.";
} /*2nd else end*/

if (isset($_POST['delete_btn'])) {


    $selquery = "SELECT lvtype_ID FROM leaves_type WHERE lvtype_ID ='$idres'";
    $selresult = mysqli_query($conn, $selquery);
    $selcount = mysqli_num_rows($selresult);
    $activityLog = "Deleted leave named ($currleavename)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName',  '$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

    if ($selcount != 0) {
        $DELquery2 = "DELETE FROM leaves_type WHERE lvtype_ID = '$idres'";
        $delval = mysqli_query($conn, $DELquery2);



        /**$auditinfo = $idres+" Profile deleted";

                 $auditquery = "INSERT INTO audittrail (emp_id, audit_info) VALUES ('$empid','$auditinfo')";
                  $auditresult = mysqli_query($conn,$auditquery) or die(mysql_error());  
      */
        if ($delval) {
            echo "success";
        } else {
            echo "Error deleting profile.";
        }
    } else {
        echo "Employee Profile does not exist.";
    }
    exit(); // Ensure nothing else is sent in the response
}

?>

<script>
    function confirmDelete() {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this Leave!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = this.responseText.trim();
                            if (response === "success") {
                                swal("Leave deleted successfully!", { icon: "success" })
                                    .then(() => {
                                        window.location.href = "adminMasterfileLeave.php";
                                    });
                            } else {
                                swal("Error deleting Leave: " + response, { icon: "error" });
                            }
                        }
                    };

                    xhttp.open("POST", "", true);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send("id=<?php echo $idres; ?>&delete_btn=1");
                } else {
                    swal("Leave is safe!", { icon: "info" });
                }
            });
    }
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

    <?php
    include('navbarAdmin.php');
    ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idres; ?>" method="POST" class="form-horizontal" onsubmit="confirmDelete(); return false;" enctype="multipart/form-data">
    <div class="container">
        <div class="title pt-4 d-flex justify-content-center">
            <h3>Delete Leaves</h3>
        </div>
        <hr>
        <div class="col-lg-8 col-md-10 col-sm-12 card shadow mx-auto my-5 p-3 mt-5">
            <form>
            <div class="form-group row">
                      
                        <label class="control-label">Leave ID:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" value="<?php echo $currprefixid . $currleaveid; ?>" name="DELCONid" readonly />
                        </div>
                    </div>
            
                    <div class="form-group row" style="margin-top: 10px;">
                    
                        <label class="control-label">Leave Name:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" value="<?php echo $currleavename; ?>" name="DELCONname" readonly />
                        </div>
                    </div>
           
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-center">
                    <button type="submit" class="inline-block bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md border border-red-500 hover:border-red-600 transition duration-300 ease-in-out" name="submit_btn">Delete</button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</form>

    <div class="row-fluid">
        <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT
            BIOMETRICS</div>
    </div>
    <script src="../js/maruti.dashboard.js"></script>

    </body>
    <style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>