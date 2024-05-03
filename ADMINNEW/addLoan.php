<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Home</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$adminId = $_SESSION['adminId'];
$error = false;

//for act log hehehehehe
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
$selectedLoantypeorg ='';

if (isset($_POST['search_btn1'])){
  $selectedLoantype = $_POST["loantype"];
  // Separate $selectedLoantype into loantype_id and loantype
  list($selectedLoantypeorg, $selectedLoantypeName) = explode('|', $selectedLoantype);
  $_SESSION['selectedLoantype'] = $selectedLoantypeName;
  $_SESSION['selectedLoanorg'] = $selectedLoantypeorg ?? '';

  $lastname1 = $_POST['lastname'];

  if ($selectedLoantypeorg == 'GSIS'){
    $gsisidnoquery = "SELECT * FROM employees where last_name='$lastname1'";
    $gsisidnoexecqry = mysqli_query($conn,$gsisidnoquery);
    $gsisidnocount = mysqli_num_rows($gsisidnoexecqry);
    $gsisidnoarray = mysqli_fetch_array($gsisidnoexecqry);
    if ($gsisidnoarray){
   
      $gsisempid = $gsisidnoarray['emp_id'];
      $last_name = $gsisidnoarray['last_name'];
      $firstname1 = $gsisidnoarray['first_name'];
      $middlename1 = $gsisidnoarray['middle_name'];
      $gsis = $gsisidnoarray['GSIS_idno'];

    }else{
      // echo"<script>alert('heafsallo')</script>";
    }
    
  }
  else if($selectedLoantypeorg == 'PAGIBIG'){
    $gsisidnoquery = "SELECT * FROM employees where last_name='$lastname1'";
    $gsisidnoexecqry = mysqli_query($conn,$gsisidnoquery);
    $gsisidnocount = mysqli_num_rows($gsisidnoexecqry);
    $gsisidnoarray = mysqli_fetch_array($gsisidnoexecqry);

    $gsisempid = $gsisidnoarray['emp_id'];
    $last_name = $gsisidnoarray['last_name'];
    $firstname1 = $gsisidnoarray['first_name'];
    $middlename1 = $gsisidnoarray['middle_name'];
    $gsis = $gsisidnoarray['PAGIBIG_idno'];
    // $empname = "$lastname, $firstname $middlename"; 
    // echo"<script>alert('hello')</script>";

  }else if($selectedLoantypeorg == 'Landbank'){
    echo"<script>$selectedLoantypeorg</script>";
  }
 
}

