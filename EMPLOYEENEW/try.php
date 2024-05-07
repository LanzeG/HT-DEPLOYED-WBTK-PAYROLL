
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];

//for checking if there are 5 absent
date_default_timezone_set('Asia/Manila');
$flagTable = 'dashboard_flag';
$currentHour = date('H:i A'); // This will give you the current time in 12-hour format with AM/PM
$currentDate = date('Y-m-d');


$getinfoqry = "SELECT * from employees WHERE user_name = '$uname'";
$getinfoexecqry = mysqli_query($conn,$getinfoqry) or die ("FAILED TO GET INFORMATION ".mysqli_error($conn));
$getinfoarray = mysqli_fetch_array($getinfoexecqry);
$getinforows = mysqli_num_rows($getinfoexecqry);
$period_start = null;
$period_end = null;


if ($getinfoarray && $getinforows !=0){

  $currprefixid = $getinfoarray['prefix_ID'];
  $currempid = $getinfoarray['emp_id'];
  $currfingerprintid = $getinfoarray['fingerprint_id'];
  $currusername = $getinfoarray['user_name'];
  $currlastname = $getinfoarray['last_name'];
  $currfirstname = $getinfoarray['first_name'];
  $currmiddlename = $getinfoarray['middle_name'];
  $currdateofbirth = $getinfoarray['date_of_birth'];
  $currposition = $getinfoarray['position'];
  $curremptype = $getinfoarray['employment_TYPE'];
  $curraddress = $getinfoarray['emp_address'];
  $currnationality = $getinfoarray['emp_nationality'];
  $currdeptname = $getinfoarray['dept_NAME'];
  $currcontact = $getinfoarray['contact_number'];
  $currdatehired = $getinfoarray['date_hired'];
  $currimg = $getinfoarray['img_name'];
  $_SESSION['empID'] = $currempid;
}

if (isset($_POST['pperiod_btn1'])) {
  $payfunction = $_POST['payfunction'];
  $payperiod = $_POST['payperiod'];
  $_SESSION['payperiods'] = $_POST['payperiod'];
  $_SESSION['payfunction'] = $_POST['payfunction'];

  echo '<script>';
  echo 'var url = "empaction.php?payfunction=' . urlencode($payfunction) . '&payperiod=' . urlencode($payperiod) . '";';
  echo 'window.open(url, "_blank");';
  echo '</script>';
      
} elseif (isset($_POST['pperiod_btn'])){

  $payperiod = $_POST['payperiod'];
  $_SESSION['payperiods'] = $_POST['payperiod'];
  $searchquery = "SELECT * FROM employees, pay_per_period WHERE employees.emp_id = pay_per_period.emp_id AND pay_per_period.emp_id = '$empid' AND pay_per_period.pperiod_range = '$payperiod' ORDER BY pperiod_range";
  $search_result = filterTable($searchquery);
      
}  else  {
  $searchquery = "SELECT * from employees, pay_per_period WHERE employees.emp_id = pay_per_period.emp_id AND pay_per_period.emp_id = '$empid' ORDER BY pay_per_period.pperiod_range ";  
  // $search_result = filterTable($searchquery);
}
      
