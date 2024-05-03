<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

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

$master = $_SESSION['master'];
$idres = $_GET['id'];
$DELquery = "SELECT * from employees WHERE emp_id ='$idres'";
$DELselresult = mysqli_query($conn,$DELquery) or die ("Failed to search DB. ".mysql_error());
$DELcurr = mysqli_fetch_array($DELselresult);
$DELcount = mysqli_num_rows($DELselresult);

$initialEmptypeValue = ""; // Initialize with an empty string
$initialPositionValue = ""; // Initialize with an empty string
$initialDeptNameValue = ""; // Initialize with an empty string
$initialEmpStatusValue = ""; // Initialize with an empty string
$initialSalaryGradeValue = ""; // Initialize with an empty string
$initialStepValue = ""; // Initialize with an empty string

// Initialize the notification message
$notificationMessage = "";

if($DELcount!=0 && $DELcurr) {
  $currprefixid = $DELcurr['prefix_ID'];
  $currempid = $DELcurr['emp_id'];
  $currfingerprintnumber = $DELcurr['fingerprint_id'];
  $currusername = $DELcurr['user_name'];
  $curremail = $DELcurr['email'];
  $currlastname = $DELcurr['last_name'];
  $currfirstname = $DELcurr['first_name'];
  $currmiddlename = $DELcurr['middle_name'];
  $curremptype = $DELcurr['employment_TYPE'];
  $currposition = $DELcurr['position'];
  $currdateofbirth = $DELcurr['date_of_birth'];
  $currgender = $DELcurr['emp_gender'];
  $curracctype = $DELcurr['acct_type'];
  $curraddress = $DELcurr['emp_address'];
  $currnationality = $DELcurr['emp_nationality'];
  $currdeptname = $DELcurr['dept_NAME'];
  $currcontact = $DELcurr['contact_number'];
  $currdatehired = $DELcurr['date_hired'];
  $currimg = $DELcurr['img_name'];
  $currgsis = $DELcurr['GSIS_idno'];
  $currphilhealth = $DELcurr['PHILHEALTH_idno'];
  $currpagibig = $DELcurr['PAGIBIG_idno'];
  $currtin = $DELcurr['TIN_number'];
  $currmaritalstatus = $DELcurr['rel_status'];
  $currspouse = $DELcurr['rel_partner'];
  $currempstatus = $DELcurr['emp_status'];
  $currchildnum = $DELcurr['num_children'];

  $payroll = "SELECT * FROM payrollinfo WHERE emp_id ='$idres'";
  $payrollresult = mysqli_query($conn,$payroll) or die ("Failed to search DB. ");
  $payrollcurr = mysqli_fetch_array($payrollresult);
  $currms = $payrollcurr ['base_pay'];
  $currhr = $payrollcurr ['hourly_rate'];
  $currsg = $payrollcurr ['salarygrade'];
  $currstep = $payrollcurr ['step'];
} else {
  $updateselecterror ="Employee information not found.";
}/*2nd else end*/

$error = false;

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

      //for payroll info
  $salaryGrade = isset($_POST['salaryGrade']) ? $_POST['salaryGrade'] : null;
  $step = isset($_POST['step']) ? $_POST['step'] : null;
  $monthlysalary = isset($_POST['displaySalary']) ? $_POST['displaySalary'] : null;
  $hourlyrate = ($_POST['displayhourlyrate']);

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



