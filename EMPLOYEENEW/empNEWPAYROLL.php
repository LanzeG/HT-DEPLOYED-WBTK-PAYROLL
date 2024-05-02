<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

include('navbar2.php'); 

if(isset($_SESSION['masterfilenotif'])){

    $mfnotif = $_SESSION['masterfilenotif'];
    ?>  
    <script>
    alert("<?php echo $mfnotif;?>");
    </script>
    <?php
    }
    
    $currentempid = $_SESSION['empID'];
    $userIdpage  = $_SESSION['empID'];
    $pageViewed = basename($_SERVER['PHP_SELF']);
    $pageInfo = pathinfo($pageViewed);
    
    // Get the filename without extension
    $pageViewed1 = $pageInfo['filename'];
    // Log the page view
    logPageView($conn, $userIdpage, $pageViewed1);
    
    // //total number of rows
    $pagecountqry = "SELECT COUNT(emp_id) from PAY_PER_PERIOD WHERE emp_id = '$currentempid'";
    $pagecountres = mysqli_query($conn,$pagecountqry) or die ("Failed to count pages ".mysqli_error($conn));
    $pagecounttotal = mysqli_fetch_row($pagecountres);
    $rows = $pagecounttotal[0];
    
    //number of results per page
    $page_rows = 20;
    //page number of last page
    $lastpage = ceil($rows/$page_rows);
    //This makes sure $lastpage cant be less than 1
    if ($lastpage < 1){
      $lastpage=1;
    }
    
    $pagenum = 1;
    //get pagenum from URL
    if (isset($_GET['pn'])){
      $pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
    }
    // makes sure page number isnt below 1 or more than $lastpage
    if ($pagenum < 1){
      $pagenum = 1;
    }else if ($pagenum > $lastpage){
      $pagenum = $lastpage;
    }
    //This set range of rows to query for $pagenum
    $limit = "LIMIT "  .($pagenum-1)* $page_rows . ',' .$page_rows;
    
    //What page and number of pages
    $pageline1 = "Page <b>$pagenum</b> of <b>$lastpage</b>";
    //pagectrls
    $paginationCtrls = '';
    //If more than 1 page
    if ($lastpage !=1){
      /*Check if on page 1. If yes, previous link not needed. If not, we generate links to the first page and to the previos page. */
      if ($pagenum>1){
          $previous = $pagenum-1;
          $paginationCtrls .= '<li><a href="'.$_SERVER['PHP_SELF'].'?id='.$idres.'&pn='.$previous.'">Prev</a></li>';
          //number links left
          for ($i = $pagenum-4; $i < $pagenum; $i++){
    
            if($i > 0){
              $paginationCtrls .= '<li><a href="'.$_SERVER['PHP_SELF'].'?id='.$idres.'&pn='.$i.'">'.$i.'</a></li>';
            }
    
          }
      }
    
      //target page
        $paginationCtrls .='<li class = "active"><a href="'.$_SERVER['PHP_SELF'].'"</a>'.$pagenum.'</li>';
      //render clickable number links appear on right target page
        for ($i = $pagenum+1; $i <= $lastpage; $i++){
          $paginationCtrls .='<li><a href="'.$_SERVER['PHP_SELF'].'?id='.$idres.'&pn='.$i.'">'.$i.'</a></li>';
          if ($i >= $pagenum+4){
            break;
          }
        }
    
        if ($pagenum != $lastpage) {
            $next = $pagenum + 1;
            $paginationCtrls .= '<li><a href = "'.$_SERVER['PHP_SELF'].'?id='.$idres.'&pn='.$next.'">Next</a></li> ';
        }
    }
    
    
    if (isset($_POST['pperiod_btn'])){
    
       $payperiod = $_POST['payperiod'];
       
       $searchquery = "SELECT * FROM employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$currentempid' AND PAY_PER_PERIOD.pperiod_range = '$payperiod' ORDER BY pperiod_range DESC $limit";
       $search_result = filterTable($searchquery);
    
    } else  {
      $searchquery = "SELECT * from employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$currentempid' ORDER BY PAY_PER_PERIOD.pperiod_range DESC $limit";  
      $search_result = filterTable($searchquery);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>Apply Leave</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<script type ="text/javascript">
$( function() {
  $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
});
  
</script>
</head>
<body>
<div class="masterdiv">
<div class="titlediv pt-5" >
<h3 style = "text-align: center;">PAYROLL RECORDS</h3>
</div>
<div class="control-group">
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <?php
    $payperiodsquery = "SELECT * FROM payperiods";
    $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die("FAILED TO EXECUTE PAYPERIOD QUERY " . mysqli_error($conn));
    ?>
    <label class="control-label" style="margin-bottom: 10px; margin-top: 10px;">Select Payroll Period: </label>
    <div class="controls" style="display: flex; align-items: center;">
      <select name="payperiod" class="form-select" style="width: 250px; margin-right: 10px;">

        <option></option>
        <?php while ($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)) :
          $selected = '';
          if (isset($_POST['payperiod']) && $_POST['payperiod'] == $payperiodchoice['pperiod_range']) {
            $selected = 'selected';
          }
        ?>

          <option <?php echo $selected; ?>><?php echo $payperiodchoice['pperiod_range']; ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit" class="btn btn-success printbtn" name="pperiod_btn">Go</button>
    </div>
  </form>
