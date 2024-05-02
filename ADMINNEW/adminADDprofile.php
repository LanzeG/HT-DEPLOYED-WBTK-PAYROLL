<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");


session_start();

$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$master = $_SESSION['master'];

if (isset($_POST['submit_btn']) ){

  $dateofbirth = $_POST['dob'];
  $datehired = $_POST['dphired'];
  $nationality = $_POST['nationality'];
  $currentDate = new DateTime();
  $currentYear = (int)$currentDate->format('Y');

  $dhired = strtotime($datehired);
  $dt = strtotime('+1 month',$dhired);
  $date13th = date("Y-m-d",$dt);

  $address = trim($_POST['address']);
  $address = strip_tags($address);
  $address = htmlspecialchars($address,ENT_QUOTES);

  $username = trim($_POST['username']);
  $username = strip_tags($username);
  $username = htmlspecialchars($username);

  $email = trim($_POST['email']);
  $email = strip_tags($email);
  $email = htmlspecialchars($email);

  $lastname = trim($_POST['lastname']);
  $lastname = strip_tags($lastname);
  $lastname = htmlspecialchars($lastname);

  $capitalizedLastname = strtoupper($lastname);

  $firstname = trim($_POST['firstname']);
  $firstname = strip_tags($firstname);
  $firstname = htmlspecialchars($firstname);

  $middlename = trim($_POST['middlename']);
  $middlename = strip_tags($middlename);
  $middlename = htmlspecialchars($middlename);

  $contact = ($_POST['cellphonenumber']);

  $employoptionvar = ($_POST['employoption']);

  $positionvar = $_POST['position'];

  $genderoptionvar = ($_POST['genderoption']);

  $acctoptionvar = ($_POST['acctoption']);
  
  $deptoptionvar = ($_POST['deptoption']);

  $empstatus = ($_POST['empstatusoption']);

  $maritalstatus = ($_POST['maritaloption']);

  $spousename = ($_POST['spousename']);
  $numberofchild = ($_POST['numberofchild']);

  $files = ($_FILES['image']['tmp_name']);

  $query ="SELECT user_name FROM employees WHERE user_name ='$username'";
  $result = mysqli_query($conn,$query);
  $count = mysqli_num_rows($result);

  $query1 ="SELECT email FROM employees WHERE email ='$email'";
  $result1 = mysqli_query($conn,$query1);
  $count1 = mysqli_num_rows($result1);
  
  if (!empty($_FILES['image']['tmp_name'])) {
    $targetDirectory = "../uploads/";
    $filename = uniqid() . '_' . basename($_FILES["image"]["name"]);
    $targetFile = $targetDirectory . $filename;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
      $filePath = $targetFile;
    }
  }
  if ($acctoptionvar == "Administrator"){

      $accounttype = "Administrator";
      $idprefix = "ADMIN-";
  
  } elseif ($acctoptionvar=="Employee") {
  
      $accounttype = "Employee";
      $idprefix = "EMP-";
  } elseif ($acctoptionvar=="Master"){
    
    $accounttype = "Master";
    $idprefix = "MSTR-";
  } else {
      $error = true;
      $errormsg = "Account type not set.";
      echo'<script>acc</script>';
  }
  if($maritalstatus == "Married" && empty($spousename)){
    $error = true;
    $errormsg = "Please enter name of spouse.";
    echo'<script>spouse</script>';
  } else if ($maritalstatus == "Single"){
    $spousename = " ";
  }

  $gsisidno = ($_POST['gsisidno']);
  $philhealthnumber = ($_POST['philhealthnumber']);
  $tin = ($_POST['tin']);
  $pagibig = ($_POST['pagibignumber']);

  if ($count!=0){
    $error = true;
    $errormsg = "Username is already in use.";
    echo'<script>un</script>';
  }
  
  // leaves
  if ($employoptionvar == "Part Time") {
     $leaveCredits= 0;
  } else if ($employoptionvar == "Full Time") {
      $datehiredObj = DateTime::createFromFormat('Y-m-d', $datehired);
      if (!$datehiredObj) {
          // Handle invalid date format
          echo "Invalid date format";
          exit; // or return an error message
      }

      // Get the current date
      $currentDateObj = new DateTime();

      // Calculate the difference in months between the "date hired" and the current date
      $interval = $datehiredObj->diff($currentDateObj);
      $monthsDifference = $interval->y * 12 + $interval->m;

      // Calculate the number of leave credits based on the months difference
      $leaveCredits = $monthsDifference * 1.25;
  }

    //for payroll info
    $salaryGrade = isset($_POST['salaryGrade']) ? $_POST['salaryGrade'] : null;
    $step = isset($_POST['step']) ? $_POST['step'] : null;
    $monthlysalary = isset($_POST['displaySalary']) ? $_POST['displaySalary'] : null;
    $hourlyrate = ($_POST['displayhourlyrate']);

    //activity log
    $activityLog = "Added a new employee profile ($firstname $lastname)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName','$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

    $eveningservice = ($monthlysalary * 12 / 2080) * 1.25 * 3;
  
  if ($employoptionvar == "Part Time") {
    $dailyrate = 0;
    $refsalary = 0;
    $gsis = 0;
    $philhealth = 0;
    $wtax=0;

  }else{
    $dailyrate = $hourlyrate * 8;
    $refsalary = 2000;
    $gsis = $monthlysalary * 0.09;
      
      if ($monthlysalary == 0) {
          $philhealth = 0;
      } elseif ($monthlysalary <= 10000) {
          $philhealth = 400;
      } elseif ($monthlysalary < 100000) {
          $philhealth = round($monthlysalary * 0.05 / 2, 2); // Apply the rate and divide by 2
      } else {
          $philhealth = 5000 / 2; // Apply the rate and divide by 2
      }

      switch ($monthlysalary) {
          case ($monthlysalary > 666667):
              $wtax = (($monthlysalary - 666667) * 0.35) + 183541.80;
              break;
          case ($monthlysalary > 166667):
              $wtax = (($monthlysalary - 166667) * 0.30) + 33541.80;
              break;
          case ($monthlysalary > 66667):
              $wtax = (($monthlysalary - 66667) * 0.25) + 8541.80;
              break;
          case ($monthlysalary > 33333):
              $wtax = (($monthlysalary - 33333) * 0.20) + 1875.80;
              break;
          case ($monthlysalary >= 20833):
              $wtax = (($monthlysalary - 20833) * 0.15);
              break;
          case ($monthlysalary < 20833):
              $wtax = 0;
              break;
      }

  }

  //insert in employees
  if(!$error){
    $sqlquery = "INSERT INTO employees
    (user_name, email, last_name, first_name, 
    middle_name, pass_word, contact_number, 
    acct_type, fingerprint_id, dept_NAME, 
    date_hired, prefix_ID, date_of_birth, 
    emp_address, emp_nationality, emp_gender, 
    employment_TYPE, position, img_name, 
    GSIS_idno, PHILHEALTH_idno, PAGIBIG_idno, 
    TIN_number, emp_status, rel_status, 
    rel_partner, num_children) 
    VALUES 
    ('$username','$email','$lastname',
    '$firstname','$middlename','$capitalizedLastname',
    '$contact','$accounttype', '0' ,'$deptoptionvar',
    '$datehired','$idprefix','$dateofbirth','$address',
    '$nationality','$genderoptionvar', '$employoptionvar',
    '$positionvar', '$filePath', '$gsisidno','$philhealthnumber',
    '$pagibig','$tin', '$empstatus', '$maritalstatus', 
    '$spousename', '$numberofchild')";
    

    if (mysqli_query($conn, $sqlquery)) {
      echo "Record inserted successfully";
      $lastid = mysqli_insert_id($conn);
      
      //insert in employeeshistory

      $emphistory ="INSERT INTO employmenthistory (EmployeeID, EmploymentType, Position, Department, StartDate, Status)
      VALUES 
      ('$lastid', '$employoptionvar', '$positionvar', '$deptoptionvar', '$datehired', '$empstatus')";

     if( mysqli_query($conn, $emphistory)){
    //insert in leaves
       $leaveinfoqry = "INSERT INTO leaves (emp_id, leave_count, leaves_year) VALUES ('$lastid', '$leaveCredits', '$currentYear')";
       $leaveexecqry = mysqli_query($conn,$leaveinfoqry) or die ("FAILED TO ADD NEW PAY INFO ".mysqli_error($conn));
     }else {
      echo "Error: " . $emphistory . "<br>" . mysqli_error($conn);
      }

    //insert in payroll info
    $payrollinfoqry = "INSERT INTO PAYROLLINFO (emp_id,base_pay,refsalary,daily_rate,hourly_rate,gsis, philhealth, pagibig, wtax, salarygrade, step, eveningservicerate) VALUES
    ('$lastid','$monthlysalary','$refsalary','$dailyrate', '$hourlyrate', '$gsis', '$philhealth','$pagibig', '$wtax','$salaryGrade','$step','$eveningservice')";
    $payrollinfoexecqry = mysqli_query($conn,$payrollinfoqry) or die ("FAILED TO ADD NEW PAY INFO ".mysqli_error($conn));
    
   ?>
   
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       swal({
         text: "Data inserted successfully",
         icon: "success",
         button: "OK",
        }).then(function() {
           window.location.href = 'adminMasterfileTry.php'; 
       });
   });
