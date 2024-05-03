<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Home</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

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
//for checking if there are 5 absent
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
$otquery = "SELECT * FROM leaves_application WHERE la_id= '$otID'";
$otqueryexec = mysqli_query($conn,$otquery) or die ("FAILED TO GET OT INFO ".mysqli_error($conn));
$otinfo = mysqli_fetch_array($otqueryexec);

if ($otinfo){

  $otemp = $otinfo['emp_id'];
  $leavestart = $otinfo['leave_datestart'];
  $leaveend = $otinfo['leave_dateend'];
  $leavetype = $otinfo['leave_type'];
  $leavedays = intval($otinfo['leave_days']);
  $leaveinformation = $otinfo['leave_info'];
  $leavestatus = $otinfo['leave_status'];
  $leave_document = $otinfo['leave_documents'];
  
  $infoquery = "SELECT last_name,first_name,middle_name, prefix_ID FROM employees WHERE emp_id = '$otemp'";
  $infoqqueryexec = mysqli_query($conn,$infoquery);
  $infofetch = mysqli_fetch_array($infoqqueryexec);
  
  if($infofetch){

    $lastname =$infofetch['last_name'];
    $firstname = $infofetch['first_name'];
    $middlename =  $infofetch['middle_name'];
    $idprefix = $infofetch['prefix_ID'];

    $empidinfo = "$idprefix$otemp";
    $fullname = "$lastname, $firstname $middlename";
  }
}

