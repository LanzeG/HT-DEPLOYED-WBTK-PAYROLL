<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
$query = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    
</head>
<body>

<?php include('navbarAdmin.php'); ?>

<div id="content">
    <div class="title d-flex justify-content-center pt-3">
        <h3>ALL NOTIFICATIONS</h3>
    </div>
    <hr>
    <br>
    <div class="container">
  
        <div class="mb-2" style="margin-left: 2em;">
            <div class="d-flex align-items-center">
                <div class="smaller-content flex-grow-1 mr-2">
                    <select class="form-control" id="sort_by_select">
                        <option value="">Sort By</option>
                        <option value="Issue">Issues</option>
                        <option value="Leave">Leaves</option>
                        <option value="Overtime">Overtimes</option>
                    </select>
                </div>
                <div class="smaller-content">
                    <button class="btn btn-success" id="sort_button" data-sort-by="">
                        <i class="fas fa-sync-alt"></i> 
                    </button>
                </div>
            </div>
        </div>
       
        <div class="row" id="notification_cards">
            <?php
            $per_page = 12;
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }
            $start_from = ($page - 1) * $per_page;

            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
            $query = "SELECT * FROM notifications";
            if ($sort_by) {
                $query .= " WHERE type='$sort_by'";
            }
            $query .= " ORDER BY created_at DESC LIMIT $start_from, $per_page";

            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $formatted_date = date("F j, Y", strtotime($row['created_at']));
                $formatted_time = date("h:i A", strtotime($row['created_at']));
                
                $background_color = '';
                if ($row['type'] == 'Issue') {
                    $background_color = 'background: #fefefe;
                                        background: -webkit-linear-gradient(0deg, #fefefe 0%, rgba(240, 201, 201, 0.63) 100%);
                                        background: linear-gradient(0deg, #fefefe 0%, rgba(240, 201, 201, 0.63) 100%);';
                } elseif ($row['type'] == 'Leave') {
                    $background_color = 'background: #fefefe;
                                        background: -webkit-linear-gradient(0deg, #fefefe 0%, rgba(201, 240, 212, 0.63) 100%);
                                        background: linear-gradient(0deg, #fefefe 0%, rgba(201, 240, 212, 0.63) 100%);';
                } elseif ($row['type'] == 'Overtime') {
                    $background_color = 'background: #fefefe;
                                        background: -webkit-linear-gradient(0deg, #fefefe 0%, rgba(201, 213, 240, 0.63) 100%);
                                        background: linear-gradient(0deg, #fefefe 0%, rgba(201, 213, 240, 0.63) 100%);';
                }
                
                echo '<div class="col-lg-12 d-flex justify-content-center">';
                echo '<div class="card notifCard" style="'.$background_color.'" onclick="redirectToPage(\'' . $row['type'] . '\', ' . $row['notification_id'] . ')">';
                echo '<div class="card-body row">';
                echo '<div class="notifHead  d-flex flex-wrap justify-content-center align-items-center col-lg-2 col-sm-4">';
                echo '<div class="">';

                if ($row['type'] == 'Issue') {
                    echo '<img  src="../img/issue-icon.png" alt="Issue Icon" class="icon">';
                } elseif ($row['type'] == 'Leave') {
                    echo '<img src="../img/leaves-icon.png" alt="Leave Icon" class="icon">';
                } elseif ($row['type'] == 'Overtime') {
                    echo '<img src="../img/overtime-icon.png" alt="Overtime Icon" class="icon">';
                }
               
                echo "<h6 class='card-subtitle text-muted'>{$row['type']}</h6>";
                echo '</div>'; // end notifHead

                echo '</div>'; // end notifHead
                echo '<div class="notifBody col-lg-10 col-sm-8">';
                echo "<h5 class='card-title'>{$row['title']}</h5>";
           
                echo "<p class='card-text'>{$row['message']}</p>";
                echo "<p class='card-text'><small class='text-muted'>$formatted_date at $formatted_time</small></p>";
                echo '</div>'; // end notifHead
                echo '</div>'; // end card-body
                echo '</div>'; // end card
                echo '</div>'; // end col-md-3
            }
            ?>
        </div> <!-- end row -->

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                <?php
                $query = "SELECT COUNT(*) FROM notifications";
                if ($sort_by) {
                    $query .= " WHERE type='$sort_by'";
                }
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_row($result);
                $total_records = $row[0];
                $total_pages = ceil($total_records / $per_page);

                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item'><a class='page-link' href='?page=$i&sort_by=$sort_by'>$i</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div> <!-- end container -->
    <hr>
</div>

<script>
    $('#sort_by_select').change(function () {
        var selectedType = $(this).val();
        window.location.href = window.location.pathname + '?sort_by=' + selectedType;
       
    });

    $('#sort_button').click(function () {
        var sortBy = $('#sort_by_select').val();
        window.location.href = window.location.pathname + '?sort_by=' + sortBy;
    });

    function redirectToPage(type, notification_id) {
        if (type === 'Overtime') {
            window.location.href = 'adminOT.php';
        } else if (type === 'Leave') {
            window.location.href = 'adminLEAVES.php';
        } else if (type === 'Issue') {
            window.location.href = 'adminFeedback.php?notification_id=' + notification_id;
        }
    }

    
</script>

</body>
<style>
     body{

  font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
}

        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            width: 80%;
            height: fit-content;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease; 
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transform: scale(1.1);
            transition: transform 0.9s ease;
        }
        .card-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .card-text {
            font-size: 16px;
            line-height: 1.5;
        }

        .icon {
            width: 25px;
            height: 25px;
            margin-left: 10px;
            margin-bottom: 5px;
        
        }
        .notifHead
        {
            border-right: 1px solid #3b3b3ba8 ;
        }

        @media (max-width: 575.98px) {
            .card{
                width: 100%;
            }
          .notifHead{
            border: none;
          }
        }
     

      

    </style>
</html>
