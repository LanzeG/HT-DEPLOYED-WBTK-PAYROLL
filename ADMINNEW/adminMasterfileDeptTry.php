<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if(isset($_SESSION['masterfilenotif'])){

$mfnotif = $_SESSION['masterfilenotif'];
?>  
<script>
alert("<?php echo $mfnotif;?>");
</script>
<?php
}

$master = $_SESSION['master'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <title>Manage Department</title>
    <link rel="icon" type="image/png" href="../img/icon1 (3).png">

</head>
<body>
    
<?php
  include('navbarAdmin.php');
  ?>
<div class="title d-flex justify-content-center pt-3">
      <h3>
       MANAGE DEPARTMENTS
      </h3>
    </div>
    
    <div class="d-flex justify-content-end mt-3 mb-1">
    <div class="button">
    <a href="adminADDdepartment.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out"><i class="fas fa-plus mr-2"></i> Add Department</a>  
  <a href="adminMasterfileDeptTry.php" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out"><i class="fas fa-sync-alt"></i></a>
</div>
    </div>
  
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
            <th style="border-top-left-radius: 10px; color: #4929aa;">Deparment ID</th>
            <th style="color: #4929aa;">Deparment Name</th>
            <th class="col-2" style="border-top-right-radius: 10px; color: #4929aa;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
              $results_perpageDEPT = 20;

              if (isset($_GET['page']))
                {
                  $pageDEPT = $_GET['page'];
                } 
              else 
                {
                  $pageDEPT=1;
                }

                $start_fromDEPT = ($pageDEPT-1) * $results_perpageDEPT;
                $searchqueryDEPT ="SELECT * FROM department ORDER BY dept_ID ASC LIMIT $start_fromDEPT,".$results_perpageDEPT;
                $searchresultDEPT= filterTableDEPT($searchqueryDEPT);

               function filterTableDEPT($searchqueryDEPT)
               {

                    $connDEPT = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
                    $filter_ResultDEPT = mysqli_query($connDEPT,$searchqueryDEPT) or die ("failed to query masterfile ".mysql_error());
                    return $filter_ResultDEPT;
               }

                $countdataqryDEPT = "SELECT COUNT(dept_ID) AS total FROM department";
                $countdataqryresultDEPT = mysqli_query($conn,$countdataqryDEPT) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());
                $rowDEPT = $countdataqryresultDEPT->fetch_assoc();
                $totalpagesDEPT=ceil($rowDEPT['total'] / $results_perpageDEPT);
                while($row1DEPT = mysqli_fetch_array($searchresultDEPT)):;
               ?>
                  <tr class="gradeX">
                  <td><?php echo $row1DEPT['dept_prefix_ID'],$row1DEPT['dept_ID'];?></td>
                  <td><?php echo $row1DEPT['dept_NAME'];?></td>
                 
                  <td class="d-flex align-items-center justify-content-center">
                  <a href="adminEDITdepartment.php?id=<?php echo $row1DEPT['dept_NAME']; ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-normal py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" role="button">View</a>
                  <a href="adminDELETEdepartment.php?id=<?php echo $row1DEPT['dept_ID']; ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white font-normal py-2 px-4 rounded-md border border-red-500 hover:border-red-600 transition duration-300 ease-in-out" role="button">Delete</a>

                  </td>
                </tr>
              <?php endwhile;?>


        </tbody>
    </table>
  </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>