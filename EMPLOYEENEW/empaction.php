<?php
include("../DBCONFIG.PHP");
session_start();

// Check if the necessary parameters are present in the URL
if (isset($_GET['payfunction']) && isset($_GET['payperiod'])) {
    // Get the values from the URL parameters
    $selectedPayFunction = $_GET['payfunction'];
    $selectedPayPeriod = $_GET['payperiod'];
    $empid = $_SESSION['empId'];
    $payperiod = $_SESSION['payperiods'];

    $searchquery = "SELECT * FROM employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$empid' AND PAY_PER_PERIOD.pperiod_range = '$payperiod' ORDER BY pperiod_range";
      $search_result = mysqli_query($conn,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn));
        $conn = mysqli_connect("localhost:3307","root","","masterdb");

    // Now you can use $selectedPayFunction and $selectedPayPeriod as needed

    // Switch statement based on the selected pay function
    switch ($selectedPayFunction) {
        case 'Generate Payslip':
            header('Location: printpayslip.php?variable=' . urlencode($selectedPayPeriod));
            break;

            case 'View DTR':
                // Handle the case for View DTR
                header('Location: printdtr.php?variable=' . urlencode($selectedPayPeriod));
                exit;
                
            case 'View Timesheet':
                // Handle the case for View Timesheet
                header('Location: printtimesheet.php?variable=' . urlencode($selectedPayPeriod));
                exit;
                
            case 'View Leaves':
                // Handle the case for View Leaves
                header('Location: printleaves.php?variable=' . urlencode($selectedPayPeriod));
                exit;
                
            default:
                // Handle default case
                echo "Invalid pay function selected";
                exit;
    }

    // You can continue with any additional processing here
} else {
    // Redirect to an error page or handle the case where parameters are missing
    echo "Error: Missing parameters in the URL";
}


?>
<!-- Rest of your HTML content goes here, if any -->
