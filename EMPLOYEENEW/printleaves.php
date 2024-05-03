<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("fpdf181/fpdf.php");

// Function to fetch and display data as PDF
function printDataAsPDF($result) {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();

    // Add watermark
    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 50, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 85, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 130, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(60,3,'',0,0);
	$pdf->Cell(130,30,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1);// end of line
    $pdf->Cell(30, 10, 'Employee ID', 1);
    $pdf->Cell(30, 10, 'Last Name', 1);
    $pdf->Cell(30, 10, 'First Name', 1);
    $pdf->Cell(28, 10, 'Middle Name', 1);
    $pdf->Cell(30, 10, 'Department', 1);
    $pdf->Cell(25, 10, 'Emp Type', 1);
    $pdf->Cell(22, 10, 'Shift', 1);
    $pdf->Cell(30, 10, 'Leave Type', 1);
    $pdf->Cell(30, 10, 'Leave Start', 1);
    $pdf->Cell(30, 10, 'Remarks', 1);
    

    // Data
    $pdf->SetFont('times', '', 10);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->Ln();
            $pdf->Cell(30, 10, $row['emp_id'], 1);
            $pdf->Cell(30, 10, $row['last_name'], 1);
            $pdf->Cell(30, 10, $row['first_name'], 1);
            $pdf->Cell(28, 10, $row['middle_name'], 1);
            $pdf->Cell(30, 10, $row['dept_NAME'], 1);
            $pdf->Cell(25, 10, $row['employment_TYPE'], 1);
            $pdf->Cell(22, 10, $row['shift_SCHEDULE'], 1);
            $pdf->Cell(30, 10, $row['leave_type'], 1);
            $pdf->Cell(30, 10, $row['leave_datestart'], 1);
            $pdf->Cell(30, 10, $row['leave_status'], 1);

        }
    
        $pdf->Ln();
        $pdf->Cell(62, 30, 'Signature: ______________________', 0, 1, 'C');
        
    
    } else {
        $pdf->Cell(100, 10, 'No data found', 1, 1);
    }

    // Output the PDF
    ob_start();  // Start output buffering
    $pdf->Output();
    ob_end_flush();  // Flush output buffer
}
    
// Check if the print button is clicked
    $empid = $_SESSION['empId'];
    // Print data as PDF query
    $query = "SELECT * 
    FROM leaves_application 
    JOIN employees ON employees.emp_id = leaves_application.emp_id 
    WHERE employees.emp_id = '$empid' AND leaves_application.leave_status = 'Approved';";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        die("Failed to fetch data: " . mysqli_error($conn));
    }

    printDataAsPDF($result);
    

mysqli_close($conn);
?>
