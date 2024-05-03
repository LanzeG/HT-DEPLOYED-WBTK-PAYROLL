<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
date_default_timezone_set('Asia/Hong_Kong'); 
$printid = $_GET['id'];
$printfrom =  $_SESSION['payperiodfrom'];
$printto= $_SESSION['payperiodto'];
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];


$printquery = "SELECT * FROM DTR, employees WHERE DTR.emp_id = employees.emp_id and DTR.emp_id = '$printid' AND DTR.DTR_day BETWEEN '$printfrom' and '$printto' ORDER BY DTR_day ASC";
$printqueryexec = mysqli_query($conn,$printquery);
$printarray = mysqli_fetch_array($printqueryexec);
$d = strtotime("now");
        $currtime = date ("Y-m-d H:i:s", $d);

$dateTime = new DateTime($printfrom);
$month = $dateTime->format('F'); // Full month name (e.g., January)
$year = $dateTime->format('Y');  // 4-digit year (e.g., 2024)

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


$payperiodval = "SELECT DTR.*,(TIME_KEEPING.hours_work) as totalhours, (TIME_KEEPING.undertime_hours) as totalut, TIME_KEEPING.hours_work, TIME_KEEPING.timekeep_remarks FROM DTR INNER JOIN TIME_KEEPING ON TIME_KEEPING.emp_id=DTR.emp_id AND TIME_KEEPING.timekeep_day=DTR.DTR_day WHERE DTR.emp_id = '$printid' AND DTR_day BETWEEN '$printfrom' AND '$printto' ORDER BY DTR_day ASC";
$payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

$totalot = "SELECT SUM(undertime_hours) as totalUT , SUM(hours_work) as totalWORKhours, SUM(hours_work) as totalness, timekeep_remarks FROM TIME_KEEPING WHERE emp_id = '$printid' AND timekeep_day BETWEEN '$printfrom' and '$printto' ORDER BY timekeep_day ASC";
$totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
$totalotres = mysqli_fetch_array($totalotexec);



require_once("fpdf181/fpdf.php");

$pdf = new FPDF ('P','mm','LETTER');

$pdf ->AddPage();

// Add watermark
$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 50, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

// Add watermark
$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 90, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

// Add watermark
$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 120, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

if (mysqli_num_rows($printqueryexec) > 0) {
//set font times, bold, 14pt
$pdf->SetFont('times','B',22);

//Spacer
$pdf->Cell(189,10,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])

$pdf->Cell(70,0,'DAILY TIME RECORD',0,1);//end

//set font to times, regular, 12pt
$pdf->SetFont('times','',12);


$pdf->Cell(12,5,'',0,0);
$pdf->Cell(47,5,'',0,1);//end of line

//Spacer
$pdf->Cell(189,5,'',0,1);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);//hspacer
$pdf->Cell(22,1,'Employee ID:',0,0);

$pdf->SetFont('times','B',10);
$pdf->Cell(90,1,$empID,0,0);

$pdf->SetFont('times','',10);

$pdf->Cell(22,6,'Date Printed:',0,0);
$pdf->Cell(20,6,$currtime,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(15,0,'Name:',0,0);

$pdf->SetFont('times','B',10);
$pdf->Cell(97,0,$name,0,0);

$pdf->SetFont('times','',10);
$pdf->Cell(20,3,'Pay Period:',0,0);

$pdf->SetFont('times','B',10);
$pdf->Cell(20,3,$printfrom . 'to' . $printto,0,1);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(22,5,'Department:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(45,5,$dept,0,1);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(22,5,'Month:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(45,5,$month,0,1);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(22,5,'Year:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(45,5,$year,0,1);//end of line
//SPACER
$pdf->Cell(189,5,'',0,1);//end of line


$pdf->Cell(40,7,'',1,0,'',true);
$pdf->Cell(38,7,'',1,0,'C');
$pdf->Cell(40,7,'',1,0,'',true);
$pdf->Cell(38,7,'',1,0,'C');
$pdf->Cell(23,7,'',1,0,'',true);
// $pdf->Cell(56,7,'OVERTIME',1,0,'C');
$pdf->Cell(19,7,'',1,1,'',true);

//set font to times, regular, 12pt
$pdf->SetFont('times','',12);

$pdf->Cell(40,7,'DATE',1,0,'C');
$pdf->Cell(19,7,'IN',1,0,'C');
$pdf->Cell(19,7,'OUT',1,0,'C');
$pdf->SetFont('times','',10);
$pdf->Cell(40,7,'Hours Worked',1,0,'C');

$pdf->SetFont('times','',10);
$pdf->Cell(38,7,'Undertime (Minutes)',1,0,'C');
$pdf->SetFont('times','',10);
$pdf->Cell(42,7,'Remarks',1,1,'C');

$pdf->SetFont('times','',11);
if ($printfrom !== null && $printto !== null){
while ($payperiodarray = mysqli_fetch_array($payperiodexec)):;
$dtrday = $payperiodarray['DTR_day'];
$day = date('d', strtotime($dtrday));
$hrswrk = $payperiodarray['hours_work'];
$undertime = $payperiodarray['undertimehours'];
$remarks = $payperiodarray['timekeep_remarks'];

$pdf->SetFont('times','',11);
$pdf->Cell(40,7,$day,1,0,'C');
$pdf->Cell(19,7,$payperiodarray['in_morning'],1,0,'C');
$pdf->Cell(19,7,$payperiodarray['out_afternoon'],1,0,'C');
$pdf->Cell(40,7,$hrswrk,1,0,'C');
$pdf->Cell(38,7,$undertime,1,0,'C');
$pdf->Cell(42,7,$remarks,1,1,'C');
endwhile;

//spacer
$pdf->Cell(189,5,'',0,1);

//set font times, bold, 12pt
$pdf->SetFont('times','B',12);

$pdf->Cell(189,5,'TOTAL:',0,1);//end of line
//spacer
$pdf->Cell(189,5,'',0,1);//end of line

//set font times, regular, 12pt
$pdf->SetFont('times','',10);

$pdf->Cell(39.8,7,'HOURS',1,0,'C');
$pdf->Cell(37.8,7,'TOTAL UNDERTIME',1,0,'C');
$pdf->SetFont('times','',10);
$pdf->Cell(40.8,7,'',1,0,'C');
$pdf->Cell(37.8,7,'',1,0,'C');
$pdf->SetFont('times','',12);
$pdf->Cell(41.8,7,'',1,1,'C');//end of line


$pdf->Cell(39.8,7,$totalotres['totalWORKhours'],1,0,'C');
$pdf->Cell(37.8,7,$totalotres['totalUT'],1,0,'C');
$pdf->Cell(40.8,7,'',1,1,'C');//end of line

//set font times, italic , 12pt
$pdf->SetFont('times','I',12);
$pdf->Cell(189,7,'I hereby certify that the above records are true and correct.',0,1,'C');//end of line
//spacer
$pdf->Cell(189,20,'',0,1);//end of line

$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,16,'________________________________',0,1);//end of line

//set font times, regular, 10
$pdf->SetFont('times','',10);
$pdf->Cell(295,5,'Printed by: ' . $adminFullName,0,1,'C');


$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,5,'Employee signature over printed name',0,1,'C');//end of line

}
}

$pdf->Output();
?>