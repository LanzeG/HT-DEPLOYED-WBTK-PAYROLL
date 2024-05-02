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

if (isset($_SESSION['pregisterpayperiod'])) {
    $getppqry = "SELECT * FROM PAY_PER_PERIOD WHERE pperiod_range = '$payperiod'";
    $getppexecqry = mysqli_query($conn,$getppqry) or die ("FAILED TO GET PAYROLL PERIOD DETAILS ".mysqli_error($conn));
    $GTbp = 0;
    $GTbpay = 0;
    $GTph = 0;
    $GTE = 0;
    $GTotpay = 0;
    $GTsss = 0;
    $GTotrhpay = 0;
    $GTotshpay = 0;
    $GTpagibig = 0;
    $GTpagibigloan = 0;
    $GTabsences = 0;
    $GTN = 0;
    $GTlvpay = 0;
    $GTshpay = 0;
    $GTrhpay = 0;
    $GTD = 0;
    $GTotp = 0;
    $GTrhp = 0;
    $GTrhp200 = 0;
    $GTotrhp = 0;
    $GTotshp = 0;
    $GTotrdp = 0;
    $GTrdrhp = 0;
    $GTrdrhp = 0;
    $GTotrdrhp = 0;
    $GTrdshp = 0;
    $GTotrdshp  = 0;
    $GTlvp = 0;
    $GTshp = 0;
    $GTrdp  = 0;
    $GTEarnings = 0;
    $GTphealth = 0;
    $GTsssded = 0;
    $GTpagibigded = 0;
    $GTsssloanded = 0;
    $GTpagibigloanded = 0;
    $GTut =0;
    $GTDeductions = 0;
    $GTnp = 0;
    $GTwtaxded = 0;
    $GTIntegratedAmount = 0;
    $GTwtax =0;
    $GTIntegratedAmount = 0;
    $salaryLoanAmount = 0;
    $policyLoanAmount = 0;
    $elaLoanAmount = 0;
    $optionalInsuranceAmount = 0;
    $optionalAmount = 0;
    $gfalAmount = 0;
    $hipAmount = 0;
    $landbankAmount = 0;
    $cplAmount = 0;
    $sosAmount = 0;
    $educAmount = 0;
    $eCardAmount = 0;
    $contributionAmount = 0;
    $mplAmount = 0;
    $realStateAmount = 0;
    $emergencyAmount = 0;
    $MP2Amount = 0;
    $IntegratedAmount = 0;
    $GTia = 0;
    $GtRefFormatted = 0;
    $GtRef = 0;
    // Initialize variables
    $GTIntegratedAmount = 0;
    $salaryLoanAmount = 0;
    $policyLoanAmount = 0;
    $elaLoanAmount = 0;
    $optionalInsuranceAmount = 0;
    $optionalAmount = 0;
    $gfalAmount = 0;
    $hipAmount = 0;
    $landbankAmount = 0;
    $cplAmount = 0;
    $sosAmount = 0;
    $educAmount = 0;
    $eCardAmount = 0;
    $contributionAmount = 0;
    $mplAmount = 0;
    $realStateAmount = 0;
    $emergencyAmount = 0;
    $MP2Amount = 0;
    $IntegratedAmount = 0;
    $GTia = 0;
    $salaryLoanAmountFormatted = 0;
    $policyLoanAmountFormatted = 0;
    $elaLoanAmountFormatted = 0;
    $optionalInsuranceAmountFormatted = 0;
    $optionalAmountFormatted = 0;
    $gfalAmountFormatted = 0;
    $hipAmountFormatted = 0;
    $cplAmountFormatted = 0;
    $sosAmountFormatted = 0;
    $educAmountFormatted = 0;
    $eCardAmountFormatted = 0;
    $contributionAmountFormatted = 0;
    $mplAmountFormatted = 0;
    $realStateAmountFormatted = 0;
    $emergencyAmountFormatted = 0;
    $MP2AmountFormatted = 0;
    $firsttotal = 0;
    $secondtotal = 0;

   
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
            </style>
        </head>
        <body>
            <div class="header">
                <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</p>
                <p>' . $payperiod . '</p>
            </div>
            <div class="left">
                <p>We acknowledge receipt of cash shown opposite names as full<br> compensation for services rendered for the period covered</p>
            </div>
            <table border="1">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Position</th>
                    <th rowspan="2">Employee ID</th>
                    <th rowspan="2">Monthly Salary</th>
                    <th rowspan="1" colspan="2">Other Compensation</th>
                    <th rowspan="2">Gross Amount</th>
                    <th colspan="6">Deductions</th>
                    <th rowspan="2">TOTAL DEDUCTIONS</th>
                    <th rowspan="2">NET AMOUNT DUE<br>* 1st half<br>. 2nd half</th>
                </tr>
                <tr>
                    <th rowspan="1">PERA</th>
                    <th rowspan="1">Additional Compensation</th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        @ Disallowance<br>
                        # Ref-Sal<br>
                        . Ref-Ocom <br>
                        & NHMC <br>
                        !
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        a &nbsp; Integ-Ins<br>
                        b &nbsp; W/tax<br>
                        c &nbsp; Philhealth<br>
                        d &nbsp; GSIS MPL<br>
                        e &nbsp; GSIS Sal
                    </th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        f &nbsp; GSIS Pol<br>
                        g &nbsp; GSIS ELA<br>
                        h &nbsp; GSIS Opln<br>
                        i &nbsp; GSIS OpLo<br>
                        j &nbsp; GFAL
                    </th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        k &nbsp; GSIS HIP<br>
                        l &nbsp; GSIS CPL<br>
                        m &nbsp; GSIS SOS<br>
                        n &nbsp; GSIS Eplan<br>
                        o &nbsp; GSIS Ecard
                    </th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        p &nbsp; HDMF MPL<br>
                        q &nbsp; H\'DMF Res<br>
                        r &nbsp; HDMF Con<br>
                        s &nbsp; LBP<br>
                        t &nbsp; Fin Ass<br>
                    </th>
                    <th rowspan="1" style="text-align: left; white-space: nowrap;">
                        u &nbsp; GSIS Educ<br>
                        v &nbsp; HDMF Eme<br>
                        w &nbsp; Undertime <br>
                        x &nbsp; Leave w/o pay
                    </th>
                </tr>';
                if (mysqli_num_rows($getppexecqry) > 0) {
                    
                while($pparray = mysqli_fetch_array($getppexecqry)){
                    $empid = $pparray['emp_id'];

                    $timekeepinfo = "SELECT  SUM(undertime_hours) as totalUT, SUM(hours_work) as totalWORKhours  FROM TIME_KEEPING WHERE emp_id = '$empid' AND timekeep_day BETWEEN '$printfrom' and '$printto' ORDER BY timekeep_day ASC";
                    $timekeepinfoexecqry = mysqli_query($conn,$timekeepinfo) or die ("FAILED TO GET TIMEKEEP INFO ".mysqli_error($conn));
                    $timekeepinfoarray = mysqli_fetch_array($timekeepinfoexecqry);
                    if ($timekeepinfoarray){

                        $hw = $timekeepinfoarray['totalWORKhours'];
                        // $othrs = $timekeepinfoarray['totalOT'];
                        $ut = $timekeepinfoarray['totalUT'];

                        }     
                        $sql = "SELECT loans.*, loantype.*
                            FROM loans
                            JOIN loantype ON loans.loantype = loantype.loantype
                            WHERE loans.emp_id = $empid
                            AND loans.start_date <= '$printto'
                            AND loans.end_date >= '$printfrom'";

                    $result = $conn->query($sql);

                    if (!$result) {
                        die("SQL Error: " . $conn->error);
                    }

                    // Fetch data and assign it to a variable
                    $loanData = [];
                    while ($row = $result->fetch_assoc()) {
                        $loanData[] = $row;
                    }      
                    $salaryLoanAmount = 0;
                    $policyLoanAmount = 0;
                    $elaLoanAmount = 0;
                    $optionalInsuranceAmount = 0;
                    $optionalAmount = 0;
                    $gfalAmount = 0;
                    $hipAmount = 0;
                    $landbankAmount = 0;
                    $cplAmount = 0;
                    $sosAmount = 0;
                    $educAmount = 0;
                    $eCardAmount = 0;
                    $contributionAmount = 0;
                    $mplAmount = 0;
                    $realStateAmount = 0;
                    $emergencyAmount = 0;
                    $MP2Amount = 0;
                    $IntegratedAmount = 0;
                    // $firsttotal = 0;
                    // $secondtotal = 0;



                    $totalloans = $salaryLoanAmount +
                    $policyLoanAmount +
                    $elaLoanAmount +
                    $optionalInsuranceAmount +
                    $optionalAmount +
                    $gfalAmount +
                    $hipAmount +
                    $landbankAmount +
                    $cplAmount +
                    $sosAmount +
                    $educAmount +
                    $eCardAmount +
                    $contributionAmount +
                    $mplAmount +
                    $realStateAmount +
                    $emergencyAmount +
                    $MP2Amount +
                    $IntegratedAmount;

                // Iterate through each loan record and assign loan amounts based on loan type
                foreach ($loanData as $loanRecord) {
                    // Access individual fields in each loan record
                    $loanAmount = $loanRecord['monthly_deduct'];
                    $loanType = $loanRecord['loantype'];

                    // Assign loan amount based on loan type using multiple if statements
                    if ($loanType == 'Salary Loan') {
                        $salaryLoanAmount += $loanAmount;
                    } elseif ($loanType == 'Policy Loan') {
                        $policyLoanAmount += $loanAmount;
                    } elseif ($loanType == 'ELA') {
                        $elaLoanAmount += $loanAmount;
                    } elseif ($loanType == 'Optional Insurance') {
                        $optionalInsuranceAmount += $loanAmount;
                    } elseif ($loanType == 'GFAL') {
                        $gfalAmount += $loanAmount;
                    } elseif ($loanType == 'HIP') {
                        $hipAmount += $loanAmount;
                    } elseif ($loanType == 'Landbank') {
                        $landbankAmount += $loanAmount;
                    } elseif ($loanType == 'CPL') {
                        $cplAmount += $loanAmount;
                    } elseif ($loanType == 'SOS') {
                        $sosAmount += $loanAmount;
                    } elseif ($loanType == 'Educ') {
                        $educAmount += $loanAmount;
                    } elseif ($loanType == 'E Card') {
                        $eCardAmount += $loanAmount;
                    } elseif ($loanType == 'Contribution') {
                        $contributionAmount += $loanAmount;
                    } elseif ($loanType == 'MPL') {
                        $mplAmount += $loanAmount;
                    } elseif ($loanType == 'Real State') {
                        $realStateAmount += $loanAmount;
                    } elseif ($loanType == 'Emergency') {
                        $emergencyAmount += $loanAmount;
                    }
                    elseif ($loanType == 'MP2') {
                        $MP2Amount += $loanAmount;
                    }
                    elseif ($loanType == 'Integrated Insurance') {
                        $IntegratedAmount += $loanAmount;
                    }
                    elseif ($loanType == 'Optional Loan') {
                        $OptionalLoanAmount += $loanAmount;
                    }
                    // ... add more if statements for other loan types
                }
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
                // $otpay = $othrs * $rph;

                // $bpay1 = $bpay + ($otpay);

                $te = ($bpay);
                $totearnings = number_format((float)$te,2,'.','');
                //GT earnings
                $GTbp = $GTbp + $bpay;
                $GTbpay = number_format((float)$GTbp,2,'.','');

                $GtRef = $GtRef + $refsalary;
                $GtRefFormatted = number_format((float)$GtRef,2,'.','');


                $GTrhp = $GTrhp;
                $GTrhpay = number_format((float)$GTrhp,2,'.','');
                
                $GTotrhp = $GTotrhp;
                $GTotrhpay = number_format((float)$GTotrhp,2,'.','');

                $GTshp = $GTshp;
                $GTshpay = number_format((float)$GTshp,2,'.','');

                $GTotshp = $GTotshp ;
                $GTotshpay = number_format((float)$GTotshp,2,'.','');

                $GTlvp = $GTlvp;
                $GTlvpay = number_format((float)$GTlvp,2,'.','');

                $GTEarnings = $GTEarnings + $te;
                $GTE = number_format((float)$GTEarnings,2,'.','');

                $GTIntegratedAmount = $GTIntegratedAmount + $IntegratedAmount;
                $GTia = number_format((float)$GTIntegratedAmount,2,'.','');

                $salaryLoanAmount += $salaryLoanAmount;
                $salaryLoanAmountFormatted = number_format((float) $salaryLoanAmount, 2, '.', '');

                $policyLoanAmount += $policyLoanAmount;
                $policyLoanAmountFormatted = number_format((float) $policyLoanAmount, 2, '.', '');

                $elaLoanAmount += $elaLoanAmount;
                $elaLoanAmountFormatted = number_format((float) $elaLoanAmount, 2, '.', '');

                $optionalInsuranceAmount += $optionalInsuranceAmount;
                $optionalInsuranceAmountFormatted = number_format((float) $optionalInsuranceAmount, 2, '.', '');

                $optionalAmount += $optionalAmount;
                $optionalAmountFormatted = number_format((float) $optionalAmount, 2, '.', '');

                $gfalAmount += $gfalAmount;
                $gfalAmountFormatted = number_format((float) $gfalAmount, 2, '.', '');

                $hipAmount += $hipAmount;
                $hipAmountFormatted = number_format((float) $hipAmount, 2, '.', '');

                $landbankAmount += $landbankAmount;
                $landbankAmountFormatted = number_format((float) $landbankAmount, 2, '.', '');

                $cplAmount += $cplAmount;
                $cplAmountFormatted = number_format((float) $cplAmount, 2, '.', '');

                $sosAmount += $sosAmount;
                $sosAmountFormatted = number_format((float) $sosAmount, 2, '.', '');

                $educAmount += $educAmount;
                $educAmountFormatted = number_format((float) $educAmount, 2, '.', '');

                $eCardAmount += $eCardAmount;
                $eCardAmountFormatted = number_format((float) $eCardAmount, 2, '.', '');

                $contributionAmount += $contributionAmount;
                $contributionAmountFormatted = number_format((float) $contributionAmount, 2, '.', '');

                $mplAmount += $mplAmount;
                $mplAmountFormatted = number_format((float) $mplAmount, 2, '.', '');

                $realStateAmount += $realStateAmount;
                $realStateAmountFormatted = number_format((float) $realStateAmount, 2, '.', '');

                $emergencyAmount += $emergencyAmount;
                $emergencyAmountFormatted = number_format((float) $emergencyAmount, 2, '.', '');

                $MP2Amount += $MP2Amount;
                $MP2AmountFormatted = number_format((float) $MP2Amount, 2, '.', '');

                $firsttotal += $first;
                $firsttotal = number_format((float) $firsttotal, 2, '.', '');

                $secondtotal += $second;
                $secondtotal = number_format((float) $secondtotal, 2, '.', '');

                //DEDUCTS

                $phdeduct = $pparray['philhealth_deduct'];
                $gsisdeduct = $pparray['sss_deduct'];
                $pagibigdeduct = $pparray['pagibig_deduct'];
                $sssloandeduct = $pparray['loan_deduct'];
                // $pagibigloandeduct = $pparray['pagibigloan_deduct'];

                $td = ($phdeduct + $gsisdeduct + $pagibigdeduct + $wtax + $sssloandeduct + $totalloans + $undertimededuct + $absences);
                $totdeduct = number_format((float)$td,2,'.','');
                //GTDeductions
                $GTphealth = $GTphealth + $phdeduct;
                $GTph = number_format((float)$GTphealth,2,'.','');

                $GTsssded = $GTsssded + $gsisdeduct;
                $GTsss = number_format((float)$GTsssded,2,'.','');

                $GTpagibigded = $GTpagibigded + $pagibigdeduct;
                $GTpagibig = number_format((float)$GTpagibigded,2,'.','');

                $GTwtaxded = $GTwtaxded + $wtax;
                $GTwtax = number_format((float)$GTwtaxded,2,'.','');


                $GTsssloanded = $GTsssloanded + $sssloandeduct;
                $GTsssloan = number_format((float)$GTsssloanded,2,'.','');

                $GTpagibigloanded = $GTpagibigloanded;
                $GTpagibigloan = number_format((float)$GTpagibigloanded,2,'.','');

                $GTut = $GTut + $undertimededuct;
                $GTut = number_format((float)$GTut,2,'.','');

                $GTabsences = $GTabsences + $absences;
                $GTabsences = number_format((float)$GTabsences,2,'.','');

                $GTDeductions = $GTDeductions + $td;
                $GTD = number_format((float)$GTDeductions,2,'.','');
                //NETPAY 
                $np = ($te - $td);
                $netpay = number_format((float)$np,2,'.','');
                $GTnp = $GTnp + $np;
                $GTN = number_format((float)$GTnp,2,'.','');

                    $html .= '
                    <tr>
                        <td>1</td>
                        <td>'.$name.'</td>                      
                        <td>'.$position.'</td>
                        <td>'.$compempid.'</td>
                        <td>'.$bpay.'</td>
                        <td>'.$refsalary.'</td>
                        <td>-</td>
                        <td>'.$bpay + $refsalary.'</td>
                        <td>@ 0.00<br>
                            # 0.00<br>
                            . 0.00<br>
                            & 0.00<br>
                            ! 0.00<br></td>
                        <td>a. '.$gsisdeduct.'<br>
                            b. '.$wtax.'<br>
                            c. '.$phdeduct.'<br>
                            d. '.$mplAmount.'<br>
                            e. '.$salaryLoanAmount.'<br></td>
                        <td>f. '.$policyLoanAmount.'<br>
                            g. '.$elaLoanAmount.'<br>
                            h. '.$optionalInsuranceAmount.'<br>
                            i. '.$optionalAmount.'<br>
                            j. '.$gfalAmount.'<br></td>
                        <td>k. '.$hipAmount.'<br>
                            l. '.$cplAmount.'<br>
                            m. '.$sosAmount.'<br>
                            n. 0<br>
                            o. '.$eCardAmount.'<br></td>
                        <td>p. '.$mplAmount.'<br>
                            q. '.$realStateAmount.'<br>
                            r. '.$pagibigdeduct.'<br>
                            s. '.$landbankAmount.'<br>
                            t. 0</td>
                        <td>u.0<br>
                            v. '.$emergencyAmount.'<br>
                            w. '.$undertimededuct.'<br>
                            x. '.$absences.'<br>
                        <td>'.$totaldeduct.'</td>
                        <td>*'.$first.'<br>
                              .'.$second.'</td>



                        
                       
                    </tr>';
        }
        $html.= '<tr>
                <td></td>
                <td>SHEET TOTAL</td>
                <td></td>
                <td></td>
                <td>'.$GTbpay.'</td>
                <td>'.$GtRefFormatted.'</td>
                <td>-</td>
                <td>'.$GTbpay + $GtRefFormatted.'</td>
                <td>@ 0.00<br>
                # 0.00<br>
                . 0.00<br>
                & 0.00<br>
                ! 0.00<br></td>
                <td>a. '.$GTsss.'<br>
                b. '.$GTwtax.'<br>
                c. '.$GTph.'<br>
                d. '.$mplAmountFormatted.'<br>
                e. '.$salaryLoanAmountFormatted.'<br></td>
                <td>f.'.$policyLoanAmountFormatted.'<br>
                g. '.$elaLoanAmountFormatted.'<br>
                h. '.$optionalInsuranceAmountFormatted.'<br>
                i. '.$optionalAmountFormatted.'<br>
                j. '.$gfalAmountFormatted.'<br></td>
                <td>k.'.$hipAmountFormatted.'<br>
                l. '.$cplAmountFormatted.'<br>
                m. '.$sosAmountFormatted.'<br>
                n. 0<br>
                o. '.$eCardAmountFormatted.'<br></td>
                <td>p.'.$mplAmountFormatted.'<br>
                q. '.$realStateAmountFormatted.'<br>
                r. '.$GTpagibig.'<br>
                s. '.$landbankAmountFormatted.'<br>
                t. 0<br></td>
                <td>u. 0<br>
                v. '.$emergencyAmountFormatted.'<br>
                w. '.$GTut.'<br>
                x. '.$GTabsences.'<br></td>
                <td>'.$GTD.'</td>
                <td>*'.$firsttotal.'<br>
                .'.$secondtotal.'</td>
        </tr>
        <tr>
        <td>A</td>
        <td colspan="15">Printed By: '.$adminFullName.'</td>
        </tr>';
    
        $html .= '
                </table>
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