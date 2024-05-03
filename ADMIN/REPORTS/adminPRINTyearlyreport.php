<?php
set_time_limit(60);
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

/**
WHILE LOOP LEGEND 
GET EMPLOYEE ID = emparrwhile
JANUARY TOTALS = janpaywhile
FEB TOTALS = febpaywhile
MAR TOTALS = marpaywhile
APR TOTALS = aprpaywhile
MAY TOTALS = maypaywhile
JUNE TOTALS = junepaywhile
JULY TOTALS = julypaywhile
AUGUST TOTALS = augustpaywhile
SEPTEMBER TOTALS = septpaywhile
OCTOBER TOTALS = octpaywhile
NOVEMBER TOTALS = novpaywhile
DECEMBER TOTALS = decpaywhile

**/


session_start();
//WHILELOOP INITIALIZATIONS

$jan = 0;
$feb = 0;
$mar = 0;
$apr = 0;
$may = 0;
$jun = 0;
$jul = 0;
$aug = 0;
$sep = 0;
$oct = 0;
$nov = 0;
$dec = 0;

//january
$JANbpay = 0;
$JANotpay = 0;
$JANrhpay = 0;
$JANrhpay200 = 0;
$JANotrhpay = 0;
$JANshpay = 0;
$JANotshpay = 0;
$JANrdpay = 0;
$JANotrdpay = 0;
$JANrdrhpay = 0;
$JANotrdrhpay = 0;
$JANrdshpay = 0;
$JANotrdshpay = 0;
$JANlvpay = 0;
$JANtotaldeduct =0;
$JANothrs =0;
$JANloans =0;
$JANearnings = 0;
$JANot = 0;

// Set all deduction variables to 0
$JANphd = 0;
$JANsssd = 0;
$JANpid = 0;
$JANtd = 0;
$JANsssld = 0;
$JANpild = 0;
$FEBloans = 0;
$MARloans = 0;
$APRloans = 0;
$MAYloans = 0;
$JUNloans = 0;
$JULloans = 0;
$AUGloans = 0;
$SEPloans = 0;
$OCTloans = 0;
$NOVloans = 0;
$DECloans = 0;

$SEPtotaldeduct=0;
$SEPbpay = 0;
$SEPphd = 0;
$SEPsssld = 0;
$SEPpid  = 0;
$SEPsssd  = 0;
$SEPtd = 0;
$SEPpild = 0;

///f0r d3f
$OCTbpay = 0;
$OCTotpay = 0;
$OCTrhpay = 0;
$OCTrhpay200 = 0;
$OCTotrhpay = 0;
$OCTshpay = 0;
$OCTotshpay = 0;
$OCTrdpay = 0;
$OCTotrdpay = 0;
$OCTrdrhpay = 0;
$OCTotrdrhpay = 0;
$OCTrdshpay = 0;
$OCTotrdshpay = 0;
$OCTlvpay = 0;

$OCTphd = 0;
$OCTsssd = 0;
$OCTpid = 0;
$OCTtd = 0;
$OCTsssld = 0;
$OCTpild = 0;
$OCTtotaldeduct = 0;
$OCTot = 0;

$OCTearnings = 0;
$OCTdeductions = 0;
$OCTnetpay = 0;
//WHILELOOP INITIALIZATIONS
$yearreport = $_SESSION['reportyear'];
$title = "PAYROLL SUMMARY FOR THE YEAR $yearreport";

