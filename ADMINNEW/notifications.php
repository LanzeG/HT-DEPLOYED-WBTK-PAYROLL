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

// Check if the admin is logged in
// if (!isset($_SESSION['admin_id'])) {
//     // Redirect or handle the case where the admin is not logged in
//     // You might want to customize this part based on your application's logic
//     header("Location: login.php");
//     exit();
// }

// Retrieve unread notifications for the admin
// $adminId = $_SESSION['admin_id'];



$unreadNotificationsQuery = "SELECT * FROM notifications WHERE status='unread'";
$unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);

// Prepare data for JSON response
$notificationsData = [
    'count' => mysqli_num_rows($unreadNotificationsResult),
    'notifications' => [],
];

if ($notificationsData['count'] > 0) {
    // Fetch unread notifications
    $notificationsData['notifications'] = mysqli_fetch_all($unreadNotificationsResult, MYSQLI_ASSOC);

    // Mark notifications as read in the database only if they are clicked
    if (isset($_GET['mark_as_read']) && $_GET['mark_as_read'] === 'true') {
        $markAsReadQuery = "UPDATE notifications SET status = 'read'";
        mysqli_query($conn, $markAsReadQuery);
    }
    
}

echo json_encode($notificationsData);
?>