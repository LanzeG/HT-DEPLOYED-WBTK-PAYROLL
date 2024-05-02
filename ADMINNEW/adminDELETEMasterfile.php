
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();
$adminId = $_SESSION['adminId'];

$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$idres = $_GET['id'];
$DELquery = "SELECT * FROM employees WHERE emp_id ='$idres'";
$DELselresult = mysqli_query($conn, $DELquery) or die("Failed to search DB. " . mysql_error());
$DELcurr = mysqli_fetch_array($DELselresult);
$DELcount = mysqli_num_rows($DELselresult);

if ($DELcount != 0 && $DELcurr) {
    $currprefixid = $DELcurr['prefix_ID'];
    $currempid = $DELcurr['emp_id'];
    $currlastname = $DELcurr['last_name'];
    $currfirstname = $DELcurr['first_name'];
    $currmiddlename = $DELcurr['middle_name'];
    $currfingerprintid = $DELcurr['fingerprint_id'];
} else {
    $updateselecterror = "Employee information not found.";
}

if (isset($_POST['delete_btn'])) {
  $selquery = "SELECT emp_id FROM employees WHERE emp_id ='$idres'";
  $selresult = mysqli_query($conn, $selquery);
  $selcount = mysqli_num_rows($selresult);

  if ($selcount != 0) {
      $DELquery2 = "DELETE FROM employees WHERE emp_id = '$idres'";
      $delval = mysqli_query($conn, $DELquery2);

      $DELquery3 = "DELETE FROM PAYROLLINFO WHERE emp_id = '$idres'";
      $delval2 = mysqli_query($conn, $DELquery3);

      $activityLog = "Deleted employee profile ($currfirstname $currlastname)";
      $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
      $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

      if ($delval && $delval2) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<!-- <link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" /> -->
 <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</head>
<body>

<?php
INCLUDE ('NAVBARadmin.php');
?>
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
                                        window.location.href = "adminMasterfileTry.php";
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



<div id="content" style="text-align:center ">

<div class="row m-4">
 
 <div class="title text-center pt-2">
<h3>DELETE EMPLOYEE DATA</h3>
<HR></HR>
</div>
</div>

          </div>
          <div class="widget-content nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
          <form action="" method="POST" class="form-horizontal">
           
              <div class="control-group">
                <label class="control-label">Employee ID: </label>
                <div class="controls">
                  <input type="text" class="span3 form-control" value = "<?php echo $currprefixid;?><?php echo $currempid;?>" name="DELCONid" readonly/>
                </div>
              </div>

               <div class="control-group">
                <label class="control-label">Name: </label>
                <div class="controls"> 
                  <input type="text" class="span11 form-control" value = "<?php echo $currlastname;?>, <?php echo $currfirstname;?> <?php echo $currmiddlename;?>" name="DELCONname" readonly/>
                </div>
              </div>
              
              <div class="form-actions control-group pt-3 text-center" >
            <button type="button" class="btn btn-danger" style="float:right;" onclick="confirmDelete()">Delete</button>
              </div>

        </div>
    </div>

    <div class="row-fluid">
      


    </div>

    <div class="row-fluid">
      
      </div>

    </div>
  </div>
</div>
</div>
</div>

</div>

<script src="../js/maruti.dashboard.js"></script> 



</body>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>