if(!$error){
    $sqlquery = "UPDATE employees SET position = '$positionvar', num_children ='$numberofchild',  rel_status = '$maritalstatus', rel_partner = '$spousename', GSIS_idno = '$gsisidno',PAGIBIG_idno = '$pagibig', PHILHEALTH_idno = '$philhealthnumber', TIN_number = '$tin', emp_status = '$empstatus', user_name = '$username', email ='$email', last_name = '$lastname', first_name = '$firstname', middle_name = '$middlename', contact_number = '$contact', acct_type = '$acctoptionvar', dept_NAME = '$deptoptionvar', date_hired = '$datehired', date_of_birth = '$dateofbirth',emp_address = '$address', emp_nationality = '$nationality', emp_gender = '$genderoptionvar', date_hired = '$datehired', employment_TYPE = '$employoptionvar' WHERE emp_id = '$currempid'";
    $result = mysqli_query($conn,$sqlquery) or die ("FAILED TO INSERT ".mysqli_error($conn));

    if ($curremptype != $employoptionvar || 
    $positionvar != $currposition || 
    $deptoptionvar != $currdeptname || 
    $empstatus != $currempstatus || 
    $currsg != $salaryGrade|| 
    $currstep != $step) {
      
    $selectSql = "SELECT id 
    FROM employmenthistory 
    WHERE EmployeeID = $idres 
    ORDER BY StartDate DESC 
    LIMIT 1";

    if ($curremptype != $employoptionvar) {
      $notificationMessage .= "Emptype changed to: " . $employoptionvar . ". ";
    }
    if ($positionvar != $currposition) {
      $notificationMessage .= "Position changed to: " . $positionvar . ". ";
    }
    if ($deptoptionvar != $currdeptname) {
      $notificationMessage .= "Department changed to: " . $deptoptionvar . ". ";
    }
    if ($empstatus != $currempstatus) {
      $notificationMessage .= "Employment status changed to: " . $empstatus . ". ";
    }
    if ($currsg != $salaryGrade) {
      $notificationMessage .= "Salary grade changed to: " . $salaryGrade . ". ";
    }
    if ($currstep != $step) {
      $notificationMessage .= "Step changed to: " . $step . ". ";
    }

    if (!empty($notificationMessage)) {
        // Insert $notificationMessage into the notif table or perform other actions
        $notificationMessage = "$adminFullName made change(s):  ($notificationMessage)";
        $insertNotificationQuery = "INSERT INTO empnotifications (admin_id,adminname, emp_id, message, type, status) VALUES ('$adminId','$adminFullName', '$idres','$notificationMessage','Profile','unread')";
        mysqli_query($conn, $insertNotificationQuery);
    } 

    $result = $conn->query($selectSql);
    if (!$result) {
      echo "Error executing the query: " . $conn->error;
    }

    $row = $result->fetch_assoc();
    $employmentHistoryId = $row['id'];

    if ($employmentHistoryId) {
        // Now you have the ID of the most recent employment history record
        // Proceed with updating its end date
        $updateSql = "UPDATE employmenthistory 
                      SET EndDate = CURDATE() 
                      WHERE ID = $employmentHistoryId";

        $conn->query($updateSql);
    }


    $updatehistory = "INSERT INTO employmenthistory (EmployeeID, EmploymentType, Position, Department, salarygrade, step, StartDate, Status) VALUES
    ('$idres', '$employoptionvar', '$positionvar', '$deptoptionvar', '$salaryGrade','$step',NOW(),'$empstatus')";
    $updateresult = mysqli_query($conn,$updatehistory) or die ("FAILED TO INSERT ".mysqli_error($conn));

    }

	   
    $payrollinfoqry = "UPDATE payrollinfo SET base_pay = '$monthlysalary', daily_rate='$dailyrate', hourly_rate='$hourlyrate', gsis='$gsis', philhealth ='$philhealth', pagibig='$pagibig', wtax='$wtax', salarygrade='$salaryGrade', step='$step',eveningservicerate='$eveningservice' WHERE emp_id = '$currempid'";
    $payrollinfoexecqry = mysqli_query($conn,$payrollinfoqry) or die ("FAILED TO ADD NEW PAY INFO ".mysqli_error($conn));

    // $leaveinfoqry = "UPDATE leaves SET leave_count = $leaves, leaves_year='$currentYear' WHERE emp_id = '$currempid'";
    // $leaveexecqry = mysqli_query($conn,$leaveinfoqry) or die ("FAILED TO ADD NEW PAY INFO ".mysqli_error($conn));

    $activityLog = "Edited employee profile ($firstname $lastname)";
    $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName', '$activityLog', NOW())";
    $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

    if($result) {

?>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
          swal({
              text: "Date edited successfully",
              icon: "success",
              button: "OK",
          }).then(function() {
              window.location.href = "adminMasterfileTry.php";
          });
      });
  </script>