</script>
    <?php
  } else {
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
}
?>
</head>
<body>


<!--Header-part-->

<?php
INCLUDE ('navbarAdmin.php');
?>

<div class="content">
  <div class="form pt-3">
     <form action="adminADDprofile.php" method="POST" enctype="multipart/form-data">
        <div class="row m-4 ">
          <div class="col-lg-8 col-sm-12 card shadow pb-3 mt-2">
            <div class="title text-center pt-2">
                <h4>PERSONAL INFORMATION</h4>
                <HR></HR>
            </div>
            <div class="col-12">
              <div class="row">
                <div class="col-lg-6 col-sm-12 col-md-6">
                  <div class="control-group ">
                    <label class="control-label">Last Name :</label>
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="Last name" name="lastname" required/>
                      </div>         
                  </div>
                        </div>
                <div class="col-lg-6 col-sm-12 col-md-6 ">
                  <div class="control-group">
                    <label class="control-label">First Name :</label> 
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="First name" name="firstname" required/>
                      </div>
                  </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-6">
                  <div class="control-group">
                    <label class="control-label">Middle Name</label>
                      <div class="controls">
                        <input type="text"  class="span11 form-control" placeholder="Middle name" name="middlename"/>
                      </div>
                    </div>
                  </div>
              
                    <div class="col-lg-6 col-sm-12 col-md-6">
                      <div class="control-group">
                        <label class="control-label">Username:</label>
                          <div class="controls">
                            <input type="text" class="span11 form-control " placeholder="Username" name="username" required/>
                          </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 ">
                      <div class="control-group ">
                        <label class="control-label">Email:</label>
                          <div class="controls">
                            <input type="text" class="span11 form-control" placeholder="Email" name="email" required/>
                          </div>
                        </div>
                    </div>
             
                 
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">Cellphone Number:</label>
                      <div class="controls">
                      <input type="text" class="span11 form-control" placeholder="Cellphone number" name="cellphonenumber" pattern="[0]{1}[9]{1}[0-9]{9}" required />
                      </div>
                    </div>

                    <div class="col-12">
                    <label class="control-label">Address:</label>
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="Address" name="address" required/>
                      </div>
                    </div>
            
                    <div class="col-lg-4">
                      <label class="control-label">Date of Birth: </label>
                        <div class="controls">
                         <input type="text" class = "span3 form-control datepicker"  id="birthdate" name ="dob" placeholder="Date of Birth" value="" required>       
                      </div>
                    </div>

                    <div class="col-lg-4 col-sm-12 ">
                     <label class="control-label">Gender:</label>
                       <div class="controls">
                        <select name="genderoption" class="form-select" required>
                          <option></option>
                          <option>Male</option>
                          <option>Female</option>
                         
                        </select>
                      </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                          <label class="control-label">Marital Status: </label>
                          <div class="controls">
                          <select name="maritaloption" class="form-select" required>
                          <option></option>
                          <option>Single</option>
                          <option>Married</option>
                          <option>Widowed</option>
                        </select>
                        </div>
                      </div>
              

                
                    <div class="col-lg-8 col-sm-12 ">
                     <label class="control-label">Name of spouse:</label>
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="Name of spouse" name="spousename"/>
                        <span class = "fs-6 fst-italic label"><small>*Fill-up only if married.*</small></span>
                      </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                    <label class="control-label">No. of Children: </label>
                      <div class="controls">
                        <select class="span2 form-select" name="numberofchild" id="numberofchild"  required>
                          <option></option>
                          <option>00</option>
                          <option>1</option>
                          <option>2</option>
                          <option>3</option>
                          <option>4</option>
                        </select>
                      </div>
                      </div>
            
             

                
                 
                    <div class="col-lg-4 col-sm-4">
                    <label class="control-label">Nationality:</label>
                      <div class="controls">
                        <input type="text" class="span6 form-control" placeholder="Nationality" name="nationality" required>
                      </div>
                    </div>
               

               
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">GSIS ID No:</label>
                      <div class="controls">
                      <input type="text" class="span6 form-control" placeholder="0000-0000000-X" name="gsisidno" pattern="[0-9]{4}-[0-9]{7}-[A-Za-z]{1}" title="Enter a valid GSIS ID (e.g., E-2022-01-01-12345)" required>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">Philhealth Number:</label>
                      <div class="controls">
                          <input type="text" class="span6 form-control" placeholder="00-000000000-0" name="philhealthnumber" pattern="[0-9]{2}-[0-9]{9}-[0-9]{1}" title="Please enter a valid 12-digit Philhealth Number" required/>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">PAG-IBIG Number:</label>
                      <div class="controls">
                          <input type="text" class="span6 form-control" placeholder="0000-0000-0000" name="pagibignumber" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}" title="Please enter a valid 12-digit PAG-IBIG Number" required/>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">TIN:</label>
                      <div class="controls">
                          <input type="text" class="span6 form-control" placeholder="000-000-000-xxx" name="tin" pattern="[0-9]{3}-[0-9]{3}-[0-9]{3}-[A-Za-z0-9]{3}" title="Please enter a valid 9-digit TIN" required/>
                      </div>
                    </div>
               
             
            </div>

     
