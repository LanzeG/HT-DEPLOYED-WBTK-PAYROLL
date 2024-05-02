<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();

if (isset($_GET['pdf']) && $_GET['pdf'] === $_SESSION['pdf_identifier']) {
    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="your_pdf_filename.pdf"');
    
    // Output your PDF file content from the provided URL
    readfile('http://localhost:8080/thesissiguro/ADMIN/printmasterlist.php?printAll');

    // Stop further execution
    exit;
} else {
    die("Invalid PDF download request.");
}
?>
