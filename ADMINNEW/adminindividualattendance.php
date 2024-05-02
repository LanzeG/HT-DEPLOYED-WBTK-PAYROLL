<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
$idres = $_GET['id'];

$results_perpage = 20;
  if (isset($_GET['page'])){
    $page = $_GET['page'];
  } else {
    $page=1;
  }
if (isset($_GET['refresh'])) {
  header("Location: adminindividualattendance.php?id=$idres");
  exit(); 
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

if (isset($_GET['print_btn'])) {
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
  $selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

  $monthFilter = $selectedMonth ? "'" . $selectedMonth . "'" : '';
  $dayFilter = $selectedDay ? "'" . $selectedDay . "'" : '';
  $yearFilter = $selectedYear ? "'" . $selectedYear . "'" : '';
//   $idres = isset($_GET['id']);

  $filterConditions = [];

  if ($monthFilter) {
      $filterConditions[] = "MONTH(time_keeping.timekeep_day) = $monthFilter";
  }
  
  if ($dayFilter) {
      $filterConditions[] = "DAY(time_keeping.timekeep_day) = $dayFilter";
  }
  
  if ($yearFilter) {
      $filterConditions[] = "YEAR(time_keeping.timekeep_day) = $yearFilter";
  }
  $idFilter = $idres ? "TIME_KEEPING.emp_id = $idres" : ''; 

  if (!empty($filterConditions)) {
      $searchquery = "SELECT * 
      FROM employees
      JOIN TIME_KEEPING ON employees.emp_id = TIME_KEEPING.emp_id
      WHERE " . implode(" AND ", $filterConditions);
      $start_from = ($page - 1) * $results_perpage;
  } else {
    $start_from = ($page - 1) * $results_perpage;
    $searchquery = "SELECT * 
      FROM employees
      JOIN TIME_KEEPING ON employees.emp_id = TIME_KEEPING.emp_id
      WHERE TIME_KEEPING.emp_id = $idres";
  }
  $searchquery .= " ORDER BY $sortColumn $sortOrder LIMIT $start_from, $results_perpage";
  $_SESSION['printatt_query'] = $searchquery;

  $countQuery = "SELECT COUNT(*) as total FROM employees JOIN TIME_KEEPING ON employees.emp_id = TIME_KEEPING.emp_id JOIN department ON department.dept_NAME = employees.dept_NAME JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE  LEFT JOIN position ON position.position_name = employees.position";
  if (!empty($filterConditions)) {
      $countQuery .= " WHERE " . implode(" AND ", $filterConditions);
  }
  
    $totalResult = mysqli_query($conn, $countQuery);
    $totalRow = mysqli_fetch_assoc($totalResult)['total'];

    // Calculate total pages
    $totalpages = ceil($totalRow / $results_perpage);

    $search_result = filterTable($searchquery);
    // Count total rows in the limited result set
    $totalrows = mysqli_num_rows($search_result);
} else { // Check if $search_result is empty or not defined
    $start_from = ($page - 1) * $results_perpage;
    // If not defined or empty, perform a default query without filters
    $searchquery = "SELECT TIME_KEEPING.in_morning, TIME_KEEPING.out_afternoon, TIME_KEEPING.emp_id, TIME_KEEPING.timekeep_day, employees.* from employees, TIME_KEEPING  WHERE  employees.emp_id = TIME_KEEPING.emp_id AND TIME_KEEPING.emp_id = '$idres' ORDER BY $sortColumn $sortOrder LIMIT $start_from, $results_perpage";

    $_SESSION['printatt_query'] = $searchquery;

    $countQuery = "SELECT COUNT(*) as total FROM employees, TIME_KEEPING WHERE employees.emp_id = TIME_KEEPING.emp_id";
    $totalResult = mysqli_query($conn, $countQuery);
    $totalRow = mysqli_fetch_assoc($totalResult)['total'];

    // Calculate total pages
    $totalpages = ceil($totalRow / $results_perpage);

    $search_result = filterTable($searchquery);
    // Count total rows in the limited result set
    $totalrows = mysqli_num_rows($search_result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Admin Masterlist</title>
  <link rel="icon" type="image/png" href="../img/icon1 (3).png">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="../timepicker/jquery.timepicker.js"></script>
</head>
<script type ="text/javascript">
  $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
  function toggleCollapse() {
    var content = document.getElementById("content1");
    content.classList.toggle("collapsed");

    // Store the collapse state in local storage
    localStorage.setItem("collapseState", content.classList.contains("collapsed"));
  }
  // Check local storage for the collapse state on page load
  window.onload = function() {
    var isCollapsed = localStorage.getItem("collapseState");

    // If the collapse state is true, toggle the collapse
    if (isCollapsed === "true") {
      toggleCollapse();
    }
  };
</script>
<body>
  <!--Header-part-->
  <?php
  include('navbaradmin.php');
  ?>
<style>
body{
  font-family: 'Poppins', sans-serif;
}
  #content1 {
    display: block;
}

#content1.collapsed {
    display: none;
}
</style>
<div class="title d-flex justify-content-center pt-4">
    <h3>Attendance Management</h3>
</div>
<div id="content">
  
<!-- end filter -->
<div class="row mt-3 mb-1 d-flex justify-content-end  ">
    <div class="col-12">
        <div class="buttons ">
            <a href="../admin/printattendancerecords.php?printIndividual&id=<?php echo $idres; ?>" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" target="_blank"><i class="fas fa-print mr-2"></i> Print All Masterlist</a>
            <a href="../admin/printattendancerecords.php?printDisplayed" class="inline-block bg-green-500 hover:bg-gr-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out mt-0 ml-1" target="_blank"><i class="fas fa-print mr-2"></i> Print Displayed Masterlist</a>
            <a class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out ml-1" id="collapseBtn" onclick="toggleCollapse()" style="float: right;">Filter Options <i class="fas fa-arrow-down ml-2"></i></a>
        </div>
    </div>
</div>

</div>
<div id="content1 ">
    <div class="filter pt-3">
        <div class="card shadow p-4"  style="border: 1px solid #F6E3F3; width: 100%; border-radius:10px;">
            <form method="GET" action="">
                <div class="row col-lg-3 col-sm-6">
                    <input type="hidden" name="id" value="<?php echo $idres; ?>">
                    <label class="form-label">Date</label>

                    <div class="col-4">
                        <select name="month" id="month" class="form-select" style="border-radius:10px;">
                            <option selected disabled>Month</option>
                        <?php
                          $months = [
                            'Jan' => 1,
                            'Feb' => 2,
                            'Mar' => 3,
                            'Apr' => 4,
                            'May' => 5,
                            'Jun' => 6,
                            'Jul' => 7,
                            'Aug' => 8,
                            'Sep' => 9,
                            'Oct' => 10,
                            'Nov' => 11,
                            'Dec' => 12
                          ];

                          foreach ($months as $monthName => $monthNumber) {
                            $selected = (isset($_GET['month']) && $_GET['month'] == $monthNumber) ? 'selected' : '';
                            echo '<option value="' . $monthNumber . '" ' . $selected . '>' . $monthName . '</option>';
                          }
                        ?>
                        </select>
                    </div> 
<div class="col-4">
    <select name="day" id="day" class="form-select" style="border-radius:10px;">
        <option selected disabled>Date</option>
        <?php
            // Adding options for days (assuming up to 31 for simplicity)
            for ($day = 1; $day <= 31; $day++) {
                $selected = (isset($_GET['day']) && $_GET['day'] == sprintf('%02d', $day)) ? 'selected' : '';
                 echo '<option value="' . sprintf('%02d', $day) . '" ' . $selected . '>' . sprintf('%02d', $day) . '</option>';
            }
        ?>
    </select>
</div>

<div class="col-4">
    <select name="year" id="year" class="form-select" style="border-radius:10px;">
        <option selected disabled>Year</option>
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
<div class="d-flex align-items-center justify-content-center">
    <div class="form-actions mt-3" >
        <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="print_btn">Apply</button>
        <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mr-5" name="refresh">Refresh</button>
</div>
</div>
</div>


</form>
</div>
<!-- end form -->
</div>
</div>

<div class="row mt-3 mb-1 d-flex justify-content-end">
    <div class="table d-flex align-items-center table-responsive">
        <table class="table table-striped">
            <thead class="table-striped" style="background-color: #2ff29e; color: #4929aa;">
                <tr>
                  <th style="border-top-left-radius: 10px;">Employee ID</th>
                  <th><a href="?print_btn=1&sort=last_name&order=<?php echo $sortColumn == 'last_name' ? ($sortOrder == 'asc' ? 'desc' : 'asc') : 'asc'; ?>&dept=<?php echo $deptchecked ?? ''; ?>&employmenttype=<?php echo $emptypechecked ?? ''; ?>&shifts=<?php echo $shiftchecked ?? ''; ?>&position=<?php echo $positionchecked ?? ''; ?>&Gender=<?php echo $gender ?? ''; ?>&employee_status=<?php echo $employeeStatus ?? ''; ?>&month=<?php echo $selectedMonth ?? ''; ?>&day=<?php echo $selectedDay ?? ''; ?>&year=<?php echo $selectedYear ?? ''; ?>&filter_by=<?php echo $filterBy ?? ''; ?>&search_value=<?php echo $searchValue ?? ''; ?>">Last Name <?php echo ($sortColumn == 'last_name') ? ($sortOrder == 'asc' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                  <th style="border-top-right-radius: 10px;">Day of Record</th>
                </tr>
            </thead>
            <tbody> 
            <?php
            $nextPage = $page + 1;
            $nextPageLink = $_SERVER['PHP_SELF'] . "?page=" . $nextPage . "&id=" . $idres;
            ?>
            <tfoot>
                <tr>
                    <td colspan="12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                            <?php
                                if ($page > 1) {
                                    echo "<li class='page-item'><a class='page-link' href='" . $_SERVER['PHP_SELF'] . "?page=" . ($page - 1) . "&id=" . $idres . "'>&laquo; Previous</a></li>";
                                }

                                $startPage = max(1, $page - 2);
                                $endPage = min($totalpages, $page + 2);

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                echo "<li class='page-item";
                                if ($i == $page) {
                                    echo " active";
                                }
                                echo "<li class='page-item'><a class='page-link' href='" . $_SERVER['PHP_SELF'] . "?page=" . $i . "&id=" . $idres . "'>" . $i . "</a></li>";
                                }

                                if ($page < $totalpages) {
                                echo "<li class='page-item'><a class='page-link' href=" . $nextPageLink . ">Next &raquo;</a></li>";
                                }
                            ?>
                            </ul>
                        </nav>
                    </td>
                </tr>
            </tfoot>
        </div>
    <div class="widget-content tab-content">
        <div class ="row-fluid">
    </div>
<?php            
    function filterTable($searchquery){
        $conn1 = mysqli_connect("localhost:3307","root","","masterdb");
            $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
            return $filter_Result;
    }
    ini_set('memory_limit', '1024M'); // Set the memory limit to 1GB (adjust as needed)
        while($row1 = mysqli_fetch_array($search_result)):;
        ?>
            <tr class="gradeX">
                <td><a href = "adminVIEWprofile.php?id=<?php echo $row1['emp_id']; ?>"><?php echo $row1['prefix_ID'];?><?php echo $row1['emp_id'];?></a></td>
                <td><?php echo $row1['last_name'];?></td>
                <td><?php echo $row1['first_name'];?></td>
                <td><?php echo $row1['middle_name']; ?></td>
                <td><?php echo $row1['in_morning'];?></td>
                <td><?php echo $row1['out_afternoon'];?></td>
                <td><?php echo $row1['timekeep_day'];?></td>
            </tr>
        <?php endwhile;?>
    </tbody>
</table>
</div>
<!-- end of content -->
</tbody>
</table>
</div>
    <!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
</div>

</div>
</div>
</div>
</div>
</div>

<div class="row-fluid">
    <div id="footer" class="span12"> 
        2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS
    </div>
</div>

<script src="../js/maruti.dashboard.js"></script>
<script src="../js/excanvas.min.js"></script>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.ui.custom.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.flot.min.js"></script>
<script src="../js/jquery.flot.resize.min.js"></script>
<script src="../js/jquery.peity.min.js"></script>
<script src="../js/fullcalendar.min.js"></script>
<script src="../js/maruti.js"></script>
<script>
    // Function to update the position dropdown state
    function updatePositionDropdownState() {
      var positionDropdown = document.getElementById('position');
      var employmentTypeDropdown = document.getElementById('employmenttype');

      var isContractual = employmentTypeDropdown.value === '4001'; // Change to the actual value for contractual

      // Save the selected value before disabling
      var selectedValue = positionDropdown.value;

      // Disable/enable based on employment type
      positionDropdown.disabled = isContractual;

      // Set the selected value after updating options
      positionDropdown.value = selectedValue;
    }

    // Initial setup on page load
    updatePositionDropdownState(); // Ensure the initial state is correct

    // Event listener for changes in the employment type dropdown
    document.getElementById('employmenttype').addEventListener('change', function () {
      updatePositionDropdownState();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>