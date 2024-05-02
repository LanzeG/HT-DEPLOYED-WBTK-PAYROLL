<?php
include("../DBCONFIG.PHP");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_loantype']) && isset($_POST['loantypeid'])) {
        $loantypeidToDelete = $_POST['loantypeid'];

        // Perform the database deletion
        $deleteQuery = "DELETE FROM loantype WHERE loantypeid = $loantypeidToDelete";
        $deleteResult = mysqli_query($conn, $deleteQuery);

        if ($deleteResult) {
            echo 'Success';
        } else {
            echo 'Error';
        }

        // Close the database connection
        mysqli_close($conn);
    } else {
        echo 'Invalid parameters';
    }
} else {
    echo 'Invalid request method';
}
?>