if(isset($_POST['submit_btn'])){
 
  $loantype1 = $_SESSION['selectedLoantype'];
  $loanorg1= $_SESSION['selectedLoanorg'];
  $loanidno = $_POST['loanidno'];
  $gsisempid = $_POST['gsisempid'];

  // $empname = $_POST['empname'];
  $lastname = $_POST['last_name'];
  $firstname1 = $_POST['first_name'];
  $middlename1 = $_POST['middle_name'];
  $startdate = $_POST['startpicker'];
  $enddate = $_POST['endpicker'];
  $loanamount = $_POST['loanamount'];
  $monthlydeductionamount = $_POST['monthlydeductionamount'];
  $noofpays = $_POST['payduration'];
  $uniquekey=md5(rand());

  $empidqry = "SELECT emp_id FROM employees where emp_id = '$gsisempid'";
  $empidexecqry = mysqli_query($conn,$empidqry) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
  $empidcount = mysqli_num_rows($empidexecqry);

  if($empidcount!=1){
    $error = true;
    $empiderror = "Employee ID does not exist.";
  }

if (empty($startdate)){

  $error = true;
  $startdateerror = "Please indicate loan start date.";

}

if (empty($enddate)){
  $error = true;
  $enddateerror = "Please indicate loan end date.";

}

if (empty($loanamount)){
  $error = true;
  $loanamounterror = "Please indicate the loan amount.";

}

if(empty($monthlydeductionamount)){
  $error = true;
  $monthlydeductionamounterror = "Please enter the amount to be deducted every month.";

}

if(empty($noofpays)){
  $error = true;
  $paydurationerror = "Please enter number of payment months.";
}

  if (!$error){

    $newdeptqry = "INSERT INTO loans (uniquekey,loanidno,loanorg,loantype, emp_id,empfirstname,emplastname, empmiddlename, start_date,end_date,loan_amount, loan_balance, monthly_deduct,no_of_pays,status, adminname) VALUES ('$uniquekey','$loanidno','$loanorg1','$loantype1','$gsisempid','$firstname1','$lastname','$middlename','$startdate','$enddate','$loanamount','$loanamount','$monthlydeductionamount','$noofpays','On-Going', '$adminFullName')";
    $newdeptqryresult = mysqli_query($conn,$newdeptqry) or die (" ".mysqli_error($conn));

    $loanhistory="INSERT INTO loan_history (uniquekey, loan_id, loantype, loanorg, emp_id, lastname, firstname, middlename, amount, start_date, end_date, monthly_payment, status, num_of_payments, current_amount, admin_name) VALUES
    ('$uniquekey','$loanidno','$loantype1','$loanorg1','$gsisempid','$lastname','$firstname1','$middlename1','$loanamount','$startdate','$enddate','$monthlydeductionamount', 'On-Going','$noofpays', '$loanamount','$adminFullName')";
     $loanhistoryresult = mysqli_query($conn, $loanhistory) or die (" ".mysqli_error($conn));

    // echo $newdeptqry;

    $activityLog = "Added Loan for ($firstname1 $lastname)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);


    $notificationMessage = "Loan has been added for $firstname1 $lastname";
    $insertNotificationQuery = "INSERT INTO empnotifications (admin_id, adminname, emp_id, message, type, status) VALUES ('$adminId', '$adminFullName','$gsisempid','$notificationMessage','Loan','unread')";
    mysqli_query($conn, $insertNotificationQuery);

    if($newdeptqryresult){

      ?>
   
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       swal({
         text: "Loan inserted successfully",
         icon: "success",
         button: "OK",
        }).then(function() {
           window.location.href = 'adminMasterLoans.php'; // Replace 'your_new_page.php' with the actual URL
       });
   });
</script>
    <?php
 
}
  } else {
    $errType = "danger";
    ?><script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
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
  <div class="title d-flex justify-content-center pt-4">
    <h3>ADD LOAN</h3>
  </div>

  <div class="nopadding col-6 card shadow mx-auto my-5 p-3 mt-5">
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
    <?php
      $loantypesquery = "SELECT * FROM loantype";
      $loantypesexecqry = mysqli_query($conn, $loantypesquery) or die("FAILED TO EXECUTE LOAN TYPE QUERY " . mysqli_error($conn));
    ?>
    <div class="row">
      <div class="col-lg-4">
        <label class="control-label" for="loantype">Loan Type:</label>
          <select name="loantype" id="loantype" class="form-select" required>
            <option value=""></option>
            <?php
              while ($loantype = mysqli_fetch_array($loantypesexecqry)):
                $selected = ($loantype['loanorg'] . '|' . $loantype['loantype'] == $_POST['loantype']) ? 'selected' : '';
            ?>
            <option value="<?php echo $loantype['loanorg'] . '|' . $loantype['loantype']; ?>" <?php echo $selected; ?>><?php echo $loantype['loantype']; ?></option>
              <?php endwhile; ?>
          </select>
      </div>
      <?php
        $lasttypesquery = "SELECT * FROM employees WHERE employment_TYPE ='Full Time'";
        $lasttypesexecqry = mysqli_query($conn, $lasttypesquery) or die("FAILED TO EXECUTE LOAN TYPE QUERY " . mysqli_error($conn));
      ?>
      <div class="col-lg-8 col-md-12">
        <label class="control-label">Last Name:</label>
          <select name="lastname" class="form-select" required>
            <option></option>
              <?php
                while ($lastname = mysqli_fetch_array($lasttypesexecqry)):
                  $selected = ($lastname['last_name'] == $_POST['lastname']) ? 'selected' : '';
              ?>
              <option <?php echo $selected; ?>><?php echo $lastname['last_name']; ?></option>
              <?php endwhile; ?>
          </select>
      </div>
  </div>
    <div class="button text-center mt-3">
      <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out printbtn" name="search_btn1">Search</button>
    </div>
</form>

<form action="<?php $_SERVER['PHP_SELF'];?>" method="POST">
  <div class="row">
    <div class="col-lg-6 col-md-12">
      <label class="control-label">Loan ID Number:</label>
        <input type="text" class="form-control" placeholder="Loan ID Number" name="loanidno" value="<?php echo $gsis ?? ''; ?>" <?php if($selectedLoantypeorg != 'Landbank') echo 'readonly'; ?>/>
    </div>
            
    <div class="col-lg-6 col-md-12">
      <label class="control-label">Employee ID:</label>
        <input type="text" class="form-control" placeholder="Employee ID" name="gsisempid" value="<?php echo $gsisempid ?? ''; ?>" readonly/>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">Last Name:</label>
        <input type="text" class="form-control" placeholder="Last Name" name="last_name" value = "<?php echo $last_name ?? '';?>" readonly/>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">First Name:</label>
        <input type="text" class="form-control" placeholder="First Name" name="first_name" value = "<?php echo $firstname1 ?? '';?>" readonly/>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">Middle Name:</label>
        <input type="text" class="form-control" placeholder="Middle Name" name="middle_name" value = "<?php echo $middlename1 ?? '';?>" readonly/>
    </div>

    <div class="col-lg-6 col-md-12">
      <label class="control-label">Start Date:</label>
        <input type="text" class="form-control datepicker" id="startdatepicker" name ="startpicker" placeholder="Start Date" value=""required >
    </div>

    <div class="col-lg-6 col-md-12">
      <label class="control-label">End Date:</label>
        <input type="text" class="form-control datepicker" id="enddatepicker" name ="endpicker" placeholder="End Date" value="" required>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">Monthly Deduction:</label>
        <input type="text" class="form-control" placeholder="Monthly Deduction Amount" name="monthlydeductionamount" readonly required/>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">Loan Amount:</label>
        <input type="text" class="form-control" placeholder="Loan Amount" name="loanamount" required/>
    </div>

    <div class="col-lg-4 col-md-6">
      <label class="control-label">Pay Duration:</label>
        <input type="text" class="form-control" placeholder="" name="payduration" readonly required/>
          <span> months</span>
    </div>
  </div>

  <div class="button d-flex justify-content-center">
    <div class="form-actions">
      <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" id="submit_btn" style="float: right;">Submit</button>
    </div>
  </div>
</form>
</div>
</div>
  
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>

<?php
unset($_SESSION['anewdept']);
?>

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
<script>
  $(document).ready(function() {
    // Assuming date format is yyyy mm dd
    $("#enddatepicker, #startdatepicker").on("change", function() {
      var startDate = $("#startdatepicker").val();
      var endDate = $("#enddatepicker").val();

      if (startDate && endDate) {
        var start = new Date(startDate);
        var end = new Date(endDate);

        var monthDiff = (end.getFullYear() - start.getFullYear()) * 12 + end.getMonth() - start.getMonth();

        if (monthDiff < 0) {
                monthDiff = 0;
            }

            $("input[name='payduration']").val(monthDiff);

            // Disable button if payduration is 0
            if (monthDiff === 0) {
                $("#submit_btn").prop("disabled", true);
            } else {
                $("#submit_btn").prop("disabled", false);
            }
      }
    });
  });

  $(document).ready(function() {
    // Assuming date format is yyyy mm dd
    $("#enddatepicker, #startdatepicker, input[name='loanamount'], input[name='payduration']").on("change", function() {
        var startDate = $("#startdatepicker").val();
        var endDate = $("#enddatepicker").val();
        var loanAmount = parseFloat($("input[name='loanamount']").val());
        var payDuration = parseInt($("input[name='payduration']").val());

        if (startDate && endDate && !isNaN(loanAmount) && !isNaN(payDuration)) {
            var start = new Date(startDate);
            var end = new Date(endDate);

            var monthDiff = (end.getFullYear() - start.getFullYear()) * 12 + end.getMonth() - start.getMonth() + 1;

            var monthlyDeduction = loanAmount / payDuration;

            $("input[name='monthlydeductionamount']").val(monthlyDeduction.toFixed(2));
        }
    });
  });
</script>

</body>
<style>
 body{
  font-family: 'Poppins', sans-serif;
  background-image: #ffff;
}
</style>
</html>
