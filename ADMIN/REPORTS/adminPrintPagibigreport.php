<?php
set_time_limit(60);
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

session_start();

$adminId = $_SESSION['adminId'];
$from = $_SESSION['fromreport'];
$toreport = $_SESSION['toreport'];

$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$payperiodquery = "SELECT pperiod_end FROM payperiods WHERE pperiod_range = '$toreport'";
$payperiodexecquery = mysqli_query($conn,$payperiodquery) or die ("FAILED1 ".mysqli_error($conn));
$payperiodarray = mysqli_fetch_array($payperiodexecquery);
if ($payperiodarray){
  $enddate = $payperiodarray['pperiod_end'];
}

$conv = strtotime($enddate);
$monthyear = date("F Y", $conv);

$checkpayperperiod = "SELECT * FROM PAY_PER_PERIOD WHERE pperiod_range = '$toreport'";
$checkpayperperiodexec = mysqli_query($conn,$checkpayperperiod) or die ("FAILED ".mysqli_error($conn));

require_once("../fpdf181/fpdf.php");

$pdf = new FPDF ('P','mm','LETTER');

$pdf ->AddPage();

$pdf->SetFont('Arial','B',12);

$pdf->Cell(189,10,'',0,1);

$pdf->Cell(189,5,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1,'C');

$pdf->SetFont('Arial','',9);

$pdf->Cell(189,10,'',0,1);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(189,5,'GSIS Contribution ',0,1,'C');
$pdf->Cell(189,5,$monthyear,0,1,'C');
$pdf->Cell(189,5,'',0,1,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(44.25,14,'SSS NUMBER',1,0,'C');
$pdf->Cell(23,14,'LAST NAME',1,0,'C');
$pdf->Cell(23,14,'FIRST NAME',1,0,'C');
$pdf->Cell(23,14,'MIDDLE NAME',1,0,'C');
$pdf->Cell(25,14,'ER',1,0,'C');
$pdf->Cell(25,14,'EE',1,0,'C');
$pdf->Cell(34,14,'TOTAL',1,1,'C');


$ertotal = 0;
$eetotal = 0;
$total = 0;

$pdf->SetFont('Arial','',10);
while ($check1array = mysqli_fetch_array($checkpayperperiodexec)):;
  
  $empid = $check1array['emp_id'];
  $getsssinfoquery = "SELECT * FROM pay_per_period JOIN payrollinfo ON pay_per_period.emp_id = payrollinfo.emp_id WHERE pay_per_period.pperiod_range = '$toreport' AND pay_per_period.emp_id ='$empid'";
  $getsssinfoexecquery = mysqli_query($conn,$getsssinfoquery) or die ("FAILED 1".mysqli_error($conn));
  $sssinfoarray = mysqli_fetch_array($getsssinfoexecquery);
  if ($sssinfoarray && $sssinfoarray['pagibig_deduct'] != 0) {
    // The key 'pagibig_deduct' is set in $sssinfoarray, regardless of its value
    $pagibigER = $sssinfoarray['pagibig_ER'];
    $pagibigEE = $sssinfoarray['pagibig_deduct'];
    $pagibigTOTAL= $pagibigEE + $pagibigER;
} else {
    // If 'sss_deduct' is not set or is null, set both variables to some default value or handle as needed
    $pagibigER = 0;
    $pagibigEE = 0;
    $pagibigTOTAL = 0;
}

  $getdetailsquery = "SELECT last_name,first_name,middle_name,pagibig_idno FROM employees WHERE emp_id = '$empid'";
  $getdetailsexecquery = mysqli_query($conn,$getdetailsquery) or die ("FAILED 2 ".mysqli_error($conn));
  $getdetailsarray = mysqli_fetch_array($getdetailsexecquery);

  if($getdetailsarray){

    $pagibigidno = $getdetailsarray['pagibig_idno'];
    $fname = $getdetailsarray['first_name'];
    $mname = $getdetailsarray['middle_name'];
    $lname = $getdetailsarray['last_name'];
    $fullname = "$lname, $fname";

  }


    $pdf->Cell(44.25,7,$pagibigidno,1,0,'C');
    $pdf->Cell(23,7,$fname,1,0,'C');
    $pdf->Cell(23,7,$lname,1,0,'C');
    $pdf->Cell(23,7,$mname,1,0,'C');
    $pdf->Cell(25,7,$pagibigER,1,0,'C');
    $pdf->Cell(25,7,$pagibigEE,1,0,'C');
    $pdf->Cell(34,7,$pagibigTOTAL,1,1,'C');//end

endwhile;


$pdf->Cell(34, 14, 'Printed By:' . $adminFullName, 0, 1, 'C');

$pdf->Output();
?>