<?php   
	}
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
?>

<?php
INCLUDE ('navbarAdmin.php');
?>
<div class="content">
<?php
  if( isset($errMSG)){
    ?>
    <div class="form-group">
      <div class="alert alert=<?php echo ($errType=="success") ? "success" : $errType; ?>">
        <font color="green" size ="3px"><span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?></font>
      </div>
    </div>
  <?php
  }    
  ?>

<div class="form pt-3">
           
<form action="adminEDITMasterfile.php?id=<?php echo $idres;?>" method="POST" class="form-horizontal" enctype="multipart/form-data">
    <div class="row m-4">
        <div class="col-lg-8 col-sm-12 pb-3 mt-2 card shadow">
        <div class="title text-center pt-2">
    <h4>PERSONAL INFORMATION</h4>
    <HR></HR>
  </div>
  <div class="col-12">
    <div class="row p-2 pb-3">
        <div class="col-12">
          <div class="control-group ">
            <label class="control-label">Employee ID:</label>
              <div class="controls">
                <input type="text" class="span11 form-control" value ="<?php echo $currprefixid; echo $currempid;?>" name="employeeID" readonly/>
              </div>         
            </div>
          </div>
          <div class="col-lg-6 col-sm-12 col-md-6">
            <div class="control-group ">
              <label class="control-label">Last Name :</label>
                <div class="controls">
                  <input type="text" class="span11 form-control" value="<?php echo $currlastname;?>" name="lastname"/>
                </div>         
            </div>
          </div>
          <div class="col-lg-6 col-sm-12 col-md-6">
            <div class="control-group">
              <label class="control-label">First Name :</label> 
                <div class="controls">
                  <input type="text" class="span11 form-control" value="<?php echo $currfirstname;?>" name="firstname" />
                </div>
            </div>
          </div>
          <div class="col-lg-6 col-sm-12 col-md-6">
            <div class="control-group">
              <label class="control-label">Middle Name</label>
                <div class="controls">
                  <input type="text"  class="span11 form-control" value="<?php echo $currmiddlename;?>" name="middlename" />
                </div>
            </div>
          </div>
          <div class="col-lg-6 col-sm-12 col-md-6">
            <div class="control-group">
              <label class="control-label">Username:</label>
                <div class="controls">
                  <input type="text" class="span11 form-control" value="<?php echo $currusername;?>" name="username"/>
                </div>
            </div>
          </div>
          <div class="col-lg-6 col-sm-12">
            <div class="control-group ">
              <label class="control-label">Email:</label>
                <div class="controls">
                  <input type="text" class="span11 form-control " value="<?php echo $curremail;?>" name="email"/>
                </div>
             </div>
          </div>
          <div class="col-lg-6 col-sm-12">
            <label class="control-label">Cellphone Number:</label>
              <div class="controls">
                <input type="text" class="span11 form-control" value="<?php echo $currcontact;?>" name="cellphonenumber" pattern="[0]{1}[9]{1}[0-9]{9}"/>
              </div>
          </div>
          <div class="col-12">
            <label class="control-label">Address:</label>
              <div class="controls">
                <input type="text" class="span11 form-control" value = "<?php echo $curraddress;?>" placeholder="Child 4" name="address"/>
              </div>
          </div>
          <div class="col-lg-4">
            <label class="control-label">Date of Birth: </label>
              <div class="controls">
                <input type="text" class = "span3 form-control datepicker" id="birthdate" name ="dob" placeholder="<?php echo $currdateofbirth;?>" value="<?php echo $currdateofbirth;?>">       
              </div>
          </div>
          <div class="col-lg-4 col-sm-12">
            <label class="control-label">Gender:</label>
              <div class="controls">
                <label>
                <?php
                  if ($currgender=="Male"){
                ?>
                <input type="radio" name="genderoption" value="Male" checked/>Male</label>
                  <label>
                  <input type="radio" name="genderoption" value="Female"/>
                  Female</label>
                <?php
                  }else{
                ?>
                <input type="radio" name="genderoption" value="Male" />Male</label>
                <label>
                <input type="radio" name="genderoption" value="Female"checked/>
                Female</label>
                <?php
                }
                ?>
              </div>
            </div>
            <div class="col-lg-4 col-sm-12">
            <label class="control-label">Marital Status: </label>
          <div class="controls">
            <select name="maritaloption" class="form-select">
              <option><?php echo $currmaritalstatus; ?></option>
              <option>Single</option>
              <option>Married</option>
              <option>Widowed</option>
            </select>
            </div>
          </div>
          <div class="col-lg-8 col-sm-12 ">
            <label class="control-label">Name of spouse:</label>
              <div class="controls">
              <input type="text" class="span11 form-control" value="<?php echo $currspouse;?>"name="spousename"/>
                <span class = "label"><small>*Fill-up only if married.*</small></span>
              </div>
          </div>
          <div class="col-lg-4 col-sm-4">
            <label class="control-label">No. of Children: </label>
              <div class="controls">
              <select class="span2 form-select" name="numberofchild" id="numberofchild" onchange="toggleChildFields()" required>
                  <option><?php echo $currchildnum?></option>
                  <option>00</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12 col-sm-4">
              <label class="control-label">Nationality:</label>
                <div class="controls">
                  <input type="text" class="span6 form-control" value="<?php echo $currnationality;?>" name="nationality"/>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
              <label class="control-label">GSIS ID No:</label>
                <div class="controls">
                <input type="text" class="span6 form-control" value="<?php echo $currgsis;?>" name="gsisidno" pattern="[0-9]{4}-[0-9]{7}-[A-Za-z]{1}"/>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
              <label class="control-label">Philhealth Number:</label>
              <div class="controls">
                  <input type="text" class="span6 form-control" value ="<?php echo $currphilhealth; ?>" name="philhealthnumber" pattern="[0-9]{2}-[0-9]{9}-[0-9]{1}"/>
              </div>
            </div>
            <div class="col-lg-6 col-sm-12">
            <label class="control-label">PAG-IBIG Number:</label>
              <div class="controls">
                  <input type="text" class="span6 form-control" placeholder="PAG-IBIG Number" name="pagibignumber" value ="<?php echo $currpagibig;?>" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}"/>
              </div>
            </div>
            <div class="col-lg-6 col-sm-12">
            <label class="control-label">TIN:</label>
              <div class="controls">
                  <input type="text" class="span6 form-control" value="<?php echo $currtin; ?>" name="tin" pattern="[0-9]{3}-[0-9]{3}-[0-9]{3}-[A-Za-z0-9]{3}"/>
              </div>
            </div>
          </div>
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
                    <input type="radio" name="acctoption" value="Administrator" ' . ($curracctype == "Administrator" ? 'checked' : '') . ' />
                    Administrator
                </label>

                <label>
                    <input type="radio" name="acctoption" value="Employee" ' . ($curracctype == "Employee" ? 'checked' : '') . ' />
                    Employee
                </label>

                <label>
                    <input type="radio" name="acctoption" value="Master" ' . ($curracctype == "Master" ? 'checked' : '') . ' />
                    Master
                </label>';
        } else {
            // Display Administrator and Employee radio buttons
            echo '
                <label>
                    <input type="radio" name="acctoption" value="Administrator" ' . ($curracctype == "Administrator" ? 'checked' : '') . ' />
                    Administrator
                </label>

                <label>
                    <input type="radio" name="acctoption" value="Employee" ' . ($curracctype == "Employee" ? 'checked' : '') . ' />
                    Employee
                </label>';
        }
        ?>
        <!-- <span class="label label-important"><?php echo $acctError; ?></span> -->
    </div>
