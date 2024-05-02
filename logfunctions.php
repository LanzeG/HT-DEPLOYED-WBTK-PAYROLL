<?php

// Function to log activity
function logActivity($conn, $userId, $activity)
{
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userId', '$activity', NOW())";
    mysqli_query($conn, $sql);
}
function adminlogActivity($conn, $userId, $adminFullName, $activity)
{
    $sql = "INSERT INTO adminactivity_log (emp_id, adminname, activity, log_timestamp) VALUES ('$userId', '$adminFullName', '$activity', NOW())";
    mysqli_query($conn, $sql);
}

// Function to get user ID from the database based on the username
function getUserId($conn, $username)
{
    $sql = "SELECT emp_id FROM employees WHERE user_name = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['emp_id'];
}
function logPasswordChange($conn, $currempid, $success)
{
    $activity = $success ? 'Password Change Success' : 'Password Change Failed Attempt';
    $userId = $currempid;

    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userId', '$activity', NOW())";
    mysqli_query($conn, $sql);
}
function logOvertime($conn, $emp_id, $otsuccess)
{
    $overtime = $otsuccess ? 'Successfully Applied for Overtime' : 'Overtime Application Failed';
    $userIdot = $emp_id;
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userIdot', '$overtime', NOW())";
    mysqli_query($conn, $sql);
}
function logOvertimeReview($conn, $otemp, $action)
{
    if($action=='Update')
    {
        $otreview = 'Overtime details updated';
    } else if ($action=='Review'){
        $otreview = 'Viewed overtime details';
    }
    $userIdotreview = $otemp;
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userIdotreview', '$otreview', NOW())";
    mysqli_query($conn, $sql);
}

function logLeave($conn, $emp_idleave, $leavesuccess)
{
    $leave = $leavesuccess ? 'Successfully Applied for Leave' : 'Leave Application Failed';
    $userIdleave = $emp_idleave;
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userIdleave', '$leave', NOW())";
    mysqli_query($conn, $sql);
}

function logPageView($conn, $userIdpage, $page)
{
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userIdpage', 'User viewed page:$page', NOW())";
    mysqli_query($conn, $sql);
}


function logLogout($conn, $userIdlogout, $logoutTime) {
    $sql = "INSERT INTO empactivity_log (emp_id, activity, log_timestamp) VALUES ('$userIdlogout', 'User Logged Out', NOW())";
    mysqli_query($conn, $sql);
    echo "SQL Query: $sql<br>";
}
function adminlogLogout($conn, $userIdlogout, $adminFullName, $logoutTime) {
    $sql = "INSERT INTO adminactivity_log (emp_id, adminname, activity, log_timestamp) VALUES ('$userIdlogout','$adminFullName', 'Admin Logged Out', NOW())";
    mysqli_query($conn, $sql);
    echo "SQL Query: $sql<br>";
}
function masteradminlogLogout($conn, $userIdlogout, $adminFullName, $logoutTime) {
    $sql = "INSERT INTO adminactivity_log (emp_id, adminname, activity, log_timestamp) VALUES ('$userIdlogout','$adminFullName', 'Master Administrator Logged Out', NOW())";
    mysqli_query($conn, $sql);
    echo "SQL Query: $sql<br>";
}

?>