if (isset($payperiod)) {
  $query = "SELECT * FROM payperiods WHERE pperiod_range = '$payperiod'";
  $result = mysqli_query($conn, $query);
        
  if ($result) {
    $data = mysqli_fetch_assoc($result);
    $period_start = isset($data['pperiod_start']) ? $data['pperiod_start'] : null;
    $period_end = isset($data['pperiod_end']) ? $data['pperiod_end'] : null;
    $dateTime = new DateTime($period_start);
    $month = $dateTime->format('F'); 
    $year = $dateTime->format('Y'); 
}
       
    $printquery = "SELECT * FROM dtr, employees WHERE dtr.emp_id = employees.emp_id and dtr.emp_id = '$empid' AND dtr.dtr_day BETWEEN '$period_start' and '$period_end' ORDER BY dtr_day ASC";
    $printqueryexec = mysqli_query($conn,$printquery);
    $printarray = mysqli_fetch_array($printqueryexec);
    $d = strtotime("now");
    $currtime = date ("Y-m-d H:i:s", $d);
       
if ($printarray){
       
  $prefix = $printarray['prefix_ID'];
  $idno = $printarray['emp_id'];
  $lname = $printarray['last_name'];
  $fname = $printarray['first_name'];
  $mname = $printarray['middle_name'];
  $dept = $printarray['dept_NAME'];
  $position = $printarray['position'];
       
  $name = "$lname, $fname $mname";
  $empID = "$prefix$idno";
}
       
       
  $payperiodval = "SELECT dtr.*,(time_keeping.hours_work) as totalhours,time_keeping.hours_work FROM dtr INNER JOIN time_keeping ON time_keeping.emp_id=dtr.emp_id AND time_keeping.timekeep_day=dtr.dtr_day WHERE dtr.emp_id = '$empid' AND dtr_day BETWEEN '$period_start' AND '$period_end' ORDER BY dtr_day ASC";
  $payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

  $evening = "SELECT COUNT(*) AS evening_service_count FROM time_keeping WHERE emp_id = '$empid' AND timekeep_remarks = 'Evening Service' AND timekeep_day BETWEEN '$period_start' AND '$period_end'";
  $eveningexec = mysqli_query($conn,$evening) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));
  $row = mysqli_fetch_assoc($eveningexec);
  $evening_service_count = $row['evening_service_count'];

       
  $totalot = "SELECT SUM(undertime_hours) as totalUT,  SUM(hours_work) as totalWORKhours FROM time_keeping WHERE emp_id = '$empid' AND timekeep_day BETWEEN '$period_start' and '$period_end' ORDER BY timekeep_day ASC";
  $totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
  $totalotres = mysqli_fetch_array($totalotexec);
       
  $searchquery = "SELECT * from employees, pay_per_period WHERE employees.emp_id = pay_per_period.emp_id AND pay_per_period.emp_id = '$empid' ORDER BY pay_per_period.pperiod_range ";  
  $search_result = filterTable($searchquery);
  $attquery = "SELECT
         emp_id,
         SUM(CASE WHEN in_morning > 0 THEN 1 ELSE 0 END) AS TOTAL_ATTENDANCE,
         SUM(CASE WHEN undertime_hours > 0 THEN 1 ELSE 0 END) AS TOTAL_UNDERTIME_HOURS
       FROM
         time_keeping
       WHERE
         emp_id = $empid
         AND timekeep_day BETWEEN '$period_start' AND '$period_end'
       GROUP BY
         emp_id";
        
        $resultattquery = mysqli_query($conn, $attquery) or die(mysqli_error($conn));
        $rowattquery = mysqli_fetch_assoc($resultattquery);
       }
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Employee</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- <link rel="stylesheet" href="../../css/bootstrap.min.css" /> -->
<!-- <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" /> -->
<!--<link rel="stylesheet" href="../../css/fullcalendar.css" />-->
<!--<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">-->
<!--<link rel="stylesheet" href="../timeline.css">-->
<link rel="stylesheet" href="../css/emp.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.css">
<script src="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.js"></script>
 <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../jquery-ui-1.12.1/jquery-ui.js"></script>
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
<header>
  <?php include('navbar2.php'); ?> 
</header>
<body>
<div class="content">
<div class="d-flex justify-content-center text-center">
  <div class="title flex-column align-content-center  justify-content-center pt-5">
    <div class="col">
      <h4>
        EMPLOYEE DASHBOARD
      </h4>
    </div>
    <h4>

    </h4>
    <div class="col text-center">
      <h7>
        <?php
        date_default_timezone_set('Asia/Manila');
        $flagTable = 'dashboard_flag';
        $currentHour = date('h:i A'); 
        $currentDate = date('F j, Y');
        ?>
        <span id="realDate"><?php echo $currentDate; ?></span> | <span id="realTime"></span>
      </h7> 
    </div>
  </div>
</div>

<script>
  function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // Handle midnight
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
    document.getElementById('realTime').textContent = timeString;
  }

  // Update time every second
  setInterval(updateTime, 1000);

  // Initial update
  updateTime();