</div>



<div class="d-flex align-items-center table-responsive">
  <table class="table table-striped">
     <thead class="table" style="background-color: #2ff29e; color: #4929aa;">
      <tr>
        <th style="border-top-left-radius: 10px;">Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Pay Period</th>
        <th>Basic Pay</th>
        <th>Philhealth</th>
        <th>GSIS</th>
        <th>PAG-IBIG/HDMF</th>
        <th>Loans</th>
        <th>withholding Tax</th>
        <th>undertime</th>
        <th>Absences</th>
        <th>Total Deduct</th>
        <th style="border-top-right-radius: 10px;">Net Pay</th>                 
      </tr>
    </thead>
  <tbody> 
<?php

function filterTable($searchquery){
     $conn1 = mysqli_connect("localhost:3307","root","","masterdb");
     $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
     return $filter_Result;
}
  while($row1 = mysqli_fetch_array($search_result)):;
  $basepay = $row1['reg_pay'];
  $philhealth = $row1['philhealth_deduct'];
  $gsis = $row1['sss_deduct'];
  $pagibig = $row1['pagibig_deduct'];
  $loans= $row1['loan_deduct'];
  $pagibigloan = $row1['pagibigloan_deduct'];
  $withholdingtax = $row1['tax_deduct'];
  $absences = $row1['absences'];
  $undertime = $row1['undertimehours'];
  $totaldeduct = $row1['total_deduct'];

  $netpay = $row1['net_pay'];
  
?>
   <tr class="gradeX">
   <td><?php echo $row1['last_name'];?></td>
   <td><?php echo $row1['first_name'];?></td>
   <td><?php echo $row1['middle_name']; ?></td>
   <td><?php echo $row1['pperiod_range'];?></td>
   <td><?php echo $basepay;?></td>
   <td><?php echo $philhealth; ?></td>
   <td><?php echo $gsis; ?></td>
   <td><?php echo $pagibig; ?></td>
   <td><?php echo $loans; ?></td>
   <td><?php echo $withholdingtax; ?></td>
   <td><?php echo $undertime; ?></td>
   <td><?php echo $absences; ?></td>
   <td><?php echo $totaldeduct; ?></td>
   <td><?php echo $netpay; ?></td>

 </tr>
 <?php endwhile;?>
</tbody>
</table>
  <div class = "pagination alternate" style="float:right;">
  </div>
</div>          
</div>
 </div>
 </div>
<div class="buttons">
   <a href ="empNEWPAYROLL.php" class = "btn btn-success" style = "float:left; margin-right: 10px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
</div>
</div>
</form>
</div>

<?php
unset($_SESSION['masterfilenotif']);
?>

<script src="../../js/maruti.dashboard.js"></script> 
<script src="../../js/excanvas.min.js"></script> 

<script src="../../js/bootstrap.min.js"></script> 
<script src="../../js/jquery.flot.min.js"></script> 
<script src="../../js/jquery.flot.resize.min.js"></script> 
<script src="../../js/jquery.peity.min.js"></script> 
<script src="../../js/fullcalendar.min.js"></script> 
<script src="../../js/maruti.js"></script> 

<style>
  .widget-box {
  border-radius: 10px;
  border: 1px solid #ccc;
  padding: 15px;
}
@media (max-width: 768px) {
  .widget-box {
    margin: auto;
    margin-top: 70px;
  }

  .widget-title li {
 
    display: block;
    margin-bottom: 10px;
  }

}

.table{
                   
    margin-left: 0px;

    width:100%;
    table-layout:auto;
}
    .table-responsive {
    overflow-x: auto;
    max-width: 100%;
}
body{
  font-family: 'Poppins', sans-serif;
}
</style>
</body>
</html>