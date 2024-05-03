<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");
require_once("./../fpdf181/fpdf.php");

$idres = $_GET['id'];

// Function to fetch and display data as PDF
function printDataAsPDF($result,$adminFullName) {
    $pdf = new FPDF('L', 'mm', 'A3');
    $pdf->AddPage();



// Add watermark
$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(80, 90, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color


$pdf->SetFont('times','B',12);

//Spacer
$pdf->Cell(189,2,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])
$pdf->Cell(260,10,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1,'C');//end
    
    // Header
    $pdf->Cell(25, 10, 'Loan ID', 1);
    $pdf->Cell(20, 10, 'Loan Organization', 1);
    $pdf->Cell(20, 10, 'Loan Type', 1);
    $pdf->Cell(20, 10, 'Employee ID', 1);
    $pdf->Cell(19, 10, 'Last Name', 1);
    $pdf->Cell(19, 10, 'First Name', 1);
    $pdf->Cell(22, 10, 'Middle Name', 1);
    $pdf->Cell(22, 10, 'Start Date', 1);
    $pdf->Cell(22, 10, 'End Date', 1);
    $pdf->Cell(22, 10, 'Amount', 1);
    $pdf->Cell(25, 10, 'Monthly Payment', 1);
    $pdf->Cell(25, 10, 'Status', 1);
    $pdf->Cell(25, 10, 'Number of Payments', 1);
    $pdf->Cell(25, 10, 'Current Amount', 1);
    $pdf->Cell(25, 10, 'Pay Period', 1);
    $pdf->Cell(25, 10, 'Remarks', 1);
    $pdf->Cell(25, 10, 'Admin Name', 1);
    // Add more columns as needed

    // Data
    $pdf->SetFont('times', '', 8);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->Ln();
            $pdf->Cell(25, 10, $row['loan_id'], 1);
            $pdf->Cell(20, 10, $row['loanorg'], 1);
            $pdf->Cell(20, 10, $row['loantype'], 1);
            $pdf->Cell(20, 10, $row['emp_id'], 1);
            $pdf->Cell(19, 10, $row['lastname'], 1);
            $pdf->Cell(19, 10, $row['firstname'], 1);
            $pdf->Cell(22, 10, $row['middlename'], 1);
            $pdf->Cell(22, 10, $row['start_date'], 1);
            $pdf->Cell(22, 10, $row['end_date'], 1);
            $pdf->Cell(22, 10, $row['amount'], 1);
            $pdf->Cell(25, 10, $row['monthly_payment'], 1);
            $pdf->Cell(25, 10, $row['status'], 1);
            $pdf->Cell(25, 10, $row['num_of_payments'], 1);
            $pdf->Cell(25, 10, $row['current_amount'], 1);
            $pdf->Cell(25, 10, $row['payperiod'], 1);
            $pdf->Cell(25, 10, $row['remarks'], 1);
            $pdf->Cell(25, 10, $row['admin_name'], 1);

            // Add more cells for additional columns
        }
    } else {
        $pdf->Cell(100, 10, 'No data found', 1, 1);
    }
    $pdf->Ln();
    $pdf->Cell(25, 10, 'Printed by:', 1);
    $pdf->Cell(60, 10, $adminFullName, 1, 1);


    // Output the PDF
    ob_start();  // Start output buffering
    $pdf->Output();
    ob_end_flush();  // Flush output buffer
}

// Check if the print button is clicked
if (isset($_GET['printAll'])) {
    // Print data as PDF query
    $query = "SELECT * FROM loan_history
          INNER JOIN employees ON employees.emp_id = loan_history.emp_id
          WHERE loan_history.emp_id = $idres
          ORDER BY uniquekey";

    $result = mysqli_query($conn, $query);

    if ($result === false) {
        die("Failed to fetch data: " . mysqli_error($conn));
    }
    $adminId = $_SESSION['adminId'];
    $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
    $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
    $adminData = mysqli_fetch_assoc($adminnameexecqry);

    $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
    printDataAsPDF($result,$adminFullName);
} elseif (isset($_GET['printDisplayed'])) {
    // Print displayed masterlist query
    session_start();

    // Debugging: Check if the session variable is set
    var_dump($_SESSION['printgsis_query']);

    $queryResult = isset($_SESSION['printgsis_query']) ? mysqli_query($conn, $_SESSION['printgsis_query']) : '';

    if ($queryResult === false) {
        die("Failed to fetch data: " . mysqli_error($conn));
    }
    $adminId = $_SESSION['adminId'];
    $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
    $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
    $adminData = mysqli_fetch_assoc($adminnameexecqry);

    $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

    printDataAsPDF($queryResult,$adminFullName);
}

mysqli_close($conn);
?>