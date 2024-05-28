<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['schedule_id'])) {
        $schedule_id = $_POST['schedule_id'];

        // Prepare the SQL statement to delete the record
        $stmt = $conn->prepare("DELETE FROM schedule WHERE schedule_id = ?");
        $stmt->bind_param("i", $schedule_id);

        if ($stmt->execute()) {
            echo "Schedule deleted successfully";
        } else {
            // Return detailed error message
            echo "Error deleting schedule: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Schedule ID not provided";
    }
} else {
    echo "Invalid request method";
}

$conn->close();
?>