</script>

    <div class="row mt-3">
        <div class="col-xl-12 col-lg" style="margin-top: 0px;">
            <div class="col-12">
                <div class="content">
                    <div class="row row-cols-1 row-cols-md-3">
                        <div class="card1 text-bg-info col-md-4 col-sm-12 background-small-left">
                            <div class="h-100">
                                <div class="title mt-2">TOTAL ATTENDANCE</div>
                                <div class="card-body text-center" style="color:#4929aa;">
                                    <h3><span id="totalAttendance"><?php echo isset($rowattquery['TOTAL_ATTENDANCE']) ? $rowattquery['TOTAL_ATTENDANCE'] : 0; ?></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="card1 text-bg-info col-md-4 col-sm-12 background-small-left-1">
                            <div class="h-100">
                                <div class="title mt-2">TOTAL UNDERTIME</div>
                                <div class="card-body text-center" style="color:#4929aa;">
                                    <h3><span id="totalUndertime"><?php echo isset($rowattquery['TOTAL_UNDERTIME_HOURS']) ? $rowattquery['TOTAL_UNDERTIME_HOURS'] : 0; ?></span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="card1 text-bg-info col-md-4 col-sm-12 background-small-left-2">
                            <div class="h-100">
                                <div class="title mt-2">TOTAL EVENING SERVICE</div>
                                <div class="card-body text-center" style="color:#4929aa;">
                                    <h3><span id="totalEveningService"><?php echo isset( $evening_service_count) ?  $evening_service_count : 0; ?></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6" style="margin-top: 20px;">
    <div class="card shadow" style="border-radius: 10px;">
        <div class="card-header" style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
            Salary Chart
        </div>
        <div class="container">
            <div class="chart pb-2 d-flex justify-content-center"> 
            <canvas id="myPieChart"></canvas>
            </div>
            <form action="" method="post">
    <div class="row mt-1 pb-2">
        <div class="col-6">
          <select class="form-select form-select-sm" id="sel" aria-label="Small select example" name="payfunction">
              <option value="Generate Payslip" <?php echo (isset($_SESSION['payfunction']) && $_SESSION['payfunction'] == 'Generate Payslip') ? 'selected' : ''; ?>>PRINT PAYSLIP</option>
              <option value="View DTR" <?php echo (isset($_SESSION['payfunction']) && $_SESSION['payfunction'] == 'View DTR') ? 'selected' : ''; ?>>PRINT DTR</option>
              <!-- <option value="View Timesheet" <?php echo (isset($_SESSION['payfunction']) && $_SESSION['payfunction'] == 'View Timesheet') ? 'selected' : ''; ?>>View Timesheet</option> -->
              <option value="View Leaves" <?php echo (isset($_SESSION['payfunction']) && $_SESSION['payfunction'] == 'View Leaves') ? 'selected' : ''; ?>>PRINT LEAVE RECORD</option>
           </select>
          
        </div>
        <div class="col-6">
      <!-- <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> -->
    <?php
    $payperiodsquery = "SELECT * FROM payperiods";
    $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY " . mysqli_error($conn));
    ?>

        <select name="payperiod" class="form-select form-select-sm" id="sel2" required>
    <option value="" disabled selected>Select payroll period</option>
                <?php
                while ($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)) {
                    $selected = ($payperiodchoice['pperiod_range'] == $_SESSION['payperiods']) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $payperiodchoice['pperiod_range']; ?>" <?php echo $selected; ?>>
                        <?php echo $payperiodchoice['pperiod_range']; ?>
                    </option>
                <?php } ?>
            </select>
      </div>
    <div class="button d-flex justify-content-center align-items-center pt-2">
    <button type="submit" class="btn btn-primary printbtn btn-sm bg-blue-green-500 hover:bg-blue-green-600 text-white font-bold py-2 px-2 rounded-full inline-flex items-center" name="pperiod_btn1" style="width: 100px;">
        Generate
    </button>
    <div class="uinfotab3">
        <a href="try.php" class="btn btn-success btn-sm bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-2 rounded-full inline-flex items-center" style="width: 100px; margin: 0 2px;">
            <span class="icon"><i class="icon-refresh"></i></span> Refresh
        </a>
    </div>
    <button type="submit" class="btn btn-primary printbtn btn-sm bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full inline-flex items-center" name="pperiod_btn" style="width: 100px; margin-left: 2px;">
        Select
    </button>
