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
   
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
  
    <title>Manage Leave</title>
    <link rel="icon" type="image/png" href="../img/icon1 (3).png">

</head>
<body>
    
<?php
  include('navbarAdmin.php');
  ?>
<div class="title d-flex justify-content-center pt-3">
      <h3>
       MANAGE LEAVE
      </h3>
    </div>
    
    <div class="d-flex justify-content-end mt-3 mb-1">
    <div class="button">
    <a href="adminADDleave.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out"><i class="fas fa-plus mr-2"></i> Add Leave</a>

<a href="adminMasterfileLeave.php" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out"><i class="fas fa-sync-alt"></i></a>

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
            <th style="border-top-left-radius: 10px; color: #4929aa;">Leave Type ID</th>
            <th>Leave Name</th>
            <th class="col-2" style="border-top-right-radius: 10px; color: #4929aa;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
               $results_perpage = 20;

               if (isset($_GET['page'])){

                    $page = $_GET['page'];
               } else {

                    $page=1;
               }

               
               $searchquery ="SELECT * FROM leaves_type ORDER BY lvtype_ID ASC";
               $searchresult= filterTable($searchquery);

               function filterTable($searchquery)
               {

                    $conn1 = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
                    $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query Shifts".mysql_error());
                    return $filter_Result;
               }

              //  $countdataqry = "SELECT COUNT(lvtype_ID) AS total FROM leaves_type";
              //  $countdataqryresult = mysqli_query($conn,$countdataqry) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());
              //  $row = $countdataqryresult->fetch_assoc();
              //  $totalpages=ceil($row['total'] / $results_perpage);
               while($row1 = mysqli_fetch_array($searchresult)):;
               ?>
                  <tr class="gradeX">
                  <td><?php echo $row1['lvtype_prefix_id'],$row1['lvtype_ID'];?></td>
                  <td><?php echo $row1['lvtype_name'];?></td>
                 
                  <td class="d-flex align-items-center justify-content-center">
                  <a href="adminEDITleaves.php?id=<?php echo $row1['lvtype_ID']; ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-normal py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out text-center" role="button">View</a>
<a href="adminDELETEleaves.php?id=<?php echo $row1['lvtype_ID']; ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white font-normal py-2 px-4 rounded-md border border-red-500 hover:border-red-600 transition duration-300 ease-in-out text-center" role="button">Delete</a>

                  </td>
                </tr>
              <?php endwhile;?>


        </tbody>
    </table>
  </div>

  <?php
unset($_SESSION['delnotif']);
unset($_SESSION['masterfilenotif']);
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>