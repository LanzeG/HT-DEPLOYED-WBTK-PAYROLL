<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
?>


<?php
if (isset($_POST['department'])) {
    $department = $_POST['department'];
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT emp_id, last_name FROM employees WHERE dept_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['emp_id'] . "'>" . $row['last_name'] . "</option>";
        }
    } else {
        echo "<option value=''>No employees found</option>";
    }
    $stmt->close();
    $conn->close();
}
?>
