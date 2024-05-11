<?php
set_time_limit(60);
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

session_start();

$adminId = $_SESSION['adminId'];
$payperiod = $_SESSION['pregisterpayperiod'];
$printfrom = $_SESSION['payperiodfrom'];
$printto=$_SESSION['payperiodto'];

$adminname = "SELECT first_name, last_name FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
$letters = range('a', 'z');

//variables
$empcount=0;
$GTbp = 0;
$GtRef = 0;
$GTEarnings = 0;
$firsttotal = 0;
$secondtotal = 0;
$GTDeductions = 0;
$GTDisallowance = 0; 
$GTphealth = 0;
$GTsssded = 0;
$GTpagibigded = 0;
$GTwtaxded = 0;
$GTabsences = 0;
$GTUT = 0;

if (isset($_SESSION['pregisterpayperiod'])) {
    $getppqry = "SELECT * FROM pay_per_period WHERE pperiod_range = '$payperiod'";
    $getppexecqry = mysqli_query($conn,$getppqry) or die ("FAILED TO GET PAYROLL PERIOD DETAILS ".mysqli_error($conn));

    // Initialize CSV string
    $csv = "";

    // Generate CSV headers
    $headers = [
        'No',
        'Name',
        'Position',
        'Employee ID',
        'Monthly Salary',
        'PERA',
        'Additional Compensation',
        'Gross Amount',
        'Disallowance',
        'Ref-Sal',
        'W/tax',
        'Philhealth',
        'Integ-Ins',
        'HDMF Con',
    ];

    // Fetch loan types from the database
    $loanTypesQuery = "SELECT * FROM loantype";
    $loanTypesResult = mysqli_query($conn, $loanTypesQuery);
    while ($loanTypeRow = mysqli_fetch_assoc($loanTypesResult)) {
        $loanType = $loanTypeRow['loantype'];
        $loanOrg = $loanTypeRow['loanorg'];
        $headers[] = $loanOrg . ' ' . $loanType;
    }

    // Append additional headers
    $headers = array_merge($headers, [
        'TOTAL DEDUCTIONS',
        'NET AMOUNT DUE (1st half)',
        'NET AMOUNT DUE (2nd half)',
    ]);

    // Add headers to CSV string
    $csv .= implode(',', $headers) . "\n";

    while($pparray = mysqli_fetch_array($getppexecqry)){
        $empid = $pparray['emp_id'];
        $empcount++;
        //emp info
        $getempdetailsqry = "SELECT prefix_ID,last_name,first_name,middle_name,dept_name, position FROM employees WHERE emp_id = '$empid'";
        $getempdetailsexecqry = mysqli_query($conn,$getempdetailsqry) or die ("FAILED TO GET EMP DETAILS ".mysqli_error($conn));
        $emparray = mysqli_fetch_array($getempdetailsexecqry);
        if($emparray){
            $prefixid = $emparray['prefix_ID'];
            $lname = $emparray['last_name'];
            $fname = $emparray['first_name'];
            $dname = $emparray['dept_name'];
            $position = $emparray['position'];
            $name = "$lname $fname";
            $compempid = "$prefixid$empid";
        }

        //EARNINGS
        $bpay = $pparray['reg_pay'];
        $refsalary = $pparray['refsalary'];
        $npay = $pparray['net_pay'];
        $rph = $pparray['rate_per_hour'];
        $totaldeduct = $pparray['total_deduct'];
        $lvpay = $pparray['lv_pay'];
        $absences = $pparray['absences'];
        $wtax = $pparray['tax_deduct'];
        $undertimededuct = $pparray['undertimehours'];
        $first = $pparray['firsthalf'];
        $second = $pparray['secondhalf'];
        $disallowance = $pparray['disallowance'];

        $te = ($bpay);
        $totearnings = number_format((float)$te,2,'.','');
        //GT earnings
        $GTbp = $GTbp + $bpay;
        $GTbpay = number_format((float)$GTbp,2,'.','');

        $GtRef = $GtRef + $refsalary;
        $GtRefFormatted = number_format((float)$GtRef,2,'.','');

        $GTEarnings = $GTEarnings + $te;
        $TE = number_format((float)$GTEarnings,2,'.','');

        $GTDisallowance = $GTDisallowance + $disallowance;
        $GTDis = number_format((float)$GTDisallowance,2,'.','');
        
        $firsttotal += $first;
        $firsttotal = number_format((float) $firsttotal, 2, '.', '');

        $secondtotal += $second;
        $secondtotal = number_format((float) $secondtotal, 2, '.', '');
        //deducts

        $phdeduct = $pparray['philhealth_deduct'];
        $gsisdeduct = $pparray['sss_deduct'];
        $pagibigdeduct = $pparray['pagibig_deduct'];
        $loandeduct = $pparray['loan_deduct'];

        $GTDeductions = $GTDeductions + $totaldeduct;
        $GTD = number_format((float)$GTDeductions,2,'.','');

        // GTDeductions
        $GTphealth = $GTphealth + $phdeduct;
        $GTph = number_format((float)$GTphealth,2,'.','');

        $GTsssded = $GTsssded + $gsisdeduct;
        $GTsss = number_format((float)$GTsssded,2,'.','');

        $GTpagibigded = $GTpagibigded + $pagibigdeduct;
        $GTpagibig = number_format((float)$GTpagibigded,2,'.','');

        $GTwtaxded = $GTwtaxded + $wtax;
        $GTwtax = number_format((float)$GTwtaxded,2,'.','');

        $GTabsences = $GTabsences + $absences;
        $GTabsences1 = number_format((float)$GTabsences,2,'.','');

        $GTUT = $GTUT + $undertimededuct;
        $GTUTime = number_format((float)$GTUT,2,'.','');

        // Initialize CSV row data
        $csvRowData = [
            $empcount,
            $name,
            $position,
            $compempid,
            $bpay,
            $refsalary,
            "-", // Placeholder for Additional Compensation
            $bpay+$refsalary,
            $disallowance,
            $undertimededuct + $absences,
            $wtax,
            $phdeduct,
            $gsisdeduct,
            $pagibigdeduct,
        ];

        // Fetch loan data for this employee
        $loanTypesQuery = "SELECT * FROM loantype";
        $loanTypesResult = mysqli_query($conn, $loanTypesQuery);
        while ($loanTypeRow = mysqli_fetch_assoc($loanTypesResult)) {
            $loanType = $loanTypeRow['loantype'];

            // Fetch loan amount for this loan type
            $sql = "SELECT COALESCE(SUM(monthly_payment), 0.00) AS totalloan 
                    FROM loan_history 
                    WHERE emp_id = '$empid' 
                    AND payperiod = '$payperiod' 
                    AND remarks != 'Disallowanced' 
                    AND loantype='$loanType'";
            $result = $conn->query($sql);
            if (!$result) {
                die("SQL Error: " . $conn->error);
            }
            $loanAmountRow = $result->fetch_assoc();
            $loanAmount = $loanAmountRow ? $loanAmountRow['totalloan'] : '0.00';

            // Append loan amount to CSV row data
            $csvRowData[] = $loanAmount;
        }
        $csvRowData = array_merge($csvRowData, [
            $GTDeductions,
            "* $first",
            "* $second",
        ]);

        // Append CSV row data to CSV string
        $csv .= implode(',', $csvRowData) . "\n";
    }

    // Output the CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payroll_data.csv"');
    echo $csv;
} else {
    // Handle the case when $_SESSION['pregisterpayperiod'] is not set
    echo "Pay period is not set.";
}
?>
