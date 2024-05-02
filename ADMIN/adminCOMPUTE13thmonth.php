<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
session_start();

$id13th = $_GET['id'];
$empname = "SELECT first_name, last_name FROM employees where emp_id = '$id13th'";
$empnameexecqry = mysqli_query($conn, $empname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$empData = mysqli_fetch_assoc($empnameexecqry);

$empFullName = $empData['first_name'] . " " . $empData['last_name'];


$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];

$getdrateqry = "SELECT daily_rate FROM PAYROLLINFO WHERE emp_id = '$id13th'";
$getdrateexecqry = mysqli_query($conn,$getdrateqry) or die ("Failed to get drate ".mysqli_error($conn));
$dratearray = mysqli_fetch_array($getdrateexecqry);

if($dratearray){

	$drate = $dratearray['daily_rate'];



$amount13th = $drate * 26;
$total13th = number_format((float)$amount13th,2,'.','');

$date = strtotime("now");
$year13th = date("Y",$date);

$check13th = "SELECT * FROM 13thmonth WHERE emp_id = '$id13th' AND 13th_year = '$year13th'";
$check13thexec = mysqli_query($conn,$check13th) or die ("FAILED TO CHECK IF COMPUTED ".mysqli_error($conn));
$check13throws = mysqli_num_rows($check13thexec);

if ($check13throws != 0){
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		swal({
		 //  title: "Good job!",
		  text: "13th month has already been computed.",
		  icon: "success",
		  button: "OK",
		 }).then(function() {
			// window.location.href = 'adminMasterfile.php'; // Replace 'your_new_page.php' with the actual URL
			window.close()
		});
	});
 </script>
	<?php

}else{


	$in13thqry = "INSERT INTO 13thmonth (emp_id, 13th_amount, 13th_year) VALUES ('$id13th','$total13th','$year13th')";
	$in13thexecqry = mysqli_query($conn,$in13thqry) or die ("FAILED TO INSERT 13TH MONTH ".mysqli_error($conn));
	if ($in13thexecqry){

	$activityLog = "13th Month Computed for $empFullName ($year13th)";
	$adminActivityQuery = "INSERT INTO adminactivity_log (emp_id,adminname, activity,log_timestamp) VALUES ('$adminId','$adminFullName', '$activityLog', NOW())";
	$adminActivityResult = mysqli_query($conn, $adminActivityQuery);

		?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		swal({
		 //  title: "Good job!",
		  text: "13th month computed.",
		  icon: "success",
		  button: "OK",
		 }).then(function() {
			// window.location.href = 'adminMasterfile.php'; // Replace 'your_new_page.php' with the actual URL
			window.close()
		});
	});
 </script>
	<?php
	}
}
} else {
?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		swal({
		 //  title: "Good job!",
		  text: "13th month not computed.",
		  icon: "error",
		  button: "OK",
		 }).then(function() {
			// window.location.href = 'adminMasterfile.php'; // Replace 'your_new_page.php' with the actual URL
			window.close()
		});
	});
 </script>
<?php
}
?>