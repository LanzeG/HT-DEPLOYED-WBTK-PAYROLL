

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("fpdf181/fpdf.php");

session_start();
date_default_timezone_set('Asia/Hong_Kong');
if (isset($_GET['id'])){
$printid = $_GET['id'];
}
$adminId = $_SESSION['adminId'];

$d = strtotime("now");
$currtime = date ("Y-m-d H:i:s", $d);

$get13th = "SELECT * FROM 13thmonth WHERE emp_id = '$printid'";
$get13thexec = mysqli_query($conn,$get13th) or die ("FAILED TO GET 13TH MONTH ".mysqli_error($conn));
$get13tharray = mysqli_fetch_array($get13thexec);

if ($get13tharray){

	$amount13th = $get13tharray['13th_amount'];
}else{
    // No data found, exit the script
    $pdf = new FPDF();
	$pdf->AddPage();
	$pdf->Output();
}

$getrate = "SELECT daily_rate FROM PAYROLLINFO WHERE emp_id = '$printid'";
$getrateexec = mysqli_query($conn,$getrate) or die ("FAILED TO GET DR ".mysqli_error($conn));
$getratearray = mysqli_fetch_array($getrateexec);

if ($getratearray){

	$dr = $getratearray['daily_rate'];

}

$getinfo = "SELECT * FROM employees WHERE emp_id = '$printid'";
$getinfoexec = mysqli_query($conn,$getinfo) or die ("FAILED TO GET INFO ".mysqli_error($conn));
$psdarray = mysqli_fetch_array($getinfoexec);
if($psdarray){

$prefix = $psdarray['prefix_ID'];
$idno = $psdarray['emp_id'];
$lname = $psdarray['last_name'];
$fname = $psdarray['first_name'];
$mname = $psdarray['middle_name'];
$dept = $psdarray['dept_NAME'];

$name = "$lname, $fname $mname";
$empID = "$prefix$idno";

}

$pdf = new FPDF ('P','mm','LETTER');
$pdf ->AddPage();


$pdf->SetFont('Arial','B',8);

//Spacer
$pdf->Cell(189,5,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,4,'WEB-BASED TIMEKEEPING',0,1,'C');


$pdf->Cell(73,4,'AND PAYROLL SYSTEM ',0,1,'C');
$pdf->Cell(92,4,'USING FINGERPRINT BIOMETRIC ',0,1,'C');


$pdf->SetFont('Arial','','8');
$pdf->Cell(40,4,'Kawit, Cavite',0,1,'C');//end of line
$pdf->Cell(31,4,'Cavite',0,1,'C');//end of line



$pdf->SetFont('Arial','B',7);
$pdf->Cell(48,6,'',0,0);
$pdf->Cell(59,6,'Payslip',0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);//hspacer
$pdf->Cell(16,3,'Employee ID:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(43,3,$empID,0,0);

$pdf->SetFont('Arial','',7);

$pdf->Cell(16,3,'Date Printed:',0,0);
$pdf->Cell(20,3,$currtime,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(9,3,'Name:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(50,3,$name,0,0);

$pdf->SetFont('Arial','',7);
$pdf->Cell(13,3,'Pay Period:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(20,3,'13th Month',0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(14,3,'Department:',0,0);
$pdf->Cell(45,3,$dept,0,1);//end of line

$pdf->Cell(189,5,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(25,3,'RATE:',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(19,3,$dr,0,0);
$pdf->Cell(55,3,'DEDUCTIONS:',0,1);//end of line

$pdf->Cell(189,3,'',0,1);//end of line
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'HRS',0,0);
$pdf->Cell(18,3,'TOTAL',0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'TOTAL',0,1);//end of line



$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Basic Pay:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'Philhealth:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line


$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Overtime:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'SSS:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Reg. Holiday:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'PAG-IBIG:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Reg. Holiday(200%):',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'Withholding Tax:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'SNW Holiday:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'SSS Loan:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'VL/SL:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,'-',0,0);
$pdf->Cell(36,3,'PAG-IBIG Loan:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'13th Month:',0,0);
$pdf->Cell(9,3,'-',0,0);
$pdf->Cell(18,3,$amount13th,0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line

$pdf->Cell(189,2,'',0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(100,0.3,'',1,1);//end of line

$pdf->Cell(6,1,'',0,0);
$pdf->Cell(100,1,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'TOTAL GROSS PAY:',0,0);
$pdf->Cell(18,3,$amount13th,0,0);
$pdf->Cell(36,3,'TOTAL DEDUCTIONS:',0,0);
$pdf->Cell(15,3,'-',0,1);//end of line

$pdf->Cell(6,7,'',0,0);
$pdf->Cell(100,7,'',0,1);//end of line

$pdf->SetFont('Arial','B',8);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'NET PAY:',0,0);
$pdf->Cell(15,3,$amount13th,0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(85,0.3,'',0,0);
$pdf->Cell(18,0.3,'',1,1);// end of line

$pdf->Cell(6,5,'',0,0);
$pdf->Cell(100,5,'',0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(25,3,'Received by:',0,0);
$pdf->Cell(18,3,'__________________________',0,1);//end of line

// Line 2
$pdf->Cell(6, 3, 'Printed by:', 0, 0);
$pdf->Cell(25, 3, '', 0, 0); // Add an empty cell for spacing
$pdf->Cell(75, 3, $adminId, 0, 1); // end of line











$pdf->Output();
?>
