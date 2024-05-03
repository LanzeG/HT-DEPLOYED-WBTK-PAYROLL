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
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
date_default_timezone_set('Asia/Hong_Kong'); 
$adminId = $_SESSION['adminId'];
$adminquery = "SELECT * FROM employees WHERE emp_id= '$adminId'";
$adminqueryexec = mysqli_query($conn,$adminquery) or die ("FAILED TO GET admin INFO ".mysqli_error($conn));
$admininfo = mysqli_fetch_array($adminqueryexec);
if ($admininfo){
  $fname = $admininfo['first_name'];
  $mname = $admininfo['middle_name'];
  $lname = $admininfo['last_name'];

  $name = $fname .' '. $lname;

}


$otID = $_GET['id'];

$currtime = strtotime("now");
$nowdate = date('Y-m-d',$currtime);
$nowtime = date('H:i:s',$currtime);
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
  $otrh = $otinfo['ot_rh'];
  $otsh = $otinfo['ot_sh'];
  $otremarks = $otinfo['ot_remarks'];
  $infoquery = "SELECT last_name,first_name,middle_name,shift_SCHEDULE, weeklyhours, prefix_ID FROM employees WHERE emp_id = '$otemp'";
  $infoqqueryexec = mysqli_query($conn,$infoquery);
  $infofetch = mysqli_fetch_array($infoqqueryexec);

  $ottime = strtotime($otday);
  $otdate = date('Y-m-d',$ottime);

  $otquery1 = "SELECT * FROM time_keeping WHERE emp_id= '$otemp' and timekeep_day = '$otday'";
  $otqueryexec1 = mysqli_query($conn,$otquery1) or die ("FAILED TO GET OT INFO ".mysqli_error($conn));
  $otinfo1 = mysqli_fetch_array($otqueryexec1);

  if ($otinfo1)
  {
    $timein = $otinfo1['in_morning'];
    $timeout = $otinfo1['out_afternoon'];
  }else{
    $timein="No data";
    $timein='No data';
  }


  if($infofetch){

    $lastname =$infofetch['last_name'];
    $firstname = $infofetch['first_name'];
    $middlename =  $infofetch['middle_name'];
    $shiftsched = $infofetch['shift_SCHEDULE'];
    $weeklyhours = $infofetch['weeklyhours'];
    $idprefix = $infofetch['prefix_ID'];

    $empidinfo = "$idprefix$otemp";
    $fullname = "$lastname, $firstname $middlename";
  }

$otDate = new DateTime($otday);

// Set the date to Monday of the same week
$otDate->modify('monday');

// Get the date for Monday and Friday of the same week
$monday = $otDate->format('Y-m-d');
$friday = $otDate->modify('+4 days')->format('Y-m-d');


// Query to get the rendered hours for Monday to Friday of the same week
$query = "SELECT SUM(hours_work) AS total_rendered_hours 
          FROM time_keeping 
          WHERE timekeep_day BETWEEN '$monday' AND '$friday' AND emp_id='$otemp'";

$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalRenderedHours = $row['total_rendered_hours'];
    // echo "Total rendered hours from Monday to Friday: $totalRenderedHours";
} else {
    // echo "Error: " . mysqli_error($conn);
}
}