</div>


</div>
</div>
</div>
</div>
    <div class="col-lg-4 col-md-6" style="margin-top: 20px;">
        <div class="card shadow" style="border-radius: 10px;">
            <div class="card-header" style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #2ff29e; color: #4929aa;">
                Monthly Attendance
            </div>
            <div class="container">
            <canvas id="lineChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>

<?php

// Query to fetch the earliest recorded month for the employee
$earliestMonthQuery = "
    SELECT
        MIN(dtr.dtr_day) AS earliest_month
    FROM
        dtr
    WHERE
        dtr.emp_id = '$empid'
";

$earliestMonthResult = mysqli_query($conn, $earliestMonthQuery) or die(mysqli_error($conn));
$earliestMonthRow = mysqli_fetch_assoc($earliestMonthResult);
$earliestMonth = $earliestMonthRow['earliest_month'];

// If the earliest month is null, set it to the beginning of the current year
if (!$earliestMonth) {
    $earliestMonth = date('Y-01-01');
}

// Check if payroll period is selected
if (!isset($period_start) || !isset($period_end)) {
    // If no period is selected, fetch data for all months since the earliest recorded month
    $attendanceQuery = "
        SELECT
            YEAR(dtr.dtr_day) AS year,
            MONTH(dtr.dtr_day) AS month,
            COUNT(*) AS attendance_count,
            (SELECT COUNT(*) FROM absences WHERE emp_id = dtr.emp_id AND MONTH(absence_date) = MONTH(dtr.dtr_day)) AS absence_count
        FROM
            dtr
        INNER JOIN
            time_keeping ON time_keeping.emp_id = dtr.emp_id AND time_keeping.timekeep_day = dtr.dtr_day
        WHERE
            dtr.emp_id = '$empid'
            AND dtr.dtr_day >= '$earliestMonth'
        GROUP BY
            YEAR(dtr.dtr_day), MONTH(dtr.dtr_day)
        ORDER BY
            YEAR(dtr.dtr_day) ASC, MONTH(dtr.dtr_day) ASC
    ";
} else {
    // If period is selected, fetch data based on selected period
    $attendanceQuery = "
        SELECT
            YEAR(dtr.dtr_day) AS year,
            MONTH(dtr.dtr_day) AS month,
            COUNT(*) AS attendance_count,
            (SELECT COUNT(*) FROM absences WHERE emp_id = dtr.emp_id AND MONTH(absence_date) = MONTH(dtr.dtr_day)) AS absence_count
        FROM
            dtr
        INNER JOIN
            time_keeping ON time_keeping.emp_id = dtr.emp_id AND time_keeping.timekeep_day = dtr.dtr_day
        WHERE
            dtr.emp_id = '$empid'
            AND dtr.dtr_day BETWEEN '$period_start' AND '$period_end'
        GROUP BY
            YEAR(dtr.dtr_day), MONTH(dtr.dtr_day)
        ORDER BY
            YEAR(dtr.dtr_day) ASC, MONTH(dtr.dtr_day) ASC
    ";
}

$attendanceResult = mysqli_query($conn, $attendanceQuery) or die(mysqli_error($conn));

$months = [];
$attendanceData = [];
$absenceData = [];

// Query for all months since the employee was added to the system
while ($row = mysqli_fetch_assoc($attendanceResult)) {
    $months[] = date("M Y", mktime(0, 0, 0, intval($row['month']), 1, intval($row['year'])));
    $attendanceData[] = intval($row['attendance_count']);
    $absenceData[] = intval($row['absence_count']);
}

