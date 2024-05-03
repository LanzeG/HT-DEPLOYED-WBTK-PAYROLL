<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
    
    $results_perpage = 20;
    
       if (isset($_GET['page'])){
        $page = $_GET['page'];
      } else {
        $page=1;
      }
    
    $currentempid = $_SESSION['empID'];
    
    $userIdpage  = $_SESSION['empID'];
    
    $pageViewed = basename($_SERVER['PHP_SELF']);
    $pageInfo = pathinfo($pageViewed);
    
    // Get the filename without extension
    $pageViewed1 = $pageInfo['filename'];
    
    // Log the page view
    logPageView($conn, $userIdpage, $pageViewed1);
    
    if (isset($_POST['searchbydate_btn'])){
      $start_from = ($page-1) * $results_perpage;
       $datefrom = $_POST['dpfrom'];
       $dateto = $_POST['dpto'];
       $searchquery = "SELECT * FROM dtr,employees WHERE dtr.emp_id = '$currentempid' AND dtr.emp_id = employees.emp_id AND DATE(dtr_day) BETWEEN '$datefrom' and '$dateto' ORDER BY dtr_day DESC LIMIT $start_from,10"; 
       $search_result = filterTable($searchquery);
    
    } else  {
      $start_from = ($page-1) * $results_perpage;
      $searchquery ="SELECT * FROM dtr,employees WHERE dtr.emp_id = '$currentempid' AND dtr.emp_id = employees.emp_id ORDER BY dtr_day DESC LIMIT $start_from,".$results_perpage; 
      $search_result = filterTable($searchquery);
      }
    
    $countdataqry = "SELECT COUNT(emp_id) AS total FROM dtr where emp_id = '$currentempid'";
    $countdataqryresult = mysqli_query($conn,$countdataqry) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());      
    $row = $countdataqryresult->fetch_assoc();
    $totalpages=ceil($row['total'] / $results_perpage);
    // echo "Generated Query: $searchquery";
    
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>Apply Leave</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!--<link rel="stylesheet" href="../../css/bootstrap.min.css" />-->
<!--<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />-->
<!--<link rel="stylesheet" href="../../css/fullcalendar.css" />-->
<!--<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">-->
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<script type ="text/javascript">
   $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
</head>

<body>

<div class="masterdiv">
<div class="titlediv pt-5" > 
<h3 href="newapplyovertime.php" style = "text-align: center;">ATTENDANCE RECORD</h3>         
</div>
 
<div class ="control-group">
  <label class="control-label" style= "margin-bottom:10px; margin-top:10px;">Search by date: </label>
    <div class="controls">
      <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
          <div class="d-flex flex-row flex-wrap gap-1">
            <div>
            <input class ="span8 form-control"  type="text" id="date" name ="dpfrom" placeholder="From"value="<?php echo isset($_POST['dpfrom']) ? $_POST['dpfrom'] : ''; ?>">
            </div>
            <div>
            <input class ="span8 form-control" type="text" id="date" name ="dpto" placeholder="To" value="<?php echo isset($_POST['dpto']) ? $_POST['dpto'] : ''; ?>">

            </div>
            <div>
            <button button type="submit" class = "btn btn-lg btn-primary" name ="searchbydate_btn">
              <i class="fas fa-search text-white"></i>
                </button>
            </div>
          </div>
      </form>
    </div>                 
</div>        

<div class=" align-items-center table-responsive">
  <table class="table table-striped">
     <thead class="table" style="background-color: #2ff29e; color: #4929aa;">   
      <tr>
        <th style="border-top-left-radius: 10px;">Employee ID</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th style="border-top-right-radius: 10px;">Day of Record</th>
      </tr>
    </thead>
<tbody> 
  <?php
    function filterTable($searchquery){
      $conn1 = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
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
      <td><?php echo $row1['in_morning'];?></td>
      <td><?php echo $row1['out_afternoon'];?></td>
      <td><?php echo $row1['DTR_day'];?></td>
    </tr>
    <?php endwhile;?>
  </tbody>
</table>
    <div class="buttons">
    <a href ="empNEWAttendance.php" class = "btn btn-success" style = "float:left; margin-right: 10px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
    </div>
  </div>
</form>
</div>
<?php
  $nextPage = $page + 1;
  $nextPageLink = $_SERVER['PHP_SELF'] . "?page=" . $nextPage;
?> 
<tfoot>
  <tr>
    <td colspan="12">
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
          <?php
            if ($page > 1) {
              echo "<li class='page-item'><a class='page-link' href=" . $_SERVER['PHP_SELF'] . "?page=" . ($page - 1) . ">&laquo; Previous</a></li>";
            }

            $startPage = max(1, $page - 2);
            $endPage = min($totalpages, $page + 2);

            for ($i = $startPage; $i <= $endPage; $i++) {
              echo "<li class='page-item";
              if ($i == $page) {
                echo " active";
              }
              echo "'><a class='page-link' href=" . $_SERVER['PHP_SELF'] . "?page=" . $i . ">" . $i . "</a></li>";
            }

            if ($page < $totalpages) {
              echo "<li class='page-item'><a class='page-link' href=" . $nextPageLink . ">Next &raquo;</a></li>";
            }
          ?>
        </ul>
      </nav>
    </td>
  </tr>
</tfoot>

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
body{
  font-family: 'Poppins', sans-serif;
}
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