</div>
<?php
  $departmentsquery = "SELECT * FROM department";
  $departmentsexecqry = mysqli_query($conn, $departmentsquery) or die ("FAILED TO EXECUTE DEPT. QUERY ".mysql_error());
?>
<div class ="control-group">
  <label class="control-label">Department: </label>
    <div class="controls">
      <select name="deptoption" class="form-select">
      <option><?php echo $currdeptname; ?></option>
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
    <select name="employoption" id= "employoption"  class="form-select" required onchange="updateDropdowns()">
        <option><?php echo $curremptype;?></option>
        <?php  while($emptypechoice = mysqli_fetch_array($emptypesexecqry)):;?>
        <option><?php echo $emptypechoice['employment_TYPE'];?></option>
        <?php endwhile; ?>
      </select>
    </div>
</div>
<div class="col-12 mt-1">
<?php
  $positionquery = "SELECT * FROM position";
  $positionexecqry = mysqli_query($conn, $positionquery) or die ("FAILED TO EXECUTE DEPT. QUERY ".mysqli_error($conn));
?>
<div class ="control-group">
  <label class="control-label">Position: </label>
    <div class="controls">
      <select name="position" id= "position" class="form-select">
        <option><?php echo $currposition;?></option>
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
          <option><?php echo $currsg;?></option>

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
          <option><?php echo $currstep;?></option>
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
        <input type="text" class="span5 form-control" value="<?php echo $currms;?>" name="displaySalary" id="displaySalary" required/>
      </div>
  </div> 
