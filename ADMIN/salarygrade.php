<?php
if (isset($_GET["position"])) {
    include("../DBCONFIG.PHP");
    include("../LoginControl.php");
    include("../BASICLOGININFO.PHP");

    $position = $_GET["position"];

    $sql = "SELECT s.monthlysalary
        FROM position p
        JOIN salarygrade s ON p.salarygrade = s.salarygrade
        WHERE p.position_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $position);
    $stmt->execute();
    $stmt->bind_result($monthlySalary);
    $stmt->fetch();
    $stmt->close();

    // Calculate the daily rate (monthly salary / number of working days in a month)
    $dailyRate = $monthlySalary / 22;

    // Check if it's a non-empty AJAX parameter
    $isAjax = isset($_GET["ajax"]) && $_GET["ajax"] === "true";

    // Return the monthly salary and daily rate as JSON for AJAX requests
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["monthlySalary" => $monthlySalary, "dailyRate" => $dailyRate]);
        exit;
    }
}
?>
