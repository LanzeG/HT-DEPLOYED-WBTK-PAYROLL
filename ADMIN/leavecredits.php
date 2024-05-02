<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
// Connect to your database (replace with your actual database connection code)

// SQL query to select relevant information from employees table
$sqlSelectEmployees = "
    SELECT e.emp_id, e.date_hired, e.employment_TYPE
    FROM employees e
    JOIN leaves l ON e.emp_id = l.emp_id
    WHERE e.employment_TYPE = 'Permanent';
";

// Execute the select query
$result = mysqli_query($conn, $sqlSelectEmployees);

// Loop through each employee and update leave credits
while ($row = mysqli_fetch_assoc($result)) {
    $employeeId = $row['emp_id'];
    $hiringDate = new DateTime($row['date_hired']);

    // Calculate the number of full months worked
    $currentDate = new DateTime();
    $monthsWorked = (($currentDate->format('Y') - $hiringDate->format('Y')) * 12) + $currentDate->format('n') - $hiringDate->format('n');

    // Update leave credits in the database
    $leaves = max(0, floor($monthsWorked / 1)) * 1.25;
    $sqlUpdate = "UPDATE leaves SET leave_count = $newLeaveCredits WHERE emp_id = $employeeId";
    // Execute the update query
    mysqli_query($conn, $sqlUpdate);
}

// Close the database connection

?>
