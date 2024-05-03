<?php
// Include necessary configurations, database connections, and session start
// include("../DBCONFIG.PHP");
// session_start();

// Retrieve unread notifications for the admin
//$adminId = $_SESSION['admin_id']; // Replace with actual session variable
// $unreadNotificationsQuery = "SELECT * FROM notifications";
// $unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);

// Prepare data for JSON response
// $notificationsData = [
    // 'count' => mysqli_num_rows($unreadNotificationsResult),
    // 'notifications' => mysqli_fetch_all($unreadNotificationsResult, MYSQLI_ASSOC),
// ];

// echo json_encode($notificationsData);
?>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();
$emp_id = $_SESSION['empId'];

// Check if the admin is logged in
// if (!isset($_SESSION['admin_id'])) {
//     // Redirect or handle the case where the admin is not logged in
//     // You might want to customize this part based on your application's logic
//     header("Location: login.php");
//     exit();
// }

// Retrieve unread notifications for the admin
// $adminId = $_SESSION['admin_id'];


// Assuming $emp_id is the ID of the logged-in employee (make sure it's sanitized to prevent SQL injection)

$unreadNotificationsQuery = "SELECT * FROM empnotifications WHERE status='unread' AND emp_id = $emp_id";
$unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);

// Prepare data for JSON response
$notificationsData = [
    'count' => 0,
    'notifications' => [],
];

if ($unreadNotificationsResult) {
    // Fetch unread notifications
    $notificationsData['count'] = mysqli_num_rows($unreadNotificationsResult);

    if ($notificationsData['count'] > 0) {
        $notificationsData['notifications'] = mysqli_fetch_all($unreadNotificationsResult, MYSQLI_ASSOC);

        // Mark the specific notification as read if it's clicked
        if (isset($_GET['mark_as_read']) && $_GET['mark_as_read'] === 'true') {
            $notificationId = intval($_GET['mark_as_read']); // Assuming notification ID is passed in the URL
            $markAsReadQuery = "UPDATE empnotifications SET status = 'read' WHERE emp_id = $emp_id";
            mysqli_query($conn, $markAsReadQuery);
        }
    }
}

// If no unread notifications, fetch all notifications for the employee
if ($notificationsData['count'] == 0) {
    $allNotificationsQuery = "SELECT * FROM empnotifications WHERE status='unread' AND emp_id = $emp_id";
    $allNotificationsResult = mysqli_query($conn, $allNotificationsQuery);

    if ($allNotificationsResult) {
        $notificationsData['notifications'] = mysqli_fetch_all($allNotificationsResult, MYSQLI_ASSOC);
        $notificationsData['count'] = count($notificationsData['notifications']);
    }
}

echo json_encode($notificationsData);

?>