if(isset($_POST['submit_btn'])){

  $actionupdate = $_POST['otaction'];
  $actioninfoupdate = $_POST['newotinfo'];
  echo $actionupdate;
  echo $actioninfoupdate;
  if ($actionupdate == "Approve"){

    $otremark = "Approved";
    $showSweetAlert = true;
    $updatetimekeep = "UPDATE TIME_KEEPING SET overtime_hours = '$othours', ot_rh ='$otrh', ot_sh='$otsh' WHERE emp_id = '$otemp' AND timekeep_day = '$otday'";
    $updatetimekeepexec = mysqli_query($conn,$updatetimekeep) or die ("FAILED TO APPROVE ".mysqli_error($conn));
   
  } else if ($actionupdate == "Reject"){
    $otremark = "Rejected";
    // $_SESSION['OTAPPROVAL'] = "OVERTIME REJECTED.";
    $showSweetAlert = true;
   
  } else if ($actionupdate ==""){
    $otremark = "Pending";
    // $_SESSION['OTAPPROVAL'] = "OVERTIME PENDING.";
    $showSweetAlert = true;
  
  }else if ($actionupdate =="Allow"){
    $otremark = "Allowed";
    // $_SESSION['OTAPPROVAL'] ="OVERTIME ALLOWED.";
    $showSweetAlert = true;
  }

  $updateot = "UPDATE OVER_TIME SET ot_info = '$actioninfoupdate', ot_remarks = '$otremark', ot_approver = '$name' WHERE ot_ID = '$otID'";
  $updateotexec = mysqli_query($conn,$updateot) or die ("FAILED TO APPROVE/REJECT ".mysqli_error($conn));

  $activityLog = "Changed overtime status ($otremark)";
  $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId', '$name','$activityLog', NOW())";
  $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

  $notificationMessage = "Overtime application updated by Admin: $name";
  $insertNotificationQuery = "INSERT INTO empnotifications (admin_id, adminname, emp_id, message, type, status) VALUES ('$adminId','$name','$otemp', '$notificationMessage','Overtime','unread')";
  mysqli_query($conn, $insertNotificationQuery);

  if ($showSweetAlert) {

      if ($otremark == 'Approved' || $otremark == 'Allowed'){
        $icon = 'success';
      }else if ($otremark == 'Rejected'){
        $icon = 'error';
      }
      else if ($otremark == 'Pending'){
        $icon = 'info';
      }

      echo '<script>
      swal({
          text: "Overtime ' . $otremark . '",
          icon: "' . $icon . '", 
          button: "OK",
      }).then(function() {
          window.location.href = "adminOT.php";
      });
  </script>';
} else {
    header("Location:adminOT.php");
}


?>
  <script>
  alert("<?php echo $actionupdatealert;?>");
  </script>
  <?php
  
}

?>



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
INCLUDE ('NAVBARadmin.php');
?>


<div id="content">
    <div class="span6 title d-flex justify-content-center pt-4">
        <h3>Review Overtime</h3>
        <hr>
    </div>
    <hr>
    <div class="widget-box d-flex justify-content-center pt-4">
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
        <div class="userinfo form-control col-lg-5 col-sm-6">Working Hours:<b> <?php echo $shiftsched; ?></b> / <b><?php echo $weeklyhours; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">Current Rendered Hours: <b> <?php echo $totalRenderedHours; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">DATE OF OVERTIME:<b> <?php echo $otday; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">OVERTIME IN:<b> <?php echo $otin; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">OVERTIME OUT:<b> <?php echo $otout; ?></b></div>
        <div class="userinfo form-control col-lg-5 col-sm-6">TOTAL OVERTIME HOUR/S:<b> <?php echo $othours; ?></b></div>


                <div class="col-12 mt-3">
                    <div class="userinfo form-control">
                        <label for="otinformation">OVERTIME INFORMATION:</label>
                        <textarea id="otinformation" name="newotinfo"><?php echo $otinformation; ?></textarea>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="userinfo">
                        <label class="" for="selectaction">Action:</label>
    </div>

                    <select class="userinfo form-select" id = "selectaction" name="otaction">

                      <option><?php echo $otremarks;?></option>
              <?php if ($nowdate<$otday){ ?>
                      
                      <option>Allow</option>
                      <option>Reject</option> 

              <?php }elseif ($nowdate==$otday && $nowtime<$otin){ ?>

                      <option>Allow</option>
                      <option>Reject</option>

              <?php }elseif ($nowdate==$otday){ ?>

                      <option>Allow</option>
                      <option>Reject</option>

               <?php }elseif ($nowdate>$otday){ ?>

                      <option>Approve</option>
                      <option>Reject</option>

              <?php } ?>


                    </select>
             
                  </span><span><button type="submit" class="btn btn-success" name = "submit_btn">Submit</button></span>
                  </p> 
                  <br>
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
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>


<script src="../../js/maruti.dashboard.js"></script> 

</body>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>
