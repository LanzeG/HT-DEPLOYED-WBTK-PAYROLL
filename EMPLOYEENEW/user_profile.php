<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];
$selectedPeriod = null;

$getinfoqry = "SELECT * FROM employees WHERE user_name = '$uname'";
$getinfoexecqry = mysqli_query($conn, $getinfoqry) or die ("FAILED TO GET INFORMATION " . mysqli_error($conn));
$getinfoarray = mysqli_fetch_array($getinfoexecqry);
$getinforows = mysqli_num_rows($getinfoexecqry);

if ($getinfoarray && $getinforows != 0) {
    $currprefixid = $getinfoarray['prefix_ID'];
    $currempid = $getinfoarray['emp_id'];
    $currfingerprintid = $getinfoarray['fingerprint_id'];
    $currusername = $getinfoarray['user_name'];
    $currlastname = $getinfoarray['last_name'];
    $currfirstname = $getinfoarray['first_name'];
    $currmiddlename = $getinfoarray['middle_name'];
    $currdateofbirth = $getinfoarray['date_of_birth'];
    $currposition = $getinfoarray['position'];
    $curremptype = $getinfoarray['employment_TYPE'];
    $curraddress = $getinfoarray['emp_address'];
    $currnationality = $getinfoarray['emp_nationality'];
    $currdeptname = $getinfoarray['dept_NAME'];
    $currcontact = $getinfoarray['contact_number'];
    $currdatehired = $getinfoarray['date_hired'];
    $currimg = $getinfoarray['img_name'];
    $_SESSION['empID'] = $currempid;
}

if (isset($_POST['pperiod_btn1'])) {
    $payfunction = $_POST['payfunction'];
    $payperiod = $_POST['payperiod'];
    $_SESSION['payperiods'] = $_POST['payperiod'];
    $_SESSION['payfunction'] = $_POST['payfunction'];

    echo '<script>';
    echo 'var url = "empaction.php?payfunction=' . urlencode($payfunction) . '&payperiod=' . urlencode($payperiod) . '";';
    echo 'window.open(url, "_blank");';
    echo '</script>';
} elseif (isset($_POST['pperiod_btn'])) {
    $payperiod = $_POST['payperiod'];
    $_SESSION['payperiods'] = $_POST['payperiod'];
    $searchquery = "SELECT * FROM employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$empid' AND PAY_PER_PERIOD.pperiod_range = '$payperiod' ORDER BY pperiod_range";
    $search_result = filterTable($searchquery);
} else {
    $searchquery = "SELECT * FROM employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$empid' ORDER BY PAY_PER_PERIOD.pperiod_range ";
    // $search_result = filterTable($searchquery);
}

if (isset($payperiod)) {
    $query = "SELECT * FROM payperiods WHERE pperiod_range = '$payperiod'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = mysqli_fetch_assoc($result);
        $period_start = isset($data['pperiod_start']) ? $data['pperiod_start'] : null;
        $period_end = isset($data['pperiod_end']) ? $data['pperiod_end'] : null;
        $dateTime = new DateTime($period_start);
        $month = $dateTime->format('F');
        $year = $dateTime->format('Y');
    }

    $printquery = "SELECT * FROM DTR, employees WHERE DTR.emp_id = employees.emp_id and DTR.emp_id = '$empid' AND DTR.DTR_day BETWEEN '$period_start' and '$period_end' ORDER BY DTR_day ASC";
    $printqueryexec = mysqli_query($conn, $printquery);
    $printarray = mysqli_fetch_array($printqueryexec);
    $d = strtotime("now");
    $currtime = date("Y-m-d H:i:s", $d);

    if ($printarray) {

        $prefix = $printarray['prefix_ID'];
        $idno = $printarray['emp_id'];
        $lname = $printarray['last_name'];
        $fname = $printarray['first_name'];
        $mname = $printarray['middle_name'];
        $dept = $printarray['dept_NAME'];
        $position = $printarray['position'];

        $name = "$lname, $fname $mname";
        $empID = "$prefix$idno";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - WBTK</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap">
    <style>
    @media(min-width: 640px) {
        .custom-pc-layout {
            max-width: 1024px;
            margin: 0 auto;
        }
    }
    
    p {
        font-family: 'moserat', sans-serif;
    }

    #period {
        font-family: 'moserat', sans-serif;
    }

    h6 {
        font-family: 'Poppins', sans-serif;
    }
    .timeline-divider {
    width: 2px;
    height: 2%;
    background-color: green;
    margin: 0 8px;
    }

    .pointer-item {
        margin-right: 8px;
        margin-top: 2px;
    }
    #editProfileBtn {
    position: absolute;
    bottom: 4rem;
    right: 4rem;
}

