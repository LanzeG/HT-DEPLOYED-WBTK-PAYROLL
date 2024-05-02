
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
// Assuming 'notification_id' is the parameter in the URL
$notification_id = isset($_GET['notification_id']) ? $_GET['notification_id'] : null;

// Validate and sanitize the input
$notification_id = intval($notification_id);  // Ensure it's an integer

// Fetch data from the database based on the notification_id
$sql = "SELECT * FROM empnotifications WHERE notification_id = $notification_id";
$result = $conn->query($sql);

// Check if the query was successful and if there is a matching notification
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Display the notification information
} else {
    // Notification not found or query failed
    echo "Notification not found or an error occurred.";
}
// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>Document</title>
</head>
<body>
    <?php
    INCLUDE('NAVBAR2.php');
    ?>

    <div class="content d-flex align-items-center justify-content-center" style="height: 80vh;">

        <div class="container" style="width:80vh;">
            <div class="card shadow">
                <div class="card-header">
                    Announcement
                </div>
                <form class="row g-3 p-3">
                <div class="col-12">
                    <label for="announcementTitle" class="form-label">Announcement Title</label>
                    <input type="text" class="form-control" id="announcementTitle" name="title" value="<?php echo isset($row['title']) ? $row['title'] : ''; ?>" required readonly>
                </div>
                <div class="col-12">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="3" required readonly><?php echo isset($row['message']) ? $row['message'] : ''; ?></textarea>
                </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