// Generate labels for all months between the earliest recorded month and the current month
$startDate = new DateTime($earliestMonth);
$currentDate = new DateTime();
$interval = new DateInterval('P1M');
$dateRange = new DatePeriod($startDate, $interval, $currentDate);

foreach ($dateRange as $date) {
    $months[] = $date->format('M Y');
    $attendanceData[] = 0;
    $absenceData[] = 0;
}

// Ensure data is sorted by month
array_multisort(array_map('strtotime', $months), SORT_ASC, $months, $attendanceData, $absenceData);
?>

<script>
var ctx = document.getElementById('lineChart').getContext('2d');
var lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Attendance',
            data: <?php echo json_encode($attendanceData); ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 1
        },
        {
            label: 'Absences',
            data: <?php echo json_encode($absenceData); ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        responsive: true,
        maintainAspectRatio: false 
    }
});
</script>



          <!-- New Card Section -->
<div class="col-lg-4 col-md-6" style="margin-top: 20px;">
    <div class="card shadow" style="border-radius: 10px; height: 440px;">
            <div style="max-height: 450px; overflow-y: auto; overflow-x: hidden; border-radius: 10px;">
                <div class="row" style="position: sticky; top: 0; background-color: white; z-index: 1;">
                    <div class="col">
                        <div class="card mb-0" style="border-top-left-radius: 10px; border-top-right-radius: 10px;  background-color: #2ff29e; color: #4929aa;">
                            <div class="shadow-body p-2">
                            <div class="header-container">
                                <div class="header-item">
                                   <span class="header-text">Date</span>
                                </div>
                                <div class="header-item">
                                   <span class="header-text">In</span>
                                </div>
                                <div class="header-item">
                                    <span class="header-text">Out</span>
                                </div>
                                <div class="header-item">
                                    <span class="header-text">Reg. Hours</span>
                                </div>
                                <div class="header-item">
                                    <span class="header-text">Remarks</span>
                                </div>
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($payperiodexec)) {
                    if (mysqli_num_rows($payperiodexec) > 0) {
                        while ($payperiodarray = mysqli_fetch_array($payperiodexec)) {
                            $dtrday = $payperiodarray['DTR_day'];
                            $day = date('d', strtotime($dtrday));
                            $hrswrk = $payperiodarray['hours_work'];
                            ?>
                            <div class="row">
                                <div class="col" style="background-color: #E6EAFE;">
                                    <div class="card mb-0">
                                        <div class="card-body" >
                                            <div class="row ">
                                                <div class="col ml-3"><?php echo $day; ?></div>
                                                <div class="col"><?php echo $payperiodarray['in_morning']; ?></div>
                                                <div class="col"><?php echo $payperiodarray['out_afternoon']; ?></div>
                                                <div class="col"><?php echo $hrswrk; ?></div>
                                                <div class="col" style="font-size: 11px;"><?php echo $payperiodarray['DTR_remarks']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                               }
                              } else {
                                $dateRange = $_SESSION['payperiods'];
                                $dateParts = explode(' to ', $dateRange);
                                if (count($dateParts) === 2) {
                                   $monthAbbreviations = [
                                    'January' => 'Jan',
                                    'February' => 'Feb',
                                    'March' => 'Mar',
                                    'April' => 'Apr',
                                    'May' => 'May',
                                    'June' => 'Jun',
                                    'July' => 'Jul',
                                    'August' => 'Aug',
                                    'September' => 'Sep',
                                    'October' => 'Oct',
                                    'November' => 'Nov',
                                    'December' => 'Dec'
                                ];
                                
                                $startDate = date('F j, Y', strtotime($dateParts[0]));
                                $startDateParts = explode(' ', $startDate);
                                $startDateParts[0] = $monthAbbreviations[$startDateParts[0]];
                                $startDate = implode(' ', $startDateParts);
                                $startDate = strtoupper($startDate);
                                
                                $endDate = date('F j, Y', strtotime($dateParts[1]));
                                $endDateParts = explode(' ', $endDate);
                                $endDateParts[0] = $monthAbbreviations[$endDateParts[0]];
                                $endDate = implode(' ', $endDateParts);
                                $endDate = strtoupper($endDate);
                                   echo "<tr><td colspan='6' style='text-align: center;'>
                                    <img src='../img/carousel/chart3d.png' alt='' style='max-width: 40%; 
                                    display: block; margin: 0 auto; margin-top: 7%;'><br><span style='display: block; 
                                    width: fit-content; margin: 0 auto; padding-bottom: 13%; font-size: 90%;'> NO RECORD FOR <span style='color: red;'>$startDate</span> - <span style='color: red;'>$endDate</span></span></td></tr>";

                                } else {
                                    echo "<tr><td col='6'>NO DATA FOUND ON " . $_SESSION['payperiods'] . "</td></tr>";
                                }
                              }
                          } else {
                             echo "<tr><td colspan='6' style='text-align: center;'>
                             <img src='../img/carousel/shield3d.png' alt='' style='max-width: 30%; 
                             display: block; margin: 0 auto; margin-top: 15%;'>
                             <br><span style='display: block; width: fit-content; margin: 0 auto; padding-bottom: 15%; '>
                             NO SELECTED PAYROLL PERIOD</span></td></tr>";
                          }
                        ?>
                    </div>
              </div>
        </div>