@media (max-width: 768px) {
    #editProfileBtn {
        position: static;
        width: 100%;
        margin-top: 1rem; 
    }
}

</style>

<?php include('navbar2.php'); ?> 
<head>
<body class="bg-gray-100">

<div class="mt-20 relative">
    <div class="max-w-lg mx-auto bg-white shadow-md rounded-lg overflow-hidden custom-pc-layout">
        <div class="bg-green-500 p-4 relative">
            <div class="flex items-center">
                <img id="profilePicture" class="h-20 w-20 rounded-full object-cover" src="<?php echo $currimg; ?>" alt="User Profile Picture">

                <div class="ml-2">
                    <h6 class="text-white font-semibold text-lg ml-4"><?php echo $currfirstname . ' ' . $currlastname; ?></h6>
                    <p class="text-sm text-white ml-4"><?php echo $currposition; ?></p>
                    <p class="text-sm text-white ml-4"><?php echo $currdeptname . '  •  ' . $curremptype?>  </p>
                </div>
            </div>
            <button id="editProfileBtn" class="text-white font-semibold text-sm px-4 py-2 rounded-full bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-600 absolute bottom-4 right-4">Edit Profile</button>
        </div>



        <?php
$empid = $_SESSION['empID'];

$periodQuery = "SELECT DISTINCT pperiod_range FROM pay_per_period WHERE emp_id = '$empid'";
$periodResult = mysqli_query($conn, $periodQuery);

$periodOptions = [];

if ($periodResult && mysqli_num_rows($periodResult) > 0) {
    while ($row = mysqli_fetch_assoc($periodResult)) {
        $periodOptions[] = $row['pperiod_range'];
    }
}

if (isset($_GET['period'])) {
    $selectedPeriod = mysqli_real_escape_string($conn, $_GET['period']);

    $dates = explode(' to ', $selectedPeriod);
    $startDate = date('F j, Y', strtotime($dates[0]));
    $endDate = date('F j, Y', strtotime($dates[1]));

    $formattedPeriod = $startDate . ' to ' . $endDate;

    $salaryQuery = "SELECT * FROM pay_per_period WHERE emp_id = '$empid' AND pperiod_range = '$selectedPeriod'";
    
    $salaryResult = mysqli_query($conn, $salaryQuery);
    
    if ($salaryResult && mysqli_num_rows($salaryResult) > 0) {
        $salaryDetails = mysqli_fetch_assoc($salaryResult);
        $firstHalfSalary = $salaryDetails['firsthalf'];
        $secondHalfSalary = $salaryDetails['secondhalf'];
    } else {
        $firstHalfSalary = 0;
        $secondHalfSalary = 0;
    }
}
?>


<div class="px-8 py-2">
   
   
</div>

<div class="px-8 py-2">
    <div class="border-b border-gray-200">
        <div class="flex items-center py-2">
            <svg class="h-6 w-6 text-green-500 mr-2 mb-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
            </svg>
            <div>
            <h6 class="text-gray-800 font-semibold">Monthly Payroll Summary</h6>
            <p class="text-gray-600 text-sm">Payroll Period: <?php echo isset($formattedPeriod) ? ($formattedPeriod ?: 'Not selected') : 'Not selected'; ?></p>
                <p class="text-gray-600 text-sm">1ST HALF : ₱<?php echo $firstHalfSalary ?? '0.00'; ?></p>
                <p class="text-gray-600 text-sm">2ND HALF: ₱<?php echo $secondHalfSalary ?? '0.00'; ?></p>
            </div>
        </div>
        <div class="px-8 py-2">
        <select id="period" name="period" class="block w-half mt-1 form-select appearance-none bg-white border border-gray-300 rounded-md py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:border-blue-500" onchange="location = 'user_profile.php?period=' + this.value;">
    <option value="">Select Payroll Period:</option>
    <?php foreach ($periodOptions as $option): ?>
        <option value="<?php echo $option; ?>" <?php echo ($option == $selectedPeriod) ? 'selected' : ''; ?>><?php echo $option; ?></option>
    <?php endforeach; ?>
</select>
        </div>
    </div>