$empqry = "SELECT emp_id FROM employees";
$empexecqry = mysqli_query($conn,$empqry) or die ("FAILED EMP ".mysqli_error($conn));
while($emparr = mysqli_fetch_array($empexecqry)):;//emparrwhile

	$empid = $emparr['emp_id'];
	//JANUARYPAY
	$janpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' 
	  AND (pperiod_month = 'January' OR pperiod_month = '1' OR pperiod_month = '01') 
	  AND pperiod_year = '$yearreport'";
	$janpayexecqry = mysqli_query($conn,$janpayqry) or die ("FAILED JANPAY ".mysqli_error($conn));
	
	while($janpayarray = mysqli_fetch_array($janpayexecqry)):;//janpaywhile
		$jan = $jan + 1;
		//JAN PAYS
		$JANbpay = number_format((float) $JANbpay + $janpayarray['net_pay'],2,'.','');
		$JANtotaldeduct = number_format((float) $JANtotaldeduct + $janpayarray['total_deduct'],2,'.','');
	endwhile;//janpaywhile

		if($jan==0){
			$JANbpay = number_format((float)0,2,'.','');
			$JANtotaldeduct = number_format((float)0,2,'.','');
		}
	
	//FEBRUARYPAY
	$febpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' 
		AND (pperiod_month = 'February' OR pperiod_month = '2' OR pperiod_month = '02') 
		AND pperiod_year = '$yearreport'";
	$febpayexecqry = mysqli_query($conn,$febpayqry) or die ("FAILED FEBPAY ".mysqli_error($conn));
	while($febpayarray = mysqli_fetch_array($febpayexecqry)):;//FEBpaywhile
		$feb = $feb+1;
		//FEB PAYS
		$FEBbpay = number_format((float) $FEBbpay + $febpayarray['net_pay'],2,'.','');
		$FEBtotaldeduct = number_format((float) $FEBtotaldeduct + $febpayarray['total_deduct'],2,'.','');
		
	endwhile;//FEBpaywhile
		if ($feb == 0 ){
			$FEBbpay = number_format((float)0,2,'.','');
			$FEBtotaldeduct = number_format((float)0,2,'.','');
		}

	//MARCHPAY
	$marpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'March' OR pperiod_month = '3' OR pperiod_month = '03') 
	AND pperiod_year = '$yearreport'";
	$marpayexecqry = mysqli_query($conn,$marpayqry) or die ("FAILED JANPAY ".mysqli_error($conn));

	while($marpayarray = mysqli_fetch_array($marpayexecqry)):;//MARpaywhile
		$mar = $mar+1;
		//MAR PAYS
		$MARbpay = number_format((float) $MARbpay + $marpayarray['net_pay'],2,'.','');
		$MARtotaldeduct = number_format((float) $MARtotaldeduct + $marpayarray['total_deduct'],2,'.','');
		
	endwhile;//MARpayendwhile
		if ($mar == 0 ){
			$MARbpay = number_format((float)0,2,'.','');
			$MARtotaldeduct = number_format((float)0,2,'.','');
		}
	//MARCHPAYEND

	//APR PAY
	$aprpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid'  AND (pperiod_month = 'April' OR pperiod_month = '4' OR pperiod_month = '04') 
	AND pperiod_year = '$yearreport'";
	$aprpayexecqry = mysqli_query($conn,$aprpayqry) or die ("FAILED APRPAY ".mysqli_error($conn));
	while($aprpayarray = mysqli_fetch_array($aprpayexecqry)):;//APRpaywhile
		$apr = $apr+1;
		//APR PAYS
		$APRbpay = number_format((float) $APRbpay + $aprpayarray['net_pay'],2,'.','');
		$APRtotaldeduct = number_format((float) $APRtotaldeduct + $aprpayarray['total_deduct'],2,'.','');
	endwhile;//APRpayendwhile
		if ($apr == 0 ){
			$APRbpay = number_format((float)0,2,'.','');
			$APRtotaldeduct = number_format((float)0,2,'.','');
		}
	//APR PAYEND

	//MAY PAYS
	$maypayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'MAY' OR pperiod_month = '5' OR pperiod_month = '05') 
	AND pperiod_year = '$yearreport'";
	$maypayexecqry = mysqli_query($conn,$maypayqry) or die ("FAILED MAYPAY ".mysqli_error($conn));
	while($maypayarray = mysqli_fetch_array($maypayexecqry)):;//MAYpaywhile
		$may = $may+1;
		//MAY PAYS
		$MAYbpay = number_format((float) $MAYbpay + $maypayarray['net_pay'],2,'.','');
		$MAYtotaldeduct = number_format((float) $MAYtotaldeduct + $maypayarray['total_deduct'],2,'.','');
		

	endwhile;//maypayendwhile
		if ($may == 0 ){
			$MAYbpay = number_format((float)0,2,'.','');
			$MAYtotaldeduct = number_format((float)0,2,'.','');
		}
	//MAY PAYEND

	//JUN PAYS
	$junpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'June' OR pperiod_month = '6' OR pperiod_month = '06') 
	AND pperiod_year = '$yearreport'";
	$junpayexecqry = mysqli_query($conn,$junpayqry) or die ("FAILED JUNPAY ".mysqli_error($conn));
	while($junpayarray = mysqli_fetch_array($junpayexecqry)):;//MAYpaywhile
		$jun = $jun+1;
		//JUN PAYS
		$JUNbpay = number_format((float) $JUNbpay + $junpayarray['net_pay'],2,'.','');
		$JUNtotaldeduct = number_format((float) $JUNtotaldeduct + $junpayarray['total_deduct'],2,'.','');
	endwhile;//junpayendwhile
		if ($jun == 0 ){
			$JUNbpay = number_format((float)0,2,'.','');
			$JUNtotaldeduct = number_format((float)0,2,'.','');
		}	

	//JUN PAYEND

	//JUL PAYS
	$julpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'July' OR pperiod_month = '7' OR pperiod_month = '07') 
	AND pperiod_year = '$yearreport'";
	$julpayexecqry = mysqli_query($conn,$julpayqry) or die ("FAILED JULPAY ".mysqli_error($conn));
	while($julpayarray = mysqli_fetch_array($julpayexecqry)):;//JULpaywhile
		$jul = $jul+1;
		//JUL PAYS
		$JULbpay = number_format((float) $JULbpay + $julpayarray['net_pay'],2,'.','');
		$JULtotaldeduct = number_format((float) $JULtotaldeduct + $julpayarray['total_deduct'],2,'.','');
	endwhile;//julpayendwhile
		if ($jul == 0 ){
			$JULbpay = number_format((float)0,2,'.','');
			$JULtotaldeduct = number_format((float)0,2,'.','');
		}
	//JUL PAYEND

	//AUG PAYS
	$augpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid'  AND (pperiod_month = 'August' OR pperiod_month = '8' OR pperiod_month = '08') 
	AND pperiod_year = '$yearreport'";
	$augpayexecqry = mysqli_query($conn,$augpayqry) or die ("FAILED AUGPAY ".mysqli_error($conn));
	while($AUGpayarray = mysqli_fetch_array($augpayexecqry)):;//AUGpaywhile
		$aug = $aug+1;
		//AUG PAYS
		$AUGbpay = number_format((float) $AUGbpay + $AUGpayarray['net_pay'],2,'.','');
		$AUGtotaldeduct = number_format((float) $AUGtotaldeduct + $AUGpayarray['total_deduct'],2,'.','');
	endwhile;//AUGpayendwhile
		if ($aug == 0 ){
			$AUGbpay = number_format((float)0,2,'.','');
			$AUGtotaldeduct = number_format((float)0,2,'.','');
		}

	//AUG PAYEND

	//SEP PAYS
	$seppayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'September' OR pperiod_month = '9' OR pperiod_month = '09') 
	AND pperiod_year = '$yearreport'";
	$seppayexecqry = mysqli_query($conn,$seppayqry) or die ("FAILED SEP PAY ".mysqli_error($conn));
	while($SEPpayarray = mysqli_fetch_array($seppayexecqry)):;//SEPpaywhile
		$sep = $sep+1;
		//SEP PAYS
		$SEPbpay = number_format((float) $SEPbpay + $SEPpayarray['net_pay'],2,'.','');
		$SEPtotaldeduct = number_format((float) $SEPtotaldeduct + $SEPpayarray['total_deduct'],2,'.','');
	endwhile;//AUGpayendwhile
		if ($sep == 0 ){
			$SEPbpay = number_format((float)0,2,'.','');
			$SEPtotaldeduct = number_format((float)0,2,'.','');
		}
	//SEP PAYEND	

	//OCT PAYS
	$octpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'October' OR pperiod_month = '10' OR pperiod_month = '010') 
	AND pperiod_year = '$yearreport'";
	$octpayexecqry = mysqli_query($conn,$octpayqry) or die ("FAILED OCT PAY ".mysqli_error($conn));
	while($OCTpayarray = mysqli_fetch_array($octpayexecqry)):;//OCTpaywhile
		$oct = $oct+1;
		//OCT PAYS
		$OCTbpay = number_format((float) $OCTbpay + $OCTpayarray['net_pay'],2,'.','');
		$OCTtotaldeduct = number_format((float) $OCTtotaldeduct + $OCTpayarray['total_deduct'],2,'.','');
	endwhile;//OCTpayendwhile
		if ($oct == 0 ){
			$OCTbpay = number_format((float)0,2,'.','');
			$OCTtotaldeduct = number_format((float)0,2,'.','');
		}
	//OCT PAYEND

	//NOV PAYS
	$novpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'November' OR pperiod_month = '11' OR pperiod_month = '011') 
	AND pperiod_year = '$yearreport'";
	$novpayexecqry = mysqli_query($conn,$novpayqry) or die ("FAILED NOV PAY ".mysqli_error($conn));
	while($NOVpayarray = mysqli_fetch_array($novpayexecqry)):;//NOVpaywhile
		$nov = $nov+1;
		//NOV PAYS
		$NOVbpay = number_format((float) $NOVbpay + $NOVpayarray['net_pay'],2,'.','');
		$NOVtotaldeduct = number_format((float) $NOVtotaldeduct + $NOVpayarray['total_deduct'],2,'.','');
	endwhile;//OCTpayendwhile
		if ($nov == 0 ){
			$NOVbpay = number_format((float)0,2,'.','');
			$NOVtotaldeduct = number_format((float)0,2,'.','');
		}
	//NOV PAYEND

	//DEC PAYS
	$decpayqry = "SELECT * FROM PAY_PER_PERIOD WHERE emp_id = '$empid' AND (pperiod_month = 'December' OR pperiod_month = '12' OR pperiod_month = '012') 
	AND pperiod_year = '$yearreport'";
	$decpayexecqry = mysqli_query($conn,$decpayqry) or die ("FAILED NOV PAY ".mysqli_error($conn));
	while($DECpayarray = mysqli_fetch_array($decpayexecqry)):;//DECpaywhile
		$dec = $dec+1;
		//DEC PAYS
		$DECbpay = number_format((float) $DECbpay + $DECpayarray['net_pay'],2,'.','');
		$DECtotaldeduct = number_format((float) $DECtotaldeduct + $DECpayarray['total_deduct'],2,'.','');
	endwhile;//DECpayendwhile
		if ($dec == 0 ){
			$DECbpay = number_format((float)0,2,'.','');
			$DECtotaldeduct = number_format((float)0,2,'.','');
		}
	//DEC PAYEND
	//TOTALS
	$TOTALbpay = $JANbpay + $FEBbpay + $MARbpay + $APRbpay + $MAYbpay + $JUNbpay + $JULbpay + $AUGbpay + $SEPbpay + $OCTbpay + $NOVbpay + $DECbpay;
	$TOTALtotaldeduct = $JANtotaldeduct + $FEBtotaldeduct + $MARtotaldeduct + $APRtotaldeduct + $MAYtotaldeduct + $JUNtotaldeduct + $JULtotaldeduct + $AUGtotaldeduct + $SEPtotaldeduct + $OCTtotaldeduct + $NOVtotaldeduct + $DECtotaldeduct;

	$TOTALbpay = number_format((float) $TOTALbpay,2,'.','');
	$TOTALtotaldeduct = number_format((float) $TOTALtotaldeduct,2,'.','');

