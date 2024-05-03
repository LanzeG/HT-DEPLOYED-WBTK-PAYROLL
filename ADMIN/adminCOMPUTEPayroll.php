<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");


session_start();
$adminId = $_SESSION['adminId'];

$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$payID = $_GET['id'];
$payperiodfrom = $_SESSION['payperiodfrom'];
$payperiodto = $_SESSION['payperiodto'];
$payperiodrange = $_SESSION['payperiodrange'];

$enddateinit = strtotime($payperiodto);
$enddateconv = date("d", $enddateinit);

$month1 = date("m", $enddateinit);
$year = date("Y", $enddateinit);

$month = intval($month1);
$year = intval($year);

if ($month == 1) {
    // If the current month is January, set the last month to December of the previous year
    $lastMonth = 12;
    $lastYear = $year - 1;
} else {
    // Otherwise, subtract 1 from the current month and keep the current year
    $lastMonth = $month - 1;
    $lastYear = $year;
}

// Get the last month's 16th day
$lastMonth16 = date_create_from_format('Y-m-d', "$lastYear-" . sprintf('%02d', $lastMonth) . "-16");

// Get the current month's 15th day
$currentMonth15 = date_create_from_format('Y-m-d', "$year-$month-15");


/**GET EMPLOYEE INFORMATION**/

$getempinfoquery = "SELECT * FROM employees WHERE emp_id = '$payID'";
$getempinfoexecquery = mysqli_query($conn,$getempinfoquery) or die ("FAILED TO GET PAY INFO ".mysqli_error($conn));
$getempinfoarray = mysqli_fetch_array($getempinfoexecquery);

if ($getempinfoarray){
	$prefix = $getempinfoarray['prefix_ID'];
	$idno = $getempinfoarray['emp_id'];
	$lname = $getempinfoarray['last_name'];
	$fname = $getempinfoarray['first_name'];
	$mname = $getempinfoarray['middle_name'];
	$dept = $getempinfoarray['dept_NAME'];
	$relstatus = $getempinfoarray['rel_status'];
	$numberofchildren = $getempinfoarray['num_children'];
	$emptype = $getempinfoarray['employment_TYPE'];

  $name = "$lname, $fname $mname";
  $empID = "$prefix$idno";

}

// Set $currentDate to $payperiodfrom
$currentDate = date('Y-m-d', strtotime($payperiodfrom));

