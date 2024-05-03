<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$master = $_SESSION['master'];

if (isset($_POST['searchbydate_btn'])){
$datesearch = $_POST['payPeriod'] ?? '';
if(empty($datesearch)){
  $searchquery = "SELECT * FROM payperiods";
$search_result = filterTable($searchquery);


}else{
$searchquery = "SELECT * FROM payperiods WHERE pperiod_range = '$datesearch'";
$search_result = filterTable($searchquery);
}
}else {

$searchquery = "SELECT * FROM payperiods";
$search_result = filterTable($searchquery);

}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
  <script type ="text/javascript">

  $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
    <title>Manage Payroll Periods</title>
    
    <link rel="icon" type="image/png" href="../img/icon1 (3).png">

</head>
<body>
    
<?php
  include('navbarAdmin.php');
  ?>
<div class="title d-flex justify-content-center pt-3">
      <h3>
       MANAGE PAYROLL PERIODS
      </h3>
    </div>
    
  
    
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" >
  <div class="row py-3">
  <div class="col-8 col-sm-2">

    <select name="payPeriod" id="payPeriod" class="form-select">
            <label for="payPeriod" class="form-label">Select Payroll Period</label>
            <option selected disabled>Select Payroll Period</option>
            <?php
                            $query = "SELECT payperiod_ID, pperiod_range FROM payperiods";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query failed: " . mysqli_error($conn));
                            }

                            $selectedPayPeriod = isset($_POST['payPeriod']) ? $_POST['payPeriod'] : '';

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $pperiodID = $row['payperiod_ID'];
                                    $pperiodRange = $row['pperiod_range'];

                                    // Output each option with 'selected' attribute if it matches the submitted value
                                    echo '<option value="' . $pperiodRange . '" ' . ($selectedPayPeriod == $pperiodID ? 'selected' : '') . '>' . $pperiodRange . '</option>';
                                }
                            } else {
                                echo "No pay periods found.";
                            }
                            ?>
        </select>

    </div>
    <div class="col-12 col-sm-4" >
    <button type="submit" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" name="searchbydate_btn"><i class="fas fa-search mr-2"></i></button>

<a href="adminADDpayrollperiod.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out"><i class="fas fa-plus mr-2"></i> Add Payroll Period</a>

<a href="adminpayrollperiods.php" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out"><i class="fas fa-sync-alt"></i></a>

</div>
    
    </div>
  </div>
       

      
        
    

    </form>
  

 
    <div class="row mt-3 mb-1 d-flex justify-content-end">
    <div class="table d-flex align-items-center table-responsive">
<table class="table table-striped">
<thead class="table-striped" style="background-color: #2ff29e; color: #4929aa;">
<style>
    body{
  font-family: 'Poppins', sans-serif;
}
  tbody tr {
    display: table-row;
    vertical-align: middle; /* You can change this to 'top' or 'bottom' based on your preference */
  }
</style>
<tr>
                  <th style="border-top-left-radius: 10px; color: #4929aa;">Payroll Period ID</th>
                  <th>Payroll Period</th>
                  <th>Period Start</th>
                  <th>Period End</th>
                  <th>Period Days</th>
                  <th style="border-top-right-radius: 10px; color: #4929aa;">Payroll Year</th>
                  
                </tr>
        </thead>
        <tbody>
        <?php

              

               function filterTable($searchquery)
               {

                    $conn1 = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
                    $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
                    return $filter_Result;
               }

               
               while($row1 = mysqli_fetch_array($search_result)):;
               ?> 
                  <tr class="gradeX">
                  <td><?php echo $row1['payperiod_ID'];?></td>
                  <td><?php echo $row1['pperiod_range'];?></td>
                  <td><?php echo $row1['pperiod_start'];?></td>
                  <td><?php echo $row1['pperiod_end'];?></td>
                  <td><?php echo $row1['payperiod_days'];?></td>
                  <td><?php echo $row1['pperiod_year'];?></td>
                  <!--<td><center><a href = "adminPRINTtimesheet.php?id=<?php echo $row1['emp_id']; ?>" class = "btn btn-info btn-mini" target = "_blank"><span class="icon"><i class="icon-print"></i></span> Print</a>
                    -->
                   
                </tr>
              <?php endwhile;?>


        </tbody>
    </table>
  

  <?php
unset($_SESSION['delnotif']);
unset($_SESSION['masterfilenotif']);
?>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->
</body>

</html>