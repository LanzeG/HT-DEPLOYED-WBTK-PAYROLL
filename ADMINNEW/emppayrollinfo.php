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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");


session_start();

$idres = $_GET['emp_id'];
$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$master = $_SESSION['master'];


$sql = "SELECT * FROM payrollinfo JOIN employees ON payrollinfo.emp_id = employees.emp_id  WHERE employees.emp_id = $idres AND payrollinfo.emp_id  = $idres";
$result = $conn->query($sql);
$row5 = $result->fetch_assoc();

if (isset($_POST['submit_btn']) ){
  $basePay = $_POST['basepay'];
  $dailyRate = $_POST['dailyrate'];
  $hourlyRate = $_POST['hourlyrate'];
  $refSalary = $_POST['refsalary'];
  $gsis = $_POST['gsis'];
  $philhealth = $_POST['philhealth'];
  $pagibig = $_POST['pagibig'];
  $wtax = $_POST['wtax'];
  $disallowance = $_POST['disallowance'];
  $currDisallowance = $_POST['currdisallowance'];

  $updateSql = "UPDATE payrollinfo SET 
      base_pay = '$basePay',
      daily_rate = '$dailyRate',
      hourly_rate = '$hourlyRate',
      refsalary = '$refSalary',
      gsis = '$gsis',
      philhealth = '$philhealth',
      pagibig = '$pagibig',
      wtax = '$wtax',
      disallowance = '$disallowance',
      current_disallowance = '$currDisallowance'
      WHERE emp_id = $idres";

if ($conn->query($updateSql) === TRUE) {
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: "Record Updated",
            icon: "success", // Corrected the casing
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCancelButton: false,
            timer: 3000, // Auto close after 5 seconds
            timerProgressBar: true // Display a progress bar
        }).then(function() {
            // Redirect to another location
            window.location.href = "adminpayrollinfo.php";
        });
    });
</script>


          <?php
} else {
  ?><script>
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


<!--Header-part-->

<?php
INCLUDE ('navbarAdmin.php');
?>

<div class="content">
  <div class="form pt-3">
    <form action="" method="POST" enctype="multipart/form-data">
      <div class="row m-4">
        <div class="col-8 card shadow  mx-auto">
          <div class="title pt-2">
            <h4 class="text-center">PAYROLL INFORMATION</h4>
            <hr>
          </div>
          <div class="col-8 mx-auto">
            <div class="row">
                <div class="col-4">
                  <div class="control-group ">
                    <label class="control-label">Employee ID :</label>
                      <div class="controls">
                      <input type="text" class="span11 form-control" placeholder="EMPID" name="employeeID" value='<?php echo $idres; ?>' required disabled/>
                      </div>         
                  </div>
                        </div>
                <div class="col-4">
                  <div class="control-group">
                    <label class="control-label">Last Name :</label> 
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="Last name" name="lastname" value="<?php echo $row5["last_name"];?>" required disabled/>
                      </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="control-group">
                    <label class="control-label">First Name :</label> 
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="First name" name="firstname" value="<?php echo $row5["first_name"];?>" required disabled/>
                      </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="control-group">
                    <label class="control-label">Middle Name</label>
                      <div class="controls">
                        <input type="text"  class="span11 form-control" placeholder="Middle name" name="middlename" value="<?php echo $row5["middle_name"];?>" disabled/>
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="row">
                    <div class="col-6">
                      <div class="control-group">
                        <label class="control-label">Department:</label>
                          <div class="controls">
                            <input type="text" class="span11 form-control " placeholder="dept" name="dept" value="<?php echo $row5["dept_NAME"];?>" required disabled/>
                          </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="control-group ">
                        <label class="control-label">Employment Type:</label>
                          <div class="controls">
                            <input type="text" class="span11 form-control" placeholder="emptype" name="emptype" value="<?php echo $row5["employment_TYPE"];?>" required disabled/>
                          </div>
                        </div>
                    </div>
                 </div>
                 
                 <div class="row mt-1">
                    <div class="col-4">
                      <label class="control-label">Base Pay: </label>
                        <div class="controls">
                         <input type="text" class = "span3 form-control"  name ="basepay" placeholder="basepay" value="<?php echo $row5["base_pay"];?>" required>       
                      </div>
                    </div>

                    <div class="col-4">
                     <label class="control-label">Daily Rate:</label>
                       <div class="controls">
                       <input type="text" class = "span3 form-control"  name ="dailyrate" placeholder="dailyrate" value="<?php echo $row5["daily_rate"];?>" required>       

                      </div>
                        </div>
                    <div class="col-4">
                     <label class="control-label">Hourly Rate:</label>
                       <div class="controls">
                       <input type="text" class = "span3 form-control"  name ="hourlyrate" placeholder="hourlyrate" value="<?php echo $row5["hourly_rate"];?>" required>       

                      </div>
                        </div>
                       

                 <div class="row mt-1">
                 <div class="col-4">
                     <label class="control-label">PERA:</label>
                       <div class="controls">
                       <input type="text" class = "span3 form-control"  name ="refsalary" placeholder="refsalary" value="<?php echo $row5["refsalary"];?>" required>       

                      </div>
                        </div>
                    <div class="col-4">
                     <label class="control-label">GSIS</label>
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="gsis" name="gsis" value="<?php echo $row5["gsis"];?>"/>
                      </div>
                    </div>
                    
                    <div class="col-lg-4 col-sm-4">
                    <label class="control-label">Philhealth: </label>
                    <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="philhealth" name="philhealth" value="<?php echo $row5["philhealth"];?>"/>
                    </div>
                </div>
        
                 </div>

                 <div class="row">
                    <div class="col-12">
                    <label class="control-label">PAGIBIG:</label>
                      <div class="controls">
                        <input type="text" class="span11 form-control" placeholder="pagibig" name="pagibig" value="<?php echo $row5["pagibig"];?>" required/>
                      </div>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                    <label class="control-label">WTAX:</label>
                      <div class="controls">
                      <input type="text" class="span11 form-control" placeholder="wtax" name="wtax" value="<?php echo $row5["wtax"];?>" required />
                      </div>
                    </div>
                    <div class="col-lg-8 col-sm-12">
                    <label class="control-label">Disallowance:</label>
                      <div class="controls">
                        <input type="text" class="span6 form-control" placeholder="disallowance" name="disallowance" value="<?php echo $row5["disallowance"];?>" required>
                      </div>
                    </div>
                 </div>

                 <div class="row mt-2">
                    <div class="col-lg-6 col-sm-12">
                    <label class="control-label">Current Disallowance:</label>
                      <div class="controls">
                      <input type="text" class="span6 form-control" placeholder="Current Disallowance" name="currdisallowance" value="<?php echo $row5["disallowance"];?>"  required>
                      </div>
                      
                    </div>
                    <div class="form-actions">
                    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 mt-3 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn">Submit</button>
                  </div>
                  <div class="pb-5">
                  </div>
                 </div>
              </div>
            </div>

           
<!-- end ng main row -->
</form>
</div>
<!-- row -->
<!-- col-8 -->
</div>
<!-- end ng span6 -->
  
<div class="row-fluid">
  <!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div> -->
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


</body>
<style>
    body{
  font-family: 'Poppins', sans-serif;
  
}
</style>
</html>
