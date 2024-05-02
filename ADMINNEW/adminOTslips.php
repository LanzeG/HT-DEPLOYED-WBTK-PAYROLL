<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if (isset($_GET['refresh']) && $_GET['refresh'] == 'true') {
  // Set start and end dates to empty strings

  // Redirect to the same page to avoid refresh on re-submission
  header("Location: adminDTR.php");
  exit();
}


$results_perpage = 20;

               if (isset($_GET['page'])){

                    $page = $_GET['page'];
               } else {

                    $page=1;
               }
               $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
               $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
if (isset($_GET['print_btn'])){
  if (!$_GET['payperiod'] == '' ){
  $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
  $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
  $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
  $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
  $gender = isset($_GET['Gender']) ? $_GET['Gender'] : '';
  $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
  $selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
  $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
  $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
  $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod']:'';

  $deptFilter = $deptchecked ? $deptchecked : '';
  $emptypeFilter = $emptypechecked ? $emptypechecked : '';
  $shiftFilter = $shiftchecked ? $shiftchecked : '';
  $positionFilter = $positionchecked ? $positionchecked : '';
  $genderFilter = $gender ? "'" . $gender . "'" : ''; // Assuming gender is a string in the database
  $employeeStatusFilter = $employeeStatus ? "'" . $employeeStatus . "'" : ''; // Assuming employee_status is a string in the database

  $monthFilter = $selectedMonth ? "'" . $selectedMonth . "'" : '';
  $dayFilter = $selectedDay ? "'" . $selectedDay . "'" : '';
  $yearFilter = $selectedYear ? "'" . $selectedYear . "'" : '';

  $filterByFilter = $filterBy ? $filterBy : '';  // New parameter
  $searchValueFilter = $searchValue ? "" . $searchValue . "" : ''; 

  
  $filterConditions = [];

    if ($deptFilter) {
        $filterConditions[] = "department.dept_ID IN ($deptFilter)";
    }

    if ($emptypeFilter) {
        $filterConditions[] = "employmenttypes.employment_ID IN ($emptypeFilter)";
    }

    if ($shiftFilter) {
        $filterConditions[] = "shift.shift_ID IN ($shiftFilter)";
    }
    // if ($positionFilter) {
    //     $filterConditions[] = "position.position_id IN ($positionFilter)";
    // }
    if ($positionFilter) {
      $filterConditions[] = "employees.position = $positionFilter";
    }
    if ($genderFilter) {
      $filterConditions[] = "employees.emp_gender = $genderFilter";
    }

    if ($employeeStatusFilter) {
        $filterConditions[] = "employees.emp_status = $employeeStatusFilter";
    }

    if ($monthFilter) {
        $filterConditions[] = "MONTH(employees.date_hired) = $monthFilter";
    }
    
    if ($dayFilter) {
        $filterConditions[] = "DAY(employees.date_hired) = $dayFilter";
    }
    
    if ($yearFilter) {
        $filterConditions[] = "YEAR(employees.date_hired) = $yearFilter";
    }
  
    if ($filterByFilter && $searchValueFilter) {
      // Add a condition for the specific search based on the selected field
      $filterConditions[] = "LOWER(employees.$filterByFilter)  LIKE LOWER ('%$searchValueFilter%')";
  }

if (!empty($filterConditions)) {
  $searchquery = "SELECT DISTINCT employees.emp_id, employees.*, dtr.*, department.*, employmenttypes.*, shift.*
  FROM employees
  JOIN dtr ON employees.emp_id = dtr.emp_id
  JOIN department ON department.dept_NAME = employees.dept_NAME 
  JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
  JOIN shift ON shift.shift_SCHEDULE = employees.shift_SCHEDULE
  JOIN over_time ON over_time.emp_id = employees.emp_id
  WHERE " . implode(" AND ", $filterConditions);
  $start_from = ($page - 1) * $results_perpage;
}
else {
  $searchquery = "SELECT DISTINCT employees.emp_id, employees.*, dtr.*, department.*, employmenttypes.*, shift.*
    FROM employees
    JOIN dtr ON employees.emp_id = dtr.emp_id
    JOIN department ON department.dept_NAME = employees.dept_NAME 
    JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
    JOIN shift ON shift.shift_SCHEDULE = employees.shift_SCHEDULE
    JOIN over_time ON over_time.emp_id = employees.emp_id";
    $start_from = ($page - 1) * $results_perpage;
}

$pperiodquery = "SELECT * FROM payperiods WHERE payperiod_ID = '$payperiod'";
  $pperiodexecquery = mysqli_query($conn, $pperiodquery) or die ("FAILED TO SET PAY PERIOD ".mysqli_error($conn));
  $pperiodarray = mysqli_fetch_array($pperiodexecquery);
  error_log($pperiodquery);

      if ($pperiodarray){
        $_SESSION['payperiodfrom'] = $pperiodarray['pperiod_start'];
        $_SESSION['payperiodto'] = $pperiodarray['pperiod_end'];
        $_SESSION['payperiodrange'] = $pperiodarray['pperiod_range'];
        $_SESSION['payperioddayss'] = $pperiodarray['payperiod_days'];
      

        $searchquery .= " AND over_time.ot_day BETWEEN '" . $_SESSION['payperiodfrom'] . "' AND '" . $_SESSION['payperiodto'] . "'";

      }


$searchquery .= " GROUP BY employees.emp_id ORDER BY $sortColumn $sortOrder LIMIT $start_from, $results_perpage";
// echo "Generated Query: $searchquery<br>";
$_SESSION['printot'] = $searchquery;

$search_result = filterTable($searchquery);
    // Count total rows in the limited result set
    $totalrows = mysqli_num_rows($search_result);

    // Calculate total pages
    $totalpages = ceil($totalrows / $results_perpage);

    // echo "Number of Rows: " . mysqli_num_rows($search_result) . "<br>";

echo $searchquery;
 
} else {

  $start_from = ($page-1) * $results_perpage;
        // $searchvalue = $_GET['searchvalue'];
        $searchvalue = isset($_GET['searchvalue']) ? $_GET['searchvalue'] : '';
        $searchquery = "SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id";
        $search_result = filterTable($searchquery);
        // echo "Generated Query: $searchquery";
        $_SESSION['printtimesheet_query'] = $searchquery;
        // Generated Query: SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id ASC LIMIT 0,20
        $_SESSION['payperiodto'] = '';
        $_SESSION['payperiodfrom'] ='';

}
  
} else {

  $start_from = ($page-1) * $results_perpage;
        // $searchvalue = $_POST['searchvalue'];
        $searchquery = "SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id ASC LIMIT $start_from,".$results_perpage;
        $search_result = filterTable($searchquery); $start_from = ($page-1) * $results_perpage;
        // $searchvalue = $_GET['searchvalue'];
        $searchvalue = isset($_GET['searchvalue']) ? $_GET['searchvalue'] : '';
        $searchquery = "SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id ASC LIMIT $start_from,".$results_perpage;
        $search_result = filterTable($searchquery);
        // echo "Generated Query: $searchquery";
        $_SESSION['printot'] = $searchquery;
        // Generated Query: SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id ASC LIMIT 0,20
        $_SESSION['payperiodto'] = '';
        $_SESSION['payperiodfrom'] ='';

}