<!-- end ng col 4 -->
</div>
          </div>
          <div class="col-lg-4 col-sm-12 card shadow mt-2 pt-2">
              <div class="title text-center pt-2">
                <h4>ACCOUNT INFORMATION</h4>
                  <HR></HR>
              </div>

              <div class="row">
                <div class="control-group">
                  <label class="control-label">Account Type:</label>
                    <div class="controls form-check">
                      <?php

                    if ($master) {
                        // Display Administrator, Employee, and Master radio buttons
                        echo '
                            <label>
                                <input type="radio" name="acctoption" value="Administrator" />
                                Administrator
                            </label>

                            <label>
                                <input type="radio" name="acctoption" value="Employee" />
                                Employee
                            </label>

                            <label>
                                <input type="radio" name="acctoption" value="Master" checked/>
                                Master
                            </label>';
                    } else {
                        // Display only the Employee radio button
                        echo '
                            <label>
                                <input type="radio" name="acctoption" value="Employee" checked/>
                                Employee
                            </label>';
                    }
                    ?>
                    </div>
                </div>
<?php
  $departmentsquery = "SELECT * FROM DEPARTMENT";
  $departmentsexecqry = mysqli_query($conn, $departmentsquery) or die ("FAILED TO EXECUTE DEPT. QUERY ".mysql_error());
