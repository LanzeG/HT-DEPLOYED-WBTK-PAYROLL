<?php

echo 'hello';
include("/home/u387373332/domains/wbtkpayrollportal.com/public_html/DBCONFIG.PHP");

// Set timezone to Philippine Standard Time
date_default_timezone_set('Asia/Manila');

$timeconv = strtotime("NOW");
$currdate = date("Y-m-d", $timeconv);
$currday = date("N", $timeconv); // Numeric representation of the day of the week (1 for Monday, 7 for Sunday)
$current_day_of_week = date('l', strtotime($currdate));

// Get the current school year
$current_sy_sql = "SELECT CONCAT(start_year, '-', end_year) AS school_year FROM school_years WHERE is_current = 1";
$current_sy_result = $conn->query($current_sy_sql);
if ($current_sy_result->num_rows > 0) {
    $current_sy_row = $current_sy_result->fetch_assoc();
    $current_school_year = $current_sy_row['school_year'];
} else {
    // Handle error: No current school year found
    exit("No current school year found");
}

// Get the current semester
$current_sem_sql = "SELECT id FROM semesters WHERE is_current = 1";
$current_sem_result = $conn->query($current_sem_sql);
if ($current_sem_result->num_rows > 0) {
    $current_sem_row = $current_sem_result->fetch_assoc();
    $current_semester = $current_sem_row['id'];
} else {
    // Handle error: No current semester found
    exit("No current semester found");
}

// Check for absence for employees without a timekeeping record for the current date
$sql = "SELECT e.emp_id, e.acct_type FROM employees e
        LEFT JOIN time_keeping tk ON e.emp_id = tk.emp_id AND DATE(tk.timekeep_day) = '$currdate'
        WHERE tk.emp_id IS NULL";

$result = $conn->query($sql);
if (!$result) {
    // Handle error: Error executing query
    exit("Error executing query: " . $conn->error);
}

// Iterate through the employees without a timekeeping record
while ($row = $result->fetch_assoc()) {
    $employee_id = $row['emp_id'];
    $acct_type = $row['acct_type'];

    // Check if it's a weekday for Administrator or Faculty w/ Admin
    if (($acct_type !="Faculty") && $currday >= 1 && $currday <= 5) {
        // Insert absence record directly
        $insert_absence_sql = "INSERT INTO absences (emp_id, absence_date)
                               VALUES ($employee_id, '$currdate')";
        if ($conn->query($insert_absence_sql) === TRUE) {
            echo "<script>Record inserted successfully for employee with ID $employee_id.</script>";
        } else {
            // Handle error: Error inserting record
            echo "<script>Error inserting record: " . $conn->error . "</script>";
        }
    } elseif ($acct_type == "Faculty") {
        // Check if the employee has a schedule for the current school year and semester
        $schedule_check_sql = "SELECT 1 FROM schedule
                               WHERE emp_id = $employee_id
                               AND sy = '$current_school_year'
                               AND semester = '$current_semester'
                               AND day_of_week = '$current_day_of_week'";
        $schedule_check_result = $conn->query($schedule_check_sql);
        if ($schedule_check_result->num_rows > 0) {
            // If the employee has a schedule, insert an absence record
            $insert_absence_sql = "INSERT INTO absences (emp_id, absence_date)
                                   VALUES ($employee_id, '$currdate')";
            if ($conn->query($insert_absence_sql) === TRUE) {
                echo "<script>Record inserted successfully for employee with ID $employee_id. $current_semester $current_school_year</script>";
            } else {
                // Handle error: Error inserting record
                echo "<script>Error inserting record: " . $conn->error . "</script>";
            }
        } else {
            // Handle case: Employee has no schedule for the current school year and semester
            echo "Employee with ID $employee_id has no schedule for the current school year and semester.$employee_id. $current_semester $current_school_year";
        }
    }
}

// Delete records from the time_keeping table where out_afternoon = '00:00:00'
$sql_delete_time_keeping = "DELETE FROM time_keeping WHERE out_afternoon = '00:00:00' AND in_morning != '00:00:00'";
if ($conn->query($sql_delete_time_keeping) === TRUE) {
    echo "<script>Records deleted from time_keeping table successfully.</script>";
} else {
    // Handle error: Error deleting records from time_keeping table
    echo "<script>Error deleting records from time_keeping table: " . $conn->error . "</script>";
}

// Delete records from the dtr table where out_afternoon = '00:00:00'
$sql_delete_dtr = "DELETE FROM dtr WHERE out_afternoon = '00:00:00' AND in_morning != '00:00:00'";
if ($conn->query($sql_delete_dtr) === TRUE) {
    echo "<script>Records deleted from dtr table where out_afternoon = '00:00:00' successfully.</script>";
} else {
    // Handle error: Error deleting records from dtr table
    echo "<script>Error deleting records from dtr table: " . $conn->error . "</script>";
}
?>
