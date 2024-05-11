<?php
include("/home/u387373332/domains/wbtkpayrollportal.com/public_html/DBCONFIG.PHP");

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

$sql_select = "SELECT emp_id, timekeep_day
               FROM time_keeping
               WHERE out_afternoon == '00:00:00'";
$result_select = $conn->query($sql_select);
if ($result_select) {
    // Use a loop to fetch each row
    while ($row = $result_select->fetch_assoc()) {
        // Store emp_id and timekeep_day in variables
        $emp_id = $row['emp_id'];
        $timekeep_day = $row['timekeep_day'];

        // Insert emp_id and timekeep_day into absent table
        $sql_insert = "INSERT INTO absences (emp_id, absence_date)
                       VALUES ('$emp_id', '$timekeep_day')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "<script>Record inserted into absent table for emp_id: $emp_id, timekeep_day: $timekeep_day</script>";
        } else {
            echo "<script>Error inserting record: " . $conn->error . "</script>";
        }
    }

    // After inserting records into absent table, delete records from time_keeping table
    $sql_delete = "DELETE FROM time_keeping WHERE out_afternoon == '00:00:00'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>Records deleted from time_keeping table successfully.</script>";
    } else {
        echo "<script>Error deleting records: " . $conn->error . "</script>";
    }
} else {
    echo "<script>Error executing query: " . $conn->error . "</script>";
}


$sql_delete_dtr = "DELETE FROM dtr WHERE out_afternoon = '00:00:00'";
if ($conn->query($sql_delete_dtr) === TRUE) {
    echo "<script>Records deleted from dtr table where out_afternoon = '00:00:00' successfully.</script>";
} else {
    echo "<script>Error deleting records from dtr table: " . $conn->error . "</script>";
}
?>
