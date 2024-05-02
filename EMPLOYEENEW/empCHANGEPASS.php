<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$uname = $_SESSION['uname'];


$getinfoqry = "SELECT * from employees WHERE user_name = '$uname'";
$getinfoexecqry = mysqli_query($conn,$getinfoqry) or die ("FAILED TO GET INFORMATION ".mysqli_error($conn));
$getinfoarray = mysqli_fetch_array($getinfoexecqry);
$getinforows = mysqli_num_rows($getinfoexecqry);
if ($getinfoarray && $getinforows !=0){

 $currprefixid = $getinfoarray['prefix_ID'];
 $currempid = $getinfoarray['emp_id'];
        $currfingerprintid = $getinfoarray['fingerprint_id'];
        $currusername = $getinfoarray['user_name'];
        $currlastname = $getinfoarray['last_name'];
        $currpassword = $getinfoarray['pass_word'];
        $currfirstname = $getinfoarray['first_name'];
        $currmiddlename = $getinfoarray['middle_name'];
        $currdateofbirth = $getinfoarray['date_of_birth'];
        $curraddress = $getinfoarray['emp_address'];
        $currnationality = $getinfoarray['emp_nationality'];
        $currdeptname = $getinfoarray['dept_NAME'];
        // $currshiftsched = $getinfoarray['shift_SCHEDULE'];
        $currcontact = $getinfoarray['contact_number'];
        // $currdatehired = $getinfoarray['date_hired'];
        // $currdateregularized = $getinfoarray['date_regularized'];
        // $currdateresigned = $getinfoarray['date_resigned'];
        $currimg = $getinfoarray['img_tmp'];
$_SESSION['empID'] = $currempid;

}


$error = false;

if(isset($_POST['submit_btn'])){

  $currpass = $_POST['currpass'];
  $newpass = $_POST['newpass'];
  $newpass2 = $_POST['newpass2'];
  if($currpass != $currpassword){
    $error = true;
    $passwordError = "Password does not match the current password.";
  }

  if ($newpass!=$newpass2){
    $error = true;
    $newpasswordError = "Passwords do not match.";

  }


  if (!$error) {
    $changepassquery = "UPDATE employees SET pass_word = '$newpass2' WHERE emp_id = '$currempid'";
    $changepassqueryexec = mysqli_query($conn, $changepassquery) or die("FAILED TO CHANGE PASS " . mysqli_error($conn));

    // Log the password change attempt or success
    logPasswordChange($conn, $currempid, $changepassqueryexec);

    ?><script>
    document.addEventListener('DOMContentLoaded', function() {
         Swal.fire({
            // title: "Good job!",
            text: "Password Update Successfully",
            icon: "success",
            timer: 5000,
            timerProgressBar: true,
            toast: true,
            position: 'center',
            showCancelButton: false,
            showConfirmButton: false
         }).then(function() {
            window.location.href = 'try.php'; // Replace 'your_new_page.php' with the actual URL
        });
    });
 </script>
 <?php
} else {
  ?><script>
  document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong.',
                        timer: 5000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'center',
                        showConfirmButton: false
      });
  }); </script>
  <?php
  logPasswordChange($conn, $currempid, false);
}
}

?>

<script type="text/javascript">
    $(function () {
        $("#holidaypicker").datepicker({ dateFormat: 'yy-mm-dd' });
    });
</script>
</head>

<body>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/fullcalendar.css" />
    <link rel="stylesheet" href="../../css/maruti-style.css" />
    <link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" />
    <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
    <script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
    <script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <?php
    include('navbar2.php');
  ?>

<div id="content">
  <div class="container-fluid justify-content: center pt-5">
    <div class = "row-fluid">
      <span class ="span3">
     </span>
    <span class="span6 d-flex justify-content-center">
      <h3>Change Password</h3>
    </span>
  </div>
  <div class="row-fluid">
    <div class = "span3">
    </div>
    <div class ="span6">
      <div class="widget-box">
        <div class = "widget-title"><span class="icon"><i class ="icon-user"></i></span>
       <hr>
        </div>

        <div class="widget-content nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
          <form method = "post" action="<?php $_SERVER['PHP_SELF']; ?>">
            
            <div class="control-group">
                <label class="control-label ">Enter Current Password:</label>
                    <div class="controls">
                      <input type="password" class="span11 form-control" placeholder="" name="currpass"/>
                    </div>
            </div>

             <div class="control-group">
                <label class="control-label">Enter New Password:</label>
                    <div class="controls">
                      <input type="password" class="span11 form-control" placeholder="" name="newpass"/>
                    </div>
            </div>

             <div class="control-group">
                <label class="control-label">Enter New Password Again:</label>
                    <div class="controls">
                      <input type="password" class="span11 form-control" placeholder="" name="newpass2"/>
                    </div>
            </div>

            <div class="form-actions pt-3">
                  <button type="submit" class="btn btn-success" name = "submit_btn" style="float:right;">Update</button>
                </div>
          </form>
        </div>
      </div>
    </div>
    <div class ="span3">
    </div>  
  </div>
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
</body>
</html>