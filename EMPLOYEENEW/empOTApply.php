<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Home</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- <link rel="stylesheet" href="../../css/bootstrap.min.css" /> -->
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<!-- <link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" /> -->
 <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>



<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$otID = $_GET['id'];
$otquery = "SELECT * FROM OVER_TIME WHERE ot_ID= '$otID'";
$otqueryexec = mysqli_query($conn,$otquery) or die ("FAILED TO GET OT INFO ".mysqli_error($conn));
$otinfo = mysqli_fetch_array($otqueryexec);

if ($otinfo){

  $otemp = $otinfo['emp_id'];
  $otin = $otinfo['ot_time'];
  $otout = $otinfo['ot_timeout'];
  $othours = $otinfo['ot_hours'];
  $otday = $otinfo['ot_day'];
  $otinformation = $otinfo['ot_info'];
  $infoquery = "SELECT last_name,first_name,middle_name,shift_SCHEDULE,prefix_ID FROM employees WHERE emp_id = '$otemp'";
  $infoqqueryexec = mysqli_query($conn,$infoquery);
  $infofetch = mysqli_fetch_array($infoqqueryexec);

  if($infofetch){

    $lastname =$infofetch['last_name'];
    $firstname = $infofetch['first_name'];
    $middlename =  $infofetch['middle_name'];
    $shiftsched = $infofetch['shift_SCHEDULE'];
    $idprefix = $infofetch['prefix_ID'];

    $empidinfo = "$idprefix$otemp";
    $fullname = "$lastname, $firstname $middlename";
  }
}

// ... (your existing code)

if (isset($_POST['submit_btn'])) {
  $action_info_update = $_POST['newotinfo'];
  $ot_remark = "For Approval";

  // Fetch existing overtime information for comparison
  $old_ot_info_query = "SELECT ot_info FROM OVER_TIME WHERE ot_ID = '$otID'";
  $old_ot_info_result = mysqli_query($conn, $old_ot_info_query) or die("FAILED TO FETCH OLD OT INFO " . mysqli_error($conn));
  $old_ot_info_array = mysqli_fetch_array($old_ot_info_result);
  $old_ot_info = $old_ot_info_array['ot_info'];

  // Log the overtime review attempt
  // logOvertimeReview($conn, $otemp, $otID, $old_ot_info, 'Review', false);

  // Check if the information has changed
  $changes_detected = $action_info_update != $old_ot_info;

  $update_ot = "UPDATE OVER_TIME SET ot_info = '$action_info_update', ot_remarks = '$ot_remark' WHERE ot_ID = '$otID'";
  $update_ot_exec = mysqli_query($conn, $update_ot) or die ("FAILED TO APPROVE/REJECT " . mysqli_error($conn));

  if ($update_ot_exec) {
      // Log the review/update based on changes
      $action = $changes_detected ? 'Update' : 'Review';
      logOvertimeReview($conn, $otemp, $action);

      $_SESSION['OTAPPROVAL'] = "OVERTIME FOR APPROVAL.";
      header("Location:empAPPLYOvertime.php");
  }else {$action = $changes_detected ? 'Update' : 'Review';
  logOvertimeReview($conn, $otemp, $action);}

  ?>
  <script>
      alert("<?php echo $action_update_alert; ?>");
  </script>
  <?php
}
?>







<script type ="text/javascript">
  $( function() {
      $( "#holidaypicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  </script>
</head>

<style>
textarea {
  max-width: 100%;
  width: 100%;
  height: auto;
  box-sizing: border-box;

}

 .userinfo {
        margin-bottom: 10px;
    }


</style>
<body>

<!--Header-part-->

<?php
INCLUDE ('navbar2.php');
?>
<div id="content">
    <div class="span6 title d-flex justify-content-center pt-4">
        <h3>Review Overtime</h3>
        <hr>
    </div>
    <hr>

        <div class="widget-title">
            <div class="icon"> <i class="icon-align-justify"></i> </div>
        </div>
        
        <div class="widget-content nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal">
                <br>
                <div>
              

<div class="row justify-content-center">
    <div class="row row1 col-lg-11">
        <div class="userinfo form-control col-lg-5 col-sm-6">Employee ID:<b> <?php echo $empidinfo; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">Name:<b> <?php echo $fullname; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">Working Hours:<b> <?php echo $shiftsched; ?></b></div>
        <!-- <div class="userinfo form-control col-lg-5 col-sm-6">Time In:<b> <?php echo $timein; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">Time Out:<b> <?php echo $timeout; ?></b></div> -->
        <div class="userinfo form-control col-lg-5 col-sm-6">DATE OF OVERTIME:<b> <?php echo $otday; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">OVERTIME IN:<b> <?php echo $otin; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">OVERTIME OUT:<b> <?php echo $otout; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">TOTAL OVERTIME HOUR/S:<b> <?php echo $othours; ?></b></div>
                  <div class = "userinfo form-control col-lg-5 col-sm-6">
                    <label for="otinformation">OVERTIME INFORMATION:</label>
                    <textarea id="otinformationn" value="<?php echo $otinformation;?>" name="newotinfo"><?php echo $otinformation;?></textarea>
                  
                   


                  </div>
                  <a href="newapplyovertime.php" class="btn btn-danger col-lg-2 col-sm-2" style="float:left; margin-right: 15px;">Go Back</a>
            </form>    
            

          </div>
          
        </div>

             
          
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
  <div id="footer" class="span12" style="width: 100%; text-align: center; padding: 10px;"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>


<script src="../js/maruti.dashboard.js"></script> 

</body>
</html>