<?php

function filterTable($searchquery)
{

     $conn1 = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
     $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
     return $filter_Result;
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
unset($_SESSION['changepassnotif']);
?>
<div class="widget-title"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Initial static data
    var staticData = [
        { label: 'Label 1', value: 50 },
        { label: 'Label 2', value: 30 },
    ];

    var initialChart = createCustomPieChart(staticData, 'myPieChart', 200, 200);
    fetch('fetch_data.php')
        .then(response => response.json())
        .then(data => updateChartWithData(initialChart, data))
        .catch(error => console.error("Error fetching data:", error));
    function createCustomPieChart(data, chartId, chartWidth, chartHeight) {
        var labels = data.map(item => item.label);
        var values = data.map(item => item.value);
        var ctx = document.getElementById(chartId).getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: getRandomColors(values.length),
                }],
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                width: chartWidth,
                height: chartHeight,
            },
        });

        return myPieChart; 
    }

    function updateChartWithData(chart, newData) {
        var labels = newData.map(item => item.label);
        var values = newData.map(item => item.value);

        chart.data.labels = labels;
        chart.data.datasets[0].data = values;

        chart.data.datasets[0].backgroundColor = getRandomColors(values.length);

        chart.update();
    }

    function getRandomColors(count) {
    var colors = ['#28DF99']; 
    for (var i = 1; i < count; i++) {
        var hue = (360 / (count - 1)) * i;
        colors.push(`hsl(${hue}, 70%, 60%)`);
    }
    return colors;
}
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

  animateValue("totalAttendance", 0, <?php echo isset($rowattquery['TOTAL_ATTENDANCE']) ? $rowattquery['TOTAL_ATTENDANCE'] : 0; ?>, 2000);
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

  animateValue("totalLate", 0, <?php echo isset($rowattquery['TOTAL_LATE_HOURS']) ? $rowattquery['TOTAL_LATE_HOURS'] : 0; ?>, 2000);
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
  animateValue("totalUndertime", 0, <?php echo isset($rowattquery['TOTAL_UNDERTIME_HOURS']) ? $rowattquery['TOTAL_UNDERTIME_HOURS'] : 0; ?>, 2000);
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
</body>
<style>
  #myPieChart {
  max-width: 300px; /* Adjust maximum width as needed */
  width: 100%;
  height: auto;
  }

  #lineChart {
    max-height: 400px;
    width: 180px;
  }

  .swal2-toast {
    

    color: #000000 !important;
  }

.header-container {
    display: flex;
    justify-content: space-between;
}
.header-item {
    flex: 1;
    text-align: center;
}
.header-text {
    font-size: 16px;
}
.col{
    flex: 1;
    text-align: center;
    justify-content: space-between;
}
@media (max-width: 768px) {
    .col{
     font-size: 12px; 
    }
    .header-text {
        font-size: 10px; 
    }
}
</style>
  </body>
</html>
