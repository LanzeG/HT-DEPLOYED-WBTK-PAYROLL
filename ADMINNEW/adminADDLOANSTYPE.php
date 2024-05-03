<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if (isset($_GET['refresh'])) {
  header("Location: adminLOANSTYPE.php");
  exit(); 
}

if (isset($_POST['addloantype'])) {
  $loantype = $_POST['loantype'];
  $loanorg = $_POST['loanorg'];

  // Perform the database insertion
  $insertQuery = "INSERT INTO loantype (loantype, loanorg) VALUES ('$loantype', '$loanorg')";
  $insertResult = mysqli_query($conn, $insertQuery);

  if ($insertResult) {
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        swal({
          text: "Loan Type inserted successfully",
          icon: "success",
          button: "OK",
        }).then(function() {
          window.location.href = 'adminaddloanstype.php';
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        swal({
          text: "Something went wrong.",
          icon: "error",
          button: "Try Again",
        });
      });
    </script>
    <?php
  }

  // Prevent further execution to avoid duplicate alerts
  exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Add Loans</title>
    <link rel="icon" type="image/png" href="../img/icon1 (3).png">


    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" -->
        <!-- integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    
    <!-- Include jQuery before SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!-- SweetAlert script -->
    
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
</head>
<body>

<?php
    include('navbarAdmin.php');
    ?>
   <div class="content containter pt-4">

        <div class="card shadaw m-3 p-3" style="border-radius: 10px;">
            <form action="" method="post">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 ">
                        <label for="loantype" class="form-label" required>Loan Type</label>
                        <input style="border-radius: 5px;" type="text"  name="loantype" class="form-control" placeholder="Enter loan type" required>

                    </div>
                    <div class="col-lg-6 col-sm-12">
                    <label for="loantype" class="form-label" required>Loan Organization</label>
                    <input style="border-radius: 5px;" type="text"  name="loanorg" class="form-control" placeholder="Enter loan org" required>

                    </div>
                </div>
                <div class="button d-flex justify-content-center mt-4">
                <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-green-500 hover:border-green-600 transition duration-300 ease-in-out" name="addloantype">Add Loan Type</button>


                </div>

            </form>
        </div>


        <div class="row mt-3 mb-1 d-flex justify-content-end">
    <div class="table d-flex align-items-center table-responsive">
<table class="table table-striped">
<thead class="table-striped" style="background-color: #2ff29e; color: #4929aa;">
<style>
  tbody tr {
    display: table-row;
    vertical-align: middle; /* You can change this to 'top' or 'bottom' based on your preference */
  }
</style>
                  <tr>
                  <th style="border-top-left-radius: 10px;">Loan Type ID</th>
                  <th>Loan Type</th>
                  <th>Loan Organization</th>
                  <th style="border-top-right-radius: 10px;">Actions</th>
                 

                  </tr>
                </thead>
                <tbody>


                 <?php
                $searchquery ="SELECT * FROM loantype";                
                $searchresult= filterTable($searchquery);



                  function filterTable($searchquery)
                  {

                    $conn = mysqli_connect("localhost", "u387373332_masterdb", "WBTKpayrollportal1234@", "u387373332_masterdb");
                    $filter_Result = mysqli_query($conn, $searchquery) or die("failed to query masterfile " . mysql_error());
                    return $filter_Result;
                  }
                  while ($row1DEPT = mysqli_fetch_array($searchresult)):
                    ;
                    ?>
                    <tr class="gradeX">
                      <td>
                        <?php echo $row1DEPT['loantypeid']; ?>
                      </td>
                      <td>
                        <?php echo $row1DEPT['loantype']; ?>
                      </td>
                      <td>
                        <?php echo $row1DEPT['loanorg']; ?>
                        <td class="text-center">
    <form method="post" action="">
        <input type="hidden" name="deleteLoantypeID" value="<?php echo $row1DEPT['loantypeid']; ?>">
        <button type="button" class="inline-block bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md border border-red-500 hover:border-red-600 transition duration-300 ease-in-out delete-button">Delete</button>

    </form>
</td>
<script>
$(document).ready(function() {
  
    $('.delete-button').click(function() {
        var positionName = $(this).closest('form').find('input[name="deleteLoantypeID"]').val();

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this loan type!",
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





      <?php
// if (isset($_POST['deleteLoantypeID'])) {
//     $deleteLoantypeID = $_POST['deleteLoantypeID'];

//     $deleteQuery = "DELETE FROM loantype WHERE loantypeid = '$deleteLoantypeID'";
//     $deleteResult = mysqli_query($conn, $deleteQuery);

//     if ($deleteResult) {
//         echo "Position deleted successfully";
//     } else {
//         echo "Something went wrong while deleting";
//     }

//     exit();
// }
?>
                
                      <!-- <td><center><a href = "adminEDITPAGIBIGLoans.php?id=<?php echo $row1DEPT['emp_id'] ?>" class = "btn btn-info btn-mini"><span class="icon"><i class="icon-eye-open"></i></span> View</a>
                    <a href = "adminDELETEMasterfileDept.php?id=<?php echo $row1DEPT['emp_id']; ?>" class = "btn btn-danger btn-mini"><span class="icon"><i class="icon-trash"></i></span> Delete</a></center></td> -->
                    </tr>
                    
                  <?php endwhile; ?>
                </tbody>
   
</table>
</div>
  <style>
    body{
  font-family: 'Poppins', sans-serif;
}
  </style>

   </div>

   <?php
  unset($_SESSION['masterfilenotif']);
  ?>


<div class="row-fluid">
    <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

</body>
</html>

<?php
if (isset($_POST['deleteLoantypeID'])) {
    $deleteLoantypeID = $_POST['deleteLoantypeID'];

 
    $deleteQuery = "DELETE FROM loantype WHERE loantypeid = '$deleteLoantypeID'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        
        // $activityLog = "Deleted loantype ($deleteLoantypeID)";
        // $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, adminname, activity,log_timestamp) VALUES ('$adminId', '$adminFullName','$activityLog', NOW())";
        // $adminActivityResult = mysqli_query($conn, $adminActivityQuery);
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    text: "Loan Type deleted successfully",
                    icon: "success",
                    button: "OK",
                }).then(function() {
                    window.location.href = 'adminADDLOANSTYPE.php';
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