endwhile;//emparrwhile
	


require_once("../fpdf181/fpdf.php");

$pdf = new FPDF ('L','mm','LEGAL');
$pdf ->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(336,3,'',0,1);

$pdf->Cell(336,4,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS',0,1,'C');//end of line
$pdf->SetFont('Arial','','8');


$pdf->Cell(336,5,'',0,1);

$pdf->SetFont('Arial','B','10');

$pdf->SetFont('Arial','','10');

$pdf->SetFont('Arial','B','9');
$pdf->Cell(39,4,'DESCRIPTION',0,0,'C');
$pdf->Cell(22.84,5,'January',0,0,'C');
$pdf->Cell(22.84,5,'February',0,0,'C');
$pdf->Cell(22.84,5,'March',0,0,'C');
$pdf->Cell(22.84,5,'April',0,0,'C');
$pdf->Cell(22.84,5,'May',0,0,'C');
$pdf->Cell(22.84,5,'June',0,0,'C');
$pdf->Cell(22.84,5,'July',0,0,'C');
$pdf->Cell(22.84,5,'August',0,0,'C');
$pdf->Cell(22.84,5,'September',0,0,'C');
$pdf->Cell(22.84,5,'October',0,0,'C');
$pdf->Cell(22.84,5,'November',0,0,'C');
$pdf->Cell(22.84,5,'December',0,0,'C');
$pdf->Cell(22.84,5,'TOTAL',0,1,'C');

$pdf->Cell(336,0.2,'',1,1);//end of 

$pdf->Cell(336,2,'',0,1);//end of line

//DESCRIPTION
$pdf->SetFont('Arial','','8');
$pdf->Cell(39,5,'Net Pay',0,0,'');
$pdf->Cell(22.84,5,$JANbpay,0,0,'R');//Jan
$pdf->Cell(22.84,5,$FEBbpay,0,0,'R');//Feb
$pdf->Cell(22.84,5,$MARbpay,0,0,'R');//Mar
$pdf->Cell(22.84,5,$APRbpay,0,0,'R');//Apr
$pdf->Cell(22.84,5,$MAYbpay,0,0,'R');//May
$pdf->Cell(22.84,5,$JUNbpay,0,0,'R');//Jun
$pdf->Cell(22.84,5,$JULbpay,0,0,'R');//Jul
$pdf->Cell(22.84,5,$AUGbpay,0,0,'R');//Aug
$pdf->Cell(22.84,5,$SEPbpay,0,0,'R');//Sept
$pdf->Cell(22.84,5,$OCTbpay,0,0,'R');//Oct
$pdf->Cell(22.84,5,$NOVbpay,0,0,'R');//Nov
$pdf->Cell(22.84,5,$DECbpay,0,0,'R');//Dec
$pdf->Cell(22.84,5,$TOTALbpay,0,1,'R');//Total
//end

// //DESCRIPTION

$pdf->Cell(39,5,'Deductions',0,0,'');
$pdf->SetFont('Arial','','8');
$pdf->Cell(22.84,5,$JANtotaldeduct,0,0,'R');//Jan
$pdf->Cell(22.84,5,$FEBtotaldeduct,0,0,'R');//Feb
$pdf->Cell(22.84,5,$MARtotaldeduct,0,0,'R');//Mar
$pdf->Cell(22.84,5,$APRtotaldeduct,0,0,'R');//Apr
$pdf->Cell(22.84,5,$MAYtotaldeduct,0,0,'R');//May
$pdf->Cell(22.84,5,$JUNtotaldeduct,0,0,'R');//Jun
$pdf->Cell(22.84,5,$JULtotaldeduct,0,0,'R');//Jul
$pdf->Cell(22.84,5,$AUGtotaldeduct,0,0,'R');//Aug
$pdf->Cell(22.84,5,$SEPtotaldeduct,0,0,'R');//Sept
$pdf->Cell(22.84,5,$OCTtotaldeduct,0,0,'R');//Oct
$pdf->Cell(22.84,5,$NOVtotaldeduct,0,0,'R');//Nov
$pdf->Cell(22.84,5,$DECtotaldeduct,0,0,'R');//Dec
$pdf->Cell(22.84,5,$TOTALtotaldeduct,0,1,'R');//Total
//end

$pdf->Cell(336,3,'',0,1); //spacer
$pdf->Cell(336,0.2,'',1,1);//end of line
$pdf->Cell(336, 10, 'Printed by ' . $adminFullName, 0, 1, 'R');

$pdf->Output();

?>