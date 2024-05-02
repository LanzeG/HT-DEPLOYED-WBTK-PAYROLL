<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if(isset($_SESSION['OTAPPROVAL'])){

    $mfnotif = $_SESSION['OTAPPROVAL'];
    ?>  
    <script>
    alert("<?php echo $mfnotif;?>");
    </script>
    <?php
    }
    $currentempid = $_SESSION['empID'];
    $results_perpage = 20;
      if (isset($_GET['page'])){
        $page = $_GET['page'];
      } else {
        $page=1;
      }

       if (isset($_POST['searchbydate_btn'])){
 
        $start_from = ($page-1) * $results_perpage;
        $datesearch = $_POST['dphired'];
        $searchquery = "SELECT OVER_TIME.*, employees.* from employees, OVER_TIME  WHERE OVER_TIME.ot_day = '$datesearch' AND employees.emp_id = OVER_TIME.emp_id and employees.emp_id = '$currentempid' ORDER BY OVER_TIME.ot_day DESC LIMIT $start_from,".$results_perpage;  
        $search_result = filterTable($searchquery);
       }else{
        $start_from = ($page-1) * $results_perpage;
        // $datesearch = $_POST['dphired'];
        $searchquery = "SELECT OVER_TIME.*, employees.* from employees, OVER_TIME  WHERE employees.emp_id = OVER_TIME.emp_id and employees.emp_id = '$currentempid' ";
        $search_result = filterTable($searchquery);
      }
    
    $countdataqry = "SELECT COUNT(ot_ID) AS total FROM OVER_TIME WHERE emp_id = '$currentempid'";
    $countdataqryresult = mysqli_query($conn,$countdataqry) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());      
    $row = $countdataqryresult->fetch_assoc();
    $totalpages=ceil($row['total'] / $results_perpage);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Apply Overtime</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../../css/fullcalendar.css" />
  <link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
  <script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
  <script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
  <script type ="text/javascript">
    $( function() {
        $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
        } );
    
  </script>
</head>

<body>
  <header>
    <?php include('navbar2.php'); ?> 
  </header>

<div class="masterdiv">
  <div class="titlediv pt-5" >
    <h3 href="newapplyovertime.php" style = "text-align: center;">OVERTIME APPLICATION</h3>
  </div>
<form action="newapplyovertime.php" method = "post">
  <div class ="control-group">
    <label class="control-label" style= "margin-bottom:10px; margin-top:10px;" >Search by date: </label>
      <div class="controls">
        <div id = "search">
          <div class="srch d-flex">
            <input class ="form-control" style="width:200px" type="text" id="date" name ="dphired" placeholder="Date" value="">
              <button type="submit" class="btn btn-primary" name="searchbydate_btn">
                <i class="fas fa-search text-white"></i>
              </button>
          </div>
                       
<div class="table d-flex align-items-center table-responsive">
  <table class="table table-striped">
     <thead class="table" style="background-color: #2ff29e; color: #4929aa;">       
        <tr>
          <th  style="border-top-left-radius: 10px;">Employee ID</th>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Middle Name</th>
          <th>Shift</th>
          <th>OT in</th>
          <th>OT out</th>
          <th>OT Hours</th>
          <th>Day of OT</th>
          <th>Remarks</th> 
          <th  style="border-top-right-radius: 10px;">Action</th>               
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
        ?>
          <tr class="gradeX">
            <td><?php echo $row1['prefix_ID'];?><?php echo $row1['emp_id'];?></td>
            <td><?php echo $row1['last_name'];?></td>
            <td><?php echo $row1['first_name'];?></td>
            <td><?php echo $row1['middle_name']; ?></td>
            <td><?php echo $row1['shift_SCHEDULE'];?></td>
            <td><?php echo $row1['ot_time'];?></td>
            <td><?php echo $row1['ot_timeout'];?></td>
            <td><?php echo $row1['ot_hours'];?></td>
            <td><?php echo $row1['ot_day'];?></td>
            <td><?php echo $row1['ot_remarks'];?></a></td>
            <td><center><a href="empOTApply.php?id=<?php echo $row1['ot_ID'];?>" class = "btn btn-info btn-mini"><span class="icon"><i class="icon-edit"></i></span> Review</a></center></td>
          </tr>
          <?php endwhile;?>
      </tbody>
    </table>
    </div>          
   </div>
  </div>
</div>

<div class="buttons">   
  <a href ="empAPPLYBeforeOvertime.php?id=<?php echo $currentempid;?>" class = "btn btn-info" style = "float:left; margin-right: 10px; margin-top: 10px;"><span class="icon"><i class="icon-time"></i></span> Apply Overtime</a>
  <a href ="newapplyovertime.php" class = "btn btn-success" style = "float:left; margin-left: 0px; margin-top: 10px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
</div>
</div>
</form>
</div>

<?php
unset($_SESSION['OTAPPROVAL']);
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
body{
  font-family: 'Poppins', sans-serif;
}
.table{
margin-left: 0px;
margin-top: 40px;
width:100%;
table-layout:auto;
}
.table-responsive {
overflow-x: auto;
max-width: 100%;
}
             
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
  flatpickr("#date", {
    dateFormat: "Y-m-d", 
  });
});
</script>
</body>
</html>