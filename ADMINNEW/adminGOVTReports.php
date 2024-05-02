<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

?>




<!DOCTYPE html>
<html lang="en">
<head>
<title>Government Contribution Reports</title>
<link rel="icon" type="image/png" href="../img/icon1 (3).png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<!-- <link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" /> -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type ="text/javascript">
 
</script>
</head>
<body>

<!--Header-part-->





<?php
INCLUDE ('navbaradmin.php');
?>


<div id="content">

  <div id="content-header">

    
    </div>
  </div>
  <div class="row m-4">
 
        <div class="title text-center pt-2">
    <h3>GOVERNMENT CONTRIBUTION REPORTS</h3>
    <HR></HR>
    </div>
       
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <!-- <h5>Print Government Contribution Reports</h5> -->
          </div>

          <div class="widget-content nopadding">
            <form action="../admin/reports/reportControl.php" method="POST" class="form-horizontal" target="_blank">

               <div class="control-group">
          <?php
          $payperiodsquery = "SELECT * FROM payperiods";
          $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY ".mysqli_error($conn));
          ?>
                <!-- <label class="control-label">From:</label> -->
                <div class="controls">

                <!-- <select class = "span4" name="fromreport" required>

                  <option></option>
                  <?php while($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)):;?>
                  <option><?php echo $payperiodchoice['pperiod_range'];?></option>
                  <?php endwhile;?>
          
                </select> -->
                <!-- <span class ="label label-important"><?php echo $fromreporterror; ?></span> -->
                  
                </div>
                
              </div>
              <div class="col-12 col-sm-4 card shadow mx-auto my-5 p-3 mt-5">
              <div class="control-group">
        
                <label class="control-label pt-2">Payroll Period:</label>
                <div class="controls">
          <?php
          $payperiodsquery = "SELECT * FROM payperiods";
          $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY ".mysqli_error($conn));
          ?>
                <select class = "span4 form-select col-12 col-md-12 mx-auto " name="toreport" required>

                  <option></option>
                  <?php while($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)):;?>
                  <option><?php echo $payperiodchoice['pperiod_range'];?></option>
          <?php endwhile;?>
                </select>
                <!-- <span class ="label label-important"><?php echo $fromreporterror; ?></span> -->
                  
                </div>
              </div>

              <div class="control-group">
                <label class="control-label pt-2">Select Report:</label>
                <div class="controls">

                <select class = "span4 form-select col-12 col-md-12 mx-auto" name="selreportoption" required>
                  <option></option>
                  <option>Philhealth</option>
                  <option>GSIS</option>
                  <option>Pag-Ibig</option>
                  <!-- <option>Tax</option> -->
                
                </select>
                <div class="control-group pt-3 text-center">
                <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="submit_btn">Submit</button>
                </div>
              </div>
              </div>
            </form>
        </div>
    </div>
    
    <div class="row-fluid">

      <!-- <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Print by Employee ID</h5>
          </div>
        <div class="widget-content nopadding">
            <form action="reportControl.php" method="POST" class="form-horizontal" target="_blank">
              <div class="control-group">
                <label class="control-label">Employee ID:</label>
                <div class="controls">
                  <input type="text" class="span7" placeholder="Employee ID" name="empidno" value="" required/>
                  
                   <span class ="label label-important"><?php echo $empidnoerror; ?></span> -->

                </div>
              </div> 

            <!-- <div class = "control-group">
              <label class="control-label">Select Year:</label>
              <label class = "control-group">
                <div class = "controls">
                  <select class = "span2" name = "yearoption" required>
                    <option></option>
                    <option>2018</option>
                    <option>2017</option>
                    <option>2023</option>
                  </select>
                  <span class = "label label-important"><?php echo $yearoptionerror;?></span> -->
                </div>
            </div> 

              <!-- <div class="control-group">
                <label class="control-label">Select Report:</label>
                <div class="controls">

                <select class = "span4" name="selreportoption" required>
                  <option></option>
                  <option>Philhealth</option>
                  <option>SSS</option>
                  <option>Withholding Tax</option>
                  <option>SSS Loan</option>
                  <option>PAG-IBIG Loan</option>
                </select>
                 <span class ="label label-important"><?php echo $selectreporterror; ?></span> -->
                <!-- <button type="submit" class="btn btn-success printbtn" name = "submitbyempid_btn">Submit</button> -->
                </div>
              </div>
        
            </form>
        </div>
      </div>

    <div class="row-fluid">
      
      

    </div>
  </div>
</div>
</div>
</div>
<div class="row-fluid">
  <!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div> -->
</div>
<?php
unset($_SESSION['anewdept']);
?>

<script src="../js/maruti.dashboard.js"></script> 

</body>
<style>
   body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>
