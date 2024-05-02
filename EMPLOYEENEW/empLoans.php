<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$results_perpage = 20;
  if (isset($_GET['page'])){
    $page = $_GET['page'];
  } else {
     $page=1;
  }

$currentempid = $_SESSION['empID'];

$userIdpage  = $_SESSION['empID'];

$searchquery ="SELECT * FROM loans JOIN employees ON loans.emp_id = employees.emp_id  WHERE employees.emp_id = $currentempid";
$searchresult= filterTable($searchquery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Employee Records</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<script type ="text/javascript">
  $( function() {
      $( "#datepickerfrom" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  $( function() {
      $( "#datepickerto" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>

<body>

<!--Header-part-->

<?php
INCLUDE ('navbar2.php');
?>

<div id="content">
    <div class="title d-flex justify-content-center pt-3">
        <h3>MY LOANS</h3>
    </div>
    <hr>
    <br>
 
    <div class="d-flex align-items-center table-responsive">
  <table class="table table-striped">
     <thead class="table" style="background-color: #2ff29e; color: #4929aa;">
                  <tr>
                    <th style="border-top-left-radius: 10px;">Loan ID</th>
                    <th>Employee ID</th>
                    <th>Loan Org</th>
                    <th>Loan Type</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Loan Amount</th>
                    <th>Loan Balance</th>
                    <th>Monthly Deduction</th>
                    <th>Number of Pays Left</th>
                    <th>Status</th>
                    <th style="border-top-right-radius: 10px;">Added by</th>
                </tr>
              </thead>
              <tbody> 
                </div>
         
               <?php
               function filterTable($searchquery){
                $conn1 = mysqli_connect("localhost:3307","root","","masterdb");
                $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
                return $filter_Result;
               }
               
               while($row1 = mysqli_fetch_array($searchresult)):;
               ?>
                  <tr class="gradeX">
                  <td><?php echo $row1['loanidno'];?></td>
                  <td><?php echo $row1['emp_id'];?></td>
                  <td><?php echo $row1['loanorg'];?></td>
                  <td><?php echo $row1['loantype'];?></td>
                  <td><?php echo $row1['emplastname'];?></td>
                  <td><?php echo $row1['empfirstname'];?></td>
                  <td><?php echo $row1['start_date'];?></td>
                  <td><?php echo $row1['end_date'];?></td>
                  <td><?php echo $row1['loan_amount'];?></td>
                  <td><?php echo $row1['loan_balance'];?></td>
                  <td><?php echo $row1['monthly_deduct'];?></td>
                  <td><?php echo $row1['no_of_pays'];?></td>
                  <td><?php echo $row1['status'];?></td>
                  <td><?php echo $row1['adminname'];?></td>
                </tr>
              <?php endwhile;?>
              </form>
               
              </tbody>
              
            </table>

            <div class = "span9">
                

               <div class = "pagination alternate" style="float:right;">
               
               </div>
               
          </div>
        </div>
      </div>
      <a href ="empLoans.php" class = "btn btn-success" style = "float:left; margin-left: 4px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
    </div>
  </div>
</div>
<?php

?>

<script src="../../js/maruti.dashboard.js"></script> 
<script src="../../js/excanvas.min.js"></script> 

<script src="../../js/bootstrap.min.js"></script> 
<script src="../../js/jquery.flot.min.js"></script> 
<script src="../../js/jquery.flot.resize.min.js"></script> 
<script src="../../js/jquery.peity.min.js"></script> 
<script src="../../js/fullcalendar.min.js"></script> 
<script src="../../js/maruti.js"></script> 
</body>
</html>
<style>
.widget-box {
  border-radius: 10px; 
  border: 1px solid #ccc; 
  padding: 15px;
}
@media (max-width: 768px) {
  .widget-box{
    margin-top:70px;
  }
  .span2 {
    margin: auto;
    margin-top: 70px; 
  }

  .span2 {
 
    display: block;
    margin-bottom: 10px;
  }
  
}
body{
  font-family: 'Poppins', sans-serif;
}
</style>