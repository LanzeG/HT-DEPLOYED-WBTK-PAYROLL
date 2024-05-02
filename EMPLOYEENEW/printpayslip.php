<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
require_once("fpdf181/fpdf.php");
session_start();
date_default_timezone_set('Asia/Hong_Kong'); 

if (isset($_GET['id'])) {
    $printid = $_GET['id'];
}
$empid = $_SESSION['empId'];

$payperiod = $_SESSION['payperiods'];
list($startDate, $endDate) = explode(' to ', $payperiod);

// Convert start date to month name and day number
$startMonth = date('F', strtotime($startDate));
$startDay = date('j', strtotime($startDate));

// Convert end date to day number
$endDay = date('j', strtotime($endDate));

// Get the year from the end date
$year = date('Y', strtotime($endDate));

// Construct the formatted date range string
$formattedDateRange = "$startMonth $startDay-$endDay, $year";

$query = "SELECT * FROM payperiods WHERE pperiod_range = '$payperiod'";
$result = mysqli_query($conn, $query);

if ($result) {
    // Fetch the data from the result set
    $data = mysqli_fetch_assoc($result);
    $period_start = isset($data['pperiod_start']) ? $data['pperiod_start'] : null;
    $period_end = isset($data['pperiod_end']) ? $data['pperiod_end'] : null;
}

$d = strtotime("now");
$currtime = date("Y-m-d H:i:s", $d);

$payslipdetailsqry = "SELECT * FROM employees 
                    JOIN PAY_PER_PERIOD ON PAY_PER_PERIOD.emp_id = employees.emp_id 
                    JOIN payrollinfo ON payrollinfo.emp_id = employees.emp_id 
                    WHERE PAY_PER_PERIOD.pperiod_range = '$payperiod' 
                    AND employees.emp_id = '$empid'
                    ORDER BY PAY_PER_PERIOD.emp_id ASC";
$payslipdetailsexecqry = mysqli_query($conn,$payslipdetailsqry) or die ("FAILED TO GET PAYROLL DETAILS ".mysqli_error($conn));
/** PDF START **/
$pdf = new FPDF ('P','mm','LETTER');
$pdf ->AddPage();

// Add watermark
$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 50, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color


if ($period_start !== null && $period_end !== null) {
    while ($psdarray = mysqli_fetch_array($payslipdetailsexecqry)):;

        $prefix = $psdarray['prefix_ID'];
        $idno = $psdarray['emp_id'];
        $lname = $psdarray['last_name'];
        $fname = $psdarray['first_name'];
        $mname = $psdarray['middle_name'];
        $dept = $psdarray['dept_NAME'];
        $rph = $psdarray['rate_per_hour'];
        $sg = $psdarray['salarygrade'];
        $step = $psdarray['step'];
        $position = $psdarray['position'];
        $dph = ($rph * 8);
        $name = "$lname, $fname $mname";
        $empID = "$prefix$idno";



		$sql = "SELECT loans.*, loantype.*
        FROM loans
        JOIN loantype ON loans.loantype = loantype.loantype
        WHERE loans.emp_id = $idno
        AND loans.start_date <= '$period_start'
        AND loans.end_date >= '$period_end'";

$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}

// Fetch data and assign it to a variable
$loanData = [];
while ($row = $result->fetch_assoc()) {
    $loanData[] = $row;
}
// $conn->close();

// Now $loanData contains the fetched data for the specified employee ID within the date range

$salaryLoanAmount = 0;
$policyLoanAmount = 0;
$elaLoanAmount = 0;
$optionalInsuranceAmount = 0;
$optionalAmount = 0;
$gfalAmount = 0;
$hipAmount = 0;
$landbankAmount = 0;
$cplAmount = 0;
$sosAmount = 0;
$educAmount = 0;
$eCardAmount = 0;
$contributionAmount = 0;
$mplAmount = 0;
$realStateAmount = 0;
$emergencyAmount = 0;
$MP2Amount = 0;
$IntegratedAmount = 0;