</div>
<?php
$currentPayrollPeriodQuery = "SELECT * FROM payperiods WHERE pperiod_start <= NOW() AND pperiod_end >= NOW() LIMIT 1";
$currentPayrollPeriodResult = mysqli_query($conn, $currentPayrollPeriodQuery);

if ($currentPayrollPeriodResult && mysqli_num_rows($currentPayrollPeriodResult) > 0) {
    $currentPayrollPeriodData = mysqli_fetch_assoc($currentPayrollPeriodResult);
    $nextPayrollProcessingDate = date("F j, Y", strtotime($currentPayrollPeriodData['pperiod_end']));
} else {
    $nextPayrollProcessingDate = "N/A";
}
?>
<div class="px-8 py-2">
    <div class="border-b border-gray-200">
        <div class="flex items-center py-2">
            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <div>
                <h6 class="text-gray-800 font-semibold">Upcoming Payroll Deadline</h6>
                <p class="text-gray-600 text-sm">Next payroll processing: <?php echo $nextPayrollProcessingDate; ?></p>
            </div>
        </div>
    </div>
</div>
<div class="px-8 py-2 mt-4">
    <div class="border-b border-gray-200">
        <div class="flex items-center py-2">
            <svg class="h-6 w-6 text-blue-500 mr-2" fill="#19be87" width="73px" height="73px" viewBox="-0.98 -0.98 15.96 15.96" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" stroke="#19be87" stroke-width="0.00014"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="m 12.35135,11.7027 -7.45946,0 c -0.30811,0 -1.2973,-1.13514 -1.2973,-1.13514 0,0 0.98919,-1.13513 1.2973,-1.13513 l 7.45946,0 C 12.70811,9.43243 13,9.72433 13,10.08108 l 0,0.97297 c 0,0.35676 -0.29189,0.64865 -0.64865,0.64865 z m 0,-3.56757 -7.45946,0 C 4.58378,8.13513 3.59459,7 3.59459,7 c 0,0 0.98919,-1.13514 1.2973,-1.13514 l 7.45946,0 C 12.70811,5.86486 13,6.15676 13,6.51351 l 0,0.97297 c 0,0.35676 -0.29189,0.64865 -0.64865,0.64865 z m 0,-3.56757 -7.45946,0 c -0.30811,0 -1.2973,-1.13513 -1.2973,-1.13513 0,0 0.98919,-1.13513 1.2973,-1.13513 l 7.45946,0 C 12.70811,2.2973 13,2.58919 13,2.94594 l 0,0.97297 c 0,0.35676 -0.29189,0.64865 -0.64865,0.64865 z M 1.81081,11.37837 C 1.35676,11.37837 1,11.02163 1,10.56756 1,10.11351 1.35676,9.75675 1.81081,9.75675 c 0.45405,0 0.81081,0.35676 0.81081,0.81081 0,0.45407 -0.35676,0.81081 -0.81081,0.81081 z m 0.16216,-3.25945 0,1.32972 c -0.0487,0 -0.11351,-0.0162 -0.16216,-0.0162 -0.0487,0 -0.11351,0.0162 -0.16216,0.0162 l 0,-1.32972 c 0.0487,0 0.11351,0.0162 0.16216,0.0162 0.0487,0 0.11351,-0.0162 0.16216,-0.0162 z M 1.81081,7.81081 C 1.35676,7.81081 1,7.45405 1,7 1,6.54595 1.35676,6.18918 1.81081,6.18918 c 0.45405,0 0.81081,0.35677 0.81081,0.81082 0,0.45406 -0.35676,0.81081 -0.81081,0.81081 z m 0.16216,-3.25946 0,1.32973 c -0.0487,0 -0.11351,-0.0162 -0.16216,-0.0162 -0.0487,0 -0.11351,0.0162 -0.16216,0.0162 l 0,-1.32973 c 0.0487,0 0.11351,0.0162 0.16216,0.0162 0.0487,0 0.11351,-0.0162 0.16216,-0.0162 z M 1.81081,4.24324 C 1.35676,4.24324 1,3.88648 1,3.43243 1,2.97837 1.35676,2.62162 1.81081,2.62162 c 0.45405,0 0.81081,0.35675 0.81081,0.81081 0,0.45405 -0.35676,0.81081 -0.81081,0.81081 z m -0.16216,-1.92973 0,-0.82702 c 0,-0.0973 0.0649,-0.16216 0.16216,-0.16216 0.0973,0 0.16216,0.0649 0.16216,0.16216 l 0,0.82702 c -0.0487,0 -0.11351,-0.0162 -0.16216,-0.0162 -0.0487,0 -0.11351,0.0162 -0.16216,0.0162 z m 0.32432,9.37297 0,0.82703 c 0,0.0973 -0.0649,0.16216 -0.16216,0.16216 -0.0973,0 -0.16216,-0.0649 -0.16216,-0.16216 l 0,-0.82703 c 0.0487,0 0.11351,0.0162 0.16216,0.0162 0.0487,0 0.11351,-0.0162 0.16216,-0.0162 z"></path></g></svg>
            <div>
                <h6 class="text-gray-800 font-semibold">Employee Timeline</h6>
                <p class="text-gray-600 text-sm">Date Hired: <?php echo $currdatehired; ?></p>
                <p class="text-gray-600 text-sm">Position: <?php echo $currposition; ?></p>
            <button id="viewTimelineBtn" class="text-blue-500 mr-2">View Timeline</button>

            </div>
        </div>
        </div>
            <div id="timelineContainer" style="display: none;">
    
            <ul class="timeline px-8 py-2">
        <?php
        $employmentHistoryQuery = "SELECT * FROM employmenthistory WHERE EmployeeID = '$empid' ORDER BY StartDate ASC";
        $employmentHistoryResult = mysqli_query($conn, $employmentHistoryQuery);

        if ($employmentHistoryResult && mysqli_num_rows($employmentHistoryResult) > 0) {
            while ($row = mysqli_fetch_assoc($employmentHistoryResult)) {
            $startDate = isset($row['StartDate']) ? date('M j, Y', strtotime($row['StartDate'])) : "N/A";
            $endDate = isset($row['EndDate']) ? date('M j, Y', strtotime($row['EndDate'])) : "Present";
                $position = isset($row['Position']) ? $row['Position'] : "N/A";
                $department = isset($row['Department']) ? $row['Department'] : "N/A";
                ?>
                <li class="timeline-item">
                    <div class="timeline-content">
                        <div class="pointers">
                            <div class="timeline-divider"></div>
                        </div>
                        <div class="text-gray-400 text-sm timeline-date ml-2"><?php echo $startDate; ?> - <?php echo $endDate; ?></div>
                        <div class="text-gray-600 timeline-title ml-2"><?php echo $position; ?></div>
                        <div class="text-gray-400 text-sm timeline-description ml-2"><?php echo $department; ?></div>
                    </div>
                </li>
            <?php
            }
        } else {
            echo "<li>No employment history found</li>";
        }
        ?>
    </ul>
            </div>
    </div>
