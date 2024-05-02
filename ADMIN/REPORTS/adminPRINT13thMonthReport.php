<?php
set_time_limit(60);
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

session_start();

$year13th = $_SESSION['13thmonthyear'];
$year13thqry = "SELECT * FROM 13thmonth WHERE 13th_year = '$year13th'";
$year13thexecqry = mysqli_query($conn,$year13thqry) or die ("FAILED TO GET 13TH YEAR RECORDS ".mysqli_error($conn));



require_once("../fpdf181/fpdf.php");

$pdf = new FPDF ('P','mm','LETTER');

$pdf ->AddPage();

$pdf->SetFont('Arial','B',14);

$pdf->Cell(189,10,'',0,1);

$pdf->Cell(189,5,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1,'C');

$pdf->SetFont('Arial','',9);

$pdf->Cell(189,5,'Cavite',0,1,'C');
$pdf->Cell(189,5,'Cavite',0,1,'C');

$pdf->Cell(189,10,'',0,1);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(189,5,'13th Month Pay Report',0,1,'C');
$pdf->Cell(189,5,$year13th,0,1,'C');
$pdf->Cell(189,5,'',0,1,'C');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(31.20,7,'',0,0,'C');
$pdf->Cell(44.25,14,'EMPLOYEE ID',1,0,'C');
$pdf->Cell(54.3,14,'EMPLOYEE FULLNAME',1,0,'C');
$pdf->Cell(28,14,'13th Month Pay',1,1,'C');//end


$pdf->SetFont('Arial','',10);
while ($check1array = mysqli_fetch_array($year13thexecqry)):;
  
  $empid = $check1array['emp_id'];
  $amount = $check1array['13th_amount'];
  $year = $check1array['13th_year'];


  $getdetailsquery = "SELECT last_name,first_name FROM employees WHERE emp_id = '$empid'";
  $getdetailsexecquery = mysqli_query($conn,$getdetailsquery) or die ("FAILED 2 ".mysqli_error($conn));
  $getdetailsarray = mysqli_fetch_array($getdetailsexecquery);

  if($getdetailsarray){

    $fname = $getdetailsarray['first_name'];
    //$mname = $getdetailsarray['middle_name'];
    $lname = $getdetailsarray['last_name'];
    $fullname = "$lname, $fname";
    // $amounttotal = $amounttotal + $amount;

  }

$pdf->Cell(31.20,7,'',0,0,'C');
$pdf->Cell(44.25,7,$empid,1,0,'C');
$pdf->Cell(54.3,7,$fullname,1,0,'C');
$pdf->Cell(28,7,$amount,1,1,'C');

endwhile;


$pdf->SetFont('Arial','B',10);
$pdf->Cell(31.20,7,'',0,0,'C');
$pdf->Cell(44.25,14,'',0,0,'C');
$pdf->Cell(54.3,14,'TOTAL:',0,0,'L');
$pdf->Cell(28,14,$amount,0,1,'C');

$pdf->Output();
?>