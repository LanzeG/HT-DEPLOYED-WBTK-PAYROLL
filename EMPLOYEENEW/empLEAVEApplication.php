<!DOCTYPE html>
<html lang="en">
<head>
<title>Leave Application</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../style.css">
<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../jquery-ui-1.12.1/jquery-ui.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script type ="text/javascript">
   $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
<?php

include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$idres =  $_SESSION['empID'];

$lvcountqry = "SELECT * FROM LEAVES WHERE emp_id = '$idres'";
$lvcountexecqry = mysqli_query($conn,$lvcountqry) or die ("FAILED TO COUNT LEAVE AVAILABILITY ".mysqli_error($conn));
$lvcount = mysqli_fetch_array($lvcountexecqry);


if ($lvcount){

  $leavecount = $lvcount['leave_count'];
}

$error = false;

if (isset($_POST['submit_btn'])){
  $lvtypesel = $_POST['lvtype'];
  

  if ($lvtypesel == "Maternity Leave"){
      $lvstartdate = $_POST['lvstart'];
      $lvenddate = $_POST['lvend'];
      $lvinfo = $_POST['newotinfo'];
      $lvstatus = "Pending";
      
      if(empty($lvtypesel)){  

        $error = true;
        $leavetypeerror = "Please select a leave type.";
    
      }
    
      if(empty($lvstartdate)){
        $error = true;
        $leavestartError = "Please provide a date start for your leave.";
    
      }
      if ($_FILES['leave_documents']['error'] == UPLOAD_ERR_OK && !empty($_FILES['leave_documents']['tmp_name'])) {
        $uploadDir = 'uploads/'; // Specify your desired upload directory
        $uploadFile = $uploadDir . basename($_FILES['leave_documents']['name']);
  
        // Move the uploaded file to the destination directory
        if (move_uploaded_file($_FILES['leave_documents']['tmp_name'], $uploadFile)) {
            // File upload successful, store the file information in the session
            $_SESSION['file_path'] = $uploadFile;
        } else {
            echo "Failed to move the uploaded file.";
        }
        }else{
          $uploadFile = '';
        }

        $insertlv = "INSERT INTO LEAVES_APPLICATION (emp_id,leave_type,leave_datestart,leave_dateend,leave_info,leave_status, leave_documents) VALUES ('$idres','$lvtypesel','$lvstartdate','$lvenddate','$lvinfo','$lvstatus','$uploadFile')";
        $insertlvexec = mysqli_query($conn,$insertlv) or die ("FAILED TO APPLY LEAVE ".mysqli_error($conn));
        $notificationMessage = "New leave application submitted by Employee ID: $idres";
        $insertNotificationQuery = "INSERT INTO notifications (emp_id, message, type) VALUES ('$idres', '$notificationMessage','Leave')";
        mysqli_query($conn, $insertNotificationQuery);
    
        logLeave($conn, $idres, true);
        if($insertlvexec){
          if(empty($lvenddate)){
            $lvdays = '1';
          } else {
            $diffInDays = date_diff(date_create($lvstartdate), date_create($lvenddate))->format("%a");
    
            // Check if weekends should be excluded
            if ($diffInDays > 0) {
                $weekendCount = 0;
    
                for ($i = 0; $i <= $diffInDays; $i++) {
                    $currentDate = date('Y-m-d', strtotime($lvstartdate . " +$i days"));
    
                    // Check if the current day is a weekend (Saturday or Sunday)
                    if (date('N', strtotime($currentDate)) >= 6) {
                        $weekendCount++;
                    }
                }
    
                // Subtract weekends from the total difference
                $lvdays = $diffInDays - $weekendCount +1;
            } else {
                // If the difference is zero or negative, set leavedays to 0
                $lvdays = 0;
            }
    
            $lvdayscount = "SELECT *, $lvdays as LEAVEDAYS FROM LEAVES_APPLICATION where emp_id = '$idres' AND leave_datestart = '$lvstartdate'";
            $lvdayscountexec = mysqli_query($conn,$lvdayscount);
            $lvdaysarray = mysqli_fetch_array($lvdayscountexec);
            if ($lvdaysarray){
    
              $lvdays = $lvdaysarray['LEAVEDAYS'];
            }
          }
    

            $updateleavedays = "UPDATE LEAVES_APPLICATION SET leave_days = '$lvdays' WHERE emp_id = '$idres' AND leave_datestart = '$lvstartdate'";
            $updateleavedaysexec = mysqli_query($conn,$updateleavedays) or die ("FAILED TO UPDATE ".mysqli_error($conn));
          
    
          ?>
          <script>
          document.addEventListener('DOMContentLoaded', function() {
              swal({
                //  title: "Good job!",
                text: "Leave Application Submitted",
                icon: "success",
                button: "OK",
                }).then(function() {
                  window.location.href = 'LeaveApplication.php'; // Replace 'your_new_page.php' with the actual URL
              });
          });
        </script>
            <?php
      }else{
        logLeave($conn, $idres, false);
      }


      
  } else {

  if ($leavecount>0){

    $lvtypesel = $_POST['lvtype'];
    $lvstartdate = $_POST['lvstart'];
    $lvenddate = $_POST['lvend'];
    $lvinfo = $_POST['newotinfo'];
    $lvstatus = "Pending";

  if(empty($lvtypesel)){  

    $error = true;
    $leavetypeerror = "Please select a leave type.";

  }

  if(empty($lvstartdate)){
    $error = true;
    $leavestartError = "Please provide a date start for your leave.";

  }

  if(!$error){


    if ($_FILES['leave_documents']['error'] == UPLOAD_ERR_OK && !empty($_FILES['leave_documents']['tmp_name'])) {
      $uploadDir = 'uploads/'; // Specify your desired upload directory
      $uploadFile = $uploadDir . basename($_FILES['leave_documents']['name']);

      // Move the uploaded file to the destination directory
      if (move_uploaded_file($_FILES['leave_documents']['tmp_name'], $uploadFile)) {
          // File upload successful, store the file information in the session
          $_SESSION['file_path'] = $uploadFile;
      } else {
          echo "Failed to move the uploaded file.";
      }
  }else{
    $uploadFile = '';
  }

    $insertlv = "INSERT INTO LEAVES_APPLICATION (emp_id,leave_type,leave_datestart,leave_dateend,leave_info,leave_status, leave_documents) VALUES ('$idres','$lvtypesel','$lvstartdate','$lvenddate','$lvinfo','$lvstatus','$uploadFile')";
    $insertlvexec = mysqli_query($conn,$insertlv) or die ("FAILED TO APPLY LEAVE ".mysqli_error($conn));
    $notificationMessage = "New leave application submitted by Employee ID: $idres";
    $insertNotificationQuery = "INSERT INTO notifications (emp_id, message, type) VALUES ('$idres', '$notificationMessage','Leave')";
    mysqli_query($conn, $insertNotificationQuery);

    logLeave($conn, $idres, true);

    if($insertlvexec){
      if(empty($lvenddate)){
        $lvdays = '1';
      } else {
        $diffInDays = date_diff(date_create($lvstartdate), date_create($lvenddate))->format("%a");

        // Check if weekends should be excluded
        if ($diffInDays > 0) {
            $weekendCount = 0;

            for ($i = 0; $i <= $diffInDays; $i++) {
                $currentDate = date('Y-m-d', strtotime($lvstartdate . " +$i days"));

                // Check if the current day is a weekend (Saturday or Sunday)
                if (date('N', strtotime($currentDate)) >= 6) {
                    $weekendCount++;
                }
            }

            // Subtract weekends from the total difference
            $lvdays = $diffInDays - $weekendCount +1;
        } else {
            // If the difference is zero or negative, set leavedays to 0
            $lvdays = 0;
        }

        $lvdayscount = "SELECT *, $lvdays as LEAVEDAYS FROM LEAVES_APPLICATION where emp_id = '$idres' AND leave_datestart = '$lvstartdate'";
        $lvdayscountexec = mysqli_query($conn,$lvdayscount);
        $lvdaysarray = mysqli_fetch_array($lvdayscountexec);
        if ($lvdaysarray){

          $lvdays = $lvdaysarray['LEAVEDAYS'];
        }
      }

      if ($leavecount - $lvdays <0){
        $deleteapplication ="DELETE FROM LEAVES_APPLICATION WHERE emp_id = '$idres' AND leave_datestart = '$lvstartdate'";
        $deleteleavedaysexec = mysqli_query($conn,$deleteapplication) or die ("FAILED TO UPDATE ".mysqli_error($conn));
            $errType = "danger";
            ?><script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                  // title: "Data ",
                  text: "Not enough leave credits..",
                  icon: "error",
                  button: "Try Again",
                  }).then(function() {
                      window.location.href = 'empLEAVEApplication.php'; // Replace 'your_new_page.php' with the actual URL
                  });
                });
            </script>
            <?php
          

      }else{
        $updateleavedays = "UPDATE LEAVES_APPLICATION SET leave_days = '$lvdays' WHERE emp_id = '$idres' AND leave_datestart = '$lvstartdate'";
        $updateleavedaysexec = mysqli_query($conn,$updateleavedays) or die ("FAILED TO UPDATE ".mysqli_error($conn));
      

      ?>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
          swal({
            //  title: "Good job!",
            text: "Leave Application Submitted",
            icon: "success",
            button: "OK",
            }).then(function() {
              window.location.href = 'LeaveApplication.php'; // Replace 'your_new_page.php' with the actual URL
          });
      });
    </script>
        <?php
      } 
  }else{
    logLeave($conn, $idres, false);
  }



  }

} else {
    $errType = "danger";
    // $_SESSION['addprofilenotif'] = "Something went wrong. Make sure you accomplish all the required fields.";
    ?><script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
          // title: "Data ",
          text: "Not enough leave credits..",
          icon: "error",
          button: "Try Again",
          }).then(function() {
              window.location.href = 'empLEAVEApplication.php'; // Replace 'your_new_page.php' with the actual URL
          });
        });
    </script>
    <?php
  
}
  }
}

