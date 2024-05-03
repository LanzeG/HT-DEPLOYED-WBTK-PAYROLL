<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
$userId=  $_SESSION['empId'];
$adminId = $_SESSION['adminId'];
$error = false;
$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
if (isset($_POST['submitbtn']))
{
    $announcementTitle = $_POST['announcementTitle'];
    $message = $_POST['message'];

    $getEmployeeIdsQuery = "SELECT emp_id FROM employees";
    $result = $conn->query($getEmployeeIdsQuery);

    if ($result->num_rows > 0) {
        // Loop through each employee ID and insert announcement data
        while ($row = $result->fetch_assoc()) {
            $employeeId = $row['emp_id'];
    
            // SQL query to insert data into the database
            $insertAnnouncementQuery = "INSERT INTO empnotifications (admin_id, title, adminname, emp_id, message, type, status, created_at) 
                                        VALUES ('$adminId','$announcementTitle','$adminFullName','$employeeId', '$message', 'Announcement', 'unread',NOW())";
    
            // Execute the query
            if ($conn->query($insertAnnouncementQuery) !== TRUE) {
                ?><script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong.',
                        timer: 3000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                });
            </script>
                <?php
                // echo "Error: " . $insertAnnouncementQuery . "<br>" . $conn->error;
            }
            else {
                ?>
   
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            // title: "Good job!",
            text: "Announcement Successfully Created",
            icon: "success",
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showCancelButton: false,
            showConfirmButton: false
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = 'announcement.php'; // Replace 'announcement.php' with the actual URL
            }
        });
    });
</script>

                 <?php          
            }
        }
            
        // echo "Announcement records inserted successfully for all employees";
    } else {
        // echo "No employees found in the database.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>Announcements</title>
    <link rel="icon" type="image/png" href="../img/icon1 (6).png">

</head>

<body>

    <?php
    INCLUDE('navbarAdmin.php');
    ?>

    <div class="content d-flex align-items-center justify-content-center" style="height: 80vh;">

        <div class="container" style="width:80vh;">
            <div class="card shadow">
                <div class="card-header">
                    Announcement Form
                </div>
                <form class="row g-3 p-3" method="POST">
                    <div class="col-12">
                        <label for="announcementTitle" class="form-label">Announcement Title</label>
                        <input type="text" class="form-control" id="announcementTitle" name="announcementTitle" required>
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    <div class="col-12 text-center">
                    <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md border border-blue-500 hover:border-blue-600 transition duration-300 ease-in-out" name="submitbtn">Submit</button>

                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        // JavaScript code to prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        </script>
</body>

</html>
<style>
     body{
  font-family: 'Poppins', sans-serif;
}
</style>