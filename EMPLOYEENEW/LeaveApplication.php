<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if(isset($_SESSION['LEAVEAPPROVAL'])){

    $mfnotif = $_SESSION['LEAVEAPPROVAL'];
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
      $searchquery = "SELECT LEAVES_APPLICATION.*, employees.* from employees, LEAVES_APPLICATION  WHERE LEAVES_APPLICATION.leave_datestart = '$datesearch'  AND employees.emp_id = LEAVES_APPLICATION.emp_id and employees.emp_id = '$currentempid' ORDER BY LEAVES_APPLICATION.leave_datestart DESC LIMIT $start_from,".$results_perpage;  
      $search_result = filterTable($searchquery);
    
    }else{
      $start_from = ($page-1) * $results_perpage;
      // $datesearch = $_POST['dphired'];
      $searchquery = "SELECT LEAVES_APPLICATION.*, employees.* from employees,  LEAVES_APPLICATION  WHERE employees.emp_id = LEAVES_APPLICATION.emp_id and employees.emp_id = '$currentempid' ORDER BY LEAVES_APPLICATION.leave_datestart DESC LIMIT $start_from,".$results_perpage;  
      $search_result = filterTable($searchquery);
    
    }
    $countdataqry = "SELECT COUNT(la_id) AS total FROM LEAVES_APPLICATION WHERE emp_id = '$currentempid'";
    $countdataqryresult = mysqli_query($conn,$countdataqry) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());      
    $row = $countdataqryresult->fetch_assoc();
    $totalpages=ceil($row['total'] / $results_perpage);
  
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
      } );

</script>
</head>
<body>
<header>
  <?php include('navbar2.php'); ?> 
</header>
<div class="masterdiv">

<h3 style = "text-align: center;">LEAVE APPLICATION</h3>
  <form action="LeaveApplication.php" method = "post">
    <div class ="control-group">
      <label class="control-label" style= "margin-bottom:10px; margin-top:10px;" >Search by date: </label>
        <div class="controls">
        <div class="srch d-flex">
            <input class ="form-control" style="width:200px" type="text" id="date" name ="dphired" placeholder="Date" value="">
              <button type="submit" class="btn btn-primary" name="searchbydate_btn">
                <i class="fas fa-search text-white"></i>
              </button>
          </div>

<div class="d-flex align-items-center table-responsive">
  <table class="table table-striped">
     <thead class="table" style="background-color: #2ff29e; color: #4929aa;">
      <tr>
        <th style="border-top-left-radius: 10px;">Employee ID</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Leave Start</th>
        <th>Leave End</th>
        <th>Leave Days</th>
        <th>Status</th> 
        <th style="border-top-right-radius: 10px;">Remarks</th> 
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
        <td><?php echo $row1['leave_datestart'];?></td>
        <td><?php echo $row1['leave_dateend'];?></td>
        <td><?php echo $row1['leave_days'];?></td>
        <td><?php echo $row1['leave_status'];?></td>
  <?php
        $leaveApplicationId = $row1['la_id']; 
        $selectFilePathQuery = "SELECT leave_documents FROM LEAVES_APPLICATION WHERE la_id = $leaveApplicationId";
        $result = mysqli_query($conn, $selectFilePathQuery);
        $row = mysqli_fetch_assoc($result);

 
        if ($row && !empty($row['leave_documents'])) {
          $filePath = $row['leave_documents'];
  
          $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
          $documentExtensions = ['pdf', 'docx'];

          $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

          if (in_array($fileExtension, $imageExtensions)) {
  
            echo '<td><img src="' . $filePath . '" alt="File Preview" style="max-width: 100px; max-height: 100px;"></td>';
          } elseif (in_array($fileExtension, $documentExtensions)) {
    
            echo '<td><a href="' . $filePath . '" target="_blank">View Document</a></td>';
          } else {
       
            echo '<td><a href="' . $filePath . '" target="_blank">View File</a></td>';
          }

        } else {
         echo '<td>No file available for preview.</td>';
        }
?>
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
    <a href ="empLEAVEApplication.php?id=<?php echo $currentempid;?>" class = "btn btn-primary" style = "float:left; margin-left: 0px; margin-top: 10px;"><span class="icon"><i class="icon-plus"></i></span> Apply Leave</a>
    <a href ="LeaveApplication.php" class = "btn btn-success" style = "float:left; margin-left: 10px; margin-top: 10px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
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
  border-radius: 10px; /* You can adjust the value to control the amount of rounding */
  border: 1px solid #ccc; /* Optional: You can add a border for further styling */
  padding: 15px; /* Optional: Add padding to the box for better appearance */
}
@media (max-width: 768px) {
  .widget-box {
    margin: auto;
    margin-top: 70px; /* This will center the widget-box */
  }

  .widget-title li {
    /* Adjust the styles for list items inside widget-title at smaller screens */
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
     dateFormat: "Y-m-d", // Adjust the date format as needed
   });
 });
</script>
</body>
</html>