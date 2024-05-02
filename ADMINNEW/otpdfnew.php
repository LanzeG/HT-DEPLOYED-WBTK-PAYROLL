<?php
ob_start(); // Start output buffering

include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

if (isset($_GET['id'])) {
    $printid = $_GET['id'];
}
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$printfrom = isset($_SESSION['payperiodfrom']) ? $_SESSION['payperiodfrom'] : null;
$printto = isset($_SESSION['payperiodto']) ? $_SESSION['payperiodto'] : null;
$payperiod = isset($_SESSION['payperiodrange']) ? $_SESSION['payperiodrange'] : null;

// if (isset($_GET['print_all'])) {
    $payslipdetailsqry = "SELECT * FROM employees";
// } elseif (isset($_GET['print_displayed'])) {
//     $payslipdetailsqry = $_SESSION['printot'];
// } else {
//     $payslipdetailsqry = "SELECT * FROM employees WHERE emp_id = '$printid'";
// }

$employeeResult = filterTable($payslipdetailsqry);

require_once '../vendor/autoload.php';
use Dompdf\Dompdf;

// Create a DOMPDF instance
$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'landscape');

// HTML content with the technological header
$html = '
<html>
<head>
    <style>
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</p>
        <p>Overload ' . $payperiod . '</p></p>
    </div>
    <table border="1">
        <tr>
            <th>EMP ID</th>
            <th>Name</th>
            <td>NO. OF HOURS</td>
            <td>RATE per HOUR</td>
            <td>Gross Amount</td>
            <td>No</td>
            <td>Signature of Payee</td>
            <td>Initial to of Witness to Payment</td>
            <td>Remarks</td>
        </tr>';
        
while ($employee = mysqli_fetch_assoc($employeeResult)) {
    $printid = $employee['emp_id'];
    $printfrom = $_SESSION['payperiodfrom'];
    $printto = $_SESSION['payperiodto'];

    // Fetch data for the employee
    $printquery = "SELECT * FROM over_time, employees 
               WHERE over_time.emp_id = employees.emp_id 
               AND over_time.emp_id = '$printid' 
               AND over_time.ot_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' AND ot_remarks='Approved' 
               ORDER BY ot_day ASC";
    $printqueryexec = mysqli_query($conn, $printquery);
    $printarray = mysqli_fetch_array($printqueryexec);

    if ($printarray) {
        $prefix = $printarray['prefix_ID'];
        $idno = $printarray['emp_id'];
        $lname = $printarray['last_name'];
        $fname = $printarray['first_name'];
        $mname = $printarray['middle_name'];
        $dept = $printarray['dept_NAME'];
        $position = $printarray['position'];

        $name = "$lname, $fname $mname";
        $empID = "$prefix$idno";
   

        $combinedResult = mysqli_query($conn, "SELECT over_time.*, payrollinfo.hourly_rate FROM over_time LEFT JOIN payrollinfo ON over_time.emp_id = payrollinfo.emp_id WHERE over_time.ot_day BETWEEN '$printfrom' AND '$printto' AND over_time.emp_id = '$idno'");
        $totalOvertimeSum = 0;  
        // Table Data
        while ($row = mysqli_fetch_assoc($combinedResult)) {
            $othours = $row['ot_hours'];
            $hourlyRate = $row['hourly_rate'];
            $totalOvertime = $othours  * $hourlyRate;
            $totalOvertimeSum += $totalOvertime;

            $html .= '<tr>
            <td>' . $employee['emp_id'] . '</td>
            <td>' . $employee['first_name'] . ' ' . $employee['last_name'] . '</td>
            <td>'.$othours.'</td>
            <td>'.$hourlyRate.'</td>
            <td>'.$totalOvertime.'</td>
            <td>1</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>

        <td>A</td>
        <td colspan="3">SHEET TOTAL</td>
        <td>'. $totalOvertimeSum.'</td>
        <td colspan=4""></td>
        </tr>
        <tr>
        
        <td colspan="9">Printed By: '.$adminFullName.'</td>
        </tr>';
        }
}
}
$html .= '
    </table>
</body>
</html>';

// Load HTML content into DOMPDF
$dompdf->loadHtml($html);

// Render PDF (optional: increase memory limit if needed)
ini_set('memory_limit', '512M');
$dompdf->render();

// Output PDF to the browser
$dompdf->stream('technological_header.pdf', array('Attachment' => 0));

function filterTable($searchquery)
{
    $conn1 = mysqli_connect("localhost:3307", "root", "", "masterdb");
    $filter_Result = mysqli_query($conn1, $searchquery) or die("failed to query employees " . mysqli_error($conn1));
    return $filter_Result;
}