// Iterate through each loan record and assign loan amounts based on loan type
foreach ($loanData as $loanRecord) {
    // Access individual fields in each loan record
    $loanAmount = $loanRecord['monthly_deduct'];
    $loanType = $loanRecord['loantype'];

    // Assign loan amount based on loan type using multiple if statements
    if ($loanType == 'Salary Loan') {
        $salaryLoanAmount += $loanAmount;
    } elseif ($loanType == 'Policy Loan') {
        $policyLoanAmount += $loanAmount;
    } elseif ($loanType == 'ELA') {
        $elaLoanAmount += $loanAmount;
    } elseif ($loanType == 'Optional Insurance') {
        $optionalInsuranceAmount += $loanAmount;
    } elseif ($loanType == 'GFAL') {
        $gfalAmount += $loanAmount;
    } elseif ($loanType == 'HIP') {
        $hipAmount += $loanAmount;
    } elseif ($loanType == 'Landbank') {
        $landbankAmount += $loanAmount;
    } elseif ($loanType == 'CPL') {
        $cplAmount += $loanAmount;
    } elseif ($loanType == 'SOS') {
        $sosAmount += $loanAmount;
    } elseif ($loanType == 'Educ') {
        $educAmount += $loanAmount;
    } elseif ($loanType == 'E Card') {
        $eCardAmount += $loanAmount;
    } elseif ($loanType == 'Contribution') {
        $contributionAmount += $loanAmount;
    } elseif ($loanType == 'MPL') {
        $mplAmount += $loanAmount;
    } elseif ($loanType == 'Real State') {
        $realStateAmount += $loanAmount;
    } elseif ($loanType == 'Emergency') {
        $emergencyAmount += $loanAmount;
    }
     elseif ($loanType == 'MP2') {
        $MP2Amount += $loanAmount;
    }
     elseif ($loanType == 'Integrated Insurance') {
        $IntegratedAmount += $loanAmount;
    }
     elseif ($loanType == 'Optional Loan') {
        $optionalAmount += $loanAmount;
    }
    // ... add more if statements for other loan types
}


$targetMonthDay = '12-15';

$thirteenthmonth = "SELECT * FROM 13thmonth WHERE emp_id = '$idno' AND 13th_year = YEAR('$period_end')";
$thirteenthmonthexecqry = mysqli_query($conn,$thirteenthmonth) or die ("FAILED TO GET PAYROLL INFO");
$thirteentharray = mysqli_fetch_array($thirteenthmonthexecqry);
if($thirteentharray){

	if (date('m-d', strtotime($period_start)) <= $targetMonthDay && date('m-d', strtotime($period_end)) >= $targetMonthDay) {
		$thirteenth = $thirteentharray['13th_amount'];
	} else {
		$thirteenth = 0.0;
	}
	
}else{
	$thirteenth = 0.0;
}


	$payinfoqry = "SELECT * FROM PAYROLLINFO WHERE emp_id = '$idno'";
	$payinfoexecqry = mysqli_query($conn,$payinfoqry) or die ("FAILED TO GET PAYROLL INFO");
	$piarray = mysqli_fetch_array($payinfoexecqry);
	if($piarray){

		$monthlyrate = $piarray['base_pay'];
		$semimonthlyrate = ($monthlyrate / 2);
		$smrate = number_format((float)$semimonthlyrate,2,'.','');
		
	} else {

		$monthlyrate = 0;
		$semimonthlyrate = 0;
		$smrate = 0.00;
	}

