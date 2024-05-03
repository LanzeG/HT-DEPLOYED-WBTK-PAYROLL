<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();


$results_perpage = 20;

if (isset($_GET['page'])) {

  $page = $_GET['page'];
} else {

  $page = 1;
}
//  echo "Current Page: $page<br>";

if (isset($_GET['refresh'])) {
  header("Location: adminPAYROLLINFO.php");
  exit();
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

if (isset($_GET['print_btn'])) {
  $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
  $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
  $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
  $gender = isset($_GET['Gender']) ? $_GET['Gender'] : '';
  $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
  $selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
  $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
  $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter

  $deptFilter = $deptchecked ? $deptchecked : '';
  $emptypeFilter = $emptypechecked ? $emptypechecked : '';
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

  if ($positionFilter) {
    $filterConditions[] = "position.position_id IN ($positionFilter)";
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
    $searchquery = "SELECT *
      FROM employees
      LEFT JOIN department ON department.dept_NAME = employees.dept_NAME
      LEFT JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
      LEFT JOIN payrollinfo ON employees.emp_id = payrollinfo.emp_id
      LEFT JOIN position ON position.position_name = employees.position
      WHERE " . implode(" AND ", $filterConditions);
    $start_from = ($page - 1) * $results_perpage;
  } else {
    $start_from = ($page - 1) * $results_perpage;
    $searchquery = "SELECT *
     FROM employees
     LEFT JOIN department ON department.dept_NAME = employees.dept_NAME
     LEFT JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
     LEFT JOIN payrollinfo ON employees.emp_id = payrollinfo.emp_id
     LEFT JOIN position ON position.position_name = employees.position";

  }


  $start_from = ($page - 1) * $results_perpage;
  $searchquery .= " ORDER BY $sortColumn $sortOrder LIMIT $start_from, $results_perpage";
  $_SESSION['printpayroll_query'] = $searchquery;

  $search_result = filterTable($searchquery);
  // Count total rows in the limited result set
  $totalrows = mysqli_num_rows($search_result);

  // Calculate total pages
  $totalpages = ceil($totalrows / $results_perpage);

}

if (empty($search_result)) {
  $start_from = ($page - 1) * $results_perpage;
  $searchquery = "SELECT * FROM employees,payrollinfo WHERE employees.emp_id = payrollinfo.emp_id";
  $_SESSION['printpayroll_query'] = $searchquery;

  $search_result = filterTable($searchquery);
  $totalrows = mysqli_num_rows($search_result);

  // Calculate total pages
  $totalpages = ceil($totalrows / $results_perpage);
  $start_from = ($page - 1) * $results_perpage;
  $searchquery .= " ORDER BY $sortColumn $sortOrder LIMIT $start_from, $results_perpage";

  $_SESSION['printpayroll_query'] = $searchquery;

  // Perform the query with pagination
  $search_result = filterTable($searchquery);

  // Count total rows in the limited result set
  $totalrows = mysqli_num_rows($search_result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Payroll Information</title>
  <link rel="icon" type="image/png" href="../img/icon1 (3).png">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
  <script src="../timepicker/jquery.timepicker.min.js"></script>
  <script src="../timepicker/jquery.timepicker.js"></script>
</head>

<script>
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
  include('navbarAdmin.php');
?>
<style>
  #content1 {
    display: block;
  }
  #content1.collapsed {
      display: none;
  }
  body{
    font-family: 'Poppins', sans-serif;
  }
  tbody tr {
    display: table-row;
    vertical-align: middle; /* You can change this to 'top' or 'bottom' based on your preference */
  }
</style>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){
    var empId;
    $(".add").hide();

    $(document).on("click",".delete",function(){
        var id = $(this).attr("id");
        var string = id;
        console.log(id);

        // Display confirmation dialog before deletion
        // Display confirmation toast before deletion
// Display confirmation toast before deletion
Swal.fire({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this record!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    toast: true, // Set to true for a toast
    position: "center", // Set the position of the toast
    showConfirmButton: true, // Show confirm button for toast
    showCancelButton: true // Show cancel button for toast
}).then((result) => {
    if (result.isConfirmed) {
        // User confirmed, proceed with deletion
        $(this).parents("tr").remove();
        $(".add-new").removeAttr("disabled");

        $.post("functions/editpayrollinfo.php", { string: string }, function (data) {
            $("#displaymessage").html(data);
        });

        // Display success toast
        Swal.fire({
            title: "Record has been Deleted!",
            icon: "success",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCancelButton: false,
            timer: 5000, // Auto close after 5 seconds
            timerProgressBar: true // Display a progress bar
        })
    } else {
        // User canceled the deletion, display info toast
        Swal.fire({
            title: "The record is safe!",
            icon: "info",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCancelButton: false,
            timer: 5000, // Auto close after 5 seconds
            timerProgressBar: true // Display a progress bar
        });
    }
});


    });

    // $(document).on("click", ".edit", function(){
    //     var $row = $(this).closest("tr");

    //     // Find and edit specific <td> elements
    //     empId = $(this).attr('id');
    //     console.log('emp_id:', empId);
    //     $row.find("td:not(:lt(6)):not(:last-child)").each(function(i){
    //         var idname = 'txtStep' + (i + 1);
    //         var cellText = $(this).text().trim(); 
    //         $(this).html('<input type="text" name="updaterec" id="' + idname + '" class="form-control" value="' + cellText + '">');
    //     });

    //     // Toggle visibility of add and edit icons
    //     $row.find(".add, .edit").toggle();

    //     // Disable other add-new buttons
    //     $(".add-new").attr("disabled", "disabled");

    //     // Update class from add to update for the current row
    //     $row.find(".add").removeClass("add").addClass("update");
    // });

    $(document).on("click",".update",function(){
        var id = empId;
        var txtStep1 = $("#txtStep1").val();
        var txtStep2 = $("#txtStep2").val();
        var txtStep3 = $("#txtStep3").val();
        var txtStep4 = $("#txtStep4").val();
        var txtStep5 = $("#txtStep5").val();
        var txtStep6 = $("#txtStep6").val();
        var txtStep7 = $("#txtStep7").val();
        var txtStep8 = $("#txtStep8").val();
        var txtStep9 = $("#txtStep9").val();
        var txtStep10 = $("#txtStep10").val();
        console.log(id);

        $.post("functions/editpayrollinfo.php",{id:id, txtStep1:txtStep1, txtStep2:txtStep2, txtStep3:txtStep3, txtStep4:txtStep4, txtStep5:txtStep5, txtStep6:txtStep6, txtStep7:txtStep7, txtStep8:txtStep8, txtStep9:txtStep9,  txtStep10:txtStep10}, function(data) {
            $("#displaymessage").html(data);
            reloadTableData();
        });
    });

    function reloadTableData() {
        location.reload();
    }
});
</script>


