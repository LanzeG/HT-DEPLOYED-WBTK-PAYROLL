<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "masterdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $error_message = "Connection failed: " . $conn->connect_error;
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        $department = $_POST['department'] ?? null;
        $employee = $_POST['employee'] ?? null;
        $year = $_POST['year'] ?? null;
        $semester = $_POST['semester'] ?? null;

        // Check if all required fields are present
        if ($department && $employee && $year && $semester) {
            // Prepare statement for inserting schedule
            $stmt = $conn->prepare("INSERT INTO Schedule (emp_id, sy, semester, day_of_week, start_time, end_time, schedule_type, level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            // Check if the prepare() call failed
            if ($stmt === FALSE) {
                $error_message = "Error preparing statement: " . $conn->error;
            } else {
                // Get the employee ID from the form
                $emp_id = $employee;

                // Parse the school year to extract the start year
                $start_year = intval(explode("-", $year)[0]);
                // Set the school year
                $sy = $year;

                // Array of days of the week
                $days_of_week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

                // Loop through each day of the week
                foreach ($days_of_week as $day) {
                    // Check if official schedule for the day is set
                    if (!empty($_POST["{$day}_start"]) && !empty($_POST["{$day}_end"])) {
                        $start_time = $_POST["{$day}_start"];
                        $end_time = $_POST["{$day}_end"];
                        $schedule_type = 'Official';
                        $level = NULL; // Set the level to NULL for official schedule
                        $day_of_week = ucfirst($day);

                        // Bind parameters for the official schedule
                        $stmt->bind_param("isssssss", $emp_id, $sy, $semester, $day_of_week, $start_time, $end_time, $schedule_type, $level);

                        // Execute the statement for the official schedule
                        if ($stmt->execute() === FALSE) {
                            $error_message = "Error: " . $stmt->error;
                            break;
                        }
                    }

                    // Check if overload schedule for the day is set
                    if (!empty($_POST["{$day}_overload_start"])) {
                        foreach ($_POST["{$day}_overload_start"] as $index => $overload_start) {
                            if (!empty($overload_start) && !empty($_POST["{$day}_overload_end"][$index])) {
                                $overload_end = $_POST["{$day}_overload_end"][$index];
                                $schedule_type = 'Overload';
                                $level = $_POST["{$day}_overload_option"][$index] ?? 'ug'; // Default to 'ug' if not set
                                $day_of_week = ucfirst($day);

                                // Bind parameters for the overload schedule
                                $stmt->bind_param("isssssss", $emp_id, $sy, $semester, $day_of_week, $overload_start, $overload_end, $schedule_type, $level);

                                // Execute the statement for the overload schedule
                                if ($stmt->execute() === FALSE) {
                                    $error_message = "Error: " . $stmt->error;
                                    break;
                                }
                            }
                        }
                    }
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();

                // If no errors, set success message
                if (!isset($error_message)) {
                    $status = 'success';
                    $message = "Schedule saved successfully.";
                } else {
                    $status = 'error';
                    $message = $error_message;
                }
            }
        } else {
            $status = 'error';
            $message = "Required fields are missing.";
        }
    } else {
        $status = 'error';
        $message = "Invalid request method.";
    }
}

// Redirect to faculty_official_time.php with the status and message
header("Location: faculty_official_time.php?status=$status&message=" . urlencode($message));
exit();
?>