$payinfo1qry = "SELECT * FROM pay_per_period WHERE emp_id = '$idno' AND PAY_PER_PERIOD.pperiod_range = '$payperiod'";
$payinfo1execqry = mysqli_query($conn,$payinfo1qry) or die ("FAILED TO GET PAYROLL INFO");
$piarray1 = mysqli_fetch_array($payinfo1execqry);
if($piarray1){

	$wtax = $piarray1['tax_deduct'];
	$gsis = $piarray1['sss_deduct'];
	$absences = $piarray1['absences'];
	$pagibig = $piarray1['pagibig_deduct'];
	$undertime = $piarray1['undertimehours'];
	$phEE = $piarray1['philhealth_deduct'];
	$totaldeduct = $piarray1['total_deduct'];
	$netpay = $piarray1['net_pay'];
    $first = $piarray1['firsthalf'];
	$second = $piarray1['secondhalf'];
	$refsalary = $piarray1['refsalary'];
	$disallowance = $piarray1['disallowance'];


} else {

	$monthlyrate = 0;
	$semimonthlyrate = 0;
	$smrate = 0.00;
}


$pdf->SetFont('times', 'B', 30);
$pdf->SetTextColor(220, 220, 220); // Set a light gray color
$pdf->Text(40, 50, 'COMPUTER-GENERATED'); // Set the text and position
$pdf->SetTextColor(0); // Reset text color

$pdf->SetFont('times','B',9);

//Spacer
//$pdf->Cell(189,5,'',0,1);//end of line

$pdf->Cell(30,3,'',0,0);
$pdf->Cell(130,3,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1);// end of line

$pdf->SetFont('times','B',6);


$pdf->SetFont('times','B',7);
$pdf->Cell(75,6,'',0,0);
$pdf->Cell(18,6,'For the period:',0,0);//end of line
$pdf->Cell(59,6,$formattedDateRange,0,1);//end of line

$pdf->SetFont('times','',7);
$pdf->Cell(6,3,'',0,0);//hspacer
$pdf->Cell(16,3,'Employee ID:',0,0);

$pdf->SetFont('times','B',7);
$pdf->Cell(125,3,$empID,0,0);

$pdf->SetFont('times','',7);

