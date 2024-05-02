<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Home</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.css"/>
<link rel="stylesheet" href="../timepicker/jquery.timepicker.css"/>
<script src="../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../jquery-ui-1.12.1/jquery-ui.js"></script>
<script src="../timepicker/jquery.timepicker.min.js"></script>
<script src="../timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
date_default_timezone_set('Asia/Hong_Kong'); 
session_start();


$error = false;
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

if(isset($_POST['submit_btn'])){

  $pperiodstart = $_POST['dppstart'];
  $pperiodend = $_POST['dppend'];
  $pperiodrange = "$pperiodstart to $pperiodend";
  $date = strtotime($pperiodstart);
  $pperiodyear = date("Y",$date);

  $startdateinit = strtotime($pperiodstart);
  //$startdateconv = strtotime('-3 days',$startdateinit);
  $startdate = date("Y-m-d", $startdateinit);


  $enddateinit = strtotime($pperiodend);
  //$enddateconv = strtotime('-3 days',$enddateinit);
  $cutoff = date("Y-m-d", $enddateinit);

  $pperiodstartdate = new DateTime($pperiodstart);
  $pperiodenddate = new DateTime($pperiodend);
  $payperioddays = $pperiodenddate->diff($pperiodstartdate)->format("%a");
  $pres = ($payperioddays + 1);

  
  if(empty($pperiodstart)){

    $error = true;
    $periodstarterror = "Please enter payroll period start date.";

  }

  if(empty($pperiodend)){

    $error = true;
    $periodenderror = "Please payroll period end date.";

  }

  if(empty($pperiodrange)){

    $error = true;
    $periodrangeerror = "No payroll period range.";

  }

  if(empty($pperiodyear)){

    $error = true;
    $periodyearerror = "No payroll period year.";

  }

  if (empty($pres)){

    $error = true;
    $perioddayserror = "Payroll number of days not specified.";
  }

  

  $pperiodrangecheckqry = "SELECT pperiod_range FROM payperiods where pperiod_range = '$pperiodrange'";
  $pperiodrangecheckexecqry = mysqli_query($conn,$pperiodrangecheckqry);
  $pperiodrangecheckcount = mysqli_num_rows($pperiodrangecheckexecqry);

  if ($pperiodrangecheckcount !=0){
    $error = true;
    $pperiodrangeerror = "Payroll period already exists.";
  }
  
  if (!$error){

    $newshiftqry = "INSERT INTO payperiods (pperiod_start, pperiod_end, pperiod_range, pperiod_year,payperiod_days) VALUES ('$startdate','$cutoff','$pperiodrange','$pperiodyear','$pres')";
    $newshiftqryresult = mysqli_query($conn,$newshiftqry) or die ("FAILED TO CREATE NEW PAYROLL PERIOD ".mysqli_error($conn));
    $activityLog = "Added a new payroll period ($pperiodrange)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName','$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

    if ($newshiftqryresult) {
      ?>
      <script>
 document.addEventListener('DOMContentLoaded', function() {
     swal({
      //  title: "Good job!",
       text: "Payroll Period inserted successfully",
       icon: "success",
       button: "OK",
      }).then(function() {
         window.location.href = 'adminPAYROLLPERIODS.php'; // Replace 'your_new_page.php' with the actual URL
     });
 });
</script>
      <?php
  }
} else {
  ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
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


<?php
INCLUDE ('../navbarAdmin.php');
?>


<div id="content">


  <div class="container-fluid">
    <div class = "row-fluid">
      <span class="span3">
      </span>
    <div class="span6">
      <h3>Add Payroll Period</h3>
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Payroll Period Information</h5>
          </div>

          <div class="widget-content nopadding">
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal">
              
              <!--<div class="control-group">
                <label class="control-label">Shift Schedule :</label>
                <div class="controls">
                  <input type="text" class="span7" placeholder="Shift Schedule" name="shiftsched"/>
                  <span class ="label label-important"><?php echo $shiftschederror; ?></span>
                </div>
              </div>
            -->
              <div class="control-group">
                <label class="control-label">Payroll Period Start:</label>
                <div class="controls">
                <input type="text" class="form-control datepicker" id="start-date" name="dppstart" placeholder="Date" value="<?php echo isset($pperiodstart) ? $pperiodstart : ''; ?>" />
                  <!-- <span class ="label label-important"><?php echo $periodstarterror; ?></span> -->
                </div>
              </div>

               <div class="control-group">
                <label class="control-label">Payroll Period End:</label>
                <div class="controls">
                <input type="text" class="form-control datepicker" id="end-date" name="dppend" placeholder="Date" value="<?php echo isset($pperiodend) ? $pperiodend : ''; ?>" />
                  <!-- <span class ="label label-important"><?php echo $periodenderror; ?></span> -->
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-success" name = "submit_btn" style="float:right;">Submit</button>
              </div>
            </form>
        </div>
    </div>
    
    <div class="row-fluid">
      


    </div>
    <hr>
    <div class="row-fluid">
      
      

    </div>
  </div>
</div>
</div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>


<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>
</body>
</html>
