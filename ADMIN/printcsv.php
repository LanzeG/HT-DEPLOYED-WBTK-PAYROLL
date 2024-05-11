<?php

// Include necessary files and start session
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();

// Main function to generate CSV for each employee
function generateEmployeeCSV($employeeData, $payPeriodFrom, $payPeriodTo) {
    // Set CSV file name
    $filename = "employee_timesheet.csv";

    // Set CSV headers for general information and timekeeping details
    $generalInfoHeaders = ['Name', 'Employee ID', 'Department', 'Month', 'Year'];
    $timekeepingHeaders = ['Date', 'IN', 'OUT', 'Hours Worked', 'Undertime (Minutes)', 'Remarks'];

    // Set CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Establish database connection
    $conn = mysqli_connect("localhost", "u387373332_masterdb", "WBTKpayrollportal1234@", "u387373332_masterdb");

    // Open file handle for writing CSV
    $fp = fopen('php://output', 'w');

    // Write general information headers to CSV
    fputcsv($fp, $generalInfoHeaders);

    // Iterate over each employee
    while ($employee = mysqli_fetch_assoc($employeeData)) {
        // Fetch and populate general information data for each employee
        $name = $employee['first_name'] . ' ' . $employee['last_name'];
        $empID = $employee['emp_id'];
        $dept = $employee['dept_NAME'];
        $month = date('F', strtotime($payPeriodFrom));
        $year = date('Y', strtotime($payPeriodFrom));

        // Write general information data to CSV
        fputcsv($fp, [$name, $empID, $dept, $month, $year]);

        // Write timekeeping details headers to CSV
        fputcsv($fp, $timekeepingHeaders);

        // Fetch data for the employee
        $printid = $employee['emp_id'];
        $printfrom = $payPeriodFrom;
        $printto = $payPeriodTo;
        $printquery = "SELECT * FROM time_keeping, employees 
                    WHERE time_keeping.emp_id = employees.emp_id 
                    AND time_keeping.emp_id = '$printid' 
                    AND time_keeping.timekeep_day BETWEEN '$printfrom' AND '$printto' 
                    ORDER BY timekeep_day ASC";
        $printqueryexec = mysqli_query($conn, $printquery);
        
        // Write timekeeping details to CSV
        while ($printarray = mysqli_fetch_assoc($printqueryexec)) {
            fputcsv($fp, [
                $printarray['timekeep_day'], // Date
                $printarray['in_morning'], // IN
                $printarray['out_afternoon'], // OUT
                $printarray['hours_work'], // Hours Worked
                $printarray['undertime_hours'], // Undertime (Minutes)
                $printarray['timekeep_remarks'], // Remarks
            ]);
        }

        // Add an empty line after each employee's data for better readability
        fputcsv($fp, []);
    }

    // Close file handle
    fclose($fp);

    // Close database connection
    mysqli_close($conn);
}

// Fetch employee data based on stored query
if (isset($_GET['printall'])) {
    $storedQuery = "SELECT * FROM employees WHERE emp_id IN (SELECT emp_id FROM TIME_KEEPING 
                    WHERE timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}')";
} elseif (isset($_GET['printdisplayed'])) {
    $storedQuery = $_SESSION['printtimesheet_query'];
}

$employeeResult = filterTable($storedQuery);

// Generate CSV for each employee
generateEmployeeCSV($employeeResult, $_SESSION['payperiodfrom'], $_SESSION['payperiodto']);

// Function to filter table based on query
function filterTable($searchquery){
    $conn = mysqli_connect("localhost", "u387373332_masterdb", "WBTKpayrollportal1234@", "u387373332_masterdb");
    $filter_Result = mysqli_query($conn, $searchquery) or die("failed to query employees " . mysqli_error($conn));
    return $filter_Result;
}
?>