if(isset($_POST['submit_btn'])){

  $actionupdate = $_POST['otaction'];
  $actioninfoupdate = $_POST['newotinfo'];
  
  if ($actionupdate == "Approve"){

    if($leavetype=="Vacation Leave"){

      if ($leavedays == 1){
        $lvdeduct = '1';
        $updatelvcount = "UPDATE LEAVES SET leave_count =(leave_count - '$lvdeduct') WHERE emp_id = '$otemp' AND leaves_year = YEAR(CURDATE())";
        $updatelvcountexecqry = mysqli_query($conn,$updatelvcount) or die ("FAILED TO DEDUCT LEAVES ".mysqli_query($conn));
    
        $updatetimekeep = "INSERT INTO time_keeping (emp_id,in_morning,out_afternoon,lv_hours,hours_work,timekeep_day,timekeep_remarks) VALUES ('$otemp','00:00:00','00:00:00','9','9','$leavestart','$leavetype')";               
        $updatetimekeepexec = mysqli_query($conn,$updatetimekeep) or die ("FAILED TO APPROVE ".mysqli_error($conn));

      } else if ($leavedays>1){
        $lvdeduct = $leavedays;
        $updatelvcount = "UPDATE LEAVES SET leave_count =(leave_count - '$lvdeduct') WHERE emp_id = '$otemp' AND leaves_year = YEAR(CURDATE())";
        $updatelvcountexecqry = mysqli_query($conn,$updatelvcount) or die ("FAILED TO DEDUCT LEAVES ".mysqli_query($conn));
        $leavedate = date('Y-m-d',strtotime("$leavestart"));
      
        for ($x = 1; $x <= $leavedays;) {
          if (date('N', strtotime($leavedate)) >= 6) {
              $leavedate = date('Y-m-d', strtotime("$leavedate + 1 day"));
              continue; // Skip the weekend and continue with the next iteration
          }
          $x++;
      
          $cntleave = "SELECT * FROM time_keeping WHERE emp_id = '$otemp' AND timekeep_day = '$leavedate'";
          $cntleaveexec = mysqli_query($conn, $cntleave) or die ("FAILED TO CHECK LEAVE DAY " . mysqli_query($conn));
          $cntleaverow = mysqli_num_rows($cntleaveexec);
      
          if ($cntleaverow != 1) {
              $updatetimekeep = "INSERT INTO time_keeping (emp_id, in_morning, out_afternoon, lv_hours, hours_work, timekeep_day, timekeep_remarks) VALUES ('$otemp','00:00:00','00:00:00','9','9','$leavedate','$leavetype')";
              $updatetimekeepexec = mysqli_query($conn, $updatetimekeep) or die ("FAILED TO APPROVE " . mysqli_error($conn));
              $leavedate = date('Y-m-d', strtotime("$leavedate + 1 day"));
          }
        }
      
        }
    }else if ($leavetype=="Sick Leave") {

        if ($leavedays == 1){
          $lvdeduct = '1';
          $updatelvcount = "UPDATE LEAVES SET leave_count =(leave_count - '$lvdeduct') WHERE emp_id = '$otemp' AND leaves_year = YEAR(CURDATE())";
          $updatelvcountexecqry = mysqli_query($conn,$updatelvcount) or die ("FAILED TO DEDUCT LEAVES ".mysqli_query($conn));
  
          $updatetimekeep = "INSERT INTO time_keeping (emp_id,in_morning,out_afternoon,lv_hours,hours_work,timekeep_day,timekeep_remarks) VALUES ('$otemp','00:00:00','00:00:00','9','9','$leavestart','$leavetype')";               
          $updatetimekeepexec = mysqli_query($conn,$updatetimekeep) or die ("FAILED TO APPROVE ".mysqli_error($conn));

          $deleteQuery = "DELETE FROM absences WHERE emp_id = '$otemp' AND absence_date = '$leavestart'";

          // Execute the query
          $result = $conn->query($deleteQuery);
        } else if ($leavedays>1){

          $lvdeduct = $leavedays;
          $updatelvcount = "UPDATE LEAVES SET leave_count =(leave_count - '$lvdeduct') WHERE emp_id = '$otemp' AND leaves_year = YEAR(CURDATE())";
          $updatelvcountexecqry = mysqli_query($conn,$updatelvcount) or die ("FAILED TO DEDUCT LEAVES ".mysqli_query($conn));
          $leavedate = date('Y-m-d',strtotime("$leavestart"));

          for ($x = 1; $x <= $leavedays;) {
            if (date('N', strtotime($leavedate)) >= 6) {
                $leavedate = date('Y-m-d', strtotime("$leavedate + 1 day"));
                continue; // Skip the weekend and continue with the next iteration
            }
            $x++;

           $cntleave = "SELECT * FROM time_keeping WHERE emp_id = '$otemp' AND timekeep_day = '$leavedate'";
           $cntleaveexec = mysqli_query($conn,$cntleave) or die ("FAILED TO CHECK LEAVE DAY ".mysqli_query($conn));
           $cntleaverow = mysqli_num_rows($cntleaveexec);
           $deleteQuery = "DELETE FROM absences WHERE emp_id = '$otemp' AND absence_date = '$leave_date'";

            // Execute the query
            $result = $conn->query($deleteQuery);
      
            if ($cntleaverow !=1){
                $updatetimekeep = "INSERT INTO time_keeping (emp_id,in_morning,out_afternoon,lv_hours,hours_work,timekeep_day,timekeep_remarks) VALUES ('$otemp','00:00:00','00:00:00','9','9','$leavedate','$leavetype')";               
                $updatetimekeepexec = mysqli_query($conn,$updatetimekeep) or die ("FAILED TO APPROVE ".mysqli_error($conn));
    
    
                $leavedate = date('Y-m-d',strtotime("$leavedate+1 day"));
            }
         }
       } 
    }else {
      $lvdeduct = $leavedays;
      $leavedate = date('Y-m-d', strtotime("$leavestart"));
  
      for ($x = 1; $x <= $leavedays;) {
        if (date('N', strtotime($leavedate)) >= 6) {
            $leavedate = date('Y-m-d', strtotime("$leavedate + 1 day"));
            continue; // Skip the weekend and continue with the next iteration
        }
        $x++;
          
          echo "Leave day: $leavedate (Loop counter: $x) <br>"; // Echoing the leave day and loop counter
          
          $cntleave = "SELECT * FROM time_keeping WHERE emp_id = '$otemp' AND timekeep_day = '$leavedate'";
          $cntleaveexec = mysqli_query($conn, $cntleave) or die("FAILED TO CHECK LEAVE DAY " . mysqli_query($conn));
          $cntleaverow = mysqli_num_rows($cntleaveexec);
  
          if ($cntleaverow != 1) {
              $updatetimekeep = "INSERT INTO time_keeping (emp_id,in_morning, out_afternoon,lv_hours,hours_work,timekeep_day,timekeep_remarks) VALUES ('$otemp','00:00:00','00:00:00','9','9','$leavedate','$leavetype')";
              $updatetimekeepexec = mysqli_query($conn, $updatetimekeep) or die("FAILED TO APPROVE " . mysqli_error($conn));
  
              echo "Leave day added for date: $leavedate <br>"; // Echoing the leave day added
              $leavedate = date('Y-m-d', strtotime("$leavedate+1 day"));
          }
      }
  }
    $otremark = "Approved";
    $showSweetAlert = true;
  } else if ($actionupdate == "Reject"){
    $otremark = "Rejected";
    $showSweetAlert = true;
  } else if ($actionupdate ==""){
    $otremark = "Pending";
    $showSweetAlert = true;
  }

  $updateot = "UPDATE leaves_application SET leave_info = '$actioninfoupdate', leave_status = '$otremark', leave_approver = '$name' WHERE la_id = '$otID'";
  $updateotexec = mysqli_query($conn,$updateot) or die ("FAILED TO APPROVE/REJECT ".mysqli_error($conn));

  $activityLog = "Changed leave status ($otremark)";
  $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
  $adminActivityResult = mysqli_query($conn, $adminActivityQuery);


  $notificationMessage = "Leave application updated by Admin ID: $adminFullName";
  $insertNotificationQuery = "INSERT INTO empnotifications (admin_id,adminname,emp_id, message, type, status) VALUES ('$adminId', '$adminFullName', '$otemp','$notificationMessage','Leave','unread')";
  mysqli_query($conn, $insertNotificationQuery);

  if ($showSweetAlert) {

    if ($otremark == 'Approved' || $otremark == 'Allowed'){
      $icon = 'success';
    }else if ($otremark == 'Rejected'){
      $icon = 'error';
    }
    else if ($otremark == "Pending"){
      $icon = 'info';
    }

?>
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       swal({
        //  title: "Good job!",
         text: "Leave <?php echo $otremark; ?>",
         icon: "<?php echo $icon; ?>",
         button: "OK",
        }).then(function() {
           window.location.href = 'adminLeaves.php'; // Replace 'your_new_page.php' with the actual URL
       });
   });
