<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
date_default_timezone_set('Asia/Hong_Kong'); 

// $adminId = $_SESSION['adminId'];


$empid = $_SESSION['empId'];
$payperiod = $_SESSION['payperiods'];

$query = "SELECT * FROM payperiods WHERE pperiod_range = '$payperiod'";
$result = mysqli_query($conn, $query);

if ($result) {
    // Fetch the data from the result set
    $data = mysqli_fetch_assoc($result);
    $period_start = isset($data['pperiod_start']) ? $data['pperiod_start'] : null;
    $period_end = isset($data['pperiod_end']) ? $data['pperiod_end'] : null;

    $dateTime = new DateTime($period_start);
    $month = $dateTime->format('F'); // Full month name (e.g., January)
    $year = $dateTime->format('Y');  // 4-digit year (e.g., 2024)
}

$printquery = "SELECT * FROM DTR, employees WHERE DTR.emp_id = employees.emp_id and DTR.emp_id = '$empid' AND DTR.DTR_day BETWEEN '$period_start' and '$period_end' ORDER BY DTR_day ASC";
$printqueryexec = mysqli_query($conn,$printquery);
$printarray = mysqli_fetch_array($printqueryexec);
$d = strtotime("now");
        $currtime = date ("Y-m-d H:i:s", $d);
// $payperiod = $_SESSION['payperiodrange'];



if ($printarray){

  $prefix = $printarray['prefix_ID'];
  $idno = $printarray['emp_id'];
  $lname = $printarray['last_name'];
  $fname = $printarray['first_name'];
  $mname = $printarray['middle_name'];
  $dept = $printarray['dept_NAME'];
  $position = $printarray['position'];

  $name = "$lname, $fname $mname";
  $empID = "$prefix$idno";
}


$payperiodval = "SELECT DTR.*,(TIME_KEEPING.hours_work+TIME_KEEPING.overtime_hours) as totalhours,TIME_KEEPING.hours_work,TIME_KEEPING.overtime_hours FROM DTR INNER JOIN TIME_KEEPING ON TIME_KEEPING.emp_id=DTR.emp_id AND TIME_KEEPING.timekeep_day=DTR.DTR_day WHERE DTR.emp_id = '$empid' AND DTR_day BETWEEN '$period_start' AND '$period_end' ORDER BY DTR_day ASC";
$payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

$totalot = "SELECT SUM(undertime_hours) as totalUT, SUM(overtime_hours) as totalOT, SUM(hours_work) as totalWORKhours, SUM(late_hours) as totalLATEhours, SUM((hours_work+overtime_hours)-late_hours) as totalness FROM TIME_KEEPING WHERE emp_id = '$empid' AND timekeep_day BETWEEN '$period_start' and '$period_end' ORDER BY timekeep_day ASC";
$totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
$totalotres = mysqli_fetch_array($totalotexec);



require_once("fpdf181/fpdf.php");

//A4 width: 219mm
//default margin : 10mm each side
//writable horizontal: 219.(10*2)= 189mm

$pdf = new FPDF ('P','mm','LETTER');

$pdf ->AddPage();


