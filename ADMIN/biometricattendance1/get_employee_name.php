<?php
// Assuming you have a database connection established
require 'connectDB.php';

if (isset($_GET['last_name'])) {
    $selectedLastName = $_GET['last_name'];

    // Fetch employee details based on last name from the database
    $query = "SELECT emp_id, user_name FROM employees WHERE last_name = '$selectedLastName'";
    $result = mysqli_query($conn1, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Output JSON data
        echo json_encode($row);
    } else {
        // Handle no matching record
        echo json_encode(array('emp_id' => '', 'user_name' => ''));
    }
} else {
    // Handle missing parameter
    echo json_encode(array('emp_id' => '', 'user_name' => 'No matching record'));
}
?>