?>
<script>
document.addEventListener("DOMContentLoaded", function () {
   flatpickr("#date", {
    dateFormat: "Y-m-d", // Adjust the date format as needed
  });
});
</script>

<body>

<!--Header-part-->

<?php
INCLUDE ('navbar2.php');
?>


<div id="content">
    <div class="span6 title d-flex justify-content-center pt-4">
        <h3>Leave Application</h3>
        <hr>
    </div>
    <hr>
    </header>

<div class="widget-title">
  <div class="icon"> <i class="icon-align-justify"></i> </div>
</div>
        
<div class="widget-content nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
  <h5>Leave Details</h5>
   <hr>
<div class="widget-content nopadding">
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" enctype="multipart/form-data">
     <div class ="control-group">
        <label class="control-label green-background">Leave Credits: <?php echo $leavecount; ?></label>
<div class="controls"></div>
<style>
  .green-background {
    background-color: green;
    color: #ffff;
    padding: 5px;
    border-radius:5px;
}
</style>
<span class ="badge badge-success"></span>
<br>
 </div>
</div>

<?php
  $leavetypesquery = "SELECT * FROM LEAVES_TYPE";
  $leavetypesexecqry = mysqli_query($conn, $leavetypesquery) or die ("FAILED TO EXECUTE leaves type QUERY ".mysql_error());
