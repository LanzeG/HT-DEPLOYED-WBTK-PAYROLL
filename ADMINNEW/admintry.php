<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

session_start();

if (!isset($_SESSION['adminId'])) {
  // Redirect to the desired page
  header("Location: ../default.php"); // Change 'login.php' to the desired page
  exit; // Terminate script execution after redirection
}
$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];


//for checking if there are 5 absent
date_default_timezone_set('Asia/Manila');
$flagTable = 'dashboard_flag';
$currentHour = date('H:i A'); // This will give you the current time in 12-hour format with AM/PM
$currentDate = date('Y-m-d');



$selectissues = "SELECT COUNT(notification_id) as issues FROM notifications WHERE type='Issue'";
$selectissuesexec = mysqli_query($conn, $selectissues) or die("FAILED TO CHECK OT APPS " . mysqli_error($conn));
$selectissuesarray = mysqli_fetch_array($selectissuesexec);
if ($selectissuesarray) {
  $selectissuess = $selectissuesarray['issues'];
}



// Check the last executed date from the flag table
$checkLastExecutedDateQuery = "SELECT last_executed_date FROM $flagTable ORDER BY last_executed_date DESC LIMIT 1";
$lastExecutedDateResult = $conn->query($checkLastExecutedDateQuery);

if (!$lastExecutedDateResult) {
    echo "Error checking last executed date: " . $conn->error;
    // handle the error, e.g., return or exit
}

$lastExecutedDate = $lastExecutedDateResult->fetch_assoc()['last_executed_date'];

// If the last execution date is more than 5 days ago or doesn't exist, proceed with the process
if (!$lastExecutedDate || strtotime($lastExecutedDate) <= strtotime('-5 days')) {
    // Check if the current time is later than 4 PM (16:00)
    if ($currentHour <= 16) {
        // Existing code...

        $sql = "SELECT emp_id, absence_date,
                ROW_NUMBER() OVER (PARTITION BY emp_id ORDER BY absence_date) as row_num
                FROM absences";

        $result = $conn->query($sql);

        if (!$result) {
            echo "Error executing query: " . $conn->error;
            // handle the error, e.g., return or exit
        }

        $employeeIdsWithConsecutiveAbsences = [];

        while ($row = $result->fetch_assoc()) {
            $emp_id = $row['emp_id'];
            $absence_date = $row['absence_date'];
            $row_num = $row['row_num'];

            if ($row_num >= 5) {
                if (!isset($employeeIdsWithConsecutiveAbsences[$emp_id])) {
                    $employeeIdsWithConsecutiveAbsences[$emp_id] = [
                        'consecutive_absences' => []
                    ];
                }

                $employeeIdsWithConsecutiveAbsences[$emp_id]['consecutive_absences'][] = $absence_date;
            }
        }

        // Notify employees with consecutive absences
        foreach ($employeeIdsWithConsecutiveAbsences as $emp_id => $data) {
            $consecutiveDates = implode(', ', $data['consecutive_absences']);
            $notificationMessage = "You have 5 or more consecutive absences on the following dates: $consecutiveDates";
            $insertNotificationQuery = "INSERT INTO empnotifications (admin_id, adminname, emp_id, message, type, status) VALUES ('$adminId', '$adminFullName', '$emp_id', '$notificationMessage', 'Leave', 'unread')";
            
            if ($conn->query($insertNotificationQuery) !== TRUE) {
                echo "Error inserting notification: " . $conn->error;
                // handle the error, e.g., return or exit
            }
        }

        // Update the flag status and last executed date
        $updateFlagQuery = "INSERT INTO $flagTable (flag_status, last_executed_date) VALUES ('Executed', CURDATE())";
        $conn->query($updateFlagQuery);

        if ($conn->error) {
            echo "Error updating flag: " . $conn->error;
            // handle the error, e.g., return or exit
        }

    } 
} else {
    // echo "Process was executed within the last 5 days. Skipping the process.\n";
}
if (isset($_GET['daterange_start']) && isset($_GET['daterange_end'])) {
  $_SESSION['start_date'] = $_GET['daterange_start'];
  $_SESSION['end_date'] = $_GET['daterange_end'];
}

$timeconv = strtotime("NOW");
$currtime = date("F d, Y", $timeconv);
$currdate = date("Y-m-d", $timeconv);
$curryear = date("Y", $timeconv);


$checkpperiod = "SELECT pperiod_range FROM payperiods WHERE CURDATE() BETWEEN pperiod_start and pperiod_end";
$checkpperiodexec = mysqli_query($conn, $checkpperiod) or die("FAILED TO CHECK PAYPERIOD " . mysqli_error($conn));
$pperiodarray = mysqli_fetch_array($checkpperiodexec);
if ($pperiodarray) {
    $currpperiod = $pperiodarray['pperiod_range'];

    // Extracting start and end dates from the current period
    $dates = explode(' to ', $currpperiod);
    $startDate = date('F j, Y', strtotime($dates[0]));
    $endDate = date('F j, Y', strtotime($dates[1]));

    // Formatted current period range
    $formattedCurrPeriod = $startDate . ' to ' . $endDate;
} else {
    $formattedCurrPeriod = "No Current Pay Period";
}

