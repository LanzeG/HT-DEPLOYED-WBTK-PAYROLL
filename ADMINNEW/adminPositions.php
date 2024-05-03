<!DOCTYPE html>
<html lang="en">
<head>
<title>Manage Positions</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
if (isset($_POST['addPosition'])) {
    $positionName = $_POST['positionName'];

    // Perform the database insertion
    $insertQuery = "INSERT INTO position (position_name) VALUES ('$positionName')";
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        $activityLog = "Added a new position ($positionName)";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName','$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
        ?>
   
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            swal({
              text: "Position inserted successfully",
              icon: "success",
              button: "OK",
             }).then(function() {
                window.location.href = 'adminPositions.php'; // Replace 'your_new_page.php' with the actual URL
            });
        });
     </script>
         <?php
    } else {
        ?><script>
        document.addEventListener('DOMContentLoaded', function() {
            swal({
              text: "Something went wrong.",
              icon: "error",
              button: "Try Again",
            });
        }); </script>
        <?php
    }

    exit();
}
?> 
<title>Manage Positions</title>
<link rel="icon" type="image/png" href="../img/icon1 (3).png">
</head>
<body>
<?php
  include('navbarAdmin.php');
?>
<div class="title d-flex justify-content-center pt-3">
    <h3>
        MANAGE POSITIONS
        <hr>
    </h3>
</div>
<div id="tab1">
    <form method="post" action="" >
        <div class="row">
            <div class="col-lg-12">
                <div class="control-group">
                    <label class="control-label" for="positionName">Position:</label>
                        <div class="controls">
                            <input type="text" class="form-control" name="positionName" id="positionName" required>
                        </div>
                </div>
            </div>
        </div>
     <div class="button text-center">
        <button type="submit" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out mt-3" name="addPosition">Add Position</button>
        <a href="adminPositions.php" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out mt-3 ml-4">
            <span class="icon"><i class="fas fa-sync-alt"></i></span> Refresh
        </a>
    </div>
</div>
</form>
<br>
    <div class="row mt-3 mb-1 d-flex justify-content-end">
        <div class="table d-flex align-items-center table-responsive">
            <table class="table table-striped">
                <thead class="table-striped" style="background-color: #2ff29e; color: #4929aa;">
                    <th style="border-top-left-radius: 10px; color: #4929aa;">Position</th>
                    <th style="border-top-right-radius: 10px; color: #4929aa;">Actions</th>
                </thead>
                <tbody> 
<?php
$searchquery ="SELECT * FROM position";                
$searchresult= filterTable($searchquery);

function filterTable($searchquery){
  $conn = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
  $filter_Result = mysqli_query($conn,$searchquery) or die ("failed to query Holidays".mysql_error());
  return $filter_Result;
}
while($row1 = mysqli_fetch_array($searchresult)):;
?>
<tr class="gradeX">
    <td><?php echo $row1['position_name'];?></td>
    <td class="col-1 text-center">
        <form method="post" action="" class="delete-form ">
            <input type="hidden" name="deletePosition" value="<?php echo $row1['position_name']; ?>">
            <button type="button" class="inline-block bg-red-500 hover:bg-red-600 text-white font-normal py-2 px-4 rounded-md border border-red-500 hover:border-red-600 transition duration-300 ease-in-out delete-button" role="button">Delete</button>

        </form>
    </td>
    <script>
    $(document).ready(function() {
    
        $('.delete-button').click(function() {
            var positionName = $(this).closest('form').find('input[name="deletePosition"]').val();

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this position!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    
                    $(this).closest('form').submit();
                }
            });
        });
    });
    </script>
</tr>
<?php endwhile;?>
</tbody>
</table>
</div>
     
<?php
if (isset($_POST['deletePosition'])) {
    $deletePosition = $_POST['deletePosition'];

 
    $deleteQuery = "DELETE FROM position WHERE position_name = '$deletePosition'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        
        $activityLog = "Deleted position ($deletePosition)";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName','$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    text: "Position deleted successfully",
                    icon: "success",
                    button: "OK",
                }).then(function() {
                    window.location.href = 'adminPositions.php';
                });
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    text: "Something went wrong while deleting",
                    icon: "error",
                    button: "Try Again",
                });
            });
        </script>
        <?php
    }

    exit();
}
?>
<?php
unset($_SESSION['masterfilenotif']);
?>

<div class="row-fluid">
<!-- <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div> -->
</div>

<style>
      body{
  font-family: 'Poppins', sans-serif;
}
</style>
<script type ="text/javascript">

  $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
</body>
</html>