?>
  <div class ="control-group">
    <label class="control-label">Leave Type: </label>
      <div class="controls">
        <select name="lvtype" class="form-select">
            <option></option>
            <?php  while($leavechoice = mysqli_fetch_array($leavetypesexecqry)):;?>
            <option><?php echo $leavechoice['lvtype_name'];?></option>
            <?php endwhile; ?>
        </select>
      </div>
  </div>

<div class="row pt-2">
  <div class="col-6">
    <div class ="control-group">
      <label class="control-label">Date Start: </label>
        <div class="controls ">
          <input type="text" class="controls form-select" id="date" name ="lvstart" placeholder="Start Date" value="">
        </div>
    </div>
   </div>
   <div class="col-6">
      <div class ="control-group">
       <label class="control-label">Date End: </label>
          <div class="controls">
            <input type="text" class="controls form-select" id="date" name ="lvend" placeholder="End Date" value="">
              <small>*Provide an end date if leave is more than one day</small>
          </div>
        </div>
  </div>
</div>
<div class ="control-group">
  <label class="control-label">Leave Details:</label>
    <div class = "controls">
      <textarea id="otinformationn" class=" form-control col-lg-5 col-sm-6" value="<?php echo $otinformation;?>" name="newotinfo"></textarea>
    </div>
</div>
<div class="control-group">
  <label class="control-label">Leave Documents:</label>
    <div class="controls">
      <input type="file" name="leave_documents">
    </div>
</div>
<div class="form-actions">
  <button type="submit" class="btn btn-success"  name = "submit_btn" style="float:right;">Submit</button>
    <a href="LeaveApplication.php" class="btn btn-danger col-lg-2 col-sm-2" style="float:right; margin-right: 15px;">Go Back</a>
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
</div>

<script src="../../js/maruti.dashboard.js"></script> 
<script src="../../js/excanvas.min.js"></script> 

<script src="../../js/bootstrap.min.js"></script> 
<script src="../../js/jquery.flot.min.js"></script> 
<script src="../../js/jquery.flot.resize.min.js"></script> 
<script src="../../js/jquery.peity.min.js"></script> 
<script src="../../js/fullcalendar.min.js"></script> 
<script src="../../js/maruti.js"></script> 
<script>
document.addEventListener("DOMContentLoaded", function () {
  flatpickr("#date", {
    dateFormat: "Y-m-d", // Adjust the date format as needed
  });
});
</script>

</body>
</html>
