<?php
include("DBCONFIG.PHP");
include("logfunctions.PHP");

session_start();
date_default_timezone_set('Asia/Manila');
$current_datetime = date('Y-m-d H:i:s');

if (isset($_POST['login_btn'])) {
    $username = $_POST['adminUser'];
    $password = $_POST['adminPass'];
    $account = '1';

    $_SESSION['uname'] = $username;

    $username = stripcslashes($username);
    $password = stripcslashes($password);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $result = mysqli_query($conn, "SELECT * FROM employees WHERE user_name = '$username' AND pass_word = '$password'")
        or die("Failed to query database " . mysqli_error($conn));

    $row = mysqli_fetch_array($result);
    if ($row['user_name'] == $username && $row['pass_word'] == $password && ($row['acct_type'] == "Administrator" || $row['acct_type'] == "Faculty w/ Admin")) {
        $userId = getUserId($conn, $username);
        $_SESSION['adminId'] = $userId;
        $_SESSION['empId'] = $userId;
        $_SESSION['master'] = false;
        //for adminname
        $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$userId'";
        $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
        $adminData = mysqli_fetch_assoc($adminnameexecqry);

        $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

        //actlog
        logActivity($conn, $userId, 'Administrator Login',$current_datetime);
        adminlogActivity($conn, $userId, $adminFullName, 'Administrator Login',$current_datetime);
        header("location: ADMINNEW/admintry.php");
    } else if ($row['user_name'] == $username  && ($row['acct_type'] == "Employee" || $row['acct_type'] == "Faculty")){
        $userId = getUserId($conn, $username);
        $_SESSION['empId'] = $userId;
        logActivity($conn, $userId, 'Employee Login',$current_datetime);
        header("Location: EMPLOYEENEW/employee-dashboard.php");
    } else if ($row['user_name'] == $username && $row['pass_word'] == $password && $row['acct_type'] == "Master") {
        $userId = getUserId($conn, $username);
        $_SESSION['adminId'] = $userId;
        $_SESSION['empId'] = $userId;
        $_SESSION['master'] = $userId;

         //for adminname
         $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$userId'";
         $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
         $adminData = mysqli_fetch_assoc($adminnameexecqry);
 
         $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

        logActivity($conn, $userId, 'Master Administrator Login',$current_datetime);
        adminlogActivity($conn, $userId, $adminFullName, 'Master Administrator Login',$current_datetime);
        header("location: ADMINNEW/admintry.php");
    }else {
        
       

        $_SESSION['status'] = "<div class='alert alert-danger' role='alert'>Invalid Username or Password.</div>";
        header('location: default.php');
        

    }
}


?>