/** CHECK PAYROLL PERIOD **/
/** CHECK OVERTIME APPLICATIONS **/
$checkotapp = "SELECT COUNT(emp_id) as otapps FROM over_time WHERE ot_remarks = 'Pending'";
$checkotappexec = mysqli_query($conn, $checkotapp) or die("FAILED TO CHECK OT APPS " . mysqli_error($conn));
$otapparray = mysqli_fetch_array($checkotappexec);
if ($otapparray) {
  $otapps = $otapparray['otapps'];
}
/** CHECK OVERTIME APPLICATIONS **/
/** CHECK LEAVE APPLICATIONS **/
$checkleavesapp = "SELECT COUNT(emp_id) as leaveapps FROM leaves_application WHERE leave_status = 'Pending  '";
$checkleavesappexec = mysqli_query($conn, $checkleavesapp) or die("FAILED TO CHECK LEAVE APPS");
$leaveapparray = mysqli_fetch_array($checkleavesappexec);
if ($leaveapparray) {
  $leaveapps = $leaveapparray['leaveapps'];
}

/**CHECK LEAVE APPLICATIONS **/

///////FORM/////////////////////////////////////////////////////////////////
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["print_btn"])) {

  list($startDate, $endDate) = explode(' - ', $_GET['dates']);

  $_SESSION['startDate'] = $startDate;
  $_SESSION['endDate'] = $endDate;

$convertedStartDate = DateTime::createFromFormat('m/d/Y', $startDate)->format('Y-m-d');
$convertedEndDate = DateTime::createFromFormat('m/d/Y', $endDate)->format('Y-m-d');

// Set the dates into session variables
$_SESSION['startDate'] = $convertedStartDate;
$_SESSION['endDate'] = $convertedEndDate;

  $selectedDepartment = isset($_GET["department"]) ? $_GET["department"] : "";
  $selectedEmploymentType = isset($_GET["employmenttype"]) ? $_GET["employmenttype"] : "";
  $selectedposition = isset($_GET["position"]) ? $_GET["position"] : "";
  $selectedGender = isset($_GET["gender"]) ? $_GET["gender"] : "";

  
    //absents
  $checkabsent = "SELECT COUNT(a.emp_id) as totalabsent
  FROM absences a
  JOIN employees d ON a.emp_id = d.emp_id
  WHERE DATE(a.absence_date) BETWEEN '{$_SESSION['startDate']}' AND '{$_SESSION['endDate']}'
    " . (!empty($selectedDepartment) ? "AND d.dept_NAME = '$selectedDepartment'" : "") . "
    " . (!empty($selectedposition) ? "AND d.position = '$selectedposition'" : "") . "
    " . (!empty($selectedGender) ? "AND d.emp_gender = '$selectedGender'" : "") . "
    " . (!empty($selectedEmploymentType) ? "AND d.employment_type= '$selectedEmploymentType'" : "");
  
  
    $checkabsentexecquery = mysqli_query($conn, $checkabsent) or die("FAILED TO CHECK MORNING ATTENDANCE " . mysqli_error($conn));
    $absenttarray = mysqli_fetch_array($checkabsentexecquery);
    if ($absenttarray) {
      $absents = $absenttarray['totalabsent'];
      // echo "Generated Query: $checkattendancemorning";
    }else{
      $absents =0;
    }
 //gender
 $genderQuery = "SELECT 
 COUNT(emp_id) as totalEmps,
 SUM(CASE WHEN emp_gender = 'Male' THEN 1 ELSE 0 END) as numMales,
 SUM(CASE WHEN emp_gender = 'Female' THEN 1 ELSE 0 END) as numFemales
FROM employees 
WHERE emp_status = 'Active'
" . (!empty($selectedDepartment) ? "AND dept_NAME = '$selectedDepartment' " : "")
. (!empty($selectedGender) ? "AND emp_gender = '$selectedGender' " : "")
. (!empty($selectedposition) ? "AND position = '$selectedposition' " : "")
. (!empty($selectedEmploymentType) ? "AND employment_type = '$selectedEmploymentType' " : "");

$genderExec = mysqli_query($conn, $genderQuery) or die("FAILED TO CHECK ABSENCES " . mysqli_error($conn));
$genderArray = mysqli_fetch_array($genderExec);

if ($genderArray) {
$empsnum = $genderArray['totalEmps'];
$numMales = $genderArray['numMales'];
$numFemales = $genderArray['numFemales'];
}
// echo $genderQuery;

// // Check late
// $late = "SELECT COUNT(e.emp_id) as late FROM time_keeping t
// JOIN employees e ON t.emp_id = e.emp_id
// WHERE
// AND DATE(t.timekeep_day) BETWEEN '{$_SESSION['startDate']}' AND '{$_SESSION['endDate']}'
// " . (!empty($selectedDepartment) ? "AND e.dept_NAME = '$selectedDepartment' " : "")
// . (!empty($selectedGender) ? "AND e.emp_gender = '$selectedGender' " : "")
// . (!empty($selectedposition) ? "AND e.position = '$selectedposition' " : "")
// . (!empty($selectedEmploymentType) ? "AND e.employment_type = '$selectedEmploymentType' " : "");

// $lateExecQuery = mysqli_query($conn, $late) or die("FAILED TO CHECK LATE ATTENDANCE " . mysqli_error($conn));
// $latearray = mysqli_fetch_array($lateExecQuery);

// if ($latearray) {
// $lateAtt = $latearray['late'];
// }

//leaves
$leave = "SELECT COUNT(e.emp_id) as numLeaves
FROM leaves_application l
JOIN employees e ON l.emp_id = e.emp_id
WHERE l.leave_status = 'Approved' 
AND DATE(l.leave_datestart) <= '{$_SESSION['endDate']}'
AND DATE(l.leave_dateend) >= '{$_SESSION['startDate']}'
" . (!empty($selectedDepartment) ? "AND e.dept_NAME = '$selectedDepartment'" : "") . "
" . (!empty($selectedGender) ? "AND e.emp_gender = '$selectedGender'" : "") . "
" . (!empty($selectedposition) ? "AND e.position = '$selectedposition'" : "") . "
" . (!empty($selectedEmploymentType) ? "AND e.employment_type = '$selectedEmploymentType'" : "");

$leavesExec = mysqli_query($conn, $leave) or die("FAILED TO CHECK LEAVES " . mysqli_error($conn));
$leavesArray = mysqli_fetch_array($leavesExec);

if ($leavesArray) {
$numLeaves = $leavesArray['numLeaves'];
}

//undertime
$undertime = "SELECT COUNT(e.emp_id) as undertime 
FROM time_keeping t
JOIN employees e ON t.emp_id = e.emp_id
WHERE t.undertime_hours > 0 
AND DATE(timekeep_day) BETWEEN '{$_SESSION['startDate']}' AND '{$_SESSION['endDate']}'
" . (!empty($selectedDepartment) ? "AND e.dept_NAME = '$selectedDepartment' " : "")
. (!empty($selectedGender) ? "AND e.emp_gender = '$selectedGender' " : "")
. (!empty($selectedposition) ? "AND e.position = '$selectedposition' " : "")
. (!empty($selectedEmploymentType) ? "AND e.employment_type = '$selectedEmploymentType' " : "");

$undertimeExecQuery = mysqli_query($conn, $undertime) or die("FAILED TO CHECK UNDERTIME ATTENDANCE " . mysqli_error($conn));
$undertimearray = mysqli_fetch_array($undertimeExecQuery);

if ($undertimearray) {
$undertimeatt = $undertimearray['undertime'];
}

$checkattendancemorning = "SELECT COUNT(t.emp_id) as morningatt
  FROM time_keeping t
  JOIN employees d ON t.emp_id = d.emp_id
  WHERE DATE(t.timekeep_day) BETWEEN '{$_SESSION['startDate']}' AND '{$_SESSION['endDate']}'
    " . (!empty($selectedDepartment) ? "AND d.dept_NAME = '$selectedDepartment'" : "") . "
    " . (!empty($selectedposition) ? "AND d.position = '$selectedposition'" : "") . "
    " . (!empty($selectedGender) ? "AND d.emp_gender = '$selectedGender'" : "") . "
    " . (!empty($selectedEmploymentType) ? "AND d.employment_type= '$selectedEmploymentType'" : "");
  
  
    $checkattendancemorningexecquery = mysqli_query($conn, $checkattendancemorning) or die("FAILED TO CHECK MORNING ATTENDANCE " . mysqli_error($conn));
    $morningattarray = mysqli_fetch_array($checkattendancemorningexecquery);
    if ($morningattarray) {
      $morningatt = $morningattarray['morningatt'];
      // $absents = $morningatt - $empsnum +1;
      // echo "Generated Query: $checkattendancemorning";
    }

    $checkabsences = "SELECT COUNT(d.emp_id) as numemps
    FROM employees d
    WHERE " . (!empty($selectedDepartment) ? "d.dept_NAME = '$selectedDepartment' AND " : "")
          . (!empty($selectedposition) ? "d.position = '$selectedposition' AND " : "")
          . (!empty($selectedGender) ? "d.emp_gender = '$selectedGender' AND " : "")
          . (!empty($selectedEmploymentType) ? "d.employment_type = '$selectedEmploymentType' AND " : "")
          . "d.emp_status = 'Active'";
    
    
    $checkabsencesexec = mysqli_query($conn,$checkabsences) or die ("FAILED TO CHECK ABSENCES ".mysqli_error($conn));
    $absencesarray = mysqli_fetch_array($checkabsencesexec);
    
    if ($absencesarray){
      $activeemps = $absencesarray['numemps'];
    
      // $absents = $absencesarray['numemps'] - $morningatt;
      // echo "Generated Query: $checkabsences";
      // echo "Generated Query: $activeemps";
      // echo "Generated Query: $absencestoday";
    }

} else {
  $combinedQuery = "
  SELECT
    COUNT(DISTINCT tk.emp_id) AS morningatt,
    COUNT(DISTINCT a.emp_id) AS absent,
    COUNT(DISTINCT e.emp_id) AS emp
    FROM employees e
    LEFT JOIN time_keeping tk ON e.emp_id = tk.emp_id AND DATE(tk.timekeep_day) = CURDATE()
    LEFT JOIN absences a ON e.emp_id = a.emp_id AND a.absence_date = CURDATE()";

$combinedResult = mysqli_query($conn, $combinedQuery) or die("FAILED TO EXECUTE COMBINED QUERY " . mysqli_error($conn));

if ($combinedResult) {
    $combinedArray = mysqli_fetch_assoc($combinedResult);
    $morningatt = $combinedArray['morningatt'] ?? 0;
    $absents = $combinedArray['absent'] ?? 0;
    $empsnum = $combinedArray['emp'] ?? 0;
    // $absents =  $empsnum - $morningatt;
} else {
    $morningatt = 0;
    $absents = 0;
    $empsnum = 0;
}


//check gender
$genderQuery = "SELECT 
 COUNT(emp_id) as totalEmps,
 SUM(CASE WHEN emp_gender = 'Male' THEN 1 ELSE 0 END) as numMales,
 SUM(CASE WHEN emp_gender = 'Female' THEN 1 ELSE 0 END) as numFemales
FROM employees 
WHERE emp_status = 'Active'";

$genderExec = mysqli_query($conn, $genderQuery) or die("FAILED TO CHECK emps " . mysqli_error($conn));
$genderArray = mysqli_fetch_array($genderExec);

if ($genderArray) {
$totalEmps = $genderArray['totalEmps'];
$numMales = $genderArray['numMales'];
$numFemales = $genderArray['numFemales'];

}


//check late
$late = "SELECT COUNT(emp_id) as late FROM time_keeping WHERE DATE(timekeep_day) = CURDATE()";
$lateexecquery = mysqli_query($conn, $late) or die("FAILED TO CHECK MORNING ATTENDANCE " . mysqli_error($conn));
$latearray = mysqli_fetch_array($lateexecquery);
if ($latearray) {
$lateatt = $latearray['late'];
}



//check on leave employees
$leave = "SELECT COUNT(emp_id) as numLeaves
FROM leaves_application
WHERE leave_status = 'Approved' AND CURDATE() >= leave_datestart AND CURDATE() <= leave_dateend";

$leavesExec = mysqli_query($conn, $leave) or die("FAILED TO CHECK ABSENCES " . mysqli_error($conn));
$leavesArray = mysqli_fetch_array($leavesExec);

if ($leavesArray) {
$leavesarray = $leavesArray['numLeaves'];
}else{
  // $leavesarray =0;
}


//check undertime
$undertime = "SELECT COUNT(emp_id) as undertime FROM time_keeping WHERE undertime_hours > 0 AND DATE(timekeep_day) = CURDATE()";
$undertimeexecquery = mysqli_query($conn, $undertime) or die("FAILED TO CHECK MORNING ATTENDANCE " . mysqli_error($conn));
$undertimearray = mysqli_fetch_array($undertimeexecquery);
if ($undertimearray) {
$undertimeatt = $undertimearray['undertime'];
// echo $undertimeatt; ge
}else {
  // $undertimeatt=0;
}
}

