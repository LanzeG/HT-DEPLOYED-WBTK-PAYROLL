<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dept_query = "SELECT DISTINCT dept_NAME FROM employees";
$dept_result = $conn->query($dept_query);

$sy_query = "SELECT DISTINCT sy FROM schedule";
$sy_result = $conn->query($sy_query);

$sem_query = "SELECT DISTINCT semester FROM schedule";
$sem_result = $conn->query($sem_query);

$type_query = "SELECT DISTINCT schedule_type FROM schedule";
$type_result = $conn->query($type_query);

$filter_by = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';
$search_value = isset($_GET['search_value']) ? $_GET['search_value'] : '';
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$sy = isset($_GET['sy']) ? $_GET['sy'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$schedule_type = isset($_GET['schedule_type']) ? $_GET['schedule_type'] : '';

$sql = "SELECT 
            s.schedule_id,
            s.emp_id, 
            e.last_name, 
            s.sy, 
            s.semester, 
            s.day_of_week, 
            s.start_time, 
            s.end_time, 
            s.schedule_type, 
            s.level 
        FROM schedule s
        JOIN employees e ON s.emp_id = e.emp_id
        WHERE 1=1";

if ($filter_by && $search_value) {
    $sql .= " AND e.$filter_by LIKE '%$search_value%'";
}

if ($dept) {
    $sql .= " AND e.dept_NAME = '$dept'";
}

if ($sy) {
    $sql .= " AND s.sy = '$sy'";
}

if ($semester) {
    $sql .= " AND s.semester = '$semester'";
}

if ($schedule_type) {
    $sql .= " AND s.schedule_type = '$schedule_type'";
}

$sql .= " ORDER BY s.day_of_week, s.start_time";

$result = $conn->query($sql);

$schedule_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedule_data[$row["day_of_week"]][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Table</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script>
        function toggleEdit(day) {
    var rows = document.querySelectorAll(`[data-day='${day}']`);
    rows.forEach(row => {
        let spans = row.querySelectorAll('span');
        let inputs = row.querySelectorAll('input');
        spans.forEach(span => {
            span.classList.toggle('d-none');
        });
        inputs.forEach(input => {
            input.classList.toggle('d-none');
        });
    });
    document.querySelectorAll(`.edit-btn[data-day='${day}']`).forEach(btn => btn.classList.toggle('d-none'));
    document.querySelectorAll(`.save-btn[data-day='${day}']`).forEach(btn => btn.classList.toggle('d-none'));
    document.querySelectorAll(`.delete-btn[data-day='${day}']`).forEach(btn => btn.classList.toggle('d-none'));
}

function saveChanges(day) {
    var rows = document.querySelectorAll(`[data-day='${day}']`);
    var updates = [];
    rows.forEach(row => {
        var scheduleIdInput = row.querySelector(".schedule-id input");
        var empIdInput = row.querySelector(".emp-id input");
        var lastNameInput = row.querySelector(".last-name input");
        var syInput = row.querySelector(".sy input");
        var semesterInput = row.querySelector(".semester input");
        var startTimeInput = row.querySelector(".start-time input");
        var endTimeInput = row.querySelector(".end-time input");
        var scheduleTypeInput = row.querySelector(".schedule-type input");
        var levelInput = row.querySelector(".level input");

        if (scheduleIdInput && empIdInput && lastNameInput && syInput && semesterInput && startTimeInput && endTimeInput && scheduleTypeInput && levelInput) {
            updates.push({
                schedule_id: scheduleIdInput.value,
                emp_id: empIdInput.value,
                last_name: lastNameInput.value,
                sy: syInput.value,
                semester: semesterInput.value,
                start_time: startTimeInput.value,
                end_time: endTimeInput.value,
                schedule_type: scheduleTypeInput.value,
                level: levelInput.value
            });
        } else {
            console.error("One or more input fields not found in the row.");
        }
    });

    if (updates.length > 0) {
        $.ajax({
            type: "POST",
            url: "save_schedule.php",
            data: JSON.stringify(updates),
            contentType: "application/json",
            success: function (response) {
                Swal.fire({
                    text: response,
                    icon: "success",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didClose: () => {
                        location.reload();
                    }
                });
            }
        });
    }
}

function deleteSchedule(schedule_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        toast: true,
        showCancelButton: true,
        confirmButtonColor: '#28ed87',
        cancelButtonColor: '#D1473D',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "schedule_delete.php",
                data: { schedule_id: schedule_id },
                success: function (response) {
                    Swal.fire({
                        text: response,
                        icon: "success",
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didClose: () => {
                            location.reload();
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error deleting schedule: ", error);
                    console.error("Status: ", status);
                    console.error("Response: ", xhr.responseText);
                    Swal.fire({
                        text: "Error deleting schedule: " + error,
                        icon: "error",
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }
            });
        }
    });
}
    </script>
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-card {
            margin-top: 50px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include('navbarAdmin.php'); ?>
    <div class="title d-flex justify-content-center">
        <h3 style="margin-top:20px;">Faculty Schedule</h3>
    </div>
    <div class="container-fluid mt-5">
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row mb-3">
                        <div class="col-lg-2 col-md-6 form-group">
                            <label for="filter_by" class="form-label">Search By:</label>
                            <select id="filter_by" class="form-select" name="filter_by" style="border-radius:10px;">
                                <option value="" <?php if ($filter_by == '') echo 'selected'; ?>>Search by</option>
                                <option value="emp_id" <?php if ($filter_by == 'emp_id') echo 'selected'; ?>>Employee ID</option>
                                <option value="last_name" <?php if ($filter_by == 'last_name') echo 'selected'; ?>>Last Name</option>
                                <option value="first_name" <?php if ($filter_by == 'first_name') echo 'selected'; ?>>First Name</option>
                                <option value="user_name" <?php if ($filter_by == 'user_name') echo 'selected'; ?>>Username</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 form-group">
                            <label for="search_value" class="form-label">Search</label>
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search" name="search_value" id="search_value" value="<?php echo htmlspecialchars($search_value); ?>" style="border-radius:10px;">
                        </div>
                        <div class="col-lg-3 col-md-6 form-group">
                            <label for="dept" class="form-label">Department</label>
                            <select id="dept" class="form-select" name="dept" style="border-radius:10px;">
                                <option value="" selected>Select Department</option>
                                <?php while ($row = $dept_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['dept_NAME']; ?>" <?php if ($dept == $row['dept_NAME']) echo 'selected'; ?>><?php echo $row['dept_NAME']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 form-group">
                            <label for="sy" class="form-label">School Year</label>
                            <select id="sy" class="form-select" name="sy" style="border-radius:10px;">
                                <option value="" selected>Select School Year</option>
                                <?php while ($row = $sy_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['sy']; ?>" <?php if ($sy == $row['sy']) echo 'selected'; ?>><?php echo $row['sy']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 form-group">
                            <label for="semester" class="form-label">Semester</label>
                            <select id="semester" class="form-select" name="semester" style="border-radius:10px;">
                                <option value="" selected>Select Semester</option>
                                <?php while ($row = $sem_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['semester']; ?>" <?php if ($semester == $row['semester']) echo 'selected'; ?>><?php echo $row['semester']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 form-group">
                            <label for="schedule_type" class="form-label">Schedule Type</label>
                            <select id="schedule_type" class="form-select" name="schedule_type" style="border-radius:10px;">
                                <option value="" selected>Select Schedule Type</option>
                                <?php while ($row = $type_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['schedule_type']; ?>" <?php if ($schedule_type == $row['schedule_type']) echo 'selected'; ?>><?php echo $row['schedule_type']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-2 col-md-6 form-group d-flex align-items-end">
                            <button class="btn btn-success px-3 mr-2" type="submit" style="border-radius:10px;">Apply</button>
                            <button onclick="window.location.href='schedule_faculty.php';" class="btn btn-success px-3 mr-2" type="button" style="border-radius:10px;">Refresh</button>
                            <button onclick="window.location.href='faculty_official_time.php';" class="btn btn-success px-3" type="button" style="border-radius: 10px; display: flex; align-items: center;">
                                <i class="fas fa-plus" style="margin-right: 5px;"></i> Schedule
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped col-12">
                <thead class="thead-light">
                    <tr>
                        <th>Day of Week</th>
                        <th>Employee ID</th>
                        <th>Last Name</th>
                        <th>SY</th>
                        <th>Semester</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Schedule Type</th>
                        <th>Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php
                    if (!empty($schedule_data)) {
                        foreach ($schedule_data as $day => $schedules) {
                            $rowCount = count($schedules);
                            $first = true;
                            foreach ($schedules as $index => $schedule) {
                                echo "<tr data-day='{$day}'>";
                                if ($first) {
                                    echo "<td rowspan='{$rowCount}'>" . $day . "</td>";
                                    $first = false;
                                }
                                echo "<td class='emp-id'><span>{$schedule['emp_id']}</span><input type='text' class='form-control d-none' value='{$schedule['emp_id']}' readonly></td>";
                                echo "<td class='last-name'><span>{$schedule['last_name']}</span><input type='text' class='form-control d-none' value='{$schedule['last_name']}' readonly></td>";
                                echo "<td class='sy'><span>{$schedule['sy']}</span><input type='text' class='form-control d-none' value='{$schedule['sy']}'></td>";
                                echo "<td class='semester'><span>{$schedule['semester']}</span><input type='text' class='form-control d-none' value='{$schedule['semester']}'></td>";
                                echo "<td class='start-time'><span>{$schedule['start_time']}</span><input type='text' class='form-control d-none' value='{$schedule['start_time']}'></td>";
                                echo "<td class='end-time'><span>{$schedule['end_time']}</span><input type='text' class='form-control d-none' value='{$schedule['end_time']}'></td>";
                                echo "<td class='schedule-type'><span>{$schedule['schedule_type']}</span><input type='text' class='form-control d-none' value='{$schedule['schedule_type']}'></td>";
                                echo "<td class='level'><span>{$schedule['level']}</span><input type='text' class='form-control d-none' value='{$schedule['level']}'></td>";
                                echo "<td class='schedule-id d-none'><input type='text' class='form-control' value='{$schedule['schedule_id']}'></td>";
                                echo "<td>
                                    <button class='btn btn-warning edit-btn' data-day='{$day}' onclick='toggleEdit(\"{$day}\")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='btn btn-success save-btn d-none' data-day='{$day}' onclick='saveChanges(\"{$day}\")'>
                                        <i class='fas fa-save'></i>
                                    </button>
                                    <button class='btn btn-danger delete-btn d-none' data-day='{$day}' onclick='deleteSchedule({$schedule['schedule_id']})'>
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                  </td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found</td></tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
