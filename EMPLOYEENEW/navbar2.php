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

$printquery = "SELECT * FROM dtr, employees WHERE dtr.emp_id = employees.emp_id and dtr.emp_id = '$empid' AND dtr.dtr_day BETWEEN '$period_start' and '$period_end' ORDER BY dtr_day ASC";
$printqueryexec = mysqli_query($conn,$printquery);
$printarray = mysqli_fetch_array($printqueryexec);
$d = strtotime("now");
        $currtime = date ("Y-m-d H:i:s", $d);
// $payperiod = $_SESSION['payperiodrange'];
$dateTime = new DateTime($period_start);
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


$payperiodval = "
SELECT 
    tk.*,
     tk.undertime_hours AS totalut,
    SUM(ol.overload_hours) AS total_overload_hours
FROM 
    time_keeping tk
LEFT JOIN 
    overload ol ON tk.timekeep_id = ol.timekeep_id
WHERE 
    tk.emp_id = '$empid' 
    AND tk.timekeep_day BETWEEN '$period_start' AND '$period_end'
GROUP BY 
    tk.emp_id, 
    tk.timekeep_day, 
    tk.in_morning;
";

$payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

$totalot = "SELECT time_keeping.*, SUM(undertime_hours) as totalUT, SUM(hours_work) as totalWORKhours, SUM((hours_work)) as totalness FROM time_keeping WHERE emp_id = '$empid' AND timekeep_day BETWEEN '$period_start' and '$period_end' ORDER BY timekeep_day ASC";
$totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
$totalotres = mysqli_fetch_array($totalotexec);



require_once("fpdf181/fpdf.php");

//A4 width: 219mm
//default margin : 10mm each side
//writable horizontal: 219.(10*2)= 189mm

$pdf = new FPDF ('P','mm','LEGAL');

$pdf ->AddPage();
// $pdf->Image('../img/images.png',100,6,15); // Adjust the image path and position as needed


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
$pdf->SetFont('times','B',12);

//Spacer


//Cell (width,height,text,border,end line, [align])
// $pdf->Cell(40,10,'',0,1);
// $pdf->Cell(40,10,'',0,0);
// // $pdf->Cell(116,10,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM',0,0);
// $pdf->Cell(189,10,'',0,1);//end of line
$pdf->SetFont('times','B',12);
$pdf->Cell(70,10,'',0,0);//end
$pdf->Cell(59,10,'DAILY TIME RECORD',0,1);//end

//set font to times, regular, 12pt
$pdf->SetFont('times','',12);



$pdf->Cell(12,5,'',0,0);


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
$pdf->Cell(20,3,$period_start. 'to' . $period_end,0,1);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(22,5,'Department:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(84,5,$dept,0,0);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(12,5,'Month:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(5,5,$month,0,0);//end of line

$pdf->SetFont('times','',10);
$pdf->Cell(13,3,'',0,0);
$pdf->Cell(9,5,'Year:',0,0);
$pdf->SetFont('times','B',10);
$pdf->Cell(45,5,$year,0,1);//end of line
//SPACER
$pdf->Cell(189,5,'',0,1);//end of line



//set font to times, regular, 12pt
$pdf->SetFont('times','',12);

$pdf->Cell(30,7,'DATE',1,0,'C');
$pdf->Cell(19,7,'IN',1,0,'C');
$pdf->Cell(19,7,'OUT',1,0,'C');
$pdf->Cell(30,7,'Hours Worked',1,0,'C');
$pdf->Cell(40,7,'Undertime (Minutes)',1,0,'C');
$pdf->Cell(30,7,'Overload Hours',1,0,'C');  // New column for overload hours
$pdf->Cell(30,7,'Remarks',1,1,'C');
$pdf->SetFillColor(51, 255, 175); 
$pdf->Cell(30,1,'',1,0,'',true);
$pdf->Cell(19,1,'',1,0,'',true);
$pdf->Cell(19,1,'',1,0,'',true);
$pdf->Cell(30,1,'',1,0,'',true);
$pdf->Cell(40,1,'',1,0,'',true);
$pdf->Cell(30,1,'',1,0,'',true);  // New column for overload hours
$pdf->Cell(30,1,'',1,1,'',true);

$pdf->SetFont('times','',11);
if ($period_start !== null && $period_end !== null){
while ($payperiodarray = mysqli_fetch_array($payperiodexec)):;
$dtrday = $payperiodarray['timekeep_day'];
$day = date('d', strtotime($dtrday));
$hrswrk = $payperiodarray['hours_work'];
$undertime = $payperiodarray['totalut'];
$overload = $payperiodarray['total_overload_hours']; // Fetch overload hours
$remarks = $payperiodarray['timekeep_remarks'];

$pdf->SetFont('times','',11);
$pdf->Cell(30,7,$day,1,0,'C');
$pdf->Cell(19,7,$payperiodarray['in_morning'],1,0,'C');
$pdf->Cell(19,7,$payperiodarray['out_afternoon'],1,0,'C');
$pdf->Cell(30,7,$hrswrk,1,0,'C');
$pdf->Cell(40,7,$undertime,1,0,'C');
$pdf->Cell(30,7,$overload,1,0,'C');  // Add overload hours to the table
$pdf->Cell(30,7,$remarks,1,1,'C');
endwhile;
//spacer
$pdf->Cell(189,1,'',0,1);

//set font times, bold, 12pt
$pdf->SetFont('times','B',12);

$pdf->Cell(189,5,'TOTAL:',0,1);//end of line
//spacer
$pdf->Cell(189,1,'',0,1);//end of line

//set font times, regular, 12pt
$pdf->SetFont('times','',10);

$pdf->Cell(65.8,7,'HOURS',1,0,'C');
$pdf->Cell(66.8,7,'TOTAL UNDERTIME',1,0,'C');
$pdf->Cell(65.8,7,'TOTAL OVERLOAD',1,1,'C');
$pdf->SetFont('times','',10);
// $pdf->Cell(25.8,7,'',1,0,'C');
// $pdf->Cell(17.8,7,'',1,0,'C');
$pdf->SetFont('times','',12);
// $pdf->Cell(41.8,7,'',1,1,'C');//end of line

$totalOverload = 0; // Initialize total overload

// Fetch and calculate total overload hours
$payperiodexec = mysqli_query($conn, $payperiodval);
while ($payperiodarray = mysqli_fetch_array($payperiodexec)) {
    $totalOverload += $payperiodarray['total_overload_hours'];
}

$pdf->Cell(65.8,7,$totalotres['totalWORKhours'],1,0,'C');
$pdf->Cell(66.8,7,$totalotres['totalUT'],1,0,'C');
$pdf->Cell(65.8,7,$totalOverload,1,1,'C'); // Total overload hours

//set font times, italic , 12pt
$pdf->SetFont('times','I',12);
$pdf->Cell(189,10   ,'',0,1);//end of line
$pdf->Cell(100,5,'I hereby certify that the above records are true and correct.',0,0,'C');//end of line
//spacer
$pdf->Cell(189,2   ,'',0,1);//end of line

$pdf->Cell(110,5,'',0,0);
$pdf->Cell(85,9,'________________________________',0,1);//end of line

//set font times, regular, 10
$pdf->SetFont('times','',10);
$pdf->Cell(295,5,'Printed by: ' . $name,0,1,'C');


$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,5,'Employee signature over printed name',0,1,'C');//end of line

}
}

$pdf->Output();
?>