</div>

<script>
    document.getElementById('viewTimelineBtn').addEventListener('click', function() {
        var timelineContainer = document.getElementById('timelineContainer');
        if (timelineContainer.style.display === 'none') {
            timelineContainer.style.display = 'block';
        } else {
            timelineContainer.style.display = 'none';
        }
    });
</script>
        </div>
    </div>
</div>
</div>
</div>

<div id="editProfileModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
<form id="profilePictureForm" action="update_profile_picture.php" method="post" enctype="multipart/form-data">
    <div class="bg-gray-50 px-4 py-5 sm:px-6">
        <h6 class="text-lg font-medium text-gray-900">Edit Profile Picture</h6>
    </div>
    <div class="bg-white px-4 py-5 sm:px-6">
        <input type="file" name="profile_picture" accept="image/*" required>
    </div>
    <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-green-300 shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Save
        </button>
        <button id="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Close
        </button>
        <div class = "uinfotab2 mt-3"><a href ="empCHANGEPASS.php" class = "btn btn-success"><span class="icon"><i class="icon-edit"></i> </span>Change Password</a></div>
        
    </div>
</form>

        </div>
    </div>
</div>

<script>
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeModal = document.getElementById('closeModal');

    editProfileBtn.addEventListener('click', () => {
        editProfileModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });

    closeModal.addEventListener('click', () => {
        editProfileModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const profilePictureForm = document.getElementById('profilePictureForm');
        const editProfileModal = document.getElementById('editProfileModal');

        profilePictureForm.addEventListener('submit', function(event) {
            event.preventDefault(); 

            const formData = new FormData(profilePictureForm);

            fetch('update_profile_picture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile picture updated successfully!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: `An error occurred: ${error.message}`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                }).then(() => {
                    location.reload();
                });
            });
        });
    });
</script>




</body>
</html>
