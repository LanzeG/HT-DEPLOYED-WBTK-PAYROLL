<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

session_start();

if (isset($_POST['submit_btn'])){

  $_SESSION['13thmonthyear'] = $_POST['yearoption'];
  header("Location: adminPRINT13thMonthReport.php");

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>13th Month Reports</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" />
 <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<script type ="text/javascript">
 
</script>
</head>
<body>

<?php
INCLUDE ('NAVBAR.php');
?>


<div id="content">

  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href ="<?php echo $_SERVER['PHP_SELF']; ?>" class="tip-bottom"><i class ="icon-th"></i> 13th Month Reports</a>
    </div>
  </div>

  <span class="span6">
        <h3>13th Month Reports</h3>
      </span>

  <div class="container-fluid">
    <div class = "row-fluid">
    <div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li class=""><a href="../adminTIMESHEET.php"><i class="icon-time"></i> Timesheets</a></li>
              <li class=""><a href="../adminDTR.php"><i class="icon-time"></i> Daily Time Record</a></li>
              <li class=""><a href="../adminPAYROLLRegister.php"><i class="icon-time"></i> Payroll Register</a></li>
              <li class=""><a href="../adminPAYROLLPrintPayslip.php"><i class="icon-th"></i> Print Payslips</a></li>
              <li class=""><a href="adminGOVTReports.php"><i class="icon-th"></i> Government Contribution Reports</a></li>
              <li class="active"><a href="adminREPORT13thmonth.php"><i class="icon-th"></i> 13th Month Reports</a></li>
              <li class=""><a href="adminREPORTyearly.php"><i class="icon-th"></i> Yearly Reports</a></li>
            </ul>
          </div>
    <div class = "row-fluid">
      <span class="span3">
      </span>
    <div class="span6">
      <!-- <h3>13th Month Reports</h3> -->
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Print 13th Month Reports</h5>
          </div>

          <div class="widget-content nopadding">
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="form-horizontal" target="_blank">
      <?php
      $payperiodsquery = "SELECT * FROM PAYROLLYEARS";
      $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY ".mysqli_error($conn));
      ?>
              <div class = "control-group">
              <label class="control-label">Select Year:</label>
              <label class = "control-group">
                <div class = "controls">
                  <select class = "span2" name = "yearoption">
                    <option></option>
                    <?php  while($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)):;?>
                          <option><?php echo $payperiodchoice['pay_year'];?></option>
                          <?php endwhile;?>
                      
                  </select>
                  <!-- <span class = "label label-important"><?php echo $yearoptionerror;?></span> -->
                  <button type="submit" class="btn btn-success" name = "submit_btn" >Submit</button>
                </div>
            </div>
                <br>
            </form>
        </div>
    </div>

            
        </div>
      </div>
    </div>
    <hr>
    <div class="row-fluid">
      
      

    </div>
  </div>
</div>
</div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>
<?php
unset($_SESSION['anewdept']);
?>

<script src="../js/maruti.dashboard.js"></script> 

</body>
</html>