// Add watermark
$pdf->SetFont('Arial', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 50, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

// Add watermark
$pdf->SetFont('Arial', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 90, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

// Add watermark
$pdf->SetFont('Arial', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 120, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

if (mysqli_num_rows($printqueryexec) > 0) {
//set font arial, bold, 14pt
$pdf->SetFont('Arial','B',16);

//Spacer
$pdf->Cell(189,10,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])
$pdf->Cell(145,10,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM',0,0);
$pdf->Cell(59,10,'TIMESHEET',0,1);//end

//set font to arial, regular, 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(130,5,'KAWIT CAVITE',0,0);
$pdf->Cell(59,5,'',0,1);//end of line

$pdf->Cell(130,5,'Cavite, Philippines, 4104',0,0);
$pdf->Cell(12,5,'',0,0);
$pdf->Cell(47,5,'',0,1);//end of line

//Spacer
$pdf->Cell(189,5,'',0,1);//end of line

$pdf->SetFont('Arial','',10);
	$pdf->Cell(6,3,'',0,0);//hspacer
	$pdf->Cell(22,1,'Employee ID:',0,0);

	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(90,1,$empID,0,0);

	$pdf->SetFont('Arial','',10);

	$pdf->Cell(22,6,'Date Printed:',0,0);
	$pdf->Cell(20,6,$currtime,0,1);//end of line

	$pdf->Cell(6,3,'',0,0);
	$pdf->Cell(15,0,'Name:',0,0);

	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(97,0,$name,0,0);

	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,'Pay Period:',0,0);

	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(20,3,$payperiod,0,1);//end of line

	$pdf->SetFont('Arial','',10);
	$pdf->Cell(6,3,'',0,0);
	$pdf->Cell(22,5,'Department:',0,0);
  $pdf->SetFont('Arial','B',10);
	$pdf->Cell(45,5,$dept,0,1);//end of line

	$pdf->SetFont('Arial','',10);
	$pdf->Cell(6,3,'',0,0);
	$pdf->Cell(22,5,'Month:',0,0);
  $pdf->SetFont('Arial','B',10);
	$pdf->Cell(45,5,$month,0,1);//end of line

	$pdf->SetFont('Arial','',10);
	$pdf->Cell(6,3,'',0,0);
	$pdf->Cell(22,5,'Year:',0,0);
  $pdf->SetFont('Arial','B',10);
	$pdf->Cell(45,5,$year,0,1);//end of line
//SPACER
$pdf->Cell(189,5,'',0,1);//end of line



$pdf->Cell(20,7,'',1,0,'',true);
$pdf->Cell(38,7,'',1,0,'C');
$pdf->Cell(18,7,'',1,0,'',true);
$pdf->Cell(56,7,'OVERTIME',1,0,'C');
$pdf->Cell(19,7,'',1,1,'',true);

//set font to arial, regular, 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(20,7,'DATE',1,0,'C');
$pdf->Cell(19,7,'IN',1,0,'C');
$pdf->Cell(19,7,'OUT',1,0,'C');
$pdf->SetFont('Arial','',6);
$pdf->Cell(18,7,'Reg. Hours',1,0,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(19,7,'IN',1,0,'C');
$pdf->Cell(19,7,'OUT',1,0,'C');
$pdf->SetFont('Arial','',6);
$pdf->Cell(18,7,'OT Hours',1,0,'C');
$pdf->SetFont('Arial','',6);
$pdf->Cell(19,7,'DAILY TOTAL',1,1,'C');


$pdf->SetFont('Arial','',11);
if ($period_start !== null && $period_end !== null){
while ($payperiodarray = mysqli_fetch_array($payperiodexec)):;
$dtrday = $payperiodarray['DTR_day'];
$day = date('d', strtotime($dtrday));
$hrswrk = $payperiodarray['hours_work'];
$overtimeinout = "SELECT * FROM OVER_TIME WHERE emp_id = '$empid' and ot_day = '$dtrday' and ot_remarks ='Approved'";
$overtimeinoutexec = mysqli_query($conn,$overtimeinout) or die ("FAILED TO EXECUTE OT QUERY ".mysqli_error($conn));
$overtimearray = mysqli_fetch_array($overtimeinoutexec);

if ($overtimearray){
	$otin = $overtimearray['ot_time'];
	$otout = $overtimearray['ot_timeout'];
}else {
	$otin = "";
	$otout ="";
}
$pdf->SetFont('Arial','',11);
$pdf->Cell(20,7,$day,1,0,'C');
$pdf->Cell(19,7,$payperiodarray['in_morning'],1,0,'C');
$pdf->Cell(19,7,$payperiodarray['out_afternoon'],1,0,'C');
$pdf->Cell(18,7,$hrswrk,1,0,'C');
$pdf->Cell(19,7,$otin,1,0,'C');
$pdf->Cell(19,7,$otout,1,0,'C');
$pdf->Cell(18,7,$payperiodarray['overtime_hours'],1,0,'C');
$pdf->Cell(19,7,$payperiodarray['totalhours'],1,1,'C');
endwhile;






//spacer
$pdf->Cell(189,5,'',0,1);

//set font arial, bold, 12pt
$pdf->SetFont('Arial','B',12);

$pdf->Cell(189,5,'TOTAL:',0,1);//end of line
//spacer
$pdf->Cell(189,5,'',0,1);//end of line

//set font arial, regular, 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(37.8,7,'HOURS',1,0,'C');
$pdf->Cell(37.8,7,'OT',1,0,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(37.8,7,'TARDINESS',1,0,'C');
$pdf->Cell(37.8,7,'UT',1,0,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(37.8,7,'TOTAL HOURS',1,1,'C');//end of line


$pdf->Cell(37.8,7,$totalotres['totalWORKhours'],1,0,'C');
$pdf->Cell(37.8,7,$totalotres['totalOT'],1,0,'C');
$pdf->Cell(37.8,7,$totalotres['totalLATEhours'],1,0,'C');
$pdf->Cell(37.8,7,$totalotres['totalUT'],1,0,'C');
$pdf->Cell(37.8,7,$totalotres['totalness'],1,1,'C');//end of line


//set font arial, italic , 12pt
$pdf->SetFont('Arial','I',12);
$pdf->Cell(189,7,'I hereby certify that the above records are true and correct.',0,1,'C');//end of line
//spacer
$pdf->Cell(189,20,'',0,1);//end of line

$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,16,'________________________________',0,1);//end of line

//set font arial, regular, 10
$pdf->SetFont('Arial','',10);
$pdf->Cell(295,5,'Printed by: ' . $empid,0,1,'C');


$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,5,'Employee signature over printed name',0,1,'C');//end of line


}
}

$pdf->Output();
?>