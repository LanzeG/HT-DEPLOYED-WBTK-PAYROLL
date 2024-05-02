<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("../../BASICLOGININFO.PHP");
if(isset($_POST['grade']) && isset($_POST['step'])) {
    // Sanitize and store the received data
    $grade = $_POST['grade'];
    $step = $_POST['step'];

    // Perform a database query to retrieve the salary based on the selected grade and step
    // Assuming you have a table named 'salarygrade' with columns for GradeNumber, Step1, Step2, ..., Step8
    // Adjust the query according to your database schema
    $sql = "SELECT Step$step AS salary FROM salarygrade WHERE GradeNumber = '$grade'";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if($result) {
        // Fetch the retrieved salary from the result
        $row = mysqli_fetch_assoc($result);
        $salary = $row['salary'];

        // Return the salary as the response to the AJAX request
        echo $salary;
    } else {
        // If the query fails, return an error message
        echo "Error: Unable to retrieve salary";
    }
} else {
    // If the required POST data is not received, return an error message
    echo "Error: Required data not received";
}

// Close the database connection
mysqli_close($conn);
?>