$countdataqry = "SELECT COUNT(emp_id) AS total FROM employees";
// $countdataqryresult = mysqli_query($conn,$countdataqry) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());      
// $row = $countdataqryresult->fetch_assoc();
// $totalpages=ceil($row['total'] / $results_perpage);
          

?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Home</title>
<link rel="icon" type="image/png" href="../img/icon1 (3).png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- <link rel="stylesheet" href="../css/bootstrap.min.css" /> -->
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <!-- <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/maruti-style.css" /> -->
    <link rel="stylesheet" href="../css/maruti-media.css" class="skin-color" />
</head>
<body>

<!--Header-part-->



<?php
INCLUDE ('NAVBARadmin.php');
?>

<div id="content">

  

   
    <div class = "widget-box">
    <div class="widget-title" style="text-align: center;">
    <span class="icon"> <i class="icon-align-justify"></i> </span>
    <h3 class="">ADMIN OT SLIPS</h3>
</div>

          <hr>
          <div class="widget-content nopadding col-10 card shadow mx-auto my-5 p-3 mt-5 col-12">
            <form method="GET" action="">
        <?php
        $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
        $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
        $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
        $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
        $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
        $month = isset($_GET['month']) ? $_GET['month'] : '';
        $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
        $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
        $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod']:'';
        
        $query ="SELECT * FROM department";
        $total_row = mysqli_query($conn,$query) or die('error');
        ?> <div class="selections">
        <style>
                /* Add CSS styles here */
        
               
            </style>
        <div class="select-container">
          <div class="row">
            <div class="col-6">
            <div class="row1 pt-2">
        <h6>Department</h6> 
                <select name="dept" class="form-select">
                <option value="" >Select Department</option><?php
                if (mysqli_num_rows($total_row) > 0) {
                    foreach ($total_row as $row) {
                        ?>
                        <option value="<?php echo $row['dept_ID']; ?>" <?php if ($deptchecked == $row['dept_ID']) echo "selected"; ?>>
                            <?php echo $row['dept_NAME']; ?>
                        </option>
                        <?php
                    }
                } else {
                    echo 'No Data Found';
                }
                ?>
                </select>
                <?php
                $query1 ="SELECT * FROM employmenttypes";
                $total_row = mysqli_query($conn,$query1) or die('error');
                ?> <h6>Employment Type</h6>
                <select name="employmenttype" class="form-select"  id="employmenttype" style="">
                <option value="" >Select Employment Type</option><?php
                  if(mysqli_num_rows($total_row)>0){
                      foreach($total_row as $row){
                        ?>
                        <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->             
                        <option value="<?php echo $row['employment_ID']; ?>" <?php if ($emptypechecked == $row['employment_ID']) echo "selected"; ?>>
                        <?php echo $row['employment_TYPE']; ?>
                        </option>
                        <?php
        
                      }
                    }else{
                      echo'No Data Found';
                    }
                    ?>
                </select>
                
                <?php
                $query3 ="SELECT * FROM position";
                $total_row = mysqli_query($conn,$query3) or die('error');
                ?> <h6>Position</h6>
                <select name="position" class="form-select" id="position">
                <option value="">Select Position</option><?php
                  if(mysqli_num_rows($total_row)>0){
                      foreach($total_row as $row){
                        ?>
                        <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->             
                        <option value="<?php echo $row['position_name']; ?>" <?php if ($positionchecked == $row['position_name']) echo "selected"; ?>>
                        <?php echo $row['position_name']; ?>
                        </option>
                        <?php
        
                      }
                    }else{
                      echo'No Data Found';
                    }
                    ?>
                </select>
               </div>
            </div>
            <div class="col-6"><div class="select-container2"> 
                  <div class="row2 pt-2">
                <h6>Shift</h6>
                <select name="shifts" class="form-select" disabled>
                <!-- <option value="">Select Shift</option> -->
                <option value="2">8AM to 5PM</option>
                <?php
                  if(mysqli_num_rows($total_row)>0){
                      foreach($total_row as $row){
                        ?>
                        <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->
        
                        <option value="<?php echo $row['shift_ID']; ?>" <?php if ($positionchecked == $row['shift_ID']) echo "selected"; ?>>
                        <?php echo $row['shift_SCHEDULE']; ?>
                    </option>
                        <?php
        
                      }
                    }else{
                      echo'No Data Found';
                    }
                ?>
                </select>
                <h6>Gender</h6>
                <select name="Gender" class="form-select">
                    <option value="" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == '') echo 'selected'; ?>>Select Gender</option>
                    <option value="Male" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
        
                <h6>Employee Status</h6>
                <select name="employee_status" class="form-select">
                    <option value="" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == '') echo 'selected'; ?>>Select Employee Status</option>
                    <option value="Active" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
               </div>
               </div></div>
          </div>
        



               </div>
                <?php
                $query2 ="SELECT * FROM shift";
                $total_row = mysqli_query($conn,$query2) or die('error');
                ?>
                <h6>Date Hired</h6>
                <div class="row">
    
                <div class="col-4"><select name="month" class="form-select">
                  <option value="">Select Month</option>
                  <?php
                  $months = [
                      'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
                      'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
                  ];
        
                  foreach ($months as $monthName => $monthNumber) {
                      $selected = (isset($_GET['month']) && $_GET['month'] == $monthNumber) ? 'selected' : '';
                      echo '<option value="' . $monthNumber . '" ' . $selected . '>' . $monthName . '</option>';
                  }
                  ?>
              </select></div>
                
        
                <div class="col-4"><select name="day" class="form-select ">
                  <option value="" >Select Day</option>
                  <?php
                  // Adding options for days (assuming up to 31 for simplicity)
                  for ($day = 1; $day <= 31; $day++) {
                    $selected = (isset($_GET['day']) && $_GET['day'] == sprintf('%02d', $day)) ? 'selected' : '';
                    echo '<option value="' . sprintf('%02d', $day) . '" ' . $selected . '>' . sprintf('%02d', $day) . '</option>';
                  }
                  ?>
              </select></div>
                
                <div class="col-4">
                <select name="year" class="form-select">
                  <option value="">Select Year</option>
                  <?php
                  // Adding options for years (current year - 5 to current year + 5)
                  $currentYear = date("Y");
                  $startYear = $currentYear - 5;
                  $endYear = $currentYear + 5;
        
                  for ($year = $startYear; $year <= $endYear; $year++) {
                    $selected = (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : '';
                    echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                  }
                  ?>
              </select>
                </div>
            
                
                
                </div>
                
                
                      
        
              
              
              <?php
                $query5 ="SELECT * FROM payperiods";
                $total_row = mysqli_query($conn,$query5) or die('error');
                ?> <h6>Payroll Period</h6>
                <select name="payperiod" class="form-select">
                <option value="">Select Payroll Period</option>
                <?php
                  if(mysqli_num_rows($total_row)>0){
                      foreach($total_row as $row){
                        ?>
                        <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->
        
                        <option value="<?php echo $row['payperiod_ID']; ?>" <?php if ($payperiod == $row['payperiod_ID']) echo "selected"; ?>>
                        <?php echo $row['pperiod_range']; ?>
                    </option>
                  
        
                      </div>
                    </div>
                        <?php
        
                      }
                    }else{
                      echo'No Data Found';
                    }
                ?>
                </select>
                </div>
                <div class="select-container4 col-2 pt-3">
              <h6>Search by:</h6>
                <select name="filter_by" class="form-select ">
                    <option value="" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == '') echo 'selected'; ?>>Search by</option>
                    <option value="emp_id" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'emp_id') echo 'selected'; ?>>Employee ID</option>
                    <option value="last_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'last_name') echo 'selected'; ?>>Last Name</option>
                    <option value="first_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'first_name') echo 'selected'; ?>>First Name</option>
                    <option value="user_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'user_name') echo 'selected'; ?>>Username</option>
                </select>
                <div id ="search"  >
        <input type="text" class="form-control" placeholder="Search" name="search_value" value="<?php echo isset($_GET['search_value']) ? htmlspecialchars($_GET['search_value']) : ''; ?>" >
       
      </div>
          </div>
        
                
          <div class="form-actions" style="float:right;">
                <button type="submit" class="btn btn-success printbtn" name="print_btn">Submit</button>
                  </form>
                <!-- <a href="generate_pdf.php" class="btn btn-primary">Print all</a> -->
                <button class="btn btn-primary printbtn1"><a href="../adminnew/otpdfnew.php?print_all" class="" target="_blank">Print All</a></button>
                <button class="btn btn-primary printbtn1"><a href="../adminnew/otpdfnew.php?print_displayed" class="" target="_blank">Print Displayed</a></button>
                <button class="btn btn-success printbtn"><a href="../adminnew/admintimesheet.php?refresh=true" class="">
            <span class="icon"><i class="icon-refresh"></i></span> Refresh 
        </a></button>
              </div>
                </div>
                
