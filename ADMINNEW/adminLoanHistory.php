<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
$idres = $_GET['emp_id'];
$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

if (isset($_GET['refresh'])) {
  header("Location: adminMasterLoans.php");
  exit(); 
}

$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';



if (isset($_GET['print_btn'])) {

  $loanorg = isset($_GET['loanorg']) ? $_GET['loanorg'] : '';
  $loantype = isset($_GET['loantype']) ? $_GET['loantype'] : '';
  $loanstatus = isset($_GET['loan_status']) ? $_GET['loan_status'] : '';
  $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod'] : '';

  $loanOrgFilter = isset($_GET['loanorg']) ? $_GET['loanorg'] : '';
  $loanTypeFilter = isset($_GET['loantype']) ? $_GET['loantype'] : '';
  $loanStatusFilter = isset($_GET['loan_status']) ? $_GET['loan_status'] : '';
  $payPeriodFilter = isset($_GET['payperiod']) ? $_GET['payperiod'] : '';
  
  // Add filters for loan organization, loan type, loan status, and pay period
  if ($loanOrgFilter) {
      $filterConditions[] = "loan_history.loanorg = '$loanOrgFilter'";
  }
  
  if ($loanTypeFilter) {
      $filterConditions[] = "loan_history.loantype = '$loanTypeFilter'";
  }
  
  if ($loanStatusFilter) {
      $filterConditions[] = "loan_history.status = '$loanStatusFilter'";
  }
  
  if ($payPeriodFilter) {
      $filterConditions[] = "loan_history.payperiod = '$payPeriodFilter'";
  }
    

  if (!empty($filterConditions)) {
    $searchquery = "SELECT *
    FROM employees
    JOIN loan_history ON loan_history.emp_id = employees.emp_id
    JOIN payperiods ON payperiods.pperiod_range = loan_history.payperiod
    WHERE " . implode(" AND ", $filterConditions);

    // echo "Generated Query: $searchquery<br>";

    $searchresult = filterTable($searchquery);
    $_SESSION['printgsis_query'] = $searchquery;
    
    // echo "Number of Rows: " . mysqli_num_rows($searchresult) . "<br>";
  } else {
    echo "No filters selected. Please select at least one filter.";
    $searchquery = "SELECT * FROM loan_history, employees WHERE employees.emp_id = loan_history.emp_id AND loan_history.emp_id='$idres' ORDER BY uniquekey";
    $searchresult = filterTable($searchquery);
    $_SESSION['printgsis_query'] = $searchquery;
    //  echo $searchquery;

  }

} else {
  $searchquery = "SELECT * FROM loan_history, employees WHERE employees.emp_id = loan_history.emp_id AND loan_history.emp_id='$idres' ORDER BY uniquekey";
  $searchresult = filterTable($searchquery);
  // echo $searchquery;
  $_SESSION['printgsis_query'] = $searchquery;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Manage Loans</title>
  <link rel="icon" type="image/png" href="../img/icon1 (3).png">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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
  include('navbaradmin.php');
?>

<div class="title d-flex justify-content-center pt-4">
  <h3>Loans History</h3>
</div>
   
<div id="content">
<!-- end filter -->
  <div class="row mt-3 mb-1 d-flex justify-content-end  ">
  <div class="buttons  ">
      <a href="../admin/reports/printloanhistory.php?printAll&id=<?php echo $idres; ?>" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mr-1" target="_blank"><i class="fas fa-print mr-2"></i> Print All Masterlist</a>
      <a href="../admin/reports/printloanhistory.php?printDisplayed" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mr-1" target="_blank"><i class="fas fa-print mr-2"></i> Print Displayed Masterlist</a>
    </div>
   <div class="col-12">
     <div class="buttons  ">
        <a class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" id="collapseBtn" onclick="toggleCollapse()" style="float: right;">Filter Options <i class="fas fa-arrow-down ml-2"></i></a>
      </div>
    </div>
  </div>

  <div class="col-2 d-flex justify-content-end ">
  </div>
</div>
<div id="content1">
  <div class="filter pt-3">
    <div class="card shadow p-4" style="border: 1px solid #F6E3F3; width: 100%; border-radius:10px;">
      <form method="GET" action="">
        <input type="hidden" name="emp_id" value="<?php echo $idres; ?>">
        <?php
          $loanorg = isset($_GET['loanorg']) ? $_GET['loanorg'] : '';
          $loantype = isset($_GET['loantype']) ? $_GET['loantype'] : '';
          $loanstatus = isset($_GET['loan_status']) ? $_GET['loan_status'] : '';
          $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod'] : '';
          
          $query = "SELECT DISTINCT loanorg FROM loantype";
          $total_row = mysqli_query($conn, $query) or die('error');
        ?>

  <div class="row row1">
    <div class="col-lg-3 col-xl- col-sm-6">
      <label for="loanorg" class="form-label">Loan Organization</label>
        <select id="loanorg" class="form-select" name="loanorg" style="border-radius:10px;">
          <option selected disabled>Select Loan Organization</option>
          <?php
            if (mysqli_num_rows($total_row) > 0) {
              foreach ($total_row as $row) {
                ?>
                <option value="<?php echo $row['loanorg']; ?>" <?php if ($loanorg == $row['loanorg'])
                      echo "selected"; ?>>
                  <?php echo $row['loanorg']; ?>
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
      $query1 = "SELECT * FROM loantype";
      $total_row = mysqli_query($conn, $query1) or die('error');
    ?>
<div class="col-lg-3 col-sm-6"><label for="loantype" class="form-label">Loan Type</label>
  <select id="loantype" class="form-select" name="loantype" style="border-radius:10px;">
    <option selected disabled>Select Loan Type</option>
    <?php
      if (mysqli_num_rows($total_row) > 0) {
        foreach ($total_row as $row) {
    ?>
        <option value="<?php echo $row['loantype'];?>" <?php if ($loantype == $row['loantype'])
              echo "selected"; ?>>
          <?php echo $row['loantype'];?>
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
  <label for="loan_status" class="form-label">Loan Status</label>
    <select name="loan_status" id="loan_status" class="form-select" style="border-radius:10px;">
      <option value="" <?php if (isset($_GET['loan_status']) && $_GET['loan_status'] == '')
        echo 'selected'; ?>>Select Loan Status</option>
      <option value="On-Going" <?php if (isset($_GET['loan_status']) && $_GET['loan_status'] == 'On-Going')
        echo 'selected'; ?>>On-Going</option>
      <option value="Paid" <?php if (isset($_GET['loan_status']) && $_GET['loan_status'] == 'Paid')
        echo 'selected'; ?>>Paid</option>
    </select>
</div>

<div class="col-lg-3 col-sm-6">
<?php
  $query1 = "SELECT * FROM payperiods";
  $total_row = mysqli_query($conn, $query1) or die('error');
?>
<label for="payperiod" class="form-label">Pay Period</label>
  <select id="payperiod" class="form-select" name="payperiod" style="border-radius:10px;">
    <option selected disabled>Select Pay Period</option>
    <?php
      if (mysqli_num_rows($total_row) > 0) {
        foreach ($total_row as $row) {
          ?>
          <option value="<?php echo $row['pperiod_range']; ?>" <?php if ($payperiod == $row['pperiod_range'])
                echo "selected"; ?>>
            <?php echo $row['pperiod_range']; ?>
          </option>
          <?php

        }
      } else {
        echo 'No Data Found';
      }
    ?>
  </select>
</div>
</div>
<!-- end row 1 -->

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
          <tr>
            <th style="border-top-left-radius: 10px; color: #4929aa;">Loan ID No.</th>
              <th>Loan Org</th>
              <th>Loan Type</th>
              <th>Employee ID</th>
              <th>Last Name</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Loan Amount</th>
              <th>No. of Payments</th>
              <th>Monthly Amount</th>
              <th>Balance</th>
              <th>Status</th>
              <th>Pay Period</th>
              <th style="border-top-right-radius: 10px; color: #4929aa;">Admin Name</th>
          </tr>
        </thead>
        <tbody>
        <?php
          function filterTable($searchquery){
            $conn = mysqli_connect("localhost:3307", "root", "", "masterdb");
            $filter_Result = mysqli_query($conn, $searchquery) or die("failed to query masterfile " .mysqli_error());
            return $filter_Result;
          }
          while ($row1DEPT = mysqli_fetch_array($searchresult)):;
        ?>
            <tr class="gradeX">
              <td><?php echo $row1DEPT['loan_id']; ?></td>
              <td><?php echo $row1DEPT['loanorg']; ?></td>
              <td><?php echo $row1DEPT['loantype']; ?></td>
              <td><?php echo $row1DEPT['emp_id']; ?></td>
              <td><?php echo $row1DEPT['lastname']; ?></td>
              <td><?php echo $row1DEPT['firstname']; ?></td>
              <td><?php echo $row1DEPT['middlename']; ?></td>
              <td><?php echo $row1DEPT['start_date']; ?></td>
              <td><?php echo $row1DEPT['end_date']; ?></td>
              <td><?php echo $row1DEPT['amount']; ?></td>
              <td><?php echo $row1DEPT['num_of_payments']; ?></td>
              <td><?php echo $row1DEPT['monthly_payment']; ?></td>
              <td><?php echo $row1DEPT['current_amount']; ?></td>
              <td><?php echo $row1DEPT['status']; ?></td>
              <td><?php echo $row1DEPT['payperiod']; ?></td>
              <td><?php echo $row1DEPT['admin_name']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="12">
              <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                </ul>
              </nav>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
<!-- end of content -->
  </tbody>
</table>
<div class="pagination alternate" style="float:right;">
</div>
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

function stopLoan(uniqueKey, adminName) {
  // AJAX request using jQuery
  $.ajax({
      url: 'functions/stoploan.php', // Replace with your server-side script URL
      type: 'POST',
      data: {
          action: 'stop',
          uniquekey: uniqueKey,
          adminname: adminName
      },
      success: function(response) {
          // Handle success response if needed
          console.log('AJAX Response:', response);
          reloadTableData();

      },
      error: function(xhr, status, error) {
          // Handle error response if needed
          console.error('AJAX Error:', error);
      }
  });
}
function reloadTableData() {
      location.reload();
}
</script>






</body>

</html>