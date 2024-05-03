<?php
session_start(); 
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

$currentempid = $_SESSION['empID'];
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at'; 

$where_clause = "emp_id = '$currentempid'";

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $notification_type = $_GET['type'];
    $where_clause .= " AND type = '$notification_type'";
}

$count_query = "SELECT COUNT(*) as total FROM empnotifications WHERE $where_clause";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_notifications = $count_row['total'];

$total_pages = ceil($total_notifications / 8);


$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($current_page - 1) * 8; 

$query = "SELECT * FROM empnotifications WHERE $where_clause ORDER BY $sort_by DESC LIMIT $start, 8";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/fullcalendar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
    <?php include('navbar2.php'); ?>

    <div id="content">
        <div class="title d-flex justify-content-center pt-3">
            <h3>ALL NOTIFICATIONS</h3>
        </div>
        <hr>
        <br>
        <div class="container">

        <div class="form-group" style="display: flex; align-items: center; justify-content: center;">
    <select id="type_select" class="form-control" style="width: 50%;">
        <option value="">All</option>
        <option value="Overtime" <?php if (isset($_GET['type']) && $_GET['type'] == 'Overtime') echo 'selected'; ?>>Overtime</option>
        <option value="Leave" <?php if (isset($_GET['type']) && $_GET['type'] == 'Leave') echo 'selected'; ?>>Leave</option>
        <option value="Loan" <?php if (isset($_GET['type']) && $_GET['type'] == 'Loan') echo 'selected'; ?>>Loan</option>
        <option value="Payroll" <?php if (isset($_GET['type']) && $_GET['type'] == 'Payroll') echo 'selected'; ?>>Payroll</option>
        <option value="Announcement" <?php if (isset($_GET['type']) && $_GET['type'] == 'Announcement') echo 'selected'; ?>>Announcement</option>
    </select>

    <button class="btn btn-success" id="refreshButton">
        <i class="fas fa-sync-alt"></i>
    </button>
            </div>
          
            <div class="row" id="notification_cards">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $formatted_date = date("F j, Y", strtotime($row['created_at']));
                    $formatted_time = date("h:i A", strtotime($row['created_at']));

                    switch ($row['type']) {
                        case 'Overtime':
                            $background_color = 'background: #ffffff;
                            background: -webkit-linear-gradient(0deg, #ffffff 0%, rgba(206, 250, 205, 0.52) 100%);
                            background: linear-gradient(0deg, #ffffff 0%, rgba(206, 250, 205, 0.52) 100%);';
                            $icon = '<img src="../img/overtime-icon.png" alt="Overtime Icon" class="icon small-icon">';
                            break;
                        case 'Leave':
                            $background_color = 'background: #ffffff;
                            background: -webkit-linear-gradient(0deg, #ffffff 0%, rgba(206, 250, 205, 0.52) 100%);
                            background: linear-gradient(0deg, #ffffff 0%, rgba(206, 250, 205, 0.52) 100%);';
                            $icon = '<img src="../img/leaves-icon.png" alt="Leave Icon" class="icon">';
                            break;
                        case 'Loan':
                            $background_color = 'background: #ffffff;
                            background: -webkit-linear-gradient(0deg, #ffffff 0%, rgba(230, 230, 139, 0.52) 100%);
                            background: linear-gradient(0deg, #ffffff 0%, rgba(230, 230, 139, 0.52) 100%);';
                            $icon = '<img src="../img/credit-card.png" alt="Loan Icon" class="icon ">';
                            break;
                        case 'Payroll':
                            $background_color = 'background: #ffffff;
                            background: -webkit-linear-gradient(0deg, #ffffff 0%, rgba(250, 228, 205, 0.52) 100%);
                            background: linear-gradient(0deg, #ffffff 0%, rgba(250, 228, 205, 0.52) 100%);;';
                            $icon = '<img src="../img/pie-chart1.png" alt="Payroll Icon" class="icon ">';
                            break;
                        case 'Announcement':
                            $background_color = 'background: #ffffff;
background: -webkit-linear-gradient(0deg, #ffffff 0%, rgba(205, 230, 250, 0.52) 100%);
background: linear-gradient(0deg, #ffffff 0%, rgba(205, 230, 250, 0.52) 100%);';
                            $icon = '<img src="../img/megaphone.png" alt="Announcement Icon" class="icon">';
                            break;
                        default:
                            $background_color = '';
                            $icon = ''; 
                            break;
                    }

                    echo "<div class='col-lg-12 d-flex justify-content-center'>";
                    echo "<div class='card notifCard fixed-card' style='$background_color' onclick='redirectToPage(\"{$row['type']}\", {$row['notification_id']})'>";
                    echo "<div class='card-body row'>";
                    echo '<div class="notifHead  d-flex flex-wrap justify-content-center align-items-center col-lg-2 col-sm-4">';
                    echo '<div class="text-center">';

                    echo $icon; 
                    echo "<h6 class='card-subtitle mb-2 text-muted'>{$row['type']}</h6>";
                    echo '</div>'; // end notifHead

                    echo '</div>'; // end notifHead
                    echo '<div class="notifBody  col-lg-10 col-sm-8">';

                    echo "<h5 class='card-title fw-semibold'>{$row['title']}</h5>";
                    if(strlen($row['message']) > 80) {
                        $short_message = substr($row['message'], 0, 80);
                        echo "<p class='card-text'>$short_message... <a href='#' class='see-more'></a></p>";
                    } else {
                        echo "<p class='card-text'>{$row['message']}</p>";
                    }
                    echo "<p class='card-subtitle text-muted'>$formatted_date at $formatted_time</p>";

                    echo "</div>"; 
                    echo "</div>"; 

                    echo "<div class='card-footer'>";
                    echo '<div class="d-flex justify-content-end">';

                    echo "<h6 class='card-subtitle text-muted'>Admin: {$row['adminname']}</h6>";
                    echo "</div>"; // end card-footer

                    echo "</div>"; // end card-footer

                    echo "</div>"; // end card
                    echo "</div>"; // end col-md-3

                    
                }

                
                ?>
            </div> <!-- end row -->
            
<style>
    .fixed-card {
    position: relative;
    height: 100%;

}

.fixed-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 2;
}

</style>
          <!-- Pagination -->
<div class="d-flex justify-content-center">
    <ul class="pagination">
        <?php
        for ($page = 1; $page <= $total_pages; $page++) {
            $url = '?page=' . $page;
            if (isset($_GET['type'])) {
                $url .= '&type=' . $_GET['type'];
            }
            if (isset($_GET['sort_by'])) {
                $url .= '&sort_by=' . $_GET['sort_by'];
            }
            echo '<li class="page-item ' . ($current_page == $page ? 'active' : '') . '"><a class="page-link" href="' . $url . '">' . $page . '</a></li>';
        }
        ?>
    </ul>
</div>

        </div>

    </div>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .fixed-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            width: 60%;
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
     <script>
        $('#sort_by_select').change(function () {
            var selectedType = $(this).val();
            window.location.href = window.location.pathname + '?sort_by=' + selectedType;
        });
        $('#type_select').change(function () {
            var selectedType = $(this).val();
            var url = window.location.pathname + '?';
            if (selectedType !== '') {
                url += 'type=' + selectedType;
            }

            window.location.href = url;
        });

        function redirectToPage(type, notification_id) {
            if (type === 'Overtime') {
                window.location.href = 'newapplyovertime.php';
            } else if (type === 'Leave') {
                window.location.href = 'LEAVEApplication.php';
            } else if (type === 'Issue') {
                window.location.href = 'adminFeedback.php?notification_id=' + notification_id;
            } else if (type === 'Loan') {
                window.location.href = 'empLOANs.php';
            } else if (type === 'Announcement') {
                window.location.href = 'empANNOUNCEMENT.php?notification_id=' + notification_id;
            }
        }
    </script>
    <script>
    document.getElementById('refreshButton').addEventListener('click', function() {
        location.reload();
    });
</script>

<script>
    document.getElementById('showAllNotifications').addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });
</script>
</body>
</html>
