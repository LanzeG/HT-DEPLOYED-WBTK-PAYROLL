<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("./fpdf181/fpdf.php");
require_once("../phpqrcode/qrlib.php");


// Check if the print button is clicked
if (isset($_GET['printAll'])) {
    
    // Print data as PDF query
    $query = "SELECT * FROM employees";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        die("Failed to fetch data: " . mysqli_error($conn));
    }
    $urlhehe = 'hi';

    $adminId = $_SESSION['adminId'];

    $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
    $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
    $adminData = mysqli_fetch_assoc($adminnameexecqry);

    $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
    // Embed QR code into PDF
    printDataAsPDF($result, $urlhehe,$adminFullName);
    
    // Clean up temporary QR code image file
    unlink($qrCodeFile);
} elseif (isset($_GET['printDisplayed'])) {
    // Print displayed masterlist query
    session_start();
    

    // Debugging: Check if the session variable is set
    var_dump($_SESSION['print_query']);

    $queryResult = isset($_SESSION['print_query']) ? mysqli_query($conn, $_SESSION['print_query']) : '';

    if ($queryResult === false) {
        die("Failed to fetch data: " . mysqli_error($conn));
    }

    $adminId = $_SESSION['adminId'];
    $adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
    $adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
    $adminData = mysqli_fetch_assoc($adminnameexecqry);

    $adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
    // Embed QR code into PDF
    printDataAsPDF($queryResult, $urlhehe, $adminFullName);
    // Clean up temporary QR code image file
    unlink($qrCodeFile);
}

// Function to fetch and display data as PDF
function printDataAsPDF($result, $urlhehe,$adminFullName) {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    
    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 50, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 90, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    // Add watermark
    $pdf->SetFont('times', 'B', 30);
    $pdf->SetTextColor(220, 220, 220); // Set a light gray color
    $pdf->Text(80, 120, 'COMPUTER-GENERATED'); // Set the text and position
    $pdf->SetTextColor(0); // Reset text color

    $pdfIdentifier = time();

    // Generate QR code data with a link to download the PDF
    // $qrCodeData = "http://localhost:8080/thesissiguro/ADMIN/download_pdf.php?pdf={$pdfIdentifier}";
    // $qrCodeFile = 'temp_qr_code.png';
    // QRcode::png($qrCodeData, $qrCodeFile);

    // $pdf->Image($qrCodeFile, 10, 10, 30, 30, 'png');
    // $pdf->SetY(50);
    $pdf->SetFont('times','B',14);
    
    //Spacer
    $pdf->Cell(189,10,'',0,1);//end of line
    
    //Cell (width,height,text,border,end line, [align])
 
    $pdf->Cell(70,10,'MASTERLIST',0,1);//end
    // Header
    // Header
    $pdf->SetFont('times', 'B', 11);
    $pdf->Cell(30, 10, 'Employee ID', 1);
    // $pdf->Cell(25, 10, 'Fingerprint ID', 1);
    $pdf->Cell(30, 10, 'Last Name', 1);
    $pdf->Cell(32, 10, 'First Name', 1);
    $pdf->Cell(35, 10, 'Middle Name', 1);
    $pdf->Cell(30, 10, 'Username', 1);
    $pdf->Cell(32, 10, 'Department', 1);
    $pdf->Cell(30, 10, 'Emp Type', 1);
    $pdf->Cell(30, 10, 'Contact #', 1);
    $pdf->Cell(30, 10, 'Date Hired', 1);
    // Add more columns as needed

    // Data
   

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->SetFont('times', 'B', 10);
            $pdf->Ln();
            $pdf->Cell(30, 10, $row['emp_id'], 1);
            $pdf->SetFont('times', '', 10);
            // $pdf->Cell(25, 10, $row['fingerprint_id'], 1);
            $pdf->Cell(30, 10, $row['last_name'], 1);
            $pdf->Cell(32, 10, $row['first_name'], 1);
            $pdf->Cell(35, 10, $row['middle_name'], 1);
            $pdf->Cell(30, 10, $row['user_name'], 1);
            $pdf->Cell(32, 10, $row['dept_NAME'], 1);
            $pdf->Cell(30, 10, $row['contact_number'], 1);
            $pdf->Cell(30, 10, $row['date_hired'], 1);
            $pdf->Cell(30, 10, $row['emp_id'], 1);
         // Add more cells for additional columns
        }
    } else {
        $pdf->Cell(100, 10, 'No data found', 1, 1);
    }

    $pdf->Ln();
    $pdf->Cell(30, 10, 'Printed by:', 1);
    $pdf->Cell(62, 10, $adminFullName, 1, 1);

    // Output the PDF
    ob_start();  // Start output buffering
    $pdf->Output();
    ob_end_flush();  // Flush output buffer
}



mysqli_close($conn);
?>
