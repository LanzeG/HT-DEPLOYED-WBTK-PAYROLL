

<!DOCTYPE html>
<html lang="en">
<head>
<title>Activity Logs</title>
<link rel="icon" type="image/png" href="../img/icon1 (6).png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

$adminId = $_SESSION['adminId'];

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$recordsPerPage = 10; // You can adjust this number based on your preference

$startFrom = ($page - 1) * $recordsPerPage;

$query = "SELECT * FROM adminactivity_log ORDER BY log_id DESC LIMIT $startFrom, $recordsPerPage";
$result = mysqli_query($conn, $query);

$countQuery = "SELECT COUNT(*) as total FROM adminactivity_log";
$countResult = mysqli_query($conn, $countQuery);
$countRow = mysqli_fetch_assoc($countResult);
$totalRecords = $countRow['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<?php
include('navbarAdmin.php');
?>

    <div id="content">
     <div class="title d-flex justify-content-center pt-3">
            <h3 style="margin-top:10px;">
            ACTIVITY LOGS
            </h3>
        </div>
    <hr>
    <br>
<div class="row mt-3 mb-1 d-flex justify-content-end">
    <div class="table d-flex align-items-center table-responsive">
        <table class="table table-striped">
            <thead class="table" style="background-color: #2ff29e; color: #4929aa;">
                <tr>
           
                <th style="border-top-left-radius: 10px;">LOG ID</th>
                <th >Employee ID</th>
                <th>Employee Name</th>
                <th>Activity</th>
                <th style="border-top-right-radius: 10px;">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the paginated records and display them in the table rows
            while ($row1 = mysqli_fetch_array($result)) {
            ?>
                <tr class="gradeX">
                    <td class=col-1><?php echo $row1['log_id']; ?></td>
                    <td class=col-2><?php echo $row1['emp_id']; ?></td>
                    <td class=col-2><?php echo $row1['adminname']; ?></td>
                    <td class="activity-column"><?php echo $row1['activity']; ?></td> 
                    <td class="col-2"><?php echo $row1['log_timestamp']; ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
         </div>
            </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        $totalPages = ceil($totalRecords / $recordsPerPage);
                        
                        $currentPage = isset($_GET['page']) ? max(1, min($_GET['page'], $totalPages)) : 1;
                        $pageRange = 2; 

                        function generatePageLink($pageNumber, $text = null) {
                            $text = $text ?? $pageNumber; 
                            $url = "adminACTIVITYLogs.php?page=$pageNumber";
                            return "<a class='page-link' href='$url'>$text</a>";
                        }

                        if ($currentPage - $pageRange > 2) {
                            echo "<li class='page-item'>" . generatePageLink(1) . "</li><li class='page-item disabled'><span class='page-link'>...</span></li>";
                        }

                        for ($i = max(1, $currentPage - $pageRange); $i <= min($totalPages, $currentPage + $pageRange); $i++) {
                            echo "<li class='page-item'>" . generatePageLink($i) . "</li>";
                        }

                        if ($currentPage + $pageRange < $totalPages - 1) {
                            echo "<li class='page-item disabled'><span class='page-link'>...</span></li><li class='page-item'>" . generatePageLink($totalPages) . "</li>";
                        }
                        ?>
                    </ul>
                </nav>
            </div>
             <style>
  
        body{
  font-family: 'Poppins', sans-serif;
  background-image: #ffff;
}
    </style>
</div>
</body>
</html>


  