</script>

<?php
} else {
  header("Location:adminLEAVES.php");
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
INCLUDE ('navbarAdmin.php');
?>


<div id="content">
    <div class="span6 title d-flex justify-content-center pt-4">
      <h3>REVIEW LEAVE</h3>
    </div>
    <div class="widget-box d-flex justify-content-center">
      <div class="widget-title">
        <div class="icon"> <i class="icon-align-justify"></i></div>
      </div>
        
    <div class="widget-content nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
      <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal">
        <div class="labels">
          <div class="row">
            <div class="col-lg-6 col-md-12">
              <div class="control-group ">
                <label class="control-label">Employee ID:</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $empidinfo;?>" name="employeeID" readonly/>
                  </div>         
              </div>
            </div>

            <div class="col-lg-6 col-md-12">
              <div class="control-group ">
                <label class="control-label">Employee Name:</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $fullname;?>" name="employeeName" readonly/>
                  </div>         
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="control-group ">
                <label class="control-label">Leave Type:</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $leavetype;?>" name="leavetype" readonly/>
                  </div>         
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="control-group ">
                <label class="control-label">Leave Days</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $leavedays;?>" name="leavedays" readonly/>
                  </div>         
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="control-group ">
                <label class="control-label">Start Date:</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $leavestart;?>" name="sdate" readonly/>
                  </div>         
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="control-group ">
                <label class="control-label">End Date:</label>
                  <div class="controls">
                    <input type="text" class="span11 form-control" value ="<?php echo $leaveend;?>" name="edate" readonly/>
                  </div>         
              </div>
            </div>
          </div>
        </div>

<p>
  <div class="userinfo" id="leavedocument-preview">
  <?php
    if (!empty($leave_document)) {
      $modifiedPath = str_replace("uploads/", "../EmployeeNew/uploads/", $leave_document);
      // Check the file type to determine how to display the preview
       $fileType = pathinfo($modifiedPath, PATHINFO_EXTENSION);

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        // If it's an image, display an image tag
          echo '<img src="' . $modifiedPath . '" style="max-width: 100%;" alt="Leave Document Preview">';
        } else {
        // Display a generic link for other file types
          echo '<a href="' . $modifiedPath . '" target="_blank">View Leave Document</a>';
        }
    }
  ?>
  </div>

  <div class = "userinfo">
    <label for="otinformation">LEAVE DETAILS:</label>
      <textarea id="otinformationn" value="<?php echo $leaveinformation;?>" name="newotinfo"><?php echo $leaveinformation;?></textarea>
  </div>
    <span class="userinfo">
      <label class = "userinfo" for ="selectaction">Action:</label>
        <select class="userinfo form-select" id = "selectaction" name="otaction">
          <option><?php echo $leavestatus;?></option>
          <option>Approve</option>
          <option>Reject</option> 
        </select>
             
      <div class="button d-flex justify-content-center" >
        <button type="submit" class="btn btn-success" name = "submit_btn">Submit</button>
      </div>
  </span>
</p> 
<br>
</form>        
</div>
</div>
</div>
</div>
 
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>
