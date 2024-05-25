<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    .day-label {
      min-width: 100px;
    }
    body {
      font-family: Poppins, sans-serif;
    }
    .dropdown {
      position: relative;
      display: inline-block;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      background-color: white;
      min-width: 300px;
      box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      z-index: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .show {
      display: block;
    }
  </style>
</head>

<body>
<div class="container mx-auto p-5 flex justify-center items-center min-h-screen">
    <form action="schedule.php" method="POST" id="schedule-form" class="w-full max-w-2xl mx-auto">
        <div class="top p-5 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-5 justify-center items-center">
            <select name="department" id="department-dropdown" class="border border-gray-300 p-2 rounded-md w-full sm:w-auto">
                <option value="">Select Department</option>
                <?php
                $conn = new mysqli('localhost:3307', 'root', '', 'masterdb');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT dept_NAME FROM Department";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['dept_NAME'] . "'>" . $row['dept_NAME'] . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select>
            <select name="employee" id="employee-dropdown" class="border border-gray-300 p-2 rounded-md w-full sm:w-auto">
                <option value="">Select Name</option>
            </select>
            <select name="year" id="year-picker" class="border border-gray-300 p-2 rounded-md w-full sm:w-auto" placeholder="Select Year">
              <option value="" disabled selected>Select Year</option>
            </select>
            <select name="semester" class="border border-gray-300 p-2 rounded-md w-full sm:w-auto">
              <option value="">Select Semester</option>
              <option value="1st">1st Semester</option>
              <option value="2nd">2nd Semester</option>
            </select>
        </div>
        <div id="subject-container" class="space-y-6">
        <!-- Monday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Monday:</span>
            <input type="text" name="monday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Monday Start Time">
            <input type="text" name="monday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Monday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content monday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Tuesday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Tuesday:</span>
            <input type="text" name="tuesday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Tuesday Start Time">
            <input type="text" name="tuesday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Tuesday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content tuesday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Wednesday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Wednesday:</span>
            <input type="text" name="wednesday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Wednesday Start Time">
            <input type="text" name="wednesday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Wednesday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content wednesday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Thursday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Thursday:</span>
            <input type="text" name="thursday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Thursday Start Time">
            <input type="text" name="thursday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Thursday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content thursday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Friday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Friday:</span>
            <input type="text" name="friday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Friday Start Time">
            <input type="text" name="friday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Friday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content friday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Saturday -->
        <div class="subject-field">
          <div class="flex flex-col space-y-2 items-center sm:flex-row sm:space-y-0 sm:space-x-2">
            <span class="day-label">Saturday:</span>
            <input type="text" name="saturday_start" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Saturday Start Time">
            <input type="text" name="saturday_end" class="timepicker border border-gray-300 p-2 rounded-md flex-grow w-full sm:w-auto" placeholder="Saturday End Time">
            <div class="dropdown">
              <button type="button" class="apply-overload bg-green-500 text-white p-2 rounded-md">Overload <i class="fa-solid fa-caret-down"></i></button>
              <div class="dropdown-content saturday-overload flex flex-col space-y-2 mt-2">
                <button type="button" class="add-timepicker bg-green-500 text-white p-2 rounded-md mt-2 w-full max-w-2xl mx-auto">+</button>
              </div>
            </div>
          </div>
        </div>
      <div class="mt-6 flex justify-center">
        <button type="submit" class="bg-blue-500 text-white p-2 rounded-md ">Apply</button>
      </div>
    </form>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // Initialize timepickers
    document.querySelectorAll('.timepicker').forEach(timepicker => {
      flatpickr(timepicker, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
      });
    });

    // Toggle overload dropdown
    document.querySelectorAll('.apply-overload').forEach(button => {
      button.addEventListener('click', function () {
        this.nextElementSibling.classList.toggle('show');
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (event) {
      if (!event.target.matches('.apply-overload')) {
        document.querySelectorAll('.dropdown-content').forEach(content => {
          content.classList.remove('show');
        });
      }
    });

    // Prevent dropdowns from closing when clicking inside
    document.querySelectorAll('.dropdown-content').forEach(content => {
      content.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    });

    // Add new timepickers for overload schedule
    document.querySelectorAll('.add-timepicker').forEach(button => {
      button.addEventListener('click', function () {
        const overloadContainer = this.parentElement;
        const day = overloadContainer.classList[1].split('-')[0];

        if (overloadContainer.querySelectorAll('.flex.flex-row.items-center.space-x-2').length < 3) {
          const newOverload = document.createElement('div');
          newOverload.className = 'flex flex-col space-y-2';

          const uniqueId = Date.now();

          newOverload.innerHTML = `
          <div class="flex flex-col sm:flex-row items-center justify-center sm:space-x-2 space-y-2 sm:space-y-2">
              <input type="text" name="${day}_overload_start[]" class="timepicker border border-gray-300 p-2 rounded-md w-full sm:w-1/3 lg:w-1/4" placeholder="${day.charAt(0).toUpperCase() + day.slice(1)} Overload Start">
              <input type="text" name="${day}_overload_end[]" class="timepicker border border-gray-300 p-2 rounded-md w-full sm:w-1/3 lg:w-1/4" placeholder="${day.charAt(0).toUpperCase() + day.slice(1)} Overload End">
              <div class="flex items-center space-x-1">
                  <label><input type="radio" name="${day}_overload_option_${uniqueId}" value="GD"> GD</label>
                  <label><input type="radio" name="${day}_overload_option_${uniqueId}" value="UG"> UG</label>
              </div>
          </div>
      `;

          overloadContainer.insertBefore(newOverload, this);
          flatpickr(newOverload.querySelectorAll('.timepicker'), {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
          });
        } else {
          Swal.fire({
            title: 'Maximum 3 Overload per day',
            icon: 'info',
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            }
          });
        }
      });
    });

    // Generate school years for the year picker
    const yearPicker = document.getElementById('year-picker');
    let startYear = 2023;
    const yearOffset = 0;

    function generateSchoolYears(startYear, endYear) {
      const schoolYears = [];
      for (let year = startYear; year <= endYear; year++) {
        schoolYears.push(`${year}-${year + 1}`);
      }
      return schoolYears;
    }

    function updateYearOptions() {
      const currentYear = new Date().getFullYear() + yearOffset;
      const schoolYears = generateSchoolYears(startYear, startYear + 100);
      yearPicker.innerHTML = '<option value="" disabled selected>Select Year</option>';
      schoolYears.forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearPicker.appendChild(option);
      });
    }

    updateYearOptions();
    yearPicker.addEventListener('focus', updateYearOptions);

    // Fetch employees based on department selection
    document.getElementById('department-dropdown').addEventListener('change', function() {
      var department = this.value;
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'get_employees.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        if (this.status === 200) {
          document.getElementById('employee-dropdown').innerHTML = this.responseText;
        }
      };
      xhr.send('department=' + department);
    });

   // Function to get query parameters from URL
function getQueryParams() {
  const params = new URLSearchParams(window.location.search);
  return {
    status: params.get('status'),
    message: params.get('message')
  };
}

// Display SweetAlert based on query parameters
const { status, message } = getQueryParams();
if (status && message) {
  Swal.fire({
    title: status === 'success' ? 'Success' : 'Error',
    text: decodeURIComponent(message),
    icon: status === 'success' ? 'success' : 'error',
    toast: true
  }).then(() => {
    // Remove query parameters and refresh the page
    window.history.replaceState({}, document.title, window.location.pathname);
    window.location.reload();
  });
}

  });
</script>

  
</body>

</html>
