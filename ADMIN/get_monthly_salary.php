<?php
// get_monthly_salary.php

if (isset($_GET['salaryGrade'])) {
    $selectedSalaryGrade = $_GET['salaryGrade'];

    // Assuming you have a database connection
    $conn1 = mysqli_connect("localhost:3307", "root", "", "masterdb");

    // Your database query to get the monthly salary based on the selected salary grade
    $query = "SELECT monthlysalary FROM salarygrade WHERE salarygrade = '$selectedSalaryGrade'";
    $result = mysqli_query($conn1, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            // Return the monthly salary
            echo $row['monthlysalary'];
        } else {
            // Salary not found
            echo 'Salary not found';
        }
    } else {
        // Database query error
        echo 'Error executing database query';
    }
} else {
    // Invalid or missing salary grade parameter
    echo 'Invalid or missing salary grade parameter';
}
?>
