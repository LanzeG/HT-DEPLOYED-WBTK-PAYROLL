<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

$timeconv = strtotime("NOW");
$currtime = date("F d, Y", $timeconv);
$currdate = date("Y-m-d", $timeconv);
$curryear = date("Y", $timeconv);

$sql = "SELECT emp_id FROM employees
        WHERE NOT EXISTS (
            SELECT 1 FROM time_keeping
            WHERE employees.emp_id = time_keeping.emp_id
            AND DATE(time_keeping.timekeep_day) = '$currdate'
        )";

$result = $conn->query($sql);
if (!$result) {
  echo "<script>Error executing query: </script>" . $conn->error;
  // handle the error, e.g., return or exit
}
// Iterate through the employees without a timekeeping record
while ($row = $result->fetch_assoc()) {
    $employee_id = $row['emp_id'];

    // Check if an absence record already exists for today
    $check_existing_sql = "SELECT 1 FROM absences
                           WHERE emp_id = $employee_id
                           AND absence_date = '$currdate'";

    $existing_result = $conn->query($check_existing_sql);

    if ($existing_result === false) {
      echo "Error executing query: " . $conn->error;
      // handle the error, e.g., return or exit
  }
    // If no absence record exists, insert a new record
    if (mysqli_num_rows($existing_result) == 0) {
        $insert_sql = "INSERT INTO absences (emp_id, absence_date)
                       VALUES ($employee_id, '$currdate')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "<script> Record inserted successfully for employee with ID $employee_id.\n</script>";
        } else {
            echo "<script>Error inserting record: " . $conn->error . "\n </script>";
        }
    } else {
        echo "<script>Absence record already exists for employee with ID $employee_id.\n </script>";
    }
}

?>