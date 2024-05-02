<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");
date_default_timezone_set('Asia/Hong_Kong'); 
session_start();

$error = false;
$adminId = $_SESSION['adminId'];

if(isset($_POST['submit_btn'])){
    $pperiodstart = $_POST['dppstart'];
    $pperiodend = $_POST['dppend'];
    $pperiodrange = "$pperiodstart to $pperiodend";
    $date = strtotime($pperiodstart);
    $pperiodyear = date("Y",$date);
    $startdateinit = strtotime($pperiodstart);
    $startdate = date("Y-m-d", $startdateinit);
    $enddateinit = strtotime($pperiodend);
    $cutoff = date("Y-m-d", $enddateinit);
    $pperiodstartdate = new DateTime($pperiodstart);
    $pperiodenddate = new DateTime($pperiodend);
    $payperioddays = $pperiodenddate->diff($pperiodstartdate)->format("%a");
    $pres = ($payperioddays + 1);

    if(empty($pperiodstart)){
        $error = true;
        $periodstarterror = "Please enter payroll period start date.";
    }

    if(empty($pperiodend)){
        $error = true;
        $periodenderror = "Please payroll period end date.";
    }

    if(empty($pperiodrange)){
        $error = true;
        $periodrangeerror = "No payroll period range.";
    }

    if(empty($pperiodyear)){
        $error = true;
        $periodyearerror = "No payroll period year.";
    }

    if(empty($pres)){
        $error = true;
        $perioddayserror = "Payroll number of days not specified.";
    }

    $pperiodrangecheckqry = "SELECT pperiod_range FROM payperiods where pperiod_range = '$pperiodrange'";
    $pperiodrangecheckexecqry = mysqli_query($conn, $pperiodrangecheckqry);
    $pperiodrangecheckcount = mysqli_num_rows($pperiodrangecheckexecqry);

    if ($pperiodrangecheckcount != 0){
        $error = true;
        $pperiodrangeerror = "Payroll period already exists.";
    }

    if (!$error){
        $newshiftqry = "INSERT INTO payperiods (pperiod_start, pperiod_end, pperiod_range, pperiod_year, payperiod_days) VALUES ('$startdate','$cutoff','$pperiodrange','$pperiodyear','$pres')";
        $newshiftqryresult = mysqli_query($conn, $newshiftqry) or die ("FAILED TO CREATE NEW PAYROLL PERIOD ".mysqli_error($conn));
        $activityLog = "Added a new payroll period ($pperiodrange)";
        $adminActivityQuery = "INSERT INTO adminactivity_log (emp_id, activity, log_timestamp) VALUES ('$adminId', '$activityLog', NOW())";
        $adminActivityResult = mysqli_query($conn, $adminActivityQuery);

        if ($newshiftqryresult) {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        text: "Payroll Period inserted successfully",
                        icon: "success",
                        button: "OK",
                    }).then(function() {
                        window.location.href = 'adminPAYROLLPERIODS.php';
                    });
                });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    text: "Something went wrong.",
                    icon: "error",
                    button: "Try Again",
                });
            });
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <title>Date Picker Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
</head>

<body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

    <div class="container mt-5">
        <h2>Date Picker Example</h2>

        <div class="form-group">
            <label for="start-date">Start Date:</label>
            <input type="text" class="form-control datepicker" id="start-date" name="dppstart" placeholder="Date" value="<?php echo isset($pperiodstart) ? $pperiodstart : ''; ?>" />
            <?php echo isset($periodstarterror) ? $periodstarterror : ''; ?>
        </div>

        <div class="form-group">
            <label for="end-date">End Date:</label>
            <input type="text" class="form-control datepicker" id="end-date" name="dppend" placeholder="Date" value="<?php echo isset($pperiodend) ? $pperiodend : ''; ?>" />
            <?php echo isset($periodenderror) ? $periodenderror : ''; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success" name="submit_btn">Submit</button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>

</body>

</html>