<style>

.printbtn1 a {
    color: white; 
    text-decoration: none;
}
.printbtn a {
    color: white; 
    text-decoration: none; 
}


.printbtn {
    background-color: green; 
    border-color: green; 
}


.printbtn:hover {
    background-color: darkgreen; 
    border-color: darkgreen; 
}

</style>
              </div>

            </div>
      </div>
    </div>

 
    </div>
  </div>
  
    </div>
    
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="#tab1"><i class="icon-time"></i> DTRs</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
          <div id="tab1" class="tab-pane fade in active"><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
               
              
                  
                </div>
              
            </div>
                    
               <table class="table table-bordered data-table">
               <thead>
                <tr>
                  <th>Employee ID</th>
                  <th>
                    <a href="?print_btn=1&sort=last_name&order=<?php echo $sortColumn == 'last_name' ? ($sortOrder == 'asc' ? 'desc' : 'asc') : 'asc'; ?>&dept=<?php echo $deptchecked ?? ''; ?>&employmenttype=<?php echo $emptypechecked ?? ''; ?>&shifts=<?php echo $shiftchecked ?? ''; ?>&position=<?php echo $positionchecked ?? ''; ?>&Gender=<?php echo $gender ?? ''; ?>&employee_status=<?php echo $employeeStatus ?? ''; ?>&month=<?php echo $selectedMonth ?? ''; ?>&day=<?php echo $selectedDay ?? ''; ?>&year=<?php echo $selectedYear ?? ''; ?>&filter_by=<?php echo $filterBy ?? ''; ?>&search_value=<?php echo $searchValue ?? ''; ?>&payperiodfrom=<?php echo $_SESSION['payperiodfrom'] ?? ''; ?>&payperiodto=<?php echo $_SESSION['payperiodto'] ?? ''; ?>&payperiod=<?php echo $payperiod ?? ''; ?>">Last Name <?php echo ($sortColumn == 'last_name') ? ($sortOrder == 'asc' ? '&#9650;' : '&#9660;') : ''; ?></a>
                </th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Department</th>
                  <th>Shift</th>
                  <th>Employment Type</th>
                  <th>Action</th>
                  
                </tr>
              </thead>
              <tbody> 

               <?php

              

               function filterTable($searchquery)
               {

                    $conn1 = mysqli_connect("localhost:3307","root","","masterdb");
                    $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
                    return $filter_Result;
               }

               
               while($row1 = mysqli_fetch_array($search_result)):;
               ?> 
                  <tr class="gradeX">
                  <td><?php echo $row1['prefix_ID'];?><?php echo $row1['emp_id'];?></td>
                  <td><?php echo $row1['last_name'];?></td>
                  <td><?php echo $row1['first_name'];?></td>
                  <td><?php echo $row1['middle_name']; ?></td>     
                  <td><?php echo $row1['dept_NAME'];?></td>
                  <td><?php echo $row1['employment_TYPE'];?></td>
                  <td><?php echo $row1['shift_SCHEDULE'];?></td>                
                  <td><center><a href = "otpdf.php?id=<?php echo $row1['emp_id']; ?>" class = "btn btn-info btn-mini" target="_blank"><span class="icon"><i class="icon-print"></i></span> Print</a>
                    
                   
                </tr>
              <?php endwhile;?>
              </tbody>
            </table>
               <div class = "pagination alternate" style="float:right;">
               <ul>
               <!-- <?php

                    for ($i=1; $i<=$totalpages; $i++){
                         echo "<li><a href='adminMasterfile.php?page=".$i."'";
                         if ($i==$page) echo " class='curPage'";
                              echo ">".$i."</a></li> ";
                         };
               ?> -->
               </ul>
               </div>
          </div><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row-fluid">
  <!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div> -->