<div class="title d-flex justify-content-center pt-4">
  <h3>Payroll Management</h3>

</div>

<div id="content">
<!-- end filter -->
  <div class="row mt-3 mb-1 d-flex justify-content-end  ">
    <div class="d-flex justify-content-end ">
      <a class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" id="collapseBtn" onclick="toggleCollapse()" style="float: right; margin-bottom: 10px;">Filter Options <i class="fas fa-arrow-down ml-2"></i></a>
    </div>
  </div>

<div id="content1">
    <div class="filter pt-3">
      <div class="card shadow p-4" style="border: 1px solid #F6E3F3; width: 100%; border-radius:10px;">
        <form method="GET" action="">
        <?php
          $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
          $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
          $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
          $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
          $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
          $month = isset($_GET['month']) ? $_GET['month'] : '';
          $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
          $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
          $query = "SELECT * FROM department";
            $total_row = mysqli_query($conn, $query) or die('error');
        ?>
        <div class="row row1">
          <div class="col-lg-3 col-xl- col-sm-6">
            <label for="dept" class="form-label">Deparment</label>
              <select id="dept" class="form-select" name="dept" style="border-radius:10px;">
                <option selected disabled>Select Department</option>
                <?php
                  if (mysqli_num_rows($total_row) > 0) {
                    foreach ($total_row as $row) {
                ?>
                <option value="<?php echo $row['dept_ID']; ?>" <?php if ($deptchecked == $row['dept_ID'])
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
<div class="col-lg-3 col-sm-6"><label for="employmenttype" class="form-label">Employment Type</label>
    <select id="employmenttype" class="form-select" name="employmenttype" style="border-radius:10px;">
      <option selected disabled>Select Employment Type</option>
      <?php
        if (mysqli_num_rows($total_row) > 0) {
          foreach ($total_row as $row) {
      ?>
        <option value="<?php echo $row['employment_ID']; ?>" <?php if ($emptypechecked == $row['employment_ID'])
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
<div class="col-lg-3 col-sm-6">
  <label for="position" class="form-label">Position</label>
    <select name="position" id="position" class="form-select" style="border-radius:10px;">
      <option selected disabled>Select Position</option>
      <?php
        if (mysqli_num_rows($total_row) > 0) {
          foreach ($total_row as $row) {
      ?>
      <option value="<?php echo $row['position_id']; ?>" <?php if ($positionchecked == $row['position_id'])
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

<div class="col-lg-3 col-sm-6">
  <label for="employee_status" class="form-label">Employee Status</label>
    <select id="employee_status" class="form-select" name="employee_status" style="border-radius:10px;">
      <option selected disabled>Select Status</option>
      <option value="Active" <?php if (isset($_GET['employee_status']) && $_GET['employee_status'] == 'Active')
        echo 'selected'; ?>>Active</option>
      <option value="Inactive" <?php if (isset($_GET['employee_status']) && $_GET['employee_status'] == 'Inactive')
        echo 'selected'; ?>>Inactive</option>
    </select>
</div>
</div>
<!-- end row 1 -->

<div class="row row2 mt-2">
  <div class="col-lg-3 col-sm-6">
    <label for="Gender" class="form-label">Sex</label>
      <select id="Gender" class="form-select" name="Gender" style="border-radius:10px;">
        <option selected disabled>Sex</option>
          <option value="Male" <?php if (isset($_GET['Gender']) && $_GET['Gender'] == 'Male')
            echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if (isset($_GET['Gender']) && $_GET['Gender'] == 'Female')
            echo 'selected'; ?>>Female</option>
      </select>
  </div>
<div class="row col-lg-3 col-sm-6">
  <label class="form-label">Date Hired</label>
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

<div class="col-lg-2 col-md-6">
  <label for="filter_by" class="form-label">Search By:</label>
    <select id="filter_by" class="form-select" name="filter_by" style="border-radius:10px;">
      <option value="" <?php if (isset($_GET['filter_by']) && $_GET['filter_by'] == '')
        echo 'selected'; ?>>Search by</option>
      <option value="emp_id" <?php if (isset($_GET['filter_by']) && $_GET['filter_by'] == 'emp_id')
        echo 'selected'; ?>>Employee ID</option>
      <option value="last_name" <?php if (isset($_GET['filter_by']) && $_GET['filter_by'] == 'last_name')
        echo 'selected'; ?>>Last Name</option>
      <option value="first_name" <?php if (isset($_GET['filter_by']) && $_GET['filter_by'] == 'first_name')
        echo 'selected'; ?>>First Name</option>
      <option value="user_name" <?php if (isset($_GET['filter_by']) && $_GET['filter_by'] == 'user_name')
        echo 'selected'; ?>>Username</option>
    </select>
</div>

<div class="col-lg-4 col-md-6">
  <label for="search_value" class="form-label">Search</label>
    <input type="text" class="form-control" placeholder="Search" aria-label="Search" name="search_value" id="search_value"
      value="<?php echo isset($_GET['search_value']) ? htmlspecialchars($_GET['search_value']) : ''; ?>" style="border-radius:10px;">
</div>
</div>

<div class=" d-flex align-items-center justify-content-center">
  <div class="  form-actions mt-3" >
    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="print_btn">Apply</button>
    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mr-5" name="refresh">Refresh</button>
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
          <th style="border-top-left-radius: 10px; color: #4929aa;">Employee ID</th>
          <th><a href="?print_btn=1&sort=last_name&order=<?php echo $sortColumn == 'last_name' ? ($sortOrder == 'asc' ? 'desc' : 'asc') : 'asc'; ?>&dept=<?php echo $deptchecked ?? ''; ?>&employmenttype=<?php echo $emptypechecked ?? ''; ?>&position=<?php echo $positionchecked ?? ''; ?>&Gender=<?php echo $gender ?? ''; ?>&employee_status=<?php echo $employeeStatus ?? ''; ?>&month=<?php echo $selectedMonth ?? ''; ?>&day=<?php echo $selectedDay ?? ''; ?>&year=<?php echo $selectedYear ?? ''; ?>&filter_by=<?php echo $filterBy ?? ''; ?>&search_value=<?php echo $searchValue ?? ''; ?>">Last Name <?php echo ($sortColumn == 'last_name') ? ($sortOrder == 'asc' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
          <th>First Name</th>
          <th>Middle Name</th>
          <th>Department</th>
          <th>Employment Type</th>
          <th>Base Pay</th>
          <!-- <th>Daily Rate</th>
          <th>Hourly Rate</th>
          <th>PERA</th>
          <th>GSIS</th>
          <th>Philhealth</th>
          <th>PAG-IBIG/HDMF</th>
          <th>WTAX</th>
          <th>Disallowance</th>
          <th>Current Disallowance</th> -->
          <th style="border-top-right-radius: 10px; color: #4929aa;">Action</th>
        </tr>
      </thead>
    <tbody>
<?php
  function filterTable($searchquery){
    $conn1 = mysqli_connect("localhost", "u387373332_masterdb", "WBTKpayrollportal1234@", "u387373332_masterdb");
    $filter_Result = mysqli_query($conn1, $searchquery) or die("failed to query masterfile " . mysqli_error($conn1));
    return $filter_Result;
  }
  while ($row1 = mysqli_fetch_array($search_result)):;
?>
  <tr class="gradeX">
    <td>
      <?php echo $row1['prefix_ID']; ?>
      <?php echo $row1['emp_id']; ?>
    </td>
    <td>
      <?php echo $row1['last_name']; ?>
    </td>
    <td>
      <?php echo $row1['first_name']; ?>
    </td>
    <td>
      <?php echo $row1['middle_name']; ?>
    </td>
    <td>
      <?php echo $row1['dept_NAME']; ?>
    </td>
    <td>
      <?php echo $row1['employment_TYPE']; ?>
    </td>
   <td>
     
        <?php echo $row1['base_pay']; ?>
   </td>
      <!-- <td>
      <center>
        <?php echo $row1['daily_rate']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['hourly_rate']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['refsalary']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['gsis']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['philhealth']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['pagibig']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['wtax']; ?>
    </td>
    <td>
      <center>
        <?php echo $row1['disallowance']; ?>
    </td>
    <td><center><?php echo $row1['current_disallowance']; ?></td> -->
 <td class="col-1 text-center">
    <a class="add mr-10" title="Add"><i class="fa fa-user-plus"></i></a>
    <a class="edit mr-10" title="Edit" data-toggle="tooltip" href="emppayrollinfo.php?emp_id=<?php echo $row1['emp_id'];?>" id="<?php echo $row1['emp_id'];?>"><i class="fa fa-pencil"></i></a>
    <a class="delete mr-10" title="Delete" data-toggle="tooltip" id="<?php echo $row1['emp_id'];?>"><i class="fa fa-trash"></i></a>
</td>

  </tr>
    <?php endwhile; ?>
  </tbody>
   <tfoot>
    <tr>
      <td colspan="12">
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center">
            <?php
              for ($i = 1; $i <= $totalpages; $i++) {
                echo "<li class='page-item";
                if ($i == $page) {
                  echo " active";
                }
                echo "'><a class='page-link' href=" . $_SERVER['PHP_SELF'] . "?page=" . $i . ">" . $i . "</a></li>";
              }
            ?>
          </ul>
        </nav>
      </td>
    </tr>
  </tfoot>
</table>
</div>
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
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>

<!-- <script src="../js/maruti.dashboard.js"></script> -->
<script src="../js/excanvas.min.js"></script>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.ui.custom.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.flot.min.js"></script>
<script src="../js/jquery.flot.resize.min.js"></script>
<script src="../js/jquery.peity.min.js"></script>
<script src="../js/fullcalendar.min.js"></script>
<!-- <script src="../js/maruti.js"></script> -->
<script>
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
</body>
</html>