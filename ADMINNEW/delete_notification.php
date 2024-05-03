<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

// echo "adminId in this file: ".$_SESSION['adminId'];
$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
if (isset($_POST['notifid'])) {
    $notif = $_POST['notifid'];

    // Log information to the console
    echo "<script>console.log('Deleting notification_id: $notif');</script>";

    $deleteQuery = "DELETE FROM notifications WHERE notification_id = '$notif'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        $activityLog = "Deleted notification (notification_id: $notif)";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity, log_timestamp) VALUES ('$adminId', '$adminFullName', '$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                swal({
                    text: "Issue resolved    successfully",
                    icon: "success",
                    button: "OK",
                }).then(function () {
                    window.location.href = 'all_notifications.php';
                });
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                swal({
                    text: "Something went wrong while deleting",
                    icon: "error",
                    button: "Try Again",
                });
            });
        </script>
        <?php
    }

    exit();
}
?>