</div>
<?php
unset($_SESSION['masterfilenotif']);
?>
<script>
$(document).ready(function() {
    // Initialize the daterangepicker with the default values
    $('#daterange').daterangepicker({
        opens: 'left',
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Set the initial values directly to the input fields
    var startDateInput = $('#start_date');
    var endDateInput = $('#end_date');

    // Update the values when the date range changes
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        if (picker.startDate && picker.endDate) {
            startDateInput.val(picker.startDate.format('YYYY-MM-DD'));
            endDateInput.val(picker.endDate.format('YYYY-MM-DD'));
        }
    });

    // Trigger the apply event to set the initial values
    $('#daterange').trigger('apply.daterangepicker');

    // Set the initial values for start_date and end_date from PHP
    var start_date_php = '<?php echo isset($_GET['daterange_start']) ? htmlspecialchars($_GET['daterange_start']) : (isset($_SESSION['start_date']) ? htmlspecialchars($_SESSION['start_date']) : date("Y-m-d")); ?>';
    var end_date_php = '<?php echo isset($_GET['daterange_end']) ? htmlspecialchars($_GET['daterange_end']) : (isset($_SESSION['end_date']) ? htmlspecialchars($_SESSION['end_date']) : date("Y-m-d")); ?>';

    startDateInput.val(start_date_php);
    endDateInput.val(end_date_php);

    console.log('Start Date:', start_date_php);
    console.log('End Date:', end_date_php);
});


</script>

<style>
  body{
  font-family: 'Poppins', sans-serif;
}
</style>