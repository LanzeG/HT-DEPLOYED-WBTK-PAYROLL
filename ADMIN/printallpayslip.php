<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("fpdf181/fpdf.php");
session_start();
date_default_timezone_set('Asia/Hong_Kong'); 
$storedQuery = $_SESSION['printpayrollquery'];

// echo $storedQuery;
$employeeResult = filterTable($storedQuery);

$printfrom = $_SESSION['payperiodfrom'];
$printto=$_SESSION['payperiodto'];
$payperiod = $_SESSION['payperiodrange'];
$d = strtotime("now");
$currtime = date ("Y-m-d H:i:s", $d);

require_once("fpdf181/fpdf.php");
$pdf = new FPDF('P', 'mm', 'LETTER');

while ($employee = mysqli_fetch_assoc($employeeResult)) {
    $printid = $employee['emp_id'];
    $payinfoqry = "SELECT * FROM PAYROLLINFO WHERE emp_id = '$printid'";
$payinfoexecqry = mysqli_query($conn,$payinfoqry) or die ("FAILED TO GET PAYROLL INFO");
$piarray = mysqli_fetch_array($payinfoexecqry);
if($piarray){

	$monthlyrate = $piarray['base_pay'];
	$semimonthlyrate = ($monthlyrate / 2);
	$smrate = number_format((float)$semimonthlyrate,2,'.','');
}

$timekeepinfo = "SELECT SUM(ot_sh) as totalOTSH, SUM(ot_rh) as totalOTRH,  SUM(lv_hours) as totalLH, SUM(sh_hours) as totalSH, SUM(rh_hours) as totalRH, SUM(undertime_hours) as totalUT ,SUM(overtime_hours) as totalOT, SUM(hours_work) as totalWORKhours, SUM(late_hours) as totalLATEhours, SUM(hours_work+overtime_hours+ot_rh+ot_sh) as totalness FROM TIME_KEEPING WHERE emp_id = '$printid' AND timekeep_day BETWEEN '$printfrom' and '$printto' ORDER BY timekeep_day ASC";
$timekeepinfoexecqry = mysqli_query($conn,$timekeepinfo) or die ("FAILED TO GET TIMEKEEP INFO ".mysqli_error($conn));
$timekeepinfoarray = mysqli_fetch_array($timekeepinfoexecqry);
if ($timekeepinfoarray){

	$hw = $timekeepinfoarray['totalWORKhours'];

	$oth = $timekeepinfoarray['totalOT'];
	$otrh = $timekeepinfoarray['totalOTRH'];
	$otsh = $timekeepinfoarray['totalOTSH'];


	$othrs = $oth + $otrh + $otsh ;

	$latehours = $timekeepinfoarray['totalLATEhours'];

	$rhhrs = $timekeepinfoarray['totalRH'];
	$shhrs = $timekeepinfoarray['totalSH'];

	$lvhrs = $timekeepinfoarray['totalLH'];
	$hrswrk = $hw - ($rhhrs + $shhrs + $lvhrs);
}else{

}


$payslipdetailsqry = "SELECT * from employees, payrollinfo, PAY_PER_PERIOD WHERE PAY_PER_PERIOD.pperiod_range = '$payperiod' and PAY_PER_PERIOD.emp_id = employees.emp_id and payrollinfo.emp_id = employees.emp_id AND PAY_PER_PERIOD.emp_id = '$printid' ORDER BY PAY_PER_PERIOD.emp_id ASC";
$payslipdetailsexecqry = mysqli_query($conn,$payslipdetailsqry) or die ("FAILED TO GET PAYROLL DETAILS ".mysqli_error($conn));
/** PDF START **/
$pdf = new FPDF ('P','mm','LETTER');
$pdf ->AddPage();

$psdarray = mysqli_fetch_array($payslipdetailsexecqry);
if($psdarray){

$prefix = $psdarray['prefix_ID'];
$idno = $psdarray['emp_id'];
$lname = $psdarray['last_name'];
$fname = $psdarray['first_name'];
$mname = $psdarray['middle_name'];
$dept = $psdarray['dept_NAME'];
$rph = $psdarray['rate_per_hour'];
$name = "$lname, $fname $mname";
$empID = "$prefix$idno";

$rpd = $rph * 8;
$basicpay = $psdarray['reg_pay'];
$otpay = $psdarray['ot_pay'];
$otrhpay = $psdarray['otrh_pay'];
$otshpay = $psdarray['otsh_pay'];

$rhdaypay = $psdarray['hday_pay'];
$shdaypay = $psdarray['shday_pay'];
$lvpay = $psdarray['lv_pay'];

$phdeduct = $psdarray['philhealth_deduct'];
$sssdeduct = $psdarray['sss_deduct'];
$pagibigdeduct = $psdarray['pagibig_deduct'];
$sssloandeduct = $psdarray['sssloan_deduct'];
$pagibigloandeduct = $psdarray['pagibigloan_deduct'];
$tax = $psdarray['tax_deduct'];
$totaldeduct = $psdarray['total_deduct'];




$row1tot = ($basicpay + $otpay + $rhdaypay+  $otrhpay + $shdaypay + $otshpay);
$row1total = number_format((float)$row1tot,2,'.','');

$row2tot = (  $lvpay);
$row2total = number_format((float)$row2tot,2,'.','');


$grosspay = $row1tot + $row2tot;
$gpay = number_format((float)$grosspay,2,'.','');

$netpay = ($grosspay - $totaldeduct);
$npay = number_format((float)$netpay,2,'.','');
}$pdf->SetFont('Arial','B',8);

//Spacer
//$pdf->Cell(189,5,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])
$pdf->Cell(54,3,'',0,0);
$pdf->Cell(130,3,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1);// end of line

$pdf->SetFont('Arial','B',6);

$pdf->Cell(63,3,'',0,0);
$pdf->Cell(130,3,'Cavite',0,1);//end of line

$pdf->Cell(66,3,'',0,0);
$pdf->Cell(130,3,'Cavite',0,1);//end of line

$pdf->SetFont('Arial','B',7);
$pdf->Cell(78,6,'',0,0);
$pdf->Cell(59,6,'Payslip',0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);//hspacer
$pdf->Cell(16,3,'Employee ID:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(90,3,$empID,0,0);

$pdf->SetFont('Arial','',7);

$pdf->Cell(16,3,'Date Printed:',0,0);
$pdf->Cell(20,3,$currtime,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(9,3,'Name:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(97,3,$name,0,0);

$pdf->SetFont('Arial','',7);
$pdf->Cell(13,3,'Pay Period:',0,0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(20,3,$payperiod,0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(14,3,'Department:',0,0);
$pdf->Cell(45,3,$dept,0,1);//end of line

$pdf->Cell(189,5,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(25,3,'RATE:',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(19,3,$smrate,0,0);
$pdf->Cell(25,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(19,3,'',0,0);
$pdf->Cell(55,3,'DEDUCTIONS:',0,1);//end of line

//$pdf->Cell(189,3,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'HRS',0,0);
$pdf->Cell(18,3,'TOTAL',0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'HRS',0,0);
$pdf->Cell(18,3,'TOTAL',0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'TOTAL',0,1);//end of line



$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Basic Pay:',0,0);
$pdf->Cell(9,3,$hrswrk,0,0);
$pdf->Cell(18,3,$basicpay,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'Philhealth:',0,0);
$pdf->Cell(15,3,$phdeduct,0,1);//end of line


$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'OT:',0,0);
$pdf->Cell(9,3,$othrs,0,0);
$pdf->Cell(18,3,$otpay,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'GSIS:',0,0);
$pdf->Cell(15,3,$sssdeduct,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Special Holiday',0,0);
$pdf->Cell(9,3,$shhrs,0,0);
$pdf->Cell(18,3,$shdaypay,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'PAG-IBIG:',0,0);
$pdf->Cell(15,3,$pagibigdeduct,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'Regular Holiday',0,0);
$pdf->Cell(9,3,$rhhrs,0,0);
$pdf->Cell(18,3,$shdaypay,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'OT RH',0,0);
$pdf->Cell(9,3,$otrh,0,0);
$pdf->Cell(18,3,$otrhpay,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'SSS Loan:',0,0);
$pdf->Cell(15,3,$sssloandeduct,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'PAG-IBIG Loan:',0,0);
$pdf->Cell(15,3,$pagibigloandeduct,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(26,3,'OT SH:',0,0);
$pdf->Cell(9,3,$otsh,0,0);
$pdf->Cell(18,3,$otshpay,0);
$pdf->Cell(26,3,'Leave:',0,0);
$pdf->Cell(9,3,$lvhrs,0,0);
$pdf->Cell(18,3,$lvpay,0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line

//$pdf->Cell(189,2,'',0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(152,0.3,'',1,1);//end of line

$pdf->Cell(6,1,'',0,0);
$pdf->Cell(152,1,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'TOTAL:',0,0);
$pdf->Cell(18,3,$row1total,0,0);
$pdf->Cell(35,3,'TOTAL:',0,0);
$pdf->Cell(18,3,$row2total,0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(35,3,'TOTAL GROSS:',0,0);
$pdf->Cell(18,3,$gpay,0,0);
$pdf->Cell(36,3,'TOTAL DEDUCTIONS:',0,0);
$pdf->Cell(15,3,$totaldeduct,0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(100,2,'',0,1);//end of line

$pdf->SetFont('Arial','B',8);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(34,3,'NET PAY:',0,0);
$pdf->Cell(15,3,$npay,0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(85,0.3,'',0,0);
$pdf->Cell(50,0.3,'',0,0);
$pdf->Cell(18,0.3,'',1,1);// end of line

$pdf->Cell(6,5,'',0,0);
$pdf->Cell(100,5,'',0,1);//end of line

$pdf->SetFont('Arial','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(25,3,'Received by:',0,0);
$pdf->Cell(18,3,'__________________________',0,1);//end of line

$pdf->Cell(6,5,'',0,0);
$pdf->Cell(100,5,'',0,1);//end of line
$pdf->Cell(189,3,'',0,1);//end of line


}

$pdf->Output();


function filterTable($searchquery)
{
    $conn1 = mysqli_connect("localhost:3307", "root", "", "masterdb");
    $filter_Result = mysqli_query($conn1, $searchquery) or die("failed to query employees " . mysqli_error($conn1));
    return $filter_Result;
}

?>