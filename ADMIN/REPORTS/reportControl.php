<?php
session_start();
$_SESSION['reporttype'] = $_POST['selreportoption'];

$reporttype = $_SESSION['reporttype'];

if (isset($_POST['submit_btn'])){

	$_SESSION['fromreport'] = $_POST['fromreport'];
	$_SESSION['toreport'] = $_POST['toreport'];
	$month = $_POST['fromreport'];

	$monthconv = strtotime($month);
	$conv = date("F-Y", $monthconv);

	$_SESSION['month'] = $conv;


	if ($reporttype == 'GSIS'){

    	header("Location: adminPrintSSSreport.php");

  	} elseif ($reporttype == 'Philhealth'){

    	header("Location: adminPrintPhilhealthreport.php");

	  }elseif ($reporttype == 'Pag-Ibig'){

    	header("Location: adminPrintPagibigreport.php");
	  }elseif ($reporttype == 'Withholding Tax'){

    	header("Location: adminTaxreport.php");

  	}

 }