if (isset($_GET['refresh'])) {
  header("Location: admintry.php");
  exit(); 
}

?>

<?php
$numEmployeesWithEveningService = 0;

if (isset($currpperiod)) {
    $dates = explode(' to ', $currpperiod);
    $startPeriod = date('Y-m-d', strtotime($dates[0])); 
    $endPeriod = date('Y-m-d', strtotime($dates[1])); 

    $searchquery = "
    SELECT 
        COUNT(*) AS num_employees_with_evening_service
    FROM time_keeping 
    WHERE timekeep_remarks = 'Evening Service' 
    AND timekeep_day BETWEEN '$startPeriod' AND '$endPeriod'";

    $result = mysqli_query($conn, $searchquery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        // number ng mga emplyees na may evening service in current pay period.
        $numEmployeesWithEveningService = $row['num_employees_with_evening_service'];
    } else {
        echo "Failed to retrieve data from the database.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin</title>
<link rel="icon" type="image/png" href="../img/icon1 (3).png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!--<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.css">-->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">-->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@3.1.0/daterangepicker.css">-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.css">
<script src="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@3.1.0/daterangepicker.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../jquery-ui-1.12.1/jquery-ui.js"></script>

<script type ="text/javascript">
  $( function() {
      $( "#datepickerfrom" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  $( function() {
      $( "#datepickerto" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
<script>
  function toggleCollapse() {
    var content = document.getElementById("content1");
    content.classList.toggle("collapsed");


    localStorage.setItem("collapseState", content.classList.contains("collapsed"));
  }


  window.onload = function() {
    var isCollapsed = localStorage.getItem("collapseState");


    if (isCollapsed === "true") {
      toggleCollapse();
    }
  };
</script>


</head>
<body>
  
<script>
    <?php
    if (isset($_SESSION['uname']) && isset($_SESSION['empId']) && !isset($_SESSION['swal_displayed'])) {
   
      $_SESSION['swal_displayed'] = true; 
    
      echo "Swal.fire({
        html: '<img src=\"../img/images.png\" style=\"float: left; margin-right: 10px; width: 25px; height: 25px;\">Welcome back, {$_SESSION['uname']}!',
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        customClass: {
          popup: 'swal2-popup-custom' // Custom class name (optional)
        }
      });";

    }
    ?>
  </script>
<?php include('navbarAdmin.php'); ?>



<style>
  .swal2-toast {
    
    background: #E4FCF1 !important;
    color: #000000 !important;
    

  
}

</style>

<style>
body{
  font-family: 'Poppins', sans-serif;
  background-image: #ffff;

}
  #content1 {
    display: block;
}

#content1.collapsed {
    display: none;
}

  .card1 {
  box-sizing: border-box;
  /* border: 1px solid white; */
  text-align: center;
  transition: all 0.5s;
  display: flex;
  align-items: center;
  justify-content: center;
  user-select: none;
  font-weight: bolder;
  color: black;
  background: #e7eaec;

  
background: -webkit-linear-gradient(308deg, #e7eaec 0%, #ffffff 50%, #ffffff 100%);
background: linear-gradient(308deg, #e7eaec 0%, #ffffff 50%, #ffffff 100%);
}
.card11 {
  font-family: 'Poppins', sans-serif;
  box-sizing: border-box;
  border: 1px solid white;
  text-align: center;

  display: flex;
  align-items: center;
  justify-content: center;
  user-select: none;
  font-weight: bolder;
  color: #123123;
}

</style>
<div class="container-fluid">
  
  <div class="row">

    <!-- Main Content -->
 <div class="content">
<div class="d-flex justify-content-center text-center">
<div class="title flex-column align-content-center  justify-content-center pt-2">
  <div class="col" >
  <h3>
DAILY STATISTICS
</h3>
  </div>
  <h4>

  </h4>
  <div class="col text-center">
<h5>
<?php
date_default_timezone_set('Asia/Manila');
$flagTable = 'dashboard_flag';
$currentHour = date('h:i A'); 
$currentDate = date('F j, Y');
?>
<span id="realDate"><?php echo $currentDate; ?></span> | <span id="realTime"></span>
</h5> 
</div>
 </div>
</div>
 <div class="d-flex justify-content-end ">
 <button type="button" class="inline-block bg-green-300 hover:bg-green-400 text-purple-900 py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out mb-2" id="collapseBtn" onclick="toggleCollapse()">
    Filter Options <i class="fas fa-arrow-down ml-2"></i>
</button>
  </div>
  </div>
  <div id="content1" >
  <div class="filter">
<div class="card shadow p-4 " style="border: 1px solid #F6E3F3; width: 100%; border-radius:10px; ">
<form method="GET" action="">
<?php
  $deptchecked = isset($_GET['department']) ? $_GET['department'] : '';
  $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
  $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
  $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
  $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
  $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
  $month = isset($_GET['month']) ? $_GET['month'] : '';
  $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : ''; 
  $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : ''; 
                    
  $query = "SELECT * FROM department";
  $total_row = mysqli_query($conn, $query) or die('error');
?>

<div class="row row1">


<div class="col-lg-3 col-sm-12" >
<label for="dept" class="form-label">Deparment</label>
<select id="dept" class="form-select" name="department" style="border-radius:10px;">
<option value="">Select Department</option>
      <?php
        if (mysqli_num_rows($total_row) > 0) {
          foreach ($total_row as $row) {
      ?>
       <option value="<?php echo $row['dept_NAME']; ?>" <?php if ($deptchecked == $row['dept_NAME'])
          echo "selected"; ?>>
          <?php echo $row['dept_NAME']; ?>
       </option>
          <?php
          }
        } else {
          echo 'No Data Found';
        }
          ?>
</select>
</div>
<?php
   $query1 = "SELECT * FROM employmenttypes";
              $total_row = mysqli_query($conn, $query1) or die('error');
?>
<div class="col-lg-2 col-sm-6"><label for="employmenttype" class="form-label">Employment Type</label>
    <select id="employmenttype" class="form-select" name="employmenttype" style="border-radius:10px;">
    <option value="">Select Employment Type</option>
      <?php
        if (mysqli_num_rows($total_row) > 0) {
          foreach ($total_row as $row) {
      ?>
      <option value="<?php echo $row['employment_TYPE']; ?>" <?php if ($emptypechecked == $row['employment_TYPE'])
                    echo "selected"; ?>>
                    <?php echo $row['employment_TYPE']; ?>
      </option>
      <?php

          }
        } else {
          echo 'No Data Found';
        }
      ?>
    </select>
</div>
<?php
  $query3 = "SELECT * FROM position";
  $total_row = mysqli_query($conn, $query3) or die('error');
?>
<div class="col-lg-2 col-sm-6">
<label for="position" class="form-label">Position</label>
    <select name="position" id="position" class="form-select" style="border-radius:10px;">
      <option value="">Select Position</option>
      <?php
        if (mysqli_num_rows($total_row) > 0) {
          foreach ($total_row as $row) {
      ?>
          <option value="<?php echo $row['position_name']; ?>" <?php if ($positionchecked == $row['position_name'])
           echo "selected"; ?>>
            <?php echo $row['position_name']; ?>
          </option>
            <?php
          }
         } else {
          echo 'No Data Found';
        }
             ?>
    </select>
</div>
<div class="col-lg-2 col-sm-6">
<label for="Gender" class="form-label">Sex</label>
    <select id="Gender" class="form-select" name="gender"  style="border-radius:10px;">
    <option value="">Sex</option>
    <option value="Male" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'Male')
      echo 'selected'; ?>>Male</option>
    <option value="Female" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'Female')
       echo 'selected'; ?>>Female</option>
    </select>
</div>
<div class="row col-lg-3 col-sm-6">

<label for="dateRangePicker">Select Date Range:</label>
<input type="text" id="dateRangePicker" name="dates" class="form-control" value="<?php echo isset($_GET['dates']) ? $_GET['dates'] : ''; ?>" style="border-radius:10px;">
</div>
</div>
<!-- end row 1 -->
<div class="row row2 mt-2">
<div class="col-lg-2 col-sm-6">
</div>
</div>
<div class="row col-lg-3 col-sm-6">
</div>
<div class=" d-flex align-items-center justify-content-center">
<div class="  form-actions mt-3" >
<button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="print_btn">Apply</button>

<a href="admintry.php">
    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mr-5" name="refresh">Refresh</button>
</a>
</div>
</div>
</form>
</div>
<!-- end form -->
</div>
  </div>
<div class="line mb-2" >
<div class="row"  >
<div class="col-lg-3 col-md-6 mt-3" style="height: 150px;">
  <div class="card1 card h-100 text-bg-white shadow" style="color: #1f155f; border-radius: 10px;">
    <div class="row d-flex justify-content-center">
      <div class="title mt-2" style="color: #123123;">
        TOTAL EMPLOYEES
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-center">
          <h1 id="employeesCounter"><?php echo $empsnum?></h1>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-lg-3 col-md-6 mt-3">
  <div class="card1 card h-100 text-bg-white shadow" style="color:#00000; border-radius:10px;">
    <div class="row d-flex justify-content-center">
      <div class="col-12 title mt-2" style="color:#123123;">
        SEX
      </div>
      <div class="col-12 mt-2">
        <div class="row p-2">
          <div class="col-6">
            <div class="row">
              <div class="col-4">
                <i class="fas fa-male fa-2x" data-toggle="tooltip" data-placement="top" title="Male" data style="font-size: 60px; color: #00b464;"></i>
              </div>
              <div class="col-8">
                <h3 id="maleCounter"><?php echo ($genderArray['numMales'] == 0) ? '0' : $genderArray['numMales']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="row">
              <div class="col-4">
                <i class="fas fa-female fa-2x" data-toggle="tooltip" data-placement="top" title="Female" style="font-size: 60px; color: #1f155f;"></i>
              </div>
              <div class="col-8">
                <h3 id="femaleCounter"><?php echo ($genderArray['numFemales'] == 0) ? '0' : $genderArray['numFemales']; ?></h3>
              </div>
            </div>
          </div>
        </div>
    </div>  
  </div>
 </div>
</div>
    <div class="col-lg-3 col-md-6 mt-3">
      <div class="card1 card h-100 shadow" style="color: #1f155f; border-radius: 10px;" onclick="window.location.href='https://wbtkpayrollportal.com/ADMINNEW/all_notifications.php?sort_by=Issue';">
      <div class="row d-flex justify-content-center">
        <div class="title mt-2" style="color: #123123;">
          PENDING ISSUES
             </div>
                <div class="card-body">
                  <div class="d-flex justify-content-center">
                    <h1 id="issueCounter">0</h1>
                  </div>
                </div>
              </div>
            </div>
        </div>
<div class="col-lg-3 col-md-6 mt-3">
  <div class="card1 card h-100  shadow" style="color:#1f155f; border-radius:10px; padding:5px; padding-top:15px;">
    <div class="row d-flex-justify-content-center">
      <div class="title mt-2 " style="color:#123123">
        CURRENT PAYROLL PERIOD
      </div>
<div class="card-body d-flex justify-content-center ">
  <h6 class="text-center"> 
  <?php
if (isset($currpperiod)) {
    // Extracting start and end dates from the current period
    $dates = explode(' to ', $currpperiod);
    $startDate = date('F j, Y', strtotime($dates[0]));
    $endDate = date('F j, Y', strtotime($dates[1]));

    // Display the formatted current period range
    echo $startDate . ' to ' . $endDate;
} else {
    echo "No Current Pay Period";
}
?>

  </h6>
  
</div>

</div>
<button type="submit" onclick="window.location.href = 'adminPAYROLLPERIODS.php';" class="inline-block  hover:bg-green-500 py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" style="width: 100px; height: 40px; color:#3c005a; background-color:#ffff; border" name="print_btn">View</button>

</div>
</div>
</div>
</div>
</div>

<div class="second" style="margin-top: 10px;">
  <div class="row">
    <div class="col-lg-4 col-md-12 " >
    <div class="card shadow" style="border-radius: 10px;">
    <div class="card-header" style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
          Attendance Chart
        </div>
  <div class="row">
    <div class="col-8 ">
      <div class="d-flex">
          <canvas id="attendanceChart" width="400" height="280"></canvas>
      </div>
    </div>
    <div class="col-4">

    <div class="card-group">
  <div class="row">
    <div class="card card11 my-3" style="color:#123123; background-color:transparent; border: transparent; ">
      <div class="present p-2">
        <h5>Present</h5>
        <div class="presnum d-flex justify-content-center">
          <h4 id="presentCounter" style="color:#4929aa"><?php echo $morningatt; ?></h4>
        </div>
      </div>
    </div>

    <div class="card card11" style="color:#123123; background-color:transparent; border: transparent;">
      <div class="absent p-2">
        <h5>Absent</h5>
        <div class="presnum d-flex justify-content-center">
          <h4 id="absentCounter" style="color: #00b464;"><?php echo $absents; ?></h4>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>


</div>

<!-- end ng una -->
<div class="col-lg-4 col-md-12" >
<div class="card shadow" style="border-radius: 10px;">
    <div class="card-header"style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
      Leave Chart
    </div>
<!-- Gauge Chart Canvas -->

<div class="container">
<canvas height="280" width="180" id="gaugeChart"></canvas>
</div>
</div>
</div>
<div class="col-lg-4 col-md-12" >
<div class="card shadow" style="border-radius: 10px;">
    <div class="card-header " style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
        Leave Pendings
    </div>
  <div class="card-body">
    <div class="d-flex justify-content-center" style="color:#1f155f;">      
      <h3><?php echo $leaveapps; ?> </h3>
    </div>
    <div class="d-flex justify-content-center">      
    <a href="adminLeaves.php" class="inline-block">
    <button type="button" class="inline-block bg-orange-300 hover:bg-orange-400 text-purple-900 py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out">
        Manage Leave
    </button>
</a>
    </div>
  </div>
</div>
  <div class="card shadow mt-2"style="color:#123123; background-color:white; border-radius: 10px;">
    <div class="card-header" style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
        Evening Service
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-center" style="color:#1f155f;">      
        <h3> <?php echo $numEmployeesWithEveningService; ?></h3>
      </div>
          <div class="d-flex justify-content-center">      
          <a href="adminEveningService.php" class="inline-block">
    <button type="button" class="inline-block bg-orange-300 hover:bg-orange-400 text-purple-900 py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out">
        Manage Evening Service
    </button>
</a>

      </div>
    </div>
   </div>
  </div>
<!-- end ng col-6 -->
</div>
<!-- end ng second row -->
</div>

<!-- end ng second -->
</div>


<!-- end ng content -->
</div>
 <!-- end ng row -->
</div>

<!--<script src="../js/maruti.dashboard.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script>
  $(document).ready(function () {
     $('input[name="dates"]').daterangepicker();
   });
</script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>-->
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>


 
<script>
  document.addEventListener('DOMContentLoaded', function () {
  var ctx = document.getElementById('attendanceChart').getContext('2d');
  // Data obtained from PHP
  var present = <?php echo $morningatt; ?>;
  var absent = <?php echo $absents; ?>;
  var totalEmployees = present + absent;

  // Calculate percentages
  var presentPercentage = (present / totalEmployees) * 100;
  var absentPercentage = (absent / totalEmployees) * 100;
  

  var data = {
    labels: ['Present', 'Absent'],
    datasets: [{
    data: [presentPercentage, absentPercentage],
    backgroundColor: ['#1f155f', '#00b464'] // Default color added here
    }]
  };

  var options = {
  legend: {
    display: true
  },
  responsive: true,
  maintainAspectRatio: false // Set to false to make the chart responsive
};

var attendanceChart = new Chart(ctx, {
  type: 'bar',
  data: data,
  options: options
});


  var attendanceChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options
  });
  
});
</script>
<script>
    var data = {
    value1: <?php echo isset($undertimeatt) ? $undertimeatt : 1; ?>,
    value2: <?php echo isset($leavesarray) ? $leavesarray : 1; ?>,
    totalEmployees: <?php echo isset($numemps) ? $numemps : 100; ?>,
    label1: 'Undertime',
    label2: 'Employee on Leave'
};

var config = {
    type: 'doughnut',
    data: {
        labels: [data.label1, data.label2],
        datasets: [{
            data: [data.value1, data.value2],
            backgroundColor: ['#1f155f', '#00b464'],
            borderWidth: 0
            
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 85,
        rotation: -90,
        circumference: 180,
        tooltips: {
            enabled: false
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                boxWidth: 15,
                fontColor: '#333'
            },
            backgroundColor: '#f8f9fa'
        },
        animation: {
            animateRotate: true,
            animateScale: false
        },
        title: {
            display: true,
            text: "Double Data Overlapping",
            fontSize: 16
        }
    }
};

var chartCtx = document.getElementById('gaugeChart').getContext('2d');
var gaugeChart = new Chart(chartCtx, config);

</script>
<script>
 
  function animateValue(id, start, end, duration) {
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    var obj = document.getElementById(id);
    var timer = setInterval(function () {
      if (current != end) {
        current += increment;
        obj.innerHTML = current;
      }
      if (current == end) {
        clearInterval(timer);
      }
    }, stepTime);
  }

  // Assuming $empsnum is your PHP variable holding the end value
  animateValue("employeesCounter", 0, <?php echo $empsnum ?>, 2000);
</script>

<script>
  function animateValue(id, start, end, duration) {
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    var obj = document.getElementById(id);
    var timer = setInterval(function () {
      if (current != end) {
            current += increment;
            obj.innerHTML = current;
      }
      if (current == end) {
        clearInterval(timer);
      }
    }, stepTime);
  }


  animateValue("issueCounter", 0, <?php echo $selectissuess ?>, 2000); 
</script>

<script>

  function animateValue(id, start, end, duration) {
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    var obj = document.getElementById(id);
    var timer = setInterval(function () {
      current += increment;
      if (increment > 0 && current >= end) {
        current = end;
      } else if (increment < 0 && current <= end) {
        current = end;
      }
      obj.innerHTML = current;
      if (current == end) {
        clearInterval(timer);
      }
    }, stepTime);
  }


  animateValue("presentCounter", 0, <?php echo $morningatt; ?>, 2000); 
  animateValue("absentCounter", 0, <?php echo $absents; ?>, 2000);
</script>
<script>

  function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
    document.getElementById('realTime').textContent = timeString;
  }

  updateTime();

  setInterval(updateTime, 1000);
</script>
     


<script>
  // Function to animate number counters
  function animateValue(id, start, end, duration) {
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    var obj = document.getElementById(id);
    var timer = setInterval(function () {
      current += increment;
      if (increment > 0 && current >= end) {
        current = end;
      } else if (increment < 0 && current <= end) {
        current = end;
      }
      obj.innerHTML = current;
      if (current == end) {
        clearInterval(timer);
      }
    }, stepTime);
  }

  // Call animateValue function to animate the number
  animateValue("maleCounter", 0, <?php echo $genderArray['numMales']; ?>, 2000); // Change duration as per your preference
  animateValue("femaleCounter", 0, <?php echo $genderArray['numFemales']; ?>, 2000); // Change duration as per your preference
</script>
<style>
#filterOptions {
  transition: max-height 0.9s ease;
  max-height: 0;
  overflow: hidden;
}

 </style>
<!--<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-pzjw8f+uxSp2F5R1WNfBpkJLEicf5f8CYEv8BH+LZ8AM+CI0UQTZ2GGSnGOIdRENU" crossorigin="anonymous"></script>-->

</body>
</html>
