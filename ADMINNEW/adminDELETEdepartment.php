<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
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
$DELquery = "SELECT * from DEPARTMENT WHERE dept_ID ='$idres'";
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

if (isset($_POST['delete_btn'])) {


    $selquery = "SELECT dept_ID  FROM DEPARTMENT WHERE dept_ID ='$idres'";
    $selresult = mysqli_query($conn, $selquery);
    $selcount = mysqli_num_rows($selresult);
    $activityLog = "Deleted department named ($currdeptname)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
    $updateQuery = "UPDATE employees SET dept_NAME = '' WHERE dept_name = '$currdeptname'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($selcount != 0) {
        $DELquery2 = "DELETE FROM DEPARTMENT WHERE dept_ID = '$idres'";
        $delval = mysqli_query($conn, $DELquery2);

        if ($delval) {
            echo "success";
        } else {
            echo "Error deleting profile.";
        }
    } else {
        echo "Employee Profile does not exist.";
    }
    exit();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
</head>

   

    <?php
    include('navbaradmin.php');
    ?>



<div class="container">
    <div class="title pt-4 d-flex justify-content-center">
        <h3>
            Remove Department
        </h3>
       
    </div>
    <hr>
    <div class="col-8 card shadow mx-auto my-5 p-3 mt-5">
    <form action="adminDELETEMasterfileDept.php?id=<?php echo $idres;?>" method="POST" class="form-horizontal">
        
           <div class="control-group">
             <label class="control-label">Department ID: </label>
             <div class="controls">
               <input type="text" class="span3 form-control" value = "<?php echo $currprefixid;?><?php echo $currdeptid;?>" name="DELCONid" readonly/>
             </div>
           </div>

            <div class="control-group mt-2">
             <label class="control-label">Department Name: </label>
             <div class="controls">
               <input type="text" class="span11 form-control" value = "<?php echo $currdeptname;?>" name="DELCONname" readonly/>
             </div>
           </div>
         </form>
           <div class="form-actions d-flex justify-content-center mt-4">
           <button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" style="float: right;" onclick="confirmDelete()">Delete</button>


             
         </div>
    </div>
</div>


                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT
                BIOMETRICS</div>
        </div>

        <script>
        function confirmDelete() {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this profile!",
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
                                    swal("Profile deleted successfully!", { icon: "success" })
                                        .then(() => {
                                            window.location.href = "adminMasterfileDeptTry.php";
                                        });
                                } else {
                                    swal("Error deleting profile: " + response, { icon: "error" });
                                }
                            }
                        };

                        xhttp.open("POST", "", true);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send("id=<?php echo $idres; ?>&delete_btn=1");
                    } else {
                        swal("Profile is safe!", { icon: "info" });
                    }
                });
        }
    </script>
        <script src="../js/maruti.dashboard.js"></script>

        </body>
        <style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>