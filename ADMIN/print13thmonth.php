<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("fpdf181/fpdf.php");

session_start();
date_default_timezone_set('Asia/Hong_Kong');
$d = strtotime("now");
$currtime = date("Y-m-d H:i:s", $d);

$get13th1 = "SELECT * FROM 13thmonth";
$get13thexec1 = mysqli_query($conn, $get13th1) or die("FAILED TO GET 13TH MONTH " . mysqli_error($conn));

$pdf = new FPDF('P', 'mm', 'LETTER');
// Fetch the employee IDs
$employeeIDs = array();
while ($get13tharray1 = mysqli_fetch_assoc($get13thexec1)) {
    $employeeIDs[] = $get13tharray1['emp_id'];
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(24, 3, '', 0, 0);
    $pdf->Cell(130, 3, 'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM', 0, 1); // end of line

    $pdf->SetFont('Arial', 'B', 6);

    $pdf->Cell(33, 3, '', 0, 0);
    $pdf->Cell(130, 3, 'KAWIT CAVITE', 0, 1); //end of line

    $pdf->Cell(36, 3, '', 0, 0);
    $pdf->Cell(130, 3, 'Cavite, Philippines, 4104', 0, 1); //end of line

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(48, 6, '', 0, 0);
    $pdf->Cell(59, 6, 'Payslip', 0, 1); //end of line

    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(6, 3, '', 0, 0); //hspacer
    $pdf->Cell(16, 3, 'Employee ID:', 0, 0);

    $pdf->SetFont('Arial', 'B', 7);
    // Convert the array to a string using implode
    $pdf->Cell(43, 3, implode(', ', $employeeIDs), 0, 0);
}

// Rest of your script...
$pdfFileName = "Employee_Payslips.pdf";
$pdf->Output($pdfFileName, 'I');
?>
