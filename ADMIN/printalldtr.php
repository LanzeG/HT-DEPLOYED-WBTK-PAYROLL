<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();
date_default_timezone_set('Asia/Hong_Kong');

$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];


$d = strtotime("now");
$currtime = date ("Y-m-d H:i:s", $d);

$printfrom =  $_SESSION['payperiodfrom'];
$printto= $_SESSION['payperiodto'] ;
$payperiodrange = $_SESSION['payperiodrange'];

$dateTime = new DateTime($printfrom);
$month = $dateTime->format('F'); // Full month name (e.g., January)
$year = $dateTime->format('Y'); 

$daycount= 15;

if (isset($_GET['printall'])) {
	$storedQuery = "SELECT *
                FROM employees
                WHERE emp_id IN (SELECT emp_id FROM time_keeping 
                WHERE timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}')";

} elseif (isset($_GET['printdisplayed'])) {
	$storedQuery = $_SESSION['printtimesheet_query'];
}

$employeeResult = filterTable($storedQuery);

require_once("fpdf181/fpdf.php");
// Create a new PDF instance
$pdf = new FPDF('P', 'mm', 'LEGAL');

// Iterate over each employee
while ($employee = mysqli_fetch_assoc($employeeResult)) {
  // Fetch and populate data specific to each employee
  $printid = $employee['emp_id'];
  $printfrom =  $_SESSION['payperiodfrom'];
  $printto= $_SESSION['payperiodto'];

    // Fetch data for the employee
  $printquery = "SELECT * FROM time_keeping, employees 
               WHERE time_keeping.emp_id = employees.emp_id 
               AND time_keeping.emp_id = '$printid' 
               AND time_keeping.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' 
               ORDER BY timekeep_day ASC";
  $printqueryexec = mysqli_query($conn,$printquery);
  $printarray = mysqli_fetch_array($printqueryexec);
  $d = strtotime("now");
  $currtime = date ("Y-m-d H:i:s", $d);

if ($printarray){

  $prefix = $printarray['prefix_ID'];
  $idno = $printarray['emp_id'];
  $lname = $printarray['last_name'];
  $fname = $printarray['first_name'];
  $mname = $printarray['middle_name'];
  $dept = $printarray['dept_NAME'];
  $position = $printarray['position'];

  $name = "$lname, $fname $mname";$totalot = "SELECT SUM(undertime_hours) as totalUT ,SUM(overtime_hours) as totalOT, SUM(hours_work) as totalWORKhours, SUM(late_hours) as totalLATEhours, SUM(hours_work+overtime_hours) as totalness FROM time_keeping WHERE emp_id = '$printid' AND timekeep_day  AND time_keeping.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' ORDER BY timekeep_day ASC";

  $empID = "$prefix$idno";
}

$payperiodval = "
SELECT 
    tk.*,
     tk.undertime_hours AS totalut,
    SUM(ol.overload_hours) AS overload_hours
FROM 
    time_keeping tk
LEFT JOIN 
    overload ol ON tk.timekeep_id = ol.timekeep_id
WHERE 
    tk.emp_id = '$printid' 
    AND tk.timekeep_day BETWEEN '$printfrom' AND '$printto'
GROUP BY 
    tk.emp_id, 
    tk.timekeep_day, 
    tk.in_morning;
";
$payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

$totalot = "SELECT SUM(undertime_hours) as totalUT , SUM(hours_work) as totalWORKhours, SUM(hours_work) as totalness, timekeep_remarks FROM time_keeping WHERE emp_id = '$printid' AND timekeep_day BETWEEN '$printfrom' and '$printto' ORDER BY timekeep_day ASC";
$totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
$totalotres = mysqli_fetch_array($totalotexec);

$absencesqry = "SELECT ('$daycount'-COUNT(timekeep_day)) as absences FROM time_keeping WHERE emp_id = '$printid' AND timekeep_day AND time_keeping.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' ORDER BY timekeep_day ASC";
$absencesqryexec = mysqli_query($conn,$absencesqry);
$absencesres = mysqli_fetch_array($absencesqryexec);

require_once("fpdf181/fpdf.php");

$pdf ->AddPage();

$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 50, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

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
  $pdf->Cell(70,10,'',0,0);//end of line
  
  //Cell (width,height,text,border,end line, [align])

  $pdf->Cell(59,10,'DAILY TIME RECORD',0,1);//end
  
  //set font to times, regular, 12pt
  $pdf->SetFont('times','',12);
  
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
  $pdf->Cell(20, 3, $printfrom . ' to ' . $printto, 0, 1); // end of line
  
  $pdf->SetFont('times','',10);
  $pdf->Cell(6,3,'',0,0);
  $pdf->Cell(22,5,'Department:',0,0);
  $pdf->SetFont('times','B',10);
  $pdf->Cell(84,5,$dept,0,0);//end of line
  
  $pdf->SetFont('times','',10);
  $pdf->Cell(6,3,'',0,0);
  $pdf->Cell(12,5,'Month:',0,0);
  $pdf->SetFont('times','B',10);
  $pdf->Cell(10,5,$month,0,0);//end of line
  
  $pdf->SetFont('times','',10);
  $pdf->Cell(6,3,'',0,0);
  $pdf->Cell(10,5,'Year:',0,0);
  $pdf->SetFont('times','B',10);
  $pdf->Cell(45,5,$year,0,1);//end of line
  //SPACER
  $pdf->Cell(189,5,'',0,1);//end of line
  
  
  //set font to times, regular, 12pt
  $pdf->SetFont('times','',12);
  
// Update the header to include overload hours
$pdf->Cell(28,7,'DATE',1,0,'C');
$pdf->Cell(19,7,'IN',1,0,'C');
$pdf->Cell(19,7,'OUT',1,0,'C');
$pdf->Cell(30,7,'Hours Worked',1,0,'C');
$pdf->Cell(40,7,'Undertime (Minutes)',1,0,'C');
$pdf->Cell(32,7,'Overload Hours',1,0,'C');  // New column for overload hours
$pdf->Cell(30,7,'Remarks',1,1,'C');
$pdf->SetFillColor(51, 255, 175); 
$pdf->Cell(28,1,'',1,0,'',true);
$pdf->Cell(19,1,'',1,0,'',true);
$pdf->Cell(19,1,'',1,0,'',true);
$pdf->Cell(30,1,'',1,0,'',true);
$pdf->Cell(40,1,'',1,0,'',true);
$pdf->Cell(32,1,'',1,0,'',true);  // New column for overload hours
$pdf->Cell(30,1,'',1,1,'',true);
  
  $pdf->SetFont('times','',11);
  if ($printfrom !== null && $printto !== null){
  while ($payperiodarray = mysqli_fetch_array($payperiodexec)):;
  $dtrday = $payperiodarray['timekeep_day'];
  $day = date('d', strtotime($dtrday));
  $hrswrk = $payperiodarray['hours_work'];
  $undertime = $payperiodarray['totalut'];
  $overload = $payperiodarray['overload_hours']; // Fetch overload hours
  $remarks = $payperiodarray['timekeep_remarks'];
  
  $pdf->SetFont('times','',11);
  $pdf->Cell(28,7,$day,1,0,'C');
  $pdf->Cell(19,7,$payperiodarray['in_morning'],1,0,'C');
  $pdf->Cell(19,7,$payperiodarray['out_afternoon'],1,0,'C');
  $pdf->Cell(30,7,$hrswrk,1,0,'C');
  $pdf->Cell(40,7,$undertime,1,0,'C');
  $pdf->Cell(32,7,$overload,1,0,'C');  
  $pdf->SetFont('times','',9);
  $pdf->Cell(30,7,$remarks,1,1,'C');
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
    $totalOverload += $payperiodarray['overload_hours'];
 }

 $pdf->Cell(65.8,7,$totalotres['totalWORKhours'],1,0,'C');
 $pdf->Cell(66.8,7,$totalotres['totalUT'],1,0,'C');
 $pdf->Cell(65.8,7,$totalOverload,1,1,'C'); // Total overload hours

  //set font times, italic , 12pt
 $pdf->SetFont('times','I',12);
$pdf->Cell(100,10,'I hereby certify that the above records are true and correct.',0,0,'C');//end of line
//spacer
$pdf->Cell(189,10   ,'',0,1);//end of line

$pdf->Cell(120,2,'',0,0);
$pdf->Cell(120,7,'________________________________',0,1);//end of line

//set font times, regular, 10
$pdf->SetFont('times','',10);
$pdf->Cell(38,2,'Printed by: ' . $adminFullName,0,0,'C');


$pdf->Cell(75,1,'',0,0);
$pdf->Cell(79,5,'Employee signature over printed name',0,1,'C');//end of line

  }
}
}
$pdf->Output();

function filterTable($searchquery){
    $conn1 = mysqli_connect("localhost", "u387373332_masterdb", "WBTKpayrollportal1234@", "u387373332_masterdb");
    $filter_Result = mysqli_query($conn1, $searchquery) or die("failed to query employees " . mysqli_error($conn1));
    return $filter_Result;
}
?>