?>
  <div class ="control-group">
    <label class="control-label">Department: </label>
      <div class="controls">
        <select name="deptoption" class="form-select" required>
          <option></option>
            <?php  while($deptchoice = mysqli_fetch_array($departmentsexecqry)):;?>
              <option><?php echo $deptchoice['dept_NAME'];?></option>
            <?php endwhile; ?>
        </select>
      </div>
  </div>

<?php
  $emptypesquery = "SELECT * FROM employmenttypes";
  $emptypesexecqry = mysqli_query($conn, $emptypesquery) or die ("FAILED TO EXECUTE DEPT. QUERY ".mysql_error());
?>
  <div class ="control-group">
    <label class="control-label">Employment Type: </label>
      <div class="controls">
        <select name="employoption" class="form-select" id= "employoption" required onchange="updateDropdowns()">
          <option></option>
            <?php  while($emptypechoice = mysqli_fetch_array($emptypesexecqry)):;?>
              <option><?php echo $emptypechoice['employment_TYPE'];?></option>
            <?php endwhile; ?>
        </select>
      </div>
  </div>
  <div class="col-12 mt-1">
<?php
  $positionquery = "SELECT * FROM position";
  $positionexecqry = mysqli_query($conn, $positionquery) or die ("FAILED TO EXECUTE DEPT. QUERY ".mysql_error());