// Extract the day of the month
$dayOfMonth = date('d', strtotime($currentDate));

	if ($emptype == 'Full Time'){
		$searchquery1 = "SELECT * FROM pay_per_period WHERE emp_id = '$payID'";
		$searchexecquery1 = mysqli_query($conn,$searchquery1) or die ("FAILED TO SEARCH ".mysqli_error($conn));
		$searchrows1 = mysqli_num_rows($searchexecquery1);

		if ($searchrows1>1){
		//check if processed already
		$searchquery = "SELECT * FROM pay_per_period WHERE emp_id = '$payID' AND pperiod_range = '$payperiodrange'";
		$searchexecquery = mysqli_query($conn,$searchquery) or die ("FAILED TO SEARCH ".mysqli_error($conn));
		$searchrows = mysqli_num_rows($searchexecquery);
		$searcharray = mysqli_fetch_array($searchexecquery);

			if ($searchrows >= 1){				
		?>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
					swal({
						text: "Payroll this pay period has already been processed.",
						icon: "success",
						button: "OK",
						}).then(function() {
						window.close()
						});
					});
				</script>
		<?php									
			} else {
				//get undertimes
				$timekeepinfoquery = "SELECT SUM(hours_work) as hourswork, SUM(undertime_hours) as undertimehours FROM time_keeping WHERE emp_id = '$payID' AND timekeep_day BETWEEN '" . $lastMonth16->format('Y-m-d') . "' AND '" . $currentMonth15->format('Y-m-d') . "' ORDER BY timekeep_day ASC";
				$timekeepinfoexecquery = mysqli_query($conn,$timekeepinfoquery) or die ("FAILED TO GET TIMEKEEPINFO ". mysqli_error($conn));
				$timekeepinfoarray = mysqli_fetch_array($timekeepinfoexecquery);

					if ($timekeepinfoarray){													
					$hw = $timekeepinfoarray['hourswork'];
					$undertimehours = $timekeepinfoarray['undertimehours'];
					}

						//get absences
						$absencesfoquery = "SELECT COUNT(absence_id) FROM absences WHERE emp_id = '$payID' AND absence_date BETWEEN '" . $lastMonth16->format('Y-m-d') . "' AND '" . $currentMonth15->format('Y-m-d') . "'";
						$absencesfoexecquery = mysqli_query($conn, $absencesfoquery) or die ("FAILED TO GET absencesFO " . mysqli_error($conn));

						// Fetch the result into an array
						$absencesfoarray = mysqli_fetch_array($absencesfoexecquery);

						// Access the count value
						$absencesCount = $absencesfoarray[0];
								
						//get payroll infro
						$payinfoquery = "SELECT * FROM payrollinfo WHERE emp_id = '$payID'";
						$payinfoexecquery = mysqli_query($conn,$payinfoquery) or die ("FAILED TO GET PAY INFO ".mysqli_error($conn));
						$payinfoarray = mysqli_fetch_array($payinfoexecquery);

						if($payinfoarray){

							$basepay =(float) $payinfoarray['base_pay'];
							$dailypay = $payinfoarray['daily_rate'];
							$rph = $payinfoarray['hourly_rate'];
							$gsis = $payinfoarray['gsis'];
							$ph = $payinfoarray['philhealth'];
							$pagibig = $payinfoarray['pagibig'];
							$wtax = $payinfoarray['wtax'];
							$disallowance = $payinfoarray['current_disallowance'];
							$refsalary = (float) $payinfoarray['refsalary'];
							$minutes = $rph/60;
							$totalut = $undertimehours * $minutes;
						}
									
						//get total loans for the month
						$totalloan = 0;
						$loanquery = "SELECT * FROM loans WHERE emp_id = '$payID' AND '" . $currentMonth15->format('Y-m-d') . "' BETWEEN start_date AND end_date AND status ='On-Going'";
						$loanexecqry = mysqli_query($conn, $loanquery) or die("FAILED TO CHECK PAG-IBIG LOANS");

							if (mysqli_num_rows($loanexecqry) > 0) {
								// Fetch each row of loan data
								while ($loanrow = mysqli_fetch_assoc($loanexecqry)) {
								// print_r($loanrow);
									// Access individual columns like $loanrow['column_name']
									$loanID = $loanrow['loanidno'];
									$uniquekey = $loanrow['uniquekey'];
									$loanAmount = $loanrow['loan_amount'];
									$loan_balance = $loanrow['loan_balance'];
									$loantype= $loanrow['loantype'];
									$loanorg= $loanrow['loanorg'];
									$monthly_deduct = $loanrow['monthly_deduct'];
									$loan_status = $loanrow['status'];
									$noofpays = $loanrow['no_of_pays'];
									$startdate = $loanrow['start_date'];
									$enddate = $loanrow['end_date'];

									$loan_balance -= $monthly_deduct;
									$noofpays = $noofpays-1;

										if ($noofpays==0){
											$loan_status = 'Paid';
										}else{
											$loan_status = 'On-Going';
										}
											
										$totalloan = $totalloan + $monthly_deduct;
										$updateQuery = "UPDATE loans SET loan_balance =$loan_balance, no_of_pays = $noofpays, status = '$loan_status' WHERE uniquekey = '$uniquekey'";
										mysqli_query($conn, $updateQuery) or  die("FAILED TO UPDATE LOAN: " . mysqli_error($conn));

										$loanhistory="INSERT INTO loan_history (uniquekey, loan_id, loantype, loanorg, emp_id, lastname, firstname, middlename, amount, start_date, end_date, monthly_payment, status, num_of_payments, current_amount, payperiod, admin_name) VALUES
										('$uniquekey','$loanID','$loantype','$loanorg','$payID','$lname','$fname','$mname','$loanAmount','$startdate','$enddate','$monthly_deduct', '$loan_status','$noofpays', '$loan_balance','$payperiodrange','$adminFullName')";
										$loanhistoryresult = mysqli_query($conn, $loanhistory) or die (" ".mysqli_error($conn));
										}
									} else {
										// echo "No loans found for the specified employee and date range.";
									}

									if($basepay<$totalloan){
										$disallowance1 = $totalloan;
										// echo $totalloan;
										$totalloan=0;
										
									}
									else{
										$disallowance1=0;
									}

									//computation of wtax and other
									$totalabsences = $absencesCount  * $dailypay;
									$totaldeduct = $totalut + $totalabsences + $gsis + $ph + $pagibig + $totalloan + $disallowance;
									$totalnet = $basepay - $totaldeduct + $refsalary;
									
								$firsthalf = floor(($totalnet/2) / 1000) * 1000;
								$secondhalf = $totalnet - $firsthalf;

								$adddisallowance = "UPDATE payrollinfo SET disallowance = disallowance + $disallowance1, current_disallowance = 0 WHERE emp_id = $payID";
								$adddisallowancequery = mysqli_query($conn,$adddisallowance) or die ("FAILED TO INSERT PAYROLL INFO ".mysqli_error($conn));

								$savepayrollquery = "INSERT INTO pay_per_period (emp_id,pperiod_range,pperiod_month,pperiod_year,rate_per_hour, reg_pay, refsalary, undertimehours, absences,net_pay,philhealth_deduct, sss_deduct, pagibig_deduct, tax_deduct, total_deduct, loan_deduct, disallowance, firsthalf,secondhalf) VALUES 
																				('$payID','$payperiodrange','$month1','$year','$rph','$basepay','$refsalary', '$totalut', '$totalabsences', '$totalnet', '$ph','$gsis','$pagibig','$wtax', $totaldeduct, $totalloan,$disallowance, $firsthalf,$secondhalf)";
								$savepayrollexecquery = mysqli_query($conn,$savepayrollquery) or die ("FAILED TO INSERT PAYROLL INFO ".mysqli_error($conn));

								$activityLog = "Payroll Computed for $fname $lname ($payperiodrange)";
								$adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
								$adminActivityResult = mysqli_query($conn, $adminActivityQuery);

								$notificationMessage = "Payroll Computed for $fname $lname ($payperiodrange)";
								$insertNotificationQuery = "INSERT INTO empnotifications (admin_id,adminname, emp_id, message, type, status) VALUES ('$adminId','$adminFullName', '$payID','$notificationMessage','Payroll','unread')";
								mysqli_query($conn, $insertNotificationQuery);

								if ($savepayrollexecquery){
									 ?>
						
									<script>
									document.addEventListener('DOMContentLoaded', function() {
										swal({
										text: "Payroll Processed",
										icon: "success",
										button: "OK",
										}).then(function() {
											window.close()
										});
									});
								</script>
								<?php
												
								}else{
									// echo "<script>alert('hatdof');</script>";
								}

							
							}
		}else{
			$timekeepinfoquery = "SELECT SUM(hours_work) as hourswork, SUM(undertime_hours) as undertimehours FROM time_keeping WHERE emp_id = '$payID' AND timekeep_day BETWEEN '$payperiodfrom ' AND '$payperiodto ' ORDER BY timekeep_day ASC";
			$timekeepinfoexecquery = mysqli_query($conn,$timekeepinfoquery) or die ("FAILED TO GET TIMEKEEPINFO ". mysqli_error($conn));
			$timekeepinfoarray = mysqli_fetch_array($timekeepinfoexecquery);

			if ($timekeepinfoarray){
				$hw = $timekeepinfoarray['hourswork'];
				$undertimehours = $timekeepinfoarray['undertimehours'];
				$hw = floor($hw);

			}

				$absencesfoquery = "SELECT COUNT(absence_id) FROM absences WHERE emp_id = '$payID' AND absence_date BETWEEN '$payperiodfrom ' AND '$payperiodto '";
				$absencesfoexecquery = mysqli_query($conn, $absencesfoquery) or die ("FAILED TO GET absencesFO " . mysqli_error($conn));
				// Fetch the result into an array
				$absencesfoarray = mysqli_fetch_array($absencesfoexecquery);
				// Access the count value
				$absencesCount = $absencesfoarray[0];

				/** GET HOURLY RATE INFORMATION **/

				$payinfoquery = "SELECT * FROM payrollinfo WHERE emp_id = '$payID'";
				$payinfoexecquery = mysqli_query($conn,$payinfoquery) or die ("FAILED TO GET PAY INFO ".mysqli_error($conn));
				$payinfoarray = mysqli_fetch_array($payinfoexecquery);

				if($payinfoarray){

					$dailypay = $payinfoarray['daily_rate'];
					$rph = $payinfoarray['hourly_rate'];
					$wtax = $payinfoarray['wtax'];

					$minutes = $rph/60;
					$totalut = $undertimehours * $minutes;
					$totalabsences = $absencesCount  * $dailypay;
					$totaldeduct = $totalut + $totalabsences + $wtax;
				}
					$salary = $hw * $rph;
					$totalnet = $salary - $totaldeduct;

					$searchquery = "SELECT * FROM pay_per_period WHERE emp_id = '$payID' AND pperiod_range = '$payperiodrange'";
					$searchexecquery = mysqli_query($conn,$searchquery) or die ("FAILED TO SEARCH ".mysqli_error($conn));
					$searchrows = mysqli_num_rows($searchexecquery);
					$searcharray = mysqli_fetch_array($searchexecquery);
					$firsthalf = floor(($totalnet/2) / 1000) * 1000;
					$secondhalf = $totalnet - $firsthalf;

						if ($searchrows >= 1){
								?>
						
							<script>
								document.addEventListener('DOMContentLoaded', function() {
								swal({
									text: "Payroll this pay period has already been processed.",
									icon: "success",
									button: "OK",
									}).then(function() {
									window.close()
									});
									});
							</script>
								<?php
						} else {				
							$savepayrollquery = "INSERT INTO pay_per_period (emp_id,pperiod_range,pperiod_month,pperiod_year,rate_per_hour, undertimehours, total_deduct, tax_deduct, absences,net_pay, firsthalf, secondhalf) VALUES 
																				('$payID','$payperiodrange','$month','$year','$rph', '$totalut', '$totaldeduct','$wtax','$totalabsences', '$totalnet', '$firsthalf','$secondhalf')";
							$savepayrollexecquery = mysqli_query($conn,$savepayrollquery) or die ("FAILED TO INSERT PAYROLL INFO ".mysqli_error($conn));


							$activityLog = "Payroll Computed for $payID ($payperiodrange)";
							$adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
							$adminActivityResult = mysqli_query($conn, $adminActivityQuery);


							$notificationMessage = "Payroll Computed for $fname $lname ($payperiodrange)";
							$insertNotificationQuery = "INSERT INTO empnotifications (admin_id, emp_id,adminname, message, type, status) VALUES ('$adminId','$adminFullName', '$payID','$notificationMessage','Payroll','unread')";
							mysqli_query($conn, $insertNotificationQuery);

							if ($savepayrollexecquery){
								?>
						
								<script>
								document.addEventListener('DOMContentLoaded', function() {
									swal({
									text: "Payroll Processed",
									icon: "success",
									button: "OK",
									}).then(function() {
									window.close()
										});
									});
								</script>
								<?php											
								}else{
									// echo "<script>alert('hatdof');</script>";
								}
							}
		}

} else if ($emptype == 'Part Time'){
	$timekeepinfoquery = "SELECT SUM(hours_work) as hourswork, SUM(undertime_hours) as undertimehours FROM time_keeping WHERE emp_id = '$payID' AND timekeep_day BETWEEN '" . $lastMonth16->format('Y-m-d') . "' AND '" . $currentMonth15->format('Y-m-d') . "' ORDER BY timekeep_day ASC";
	$timekeepinfoexecquery = mysqli_query($conn,$timekeepinfoquery) or die ("FAILED TO GET TIMEKEEPINFO ". mysqli_error($conn));
	$timekeepinfoarray = mysqli_fetch_array($timekeepinfoexecquery);

		if ($timekeepinfoarray){
			$hw = $timekeepinfoarray['hourswork'];
			$undertimehours = $timekeepinfoarray['undertimehours'];
			$hw = floor($hw);

		}

			$absencesfoquery = "SELECT COUNT(absence_id) FROM absences WHERE emp_id = '$payID' AND absence_date BETWEEN '" . $lastMonth16->format('Y-m-d') . "' AND '" . $currentMonth15->format('Y-m-d') . "'";
			$absencesfoexecquery = mysqli_query($conn, $absencesfoquery) or die ("FAILED TO GET absencesFO " . mysqli_error($conn));
			// Fetch the result into an array
			$absencesfoarray = mysqli_fetch_array($absencesfoexecquery);
			// Access the count value
			$absencesCount = $absencesfoarray[0];

			/** GET HOURLY RATE INFORMATION **/

			$payinfoquery = "SELECT * FROM payrollinfo WHERE emp_id = '$payID'";
			$payinfoexecquery = mysqli_query($conn,$payinfoquery) or die ("FAILED TO GET PAY INFO ".mysqli_error($conn));
			$payinfoarray = mysqli_fetch_array($payinfoexecquery);

			if($payinfoarray){

				$dailypay = $payinfoarray['daily_rate'];
				$rph = $payinfoarray['hourly_rate'];
				$wtax = $payinfoarray['wtax'];

				$minutes = $rph/60;
				$totalut = $undertimehours * $minutes;
				$totalabsences = $absencesCount  * $dailypay;
				$totaldeduct = $totalut + $totalabsences + $wtax;
			}
					$salary = $hw * $rph;
					$totalnet = $salary - $totaldeduct;

					$searchquery = "SELECT * FROM pay_per_period WHERE emp_id = '$payID' AND pperiod_range = '$payperiodrange'";
					$searchexecquery = mysqli_query($conn,$searchquery) or die ("FAILED TO SEARCH ".mysqli_error($conn));
					$searchrows = mysqli_num_rows($searchexecquery);
					$searcharray = mysqli_fetch_array($searchexecquery);
					$firsthalf = floor(($totalnet/2) / 1000) * 1000;
					$secondhalf = $totalnet - $firsthalf;

						if ($searchrows >= 1){
								?>
						
							<script>
								document.addEventListener('DOMContentLoaded', function() {
								swal({
									text: "Payroll this pay period has already been processed.",
									icon: "success",
									button: "OK",
									}).then(function() {
									window.close()
									});
									});
							</script>
								<?php
						} else {				
							$savepayrollquery = "INSERT INTO pay_per_period (emp_id,pperiod_range,pperiod_month,pperiod_year,rate_per_hour, undertimehours, total_deduct, tax_deduct, absences,net_pay, firsthalf, secondhalf) VALUES 
																				('$payID','$payperiodrange','$month','$year','$rph', '$totalut', '$totaldeduct','$wtax','$totalabsences', '$totalnet', '$firsthalf','$secondhalf')";
							$savepayrollexecquery = mysqli_query($conn,$savepayrollquery) or die ("FAILED TO INSERT PAYROLL INFO ".mysqli_error($conn));


							$activityLog = "Payroll Computed for $payID ($payperiodrange)";
							$adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
							$adminActivityResult = mysqli_query($conn, $adminActivityQuery);


							$notificationMessage = "Payroll Computed for $fname $lname ($payperiodrange)";
							$insertNotificationQuery = "INSERT INTO empnotifications (admin_id, emp_id,adminname, message, type, status) VALUES ('$adminId','$adminFullName', '$payID','$notificationMessage','Payroll','unread')";
							mysqli_query($conn, $insertNotificationQuery);

							if ($savepayrollexecquery){
								 ?>
						
								<script>
								document.addEventListener('DOMContentLoaded', function() {
									swal({
									text: "Payroll Processed",
									icon: "success",
									button: "OK",
									}).then(function() {
									window.close()
										});
									});
								</script>
								<?php											
								}else{
									// echo "<script>alert('hatdof');</script>";
								}
							}
			}
?>