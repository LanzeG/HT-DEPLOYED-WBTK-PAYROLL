<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("../../BASICLOGININFO.PHP");

$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
// Check if action is 'stop' and uniquekey is provided
if ($_POST['action'] == 'stop' && isset($_POST['uniquekey']) && isset($_POST['adminname'])) {
    $uniqueKey = $_POST['uniquekey'];
    $adminName = $_POST['adminname'];

    // Update the database record
    $updateQuery = "UPDATE loans SET status = 'Paid', loan_balance = 0, no_of_pays = 0, adminname = '$adminName' WHERE uniquekey = '$uniqueKey'";
    
    if (mysqli_query($conn, $updateQuery)) {
        // Fetch loan record
        $selectQuery = "SELECT * FROM loans WHERE uniquekey = '$uniqueKey'";
        $selectResult = mysqli_query($conn, $selectQuery);
        
        if ($selectResult && mysqli_num_rows($selectResult) > 0) {
            $row = mysqli_fetch_assoc($selectResult);
            // Extract loan record data
            $loanId = $row['loanidno'];
            $loanType = $row['loantype'];
            $loanOrg = $row['loanorg'];
            $empId = $row['emp_id'];
            $lastName1 = $row['emplastname'];
            $firstName1 = $row['empfirstname'];
            $middleName1 = $row['empmiddlename'];
            $amount = $row['loan_amount'];
            $startDate = $row['start_date'];
            $endDate = $row['end_date'];
            $monthlyPayment = $row['monthly_deduct'];
            $noOfPayments = $row['no_of_pays'];
            $currentAmount = $row['loan_balance'];
            $status = $row['status'];

            // Insert data into loan history table
            $insertQuery = "INSERT INTO loan_history (uniquekey, loan_id, loantype, loanorg, emp_id, lastname, firstname, middlename, amount, start_date, end_date, monthly_payment, status, num_of_payments, current_amount,remarks, admin_name) VALUES ('$uniqueKey', '$loanId', '$loanType', '$loanOrg', '$empId', '$lastName1', '$firstName1', '$middleName1', '$amount', '$startDate', '$endDate', '$monthlyPayment', '$status', '$noOfPayments', '$currentAmount', 'Stopped By', '$adminName')";
            $loanHistoryResult = mysqli_query($conn, $insertQuery);
            //actlog
            $activityLog = "Stopped Loan for ($firstName1 $lastName1)";
            $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
            $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
            
            if ($loanHistoryResult) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert loan history']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Loan record not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update loan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// Close the database connection
mysqli_close($conn);
?>