</div> 

  <div class="col-6 mt-1">
    <div class="control-group">
      <label class="control-label">Hourly Rate:</label>
        <div class="controls">
          <?php
          if ($curremptype=='Part Time'){
            ?>
              <input type="text" class="span5 form-control" value="<?php echo $currhr;?>"  name="displayhourlyrate" required/>

            <?php
          }else{?>
              <input type="text" class="span5 form-control" value="<?php echo $currhr;?>"  name="displayhourlyrate" id="displayhourlyrate" required/>
              <?php
          }
          ?>
        </div>
    </div> 
  </div> 

  <div class ="control-group">
    <label class="control-label">Date Hired: </label>
      <div class="controls">
        <input type="text" id="datepicker" class="form-control datepicker" value="<?php echo $currdatehired;?>"  name ="dphired" placeholder="Date Hired" value="" required>
      </div>
  </div>
        
  <div class="control-group">
  <label class="control-label">Picture:</label>
  <div class="controls">
      <!-- Display the current image -->
      <img src="<?php echo $currimg; ?>" alt="Current Image" style="max-width: 100px; max-height: 100px; display: block; margin-bottom: 10px;">

      <!-- Input field for uploading a new image -->
      <input type="file" class="form-control" name="image">
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
            
  <div class="form-actions d-flex justify-content-center p-3">
  <?php if ($idres != $adminId): ?>
    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="float: right;">
        Submit
    </button>
<?php elseif ($idres == $adminId): ?>
    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn" style="float: right; display: none;" disabled>
        Submit
    </button>
<?php endif; ?>


</div>
</div>
<!-- end ng row -->
</div>
<!-- end ng col 4 -->
</div>
<!-- end ng main row -->
</form>
</div>
<!-- row -->
<!-- col-8 -->
</div>
<!-- end ng span6 -->
<div class="row-fluid">
<div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>
<?php
unset($_SESSION['addprofilenotif']);
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
  $(document).ready(function() {
    // Function to update dropdowns
    function updateDropdowns1() {
        // Your logic here
        console.log('Dropdowns updated');
    }

    // Call the function when the page loads
    updateDropdowns();
  });
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
}
</style>
</html>