?>
  <div class ="control-group">
    <label class="control-label">Position: </label>
      <div class="controls">
        <select name="position" id= "position" class="form-select">
          <option></option>
            <?php  while($positionchoice = mysqli_fetch_array($positionexecqry)):;?>
              <option><?php echo $positionchoice['position_name'];?></option>
            <?php endwhile; ?>
        </select>
      </div>
    </div>
  </div>
                
  <div class="col-6 mt-1">
    <div class="control-group">
      <label class="control-label">Salary Grade:</label>
        <div class="controls">
          <select id="salaryGrade" class="form-control" name="salaryGrade">
            <?php
              for ($i = 1; $i <= 33; $i++) {
                echo "<option value='$i'>$i</option>";
              }
            ?>
          </select>
        </div>
    </div>
  </div>

  <div class="col-6 mt-1">
    <div class="control-group">
      <label class="control-label">Step:</label>
        <div class="controls">
          <select id="step" class="form-control" name="step">
            <?php
              for ($i = 1; $i <= 8; $i++) {
                echo "<option value='$i'>$i</option>";
              }
             ?>
          </select>
        </div>
    </div>
  </div>
                  
    <div class="col-6 mt-1">
      <div class="control-group">
        <label class="control-label">Monthly Salary:</label>
          <div class="controls">
            <input type="text" class="span5 form-control" name="displaySalary" id="displaySalary" required/>
          </div>
      </div> 
    </div> 

    <div class="col-6 mt-1">
      <div class="control-group">
        <label class="control-label">Hourly Rate:</label>
          <div class="controls">
            <input type="text" class="span5 form-control" name="displayhourlyrate" id="displayhourlyrate" required/>
          </div>
      </div> 
    </div> 

    <div class ="control-group">
      <label class="control-label">Date Hired: </label>
        <div class="controls">
          <input type="text" id="datepicker" class="form-control datepicker" name ="dphired" placeholder="Date Hired" value="" required>
        </div>
    </div>
        
    <div class = "control-group">
      <label class="control-label">Picture: </label>
        <div class="controls">
          <input type="file" class="form-control" name="image" required/>
        </div>
    </div>

    <div class="control-group mt-1">
      <label class="control-label">Employee Status:</label>
        <div class="controls form-check">
          <label>
            <input type="radio" name="empstatusoption" value="Active" checked />
             Active
          </label>
          <label>
            <input type="radio" name="empstatusoption" value="Inactive"/>
              Inactive
          </label>
        </div>
    </div>
            
   
  </div>
<!-- end ng row -->
 
<div class="form-actions d-flex justify-content-center p-3" >
      <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="float: right;">Submit</button>
    </div> 
</div>

</form>
</div>
<!-- row -->
<!-- col-8 -->
</div>
<!-- end ng span6 -->
  
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>
</div>
  <!-- end ng content -->
<?php
unset($_SESSION['addprofilenotif']);
?>


<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


<script>
  function updateDropdowns() {
    var employmentTypeDropdown = document.getElementById('employoption');
    var positionDropdown = document.getElementById('position');
    var salaryGradeInput = document.getElementById('salaryGrade');
    var step = document.getElementById('step');
    var monthlysalary = document.getElementById('displaySalary');
    var hourlyrate = document.getElementById('displayhourlyrate');

    var selectedEmploymentType = employmentTypeDropdown.value;

    if (selectedEmploymentType.toLowerCase() === 'part time') {

      salaryGradeInput.value = '';
      salaryGradeInput.disabled = true;
      step.value = '';
      step.disabled = true;
      monthlysalary.value = '';
      monthlysalary.disabled = true;
      hourlyrate.value='';

    } else {
      salaryGradeInput.disabled = false;
      step.disabled = false;
      monthlysalary.disabled = false;

      $(document).ready(function() {
      // Function to fetch and display salary
      function fetchAndDisplaySalary() {
        console.log("hello");
          // Check if both salary grade and step are selected
          if ($('#salaryGrade').val() && $('#step').val()) {
              // Send AJAX request to fetch salary based on selected grade and step
              $.post('functions/getsalary.php', {
                  grade: $('#salaryGrade').val(),
                  step: $('#step').val()
              }, function(response) {
                  // Update UI with retrieved salary
                  var salary = parseFloat(response);
                  console.log("Response received:", response);
                  
                  var monthlysalary = $('#displaySalary').val(salary);
                  var monthlysalary = salary;
                  var hourlyrate = ((monthlysalary / 22)/ 8).toFixed(2);
                  $('#displayhourlyrate').val(hourlyrate);
                  console.log(hourlyrate);
              });
          }
      }

      // Execute the function when the page loads
      fetchAndDisplaySalary();

      // Event listeners for salary grade and step dropdown change
      $('#salaryGrade, #step').on('change', function() {
          // Call the function to fetch and display salary
          fetchAndDisplaySalary();
          console.log("hello");
    });
});

    }
  }
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>

</body>
<style>
    body{
  font-family: 'Poppins', sans-serif;
  background-image: linear-gradient(190deg, #FFFFFF, #DCF6FF);
  background-repeat: no-repeat;
  background-image: linear-gradient(190deg, #FFFFFF, #DCF6FF 100vh, #DCF6FF);
  height: auto;
}
</style>
</html>
