<?php
// Include your database connection logic here

include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");


$payperiod =$_SESSION['payperiods'];
// Example query to fetch data
if ($payperiod != 'noset'){
$searchquery = "SELECT * FROM employees, pay_per_period WHERE employees.emp_id = pay_per_period.emp_id AND pay_per_period.emp_id = '$empid' AND pay_per_period.pperiod_range = '$payperiod' ORDER BY pperiod_range";
$search_result = filterTable($searchquery);
$numRows = mysqli_num_rows($search_result);

if ($numRows > 0) {


        $data = array();
        while ($row = mysqli_fetch_assoc($search_result)) {
        $data[] = [
                'label' => 'Base Pay',
                'value' => $row['reg_pay'],
            ];
            $data[] = [
                'label' => 'Philhealth',
                'value' => $row['philhealth_deduct'],
            ];
            $data[] = [
                'label' => 'GSIS',
                'value' => $row['sss_deduct'],
            ];
            $data[] = [
                'label' => 'Pag-Ibig',
                'value' => $row['pagibig_deduct'],
            ];
            $data[] = [
                'label' => 'Loans',
                'value' => $row['loan_deduct'],
            ];
            $data[] = [
                'label' => 'Undertime Hours',
                'value' => $row['undertimehours'],
            ];
            $data[] = [
                'label' => 'Absences',
                'value' => $row['absences'],
            ];
            $data[] = [
                'label' => 'Withholding Tax',
                'value' => $row['tax_deduct'],
            ];
            }
}else {
    $defaultData = [
        ['label' => 'Default Label 1', 'value' => 100],
        ['label' => 'Default Label 2', 'value' => 100],


        // Add more default data as needed
    ];
    $data = $defaultData;
}
} else {
    $defaultData = [
        ['label' => 'Default Label 1', 'value' => 100],
        ['label' => 'Default Label 2', 'value' => 100],
        
    ];
    $data = $defaultData;
}

echo json_encode($data);


function filterTable($searchquery)
{

     $conn1 = mysqli_connect("localhost","u387373332_masterdb","WBTKpayrollportal1234@","u387373332_masterdb");
     $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
     return $filter_Result;
}
?>
