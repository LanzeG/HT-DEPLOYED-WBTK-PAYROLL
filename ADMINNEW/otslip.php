<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
$adminId = $_SESSION['adminId'];

if (isset($_POST['print_btn'])){
  $_SESSION['pregisterpayperiod'] = $_POST['payperiod'];
  $payperiod = $_POST['payperiod'];
  $pperiodquery = "SELECT * FROM payperiods WHERE pperiod_range = '$payperiod'";
  $pperiodexecquery = mysqli_query($conn, $pperiodquery) or die ("FAILED TO SET PAY PERIOD ".mysqli_error($conn));
  $pperiodarray = mysqli_fetch_array($pperiodexecquery);

      if ($pperiodarray){
        $_SESSION['payperiodfrom'] = $pperiodarray['pperiod_start'];
        $_SESSION['payperiodto'] = $pperiodarray['pperiod_end'];
        $_SESSION['payperiodrange'] = $pperiodarray['pperiod_range'];
      }
  
  header("Location:./otpdfnew.php");

} 



?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>Overtime Slip</title>
<link rel="icon" type="image/png" href="../img/icon1 (3).png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<!-- <link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" /> -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
</head>

<body>

<!--Header-part-->

<?php
INCLUDE ('NAVBARadmin.php');
?>



<div id="content">

<div class="title text-center pt-2">
  <span class="span6">
        <h3>Overtime Slip</h3>
      </span>

  <div class="container-fluid">
    <div class = "row-fluid">
    <div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              
            </ul>
          </div>
  
    <div class ="row-fluid">
      <div class="span4">
      </div>
    </div>

    <div class = "row-fluid"><!--ROW-->

      <div class="span4">
      </div>
      <div class="span5">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
       
          </div>

          <div class="widget-content nopadding">

            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="form-horizontal" target="_blank">
            
      <?php
      $payperiodsquery = "SELECT * FROM payperiods";
      $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY ".mysqli_error($conn));
      ?>
            <div class = "control-group col-12 col-sm-4 card shadow mx-auto my-2 p-3 mt-5">
                
                 <label class="control-label">Select Payroll Period: </label>
                      <div class="controls ">
                        <select name ="payperiod" class= "form-select col-12 col-md-12 mx-auto">
                      
                          <option></option>
                          <?php  while($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)):;?>
                          <option><?php echo $payperiodchoice['pperiod_range'];?></option>
                          <?php endwhile;?>
                          
                        </select>
                        <div class = "control-group pt-3  text-center">
                        <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out printbtn" name="print_btn">Submit</button>

                      </div>
                    
            </div>
            </form>
          </div>
        </div>
      </div>
    </div><!-- ROW -->
    

</div><!--CONTAINER-->
     
<?php
unset($_SESSION['masterfilenotif']);
?>

</div>

<div class="row-fluid">
  <!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div> -->
</div>

<script src="../js/maruti.dashboard.js"></script> 
<script src="../js/excanvas.min.js"></script> 
<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/maruti.js"></script> 
</body>
<style>
  body{
  font-family: 'Poppins', sans-serif;
}
</style>
</html>

