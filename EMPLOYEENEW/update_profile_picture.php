<?php
session_start();
include("../DBCONFIG.PHP");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empid = $_SESSION['empID'];

    $targetDir = "../uploads/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            $updateQuery = "UPDATE employees SET img_name='$targetFilePath' WHERE emp_id='$empid'";
            if (mysqli_query($conn, $updateQuery)) {
                echo "success";
            } else {
                echo "Error updating profile picture: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file format.";
    }
}
?>