$pdf->Cell(16,3,'Date Printed:',0,0);
$pdf->Cell(20,3,$currtime,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(9,3,'Name:',0,0);

$pdf->SetFont('times','B',7);
$pdf->Cell(132,3,$name,0,0);

$pdf->SetFont('times','',7);
$pdf->Cell(13,3,'Pay Period:',0,0);

$pdf->SetFont('times','B',7);
$pdf->Cell(20,3,$payperiod,0,1);//end of line


$pdf->SetFont('times','',7);
$pdf->Cell(6,3,'',0,0);

$pdf->Cell(14,3,'Department:',0,0);
$pdf->Cell(10,3,$dept,0,0);//end of line

$pdf->Cell(117,3,'',0,0.);
$pdf->Cell(5,3,'SG:',0,0);
$pdf->Cell(20,3,$sg,0,1);//end of line
$pdf->Cell(6,3,'',0,0.);
$pdf->Cell(10,3,'Position:',0,0);
$pdf->Cell(20,3,$position,0,0);//end of line
$pdf->Cell(111,3,'',0,0.);
$pdf->Cell(6,3,'Step:',0,0);
$pdf->Cell(20,3,$step,0,1);//end of line





$pdf->Cell(189,5,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(15,3,'Basic Salary:',0,0);
$pdf->Cell(130,3,'------------------------------------------------------------------------------------------------------------------------------------------------------------',0,0);
$pdf->Cell(15,3,$monthlyrate,0,1);

$pdf->Cell(6,3,'',0,0); // Adjust width as needed
$pdf->Cell(15,3,'PERA/Adcom:  ',0,0); // Adjust label as needed
$pdf->Cell(2,3,'',0,0); // Add two cells with empty content for spacing
$pdf->Cell(128,3,'----------------------------------------------------------------------------------------------------------------------------------------------------------',0,0); // Adjust separator as needed
$pdf->Cell(15,3,$refsalary,0,1); // Adjust content as needed

$pdf->Cell(6,3,'',0,0); // Adjust width as needed
$pdf->Cell(15,3,'Gross Amount Due:  ',0,0); // Adjust label as needed
$pdf->Cell(7,3,'',0,0); // Add three cells with empty content for spacing
$pdf->Cell(123,3,'----------------------------------------------------------------------------------------------------------------------------------------------------',0,0); // Adjusted separator width
$pdf->Cell(15,3,$monthlyrate+$refsalary,0,1); // Adjust content as needed

$pdf->SetFont('times','',7);
$pdf->Cell(189,5,'',0,1);//end of line


//$pdf->Cell(189,3,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Ref-Salary',0,0);
$pdf->Cell(13,3,$absences + $undertime,0,0);
$pdf->Cell(26,3,'',0,0);

$pdf->Cell(25, 3, 'GSIS - Optional Loan', 0, 0);
$pdf->Cell(30, 3, $optionalAmount, 0, 0); // Note: use single quotes to display the variable name
$pdf->Cell(20, 3, 'Fin. Assistance', 0, 0);
$pdf->Cell(15, 3, '0.00', 0, 1);

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Ref-PERA/ACA',0,0);
$pdf->Cell(13,3,'0.00',0,0);
$pdf->Cell(26,3,'',0,0);

$pdf->Cell(25,3,'GSIS - GFAL',0,0);
$pdf->Cell(30,3,$sosAmount,0,0);//end of line
$pdf->Cell(20,3,'Landbank',0,0);
$pdf->Cell(15,3,$landbankAmount,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Disallowance',0,0);
$pdf->Cell(13,3,$disallowance,0,0);
$pdf->Cell(26,3,'',0,0);
$pdf->Cell(25,3,'GSIS - HIP',0,0);
$pdf->Cell(30,3,$hipAmount,0,0);
$pdf->Cell(20,3,'HDMF- MP2',0,0);
$pdf->Cell(6,3,$MP2Amount,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Withholding Tax',0,0);
$pdf->Cell(18,3,$wtax,0,0);
$pdf->Cell(21,3,'',0,0);

$pdf->Cell(9,3,'GSIS - CPL',0,0);
$pdf->Cell(16,3,'',0,0);
$pdf->Cell(36,3,$cplAmount,0,0);
$pdf->Cell(15,3,'',0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Philhealth',0,0);

$pdf->Cell(13,3,$phEE,0,0);
$pdf->Cell(26,3,'',0,0);

$pdf->Cell(25,3,'GSIS - SOS',0,0);
$pdf->Cell(15,3,$eCardAmount,0,1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'GSIS-Integrated Insurance',0,0);

$pdf->Cell(13,3,$gsis,0,0);
$pdf->Cell(26,3,'',0,0);

$pdf->Cell(25,3,'GSIS - Educ',0,0);
$pdf->Cell(15,3,$educAmount,0,1);//end of line

$pdf->Cell(6, 3, '', 0, 0);
$pdf->Cell(32, 3, 'GSIS - MPL:', 0, 0); // Empty space to align with the previous column

$pdf->Cell(13, 3, $mplAmount, 0, 0);
$pdf->Cell(26, 3, '', 0, 0);

$pdf->Cell(25, 3, 'GSIS - E Card', 0, 0);
$pdf->MultiCell(15, 3, $eCardAmount, 0, 1);//end of line

$pdf->Cell(6,3,'',0,0);
$pdf->Cell(23,3,'GSIS - Salary Loan',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(33,3,$salaryLoanAmount,0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(4,3,'',0,0);
$pdf->Cell(25, 3, 'HDMF Contribution', 0, 0);
$pdf->MultiCell(15, 3, $pagibig, 0, 0);//end of line
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(23,3,'GSIS - Policy Loan:',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(33,3,$policyLoanAmount,0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(4,3,'',0,0);
$pdf->Cell(25, 3, 'HDMF - MPL', 0, 0);
$pdf->MultiCell(15, 3, $mplAmount, 0, 0);//end of line
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(23,3,'GSIS - ELA:',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(33   ,3,$elaLoanAmount,0,0);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(16,3,'HDMF - Real Estate',0,0);
$pdf->Cell(9,3,'',0,0);
$pdf->Cell(30,3,$realStateAmount,0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(4,3,'',0,0);
$pdf->Cell(25, 3, '', 0, 0);
$pdf->MultiCell(15, 3, '', 0, 0);//end of line

$pdf->Cell(1,3,'',0,0);
$pdf->Cell(1,3,'',0,0);
$pdf->Cell(4,3,'',0,0);
$pdf->Cell(32, 3, 'GSIS - Optional Insurance', 0, 0);
$pdf->Cell(39, 3, $optionalInsuranceAmount, 0, 0);
$pdf->Cell(25, 3, 'HDMF Emergency', 0, 0);
$pdf->Cell(32, 3, $optionalInsuranceAmount, 0, 0);
// $pdf->MultiCell(15, 3, $optionalInsuranceAmount, 0, 0);//end of line
// $pdf->Cell(6,3,'',0,1);
// $pdf->Cell(6,3,'',0,0);
// $pdf->Cell(23,3,'Leave without Pay:',0,0);
// $pdf->Cell(9,3,'',0,0);
// $pdf->Cell(33,3,$absences,0,0);
// $pdf->Cell(6,3,'',0,0);
// $pdf->Cell(16,3,'Undertime:',0,0);
// $pdf->Cell(9,3,'',0,0);
// $pdf->Cell(30,3,$undertime,0,1);



$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line

$pdf->Cell(189,2,'',0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(152,0.3,'',1,1);//end of line

$pdf->Cell(6,1,'',0,0);
$pdf->Cell(152,1,'',0,1);//end of line
$pdf->SetFont('times','B',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(32,3,'Salary:       1st Half',0,0);
$pdf->Cell(33,3,$first,0,0);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(25,3,'2nd Half',0,0);
$pdf->Cell(18,3,$second,0,1);
$pdf->Cell(18,1,'       _______________________________                                           _________________________',0,1);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'',0,0);
$pdf->Cell(15,3,'',0,1);//end of line
$pdf->SetFont('times','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(36,3,'TOTAL DEDUCTIONS:',0,0);
$pdf->Cell(15,3,$totaldeduct,0,1);//end of line





$pdf->Cell(6,2,'',0,0);
$pdf->Cell(100,2,'',0,1);//end of line

$pdf->SetFont('times','B',8);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(35,3,'',0,0);
$pdf->Cell(18,3,'',0,0);
$pdf->Cell(34,3,'NET Amount:',0,0);
$pdf->Cell(15,3,$netpay,0,1);//end of line

$pdf->Cell(6,2,'',0,0);
$pdf->Cell(85,0.3,'',0,0);
$pdf->Cell(50,0.3,'',0,0);
$pdf->Cell(18,0.3,'',1,1);// end of line

$pdf->Cell(6,5,'',0,0);
$pdf->Cell(100,5,'',0,1);//end of line

$pdf->SetFont('times','',7);
$pdf->Cell(6,3,'',0,0);
$pdf->Cell(15,3,'Received by:',0,0);
$pdf->Cell(18,3,'__________________________',0,1);//end of line

$pdf->Cell(6,5,'',0,0);
	// $pdf->Cell(100,5,'',0,1);//end of line
	// $pdf->Cell(6,3,'',0,0);//end of line
	// $pdf->Cell(20, 1, 'Employee ID: ' . '$empID', 0, 1);

	$pdf->Cell(6,5,'',0,0);
	$pdf->Cell(100,5,'',0,1);//end of line
	$pdf->Cell(6,3,'',0,0);//end of line
	$pdf->Cell(15, 1, 'Printed By: ',0, 0);
	$pdf->Cell(15,1,$name,0,1);//end of line


endwhile;

$pdf->Output();
}
?>