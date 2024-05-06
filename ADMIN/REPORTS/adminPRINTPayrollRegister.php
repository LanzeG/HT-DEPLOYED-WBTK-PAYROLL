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

   
    $html = '
        <html>
        <head>
            <style>
                .header {
                    text-align: center;
                }
                .left {
                    text-align: left;
                }
                .watermark {
                    position: absolute;
                    top: 30%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 100px;
                    opacity: 0.4; /* Adjust the opacity as needed */
                    color: #CCCCCC; /* Adjust the color as needed */
                    white-space:nowrap;
                }
            </style>
        </head>
        <body>
        <div class="watermark">Computer Generated</div>
            <div class="header">
                <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</p>
                <p>' . $payperiod . '</p>
            </div>
            <div class="left">
                <p>We acknowledge receipt of cash shown opposite names as full<br> compensation for services rendered for the period covered</p>
            </div>
            <table border="1" style="border: 1px solid black;">>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Position</th>
                    <th rowspan="2">Employee ID</th>
                    <th rowspan="2">Monthly Salary</th>
                    <th rowspan="1" colspan="2">Other Compensation</th>
                    <th rowspan="2">Gross Amount</th>
                    <th colspan="4">Deductions</th>
                    <th rowspan="2">TOTAL DEDUCTIONS</th>
                    <th rowspan="2">NET AMOUNT DUE<br>* 1st half<br>. 2nd half</th>
                </tr>
                <tr>
                    <th rowspan="1">PERA</th>
                    <th rowspan="1">Additional Compensation</th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        @ Disallowance<br>
                        # Ref-Sal<br>
                        . W/tax <br>
                        & Philhealth <br>
                        ! Integ-Ins <br>
                        % &nbsp; HDMF Con<br>
                    </th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">';

                    $loanTypesQuery = "SELECT * FROM loantype";
                    $loanTypesResult = mysqli_query($conn, $loanTypesQuery);
                    $currentLetterIndex = 0;
                    while ($loanTypeRow = mysqli_fetch_assoc($loanTypesResult)) {
                        $loanType = $loanTypeRow['loantype'];
                        $loanOrg = $loanTypeRow['loanorg'];
                        $currentLetter = $letters[$currentLetterIndex];

                        $html .= $currentLetter . ' ' .$loanOrg.' '. $loanType . '<br>';
                        $currentLetterIndex++;

                        if ($currentLetterIndex % 6 === 0) {
                            // Close the current table cell
                            $html .= '</th>';
                            
                            // If it's not the last letter, open a new table cell
                            if ($currentLetterIndex < count($letters)) {
                                $html .= '<th rowspan="1" style="text-align: left; white-space: nowrap;">';
                            }
                        }
                    }

                    if ($currentLetterIndex % 6 !== 0) {
                        $html .= '</th>';
                    }

                    if (mysqli_num_rows($getppexecqry) > 0) {
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
                                $name = "$lname, $fname";
                                $compempid = "$prefixid$empid";
                            }

                            $gettkqry = 

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
                            $GTE = number_format((float)$GTEarnings,2,'.','');

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


                        
                        $html .= '
                        <tr>
                            <td>'.$empcount.'</td>
                            <td>'.$name.'</td>                      
                            <td>'.$position.'</td>
                            <td>'.$compempid.'</td>
                            <td>'.$bpay.'</td>
                            <td>'.$refsalary.'</td>
                            <td>-</td>
                            <td> '.number_format($bpay + $refsalary,2).'</td>
                            <td>@ '.$disallowance.'<br>
                                # '.$undertimededuct + $absences.'<br>
                                . '.$wtax.'<br>
                                & '.$phdeduct.'<br>
                                ! '.$gsisdeduct.'<br>
                                % '.$pagibigdeduct.'<br></td>
                                <td>';
                                $loanTypesQuery1 = "SELECT * FROM loantype";
                                $loanTypesResult1 = mysqli_query($conn, $loanTypesQuery1);
                                $currentLetterIndex1 =0;
                                $letterAmountCount = 0;
                                while ($loanTypeRow1= mysqli_fetch_assoc($loanTypesResult1)) {

                                    $loanType = $loanTypeRow1['loantype'];
                                    $currentLetter1 = $letters[$currentLetterIndex1];

                                    //loan
                                    $sql = "SELECT * FROM loan_history WHERE emp_id = '$empid' AND payperiod = '$payperiod' AND remarks != 'Disallowanced' AND loantype='$loanType'";
                                    $result = $conn->query($sql);
                                    if (!$result) {
                                        die("SQL Error: " . $conn->error);
                                    }

                                    $loanAmountRow = $result->fetch_assoc();
                                    $loanAmount = $loanAmountRow ? $loanAmountRow['monthly_payment'] : '0.00';
                                    $letterAmountString = $currentLetter1 . ' ' . $loanAmount;

                                    // Output the letter and loan amount directly into the table cell
                                    $html .= ''.$letterAmountString.'<br>';
                                
                                    // // Increment the current letter index and count
                                    $currentLetterIndex1++;
                                    $letterAmountCount++;
                                
                                    // // // If six elements have been printed, close the row and start a new one
                                    if ($letterAmountCount % 6 === 0) {
                                        $html .= '</td><td>';
                                    }
                                }
                                
                            // End the row
                            $html .= '
                            <td>'.$totaldeduct.'</td>
                            <td>* '.$first.'<br>* '.$second.'</td>
                            </tr>';
                    }
                
                
    
        $html .= '<tr><td></td>
                <td>SHEET TOTAL</td>
                <td></td>
                <td></td><td>'.$GTbpay.'</td>
                <td>'.$GtRefFormatted.'</td>
                <td>-</td>
                <td>'.number_format($GTbpay + $GtRefFormatted, 2).'</td>
                <td>@ '.$GTDis.'<br>
                    # '. number_format((float)$GTUTime +$GTabsences1,2).'<br>
                    . '.$GTwtax.'<br>
                    & '.$GTph.'<br>
                    ! '.$GTsss.'<br>
                    % '.$GTpagibig.'<br>
                    </td>
                <td>';
                $loanTypesQuery1 = "SELECT * FROM loantype";
                $loanTypesResult1 = mysqli_query($conn, $loanTypesQuery1);
                $currentLetterIndex1 =0;
                $letterAmountCount = 0;
                while ($loanTypeRow1= mysqli_fetch_assoc($loanTypesResult1)) {

                    $loanType = $loanTypeRow1['loantype'];
                    $currentLetter1 = $letters[$currentLetterIndex1];

                    //loan
                    $sql = "SELECT COALESCE(SUM(monthly_payment), 0.00) AS totalloan 
                    FROM loan_history 
                    WHERE payperiod = '$payperiod' 
                    AND remarks != 'Disallowanced' 
                    AND loantype='$loanType'";
                                $result = $conn->query($sql);
                    if (!$result) {
                        die("SQL Error: " . $conn->error);
                    }
                    $loanAmountRow = $result->fetch_assoc();
                    $loanAmount = $loanAmountRow ? $loanAmountRow['totalloan'] : '0.00';
                    $letterAmountString = $currentLetter1 . ' ' . $loanAmount;

                    // Output the letter and loan amount directly into the table cell
                    $html .= ''.$letterAmountString.'<br>';
                
                    // // Increment the current letter index and count
                    $currentLetterIndex1++;
                    $letterAmountCount++;
                
                    // // // If six elements have been printed, close the row and start a new one
                    if ($letterAmountCount % 6 === 0) {
                        $html .= '</td><td>';
                    }
                }

        $html.='
                <td>'.$GTDeductions.'</td>
                <td>* '.$firsttotal.'<br>* '.$secondtotal.'</td></tr>
                <tr><td rowspan="4">A</td>
                <td rowspan="4" colspan="7"><p>CERTIFIED: Service duly rendered as stated:</p><p style="text-align:center;">____________________________</p></td>
                <td rowspan="4">B</td>
                <td rowspan="4" colspan="7"><p>Approved for Payment:</p><p style="text-align:center;">____________________________</p></td></tr>
                <tr></tr>
                <tr></tr>
                <tr></tr>
                <tr><td rowspan="4">C</td>
                <td rowspan="4" colspan="7"><p>CERTIFIED:  Supporting socuments complete and proper;<br>			
                and cash available in the amount of P</p><p style="text-align:center;">____________________________</p></td>
                <td rowspan="4">D</td>
                <td rowspan="4" colspan="7"><p>CERTIFIED:  Each employee whom name appears shown has been <br>				
                paid the amount indicated opposite is/her name.</p><p style="text-align:center;">____________________________</p></td></tr></tr>
            </table>

            <p>Printed By: '.$adminFullName.'</p>
            </body>
            </html>';
                
    // Create a Dompdf instance
    $dompdf = new Dompdf();
    $dompdf->setPaper('A2', 'landscape');
    $dompdf->loadHtml($html);
    $dompdf->render();

    // Set headers to indicate that the PDF should be displayed inline
    header('Content-Type: application/pdf');
    $dompdf->stream("", array("Attachment" => false));
        }else{
            echo "<script>alert('No data found for the specified payroll period.'); window.close();</script>";
        }
    } else {
        // Handle the case when $_SESSION['pregisterpayperiod'] is not set
        echo "Pay period is not set.";
    }
?>
