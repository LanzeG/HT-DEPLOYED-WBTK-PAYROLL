<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style11.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <script src="notificationScript.js"></script>

<link rel="stylesheet" href="notificationStyle.css">
<?php
include("../DBCONFIG.PHP");
include("../BASICLOGININFO.PHP");

session_start();

$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];
$adminId = $_SESSION['adminId'];
$adminname = "SELECT first_name, last_name, img_tmp FROM employees where emp_id = '$adminId'";
$adminnameexecqry = mysqli_query($conn, $adminname) or die ("FAILED TO CHECK EMP ID ".mysqli_error($conn));
$adminData = mysqli_fetch_assoc($adminnameexecqry);

$adminFullName = $adminData['first_name'] . " " . $adminData['last_name'];
$imgTmp = $adminData['img_tmp']; 
?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        toggle.classList.toggle('bx-x');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            const linkColor = document.querySelectorAll('.nav_link')

            function colorLink() {
                if (linkColor) {
                    linkColor.forEach(l => l.classList.remove('active'))
                    this.classList.add('active')
                }
            }

            linkColor.forEach(l => l.addEventListener('click', colorLink))
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.sidebar .nav-link').forEach(function (element) {

                element.addEventListener('click', function (e) {

                    let nextEl = element.nextElementSibling;
                    let parentEl = element.parentElement;

                    if (nextEl) {
                        e.preventDefault();
                        let mycollapse = new bootstrap.Collapse(nextEl);

                        if (nextEl.classList.contains('show')) {
                            mycollapse.hide();
                        } else {
                            mycollapse.show();
                            var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                            if (opened_submenu) {
                                new bootstrap.Collapse(opened_submenu);
                            }
                        }
                    }
                }); // addEventListener
            }) // forEach
        });
    </script>


    <style>
ul{
    font-family: 'Poppins', sans-serif;
}
li{
    font-family: 'Poppins', sans-serif;
}
        .sidebar li .submenu {
            list-style: none;
            margin: 0;
            padding: 0;
            padding-left: 1rem;
            padding-right: 1rem;
        }


.header_notification {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    cursor: pointer;
}
.notification-dropdown {
    display: none;
    position: absolute;
    top: 30px; 
    right: 0;
    min-width: 200px;
    background-color: #fff;
    border-radius: 5px;
    padding: 10px;
}
.notification-container {
    position: relative;
}

.notification-bell {
    cursor: pointer;
    position: relative;
    display: inline-block;
}

.bell-icon {
    font-size: 24px;
    cursor: pointer;
}


    </style>
    <title>Document</title>
</head>

<body id="body-pd">
<header class="header" id="header">
<div class="header_toggle"> 
  <i class='bx bx-menu' id="header-toggle"></i> 
  <span class="admin-management">ADMIN MANAGEMENT</span>
</div>

    <div id="notification-icon" class="notification-container">
   <span class="icon-wrapper" style="margin-left: 5px; padding-bottom: 10px;">
    <a href="adminVIEWprofile.php?id=<?php echo $empid ?>" style="color: black;">
        <span class="icon-text"><?php echo $adminFullName; ?></span>
        <span class="icon fas fa-user profile-icon"></span>
    </a>
</span>


<style>
.icon-wrapper .icon-text {
    display: inline;
    font-family: 'Poppins', sans-serif;
    text-decoration: none;
}
.icon-wrapper .icon-text:hover{
    text-decoration: none;
}

.icon-wrapper .icon {
    display: none; 
}

@media (max-width: 768px) {
    .icon-wrapper .icon-text {
        display: none; 
    }

    .icon-wrapper .icon {
        display: inline; 
    }
}

.admin-management {
  font-size: 23px;
}

@media screen and (max-width: 768px) {
  .admin-management {
    font-size: 15px;
  }
}

</style>


    <button class="bell-icon" style="background: none; border: none;">&#128276;</button>
    <div class="notification-badge" style="font-family:arial;">0</div> 
    <div class="notification-dropdown">
    </div>
</div>

</header>
    <div class="l-navbar" id="nav-bar" >
        <nav class="nav sidebar" >
            
            <div> 
            <a href="admintry.php" class="nav_logo" style="text-decoration: none;">
    <img src="../img/cube.png" alt="Manage Account Icon" style="width: 20px; height: 20px;">
    <span class="nav_logo-name" style="font-family: 'Poppins', sans-serif;">Manage Account</span>
</a>
            <div class="nav_list">

<li>
    <a href="admintry.php" class="nav_link nav-link mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Dashboard">
        <img src="../img/presentation.png" alt="Dashboard Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Dashboard
    </a>
</li>

<li class="nav-item">
    <a href="adminactivitylogs.php" class="nav_link nav-link mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Activity Logs">
        <img src="../img/3d-alarm.png" alt="Activity Logs Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Activity Logs
    </a>
</li>

           <li class="nav-item has-submenu">
    <a href="try.php" class="nav_link nav-link mb-0 mt-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Management">
        <img src="../img/folder.png" alt="Data Management Icon" style="width: 20px; height: 20px;">
        <span style="font-family: 'Poppins', sans-serif; font-size: 1rem;">Manage Data     <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i>  </span>
       
    </a>
    <ul class="submenu collapse">
          
			<li><a class="nav-link text-white" href="adminMasterfileTry.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Employee" ><i class='bx bx-user nav_icon'></i> Employees </a></li>
			<li><a class="nav-link text-white" href="adminMasterfileDeptTry.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Department"><i class='bx bx-buildings nav_icon'></i> Department </a></li>
			<li><a class="nav-link text-white" href="adminMasterfileLeave.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Leave" ><i class='bx bx-time nav_icon'></i> Leave </a></li>
            <li><a class="nav-link text-white" href="adminPAYROLLPERIODS.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Payroll Period"><i class='bx bx-calendar nav_icon'></i> Payroll Period </a></li>
            <li><a class="nav-link text-white" href="adminPositions.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage"><i class='fa-solid fa-sitemap nav_icon'></i> Positions </a></li>
            <li><a class="nav-link text-white" href="adminSalaryGrades.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Employee"><i class="fa-solid fa-file-invoice-dollar nav_icon"></i> Salary Grades</a></li>
		</ul>
        <li class="nav-item has-submenu">
    <a href="adminMasterfile.php" class="nav_link nav-link mb-2 mt-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Attendance">
        <img src="../img/human.png" alt="Attendance Icon" style="width: 20px; height: 20px;">
        <span style="font-family: 'Poppins', sans-serif; font-size: 1rem; padding-left: 8px;">Attendance <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i></i></span>
        
 
    </a>
    <!-- submenu items go here -->


		<ul class="submenu collapse">
			<li ><a class="nav-link text-white" href="adminATTENDANCErecordsTry.php"><i class='fa-solid fa-clipboard nav_icon'></i> Records</a></li>
			<li><a class="nav-link text-white" href="adminEveningService.php"><i class='fa-solid fa-clock nav_icon'></i> Evening Service</a></li>
            <li><a class="nav-link text-white" href="adminLeaves.php"><i class="fa-solid fa-users nav-icon"></i> Leaves </a></li>
            <li><a class="nav-link text-white" href="manualtime.php"><i class="fa-solid fa-clock nav_icon"></i> Time In/Out </a></li>
		</ul>
	</li>

    <li class="nav-item has-submenu">
    <a href="adminMasterfile.php" class="nav_link nav-link mb-2 mt-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Payroll Management">
        <img src="../img/credit-card.png" alt="Payroll Management Icon" style="width: 20px; height: 20px;">
        <span style="font-family: 'Poppins', sans-serif; font-size: 1rem; padding-left: 8px;">Payroll  <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i></span>
       
    </a>
            <ul class="submenu collapse">
                <li><a class="nav-link text-white" href="adminPayperperiod.php"><i class='fa-solid fa-user nav_icon'></i> Payroll</a></li>
                <li><a class="nav-link text-white" href="adminMasterLoans.php"><i class='fa-solid fa-landmark nav_icon'></i> Add Loans </a></li>
                <li><a class="nav-link text-white" href="adminADDLOANSTYPE.php"><i class="fa-solid fa-money-check-dollar nav-icon"></i> Add Loan Type</a></li>
                <li><a class="nav-link text-white" href="adminPAYROLLProcess.php"><i class="fa-solid fa-users nav-icon"></i> Payroll Process  </a></li>
                <li><a class="nav-link text-white" href="adminPAYROLLInfo.php"><i class="fa-solid fa-users nav-icon"></i> Payroll Information  </a></li>
            </ul>
	</li>

    <li class="nav-item has-submenu">
    <a href="adminMasterfile.php" class="nav_link nav-link mb-2 mt-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Reports">
        <img src="../img/pie-chart.png" alt="Reports Icon" style="width: 20px; height: 20px;">
        <span style="font-family: 'Poppins', sans-serif; font-size: 1rem; padding-left: 8px;">Reports  <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i></span>
       
    </a>
            <ul class="submenu collapse">
                <li><a class="nav-link text-white" href="adminTimesheet.php"><i class='bx bx-buildings nav_icon'></i> DTR</a></li>
                <li><a class="nav-link text-white" href="adminPAYROLLRegister.php"><i class='bx bx-time nav_icon'></i> Payroll Register</a></li>
                <li><a class="nav-link text-white" href="adminPAYROLLPrintPayslip.php"><i class='bx bx-calendar nav_icon'></i> Payslip</a></li>
                <!-- <li><a class="nav-link text-white" href="otslip.php"><i class="fa-solid fa-clock nav_icon"></i> Overtime Slip</a></li> -->
                <!-- <li><a class="nav-link text-white" href="adminGOVTReports.php"><i class='fa-solid fa-sitemap nav_icon'></i> Contributions</a></li> -->
                <!-- <li><a class="nav-link text-white" href="adminREPORTyearly.php"><i class="fa-solid fa-file-invoice-dollar nav_icon"></i> Yearly Report</a></li> -->
            </ul>   
            <li>
    <a href="Announcement.php" class="nav_link nav-link mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="  Announcement">
        <img src="../img/megaphone.png" alt="Announcement Icon" style="width: 20px; height: 20px;"><span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">
        Announcement
    </a>
</li>
<a href="../employeenew/try.php" class="nav_link nav-link mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="  Employee Module">
        <img src="../img/storyboard.png" alt="Employee Module Icon" style="width: 20px; height: 20px;"><span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">
        Employee Module
    </a>
</li>
        
</div>
                    
    
     </div> <a href="../LOGOUT.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sign Out</span> </a>
        </nav>
    </div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const bellIcon = document.querySelector('.bell-icon');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const badge = document.querySelector('.notification-badge');

    function fetchNotifications(markAsRead) {
        console.log(`Fetching notifications (markAsRead: ${markAsRead})...`);
        var xhr = new XMLHttpRequest();
        xhr.open("GET", `notifications.php?mark_as_read=${markAsRead}`, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    console.log('Received data:', data);
                    updateDropdown(data);
                    updateBadge(data.count);
                } else {
                    console.error('Error fetching notifications:', xhr.statusText);
                }
            }
        };
        xhr.send();
    }

    function updateDropdown(data) {
        console.log('Updating dropdown with data:', data);
        notificationDropdown.innerHTML = '';

        if (data.count > 0) {
            data.notifications.forEach(function(notification) {
                const link = getNotificationLink(notification); 
                const title = notification.title ? notification.title : notification.message;
                const notificationItem = document.createElement('div');
                notificationItem.classList.add('notification-item');
                const linkElement = document.createElement('a');
                linkElement.href = link;
                linkElement.style.color = '#050505';
                linkElement.textContent = title;
                notificationItem.appendChild(linkElement);
                notificationDropdown.appendChild(notificationItem);
            });
        } else {
            const noNotificationItem = document.createElement('div');
            noNotificationItem.style.color = '#050505';
            noNotificationItem.textContent = 'No new notifications';
            notificationDropdown.appendChild(noNotificationItem);
        }

        const seeAllLink = document.createElement('div');
        seeAllLink.classList.add('notification-item', 'see-all');
        const seeAllAnchor = document.createElement('a');
        seeAllAnchor.href = 'all_notifications.php';
        seeAllAnchor.style.color = '#050505';
        seeAllAnchor.textContent = 'See All Notifications';
        seeAllLink.appendChild(seeAllAnchor);
        notificationDropdown.appendChild(seeAllLink);
    }

    function getNotificationLink(notification) {
        if (notification.type === 'Overtime') {
            return 'adminOT.php';
        } else if (notification.type === 'Leave') {
            return 'adminLEAVES.php';
        } else if (notification.type === 'Issue') {
            return 'adminFeedback.php?notification_id=' + notification.notification_id;
        } else {
            return '#';
        }
    }

    function updateBadge(count) {
        console.log('Updating badge with count:', count);
        badge.textContent = count;
        badge.style.display = (count > 0) ? 'block' : 'none';
    }

    bellIcon.addEventListener('click', function() {
        console.log('Bell icon clicked');
        notificationDropdown.style.display = (notificationDropdown.style.display === 'block') ? 'none' : 'block';

        if (notificationDropdown.style.display === 'block') {
            fetchNotifications(true);
        }
    });

    document.addEventListener('click', function(event) {
        if (!event.target.classList.contains('bell-icon') && !event.target.closest('.notification-dropdown')) {
            notificationDropdown.style.display = 'none';
        }
    });

    fetchNotifications(false);
});
</script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-pzjw8f+uxSp2F5R1WNfBpkJLEicf5f8CYEv8BH+LZ8AM+CI0UQTZ2GGSnGOIdRENU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>


<style>
.notification-modal-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    z-index: 9999;
    
}

.notification-modal {
    border: 1px solid #dddfe2;
    background-color: #fff;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px; /* Add margin between notifications */
    max-width: 300px; /* Adjust the maximum width of notifications if needed */
    display: none;
}

.notification-modal.show {
    display: flex;
    width:300px;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
}
.close-btn {
    background: transparent;
    border: none;
    color: #90949c;
    font-size: 20px;
    cursor: pointer;
    margin-left: auto;
}

.facebook-icon {
    width: 30px;
    height: 30px;
    object-fit: contain;
    margin-right: 10px;
    margin-top:10px;
}
</style>

<div class="notification-modal-container" id="notificationModalContainer">
    <!-- NOTIFICATION NG MODAL -->
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var shownNotifications = [];

    function loadNotifications() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "notifications.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if (data.count > 0) {
                    data.notifications.forEach(function(notification, index) {
                        var message = notification.message;
                        if (!shownNotifications.includes(message)) {
                            showNotification(message);
                            shownNotifications.push(message);
                        }
                    });
                }
            }
        };
        xhr.send();
    }

    // Initially load notifications
    loadNotifications();

    // Set interval to continuously fetch notifications
    setInterval(loadNotifications, 2000); // Fetch data every 2 seconds
});

function showNotification(title) {
    if (window.location.href.indexOf("all_notifications.php") === -1) {
        var notificationModal = document.createElement('div');
        notificationModal.className = "notification-modal";
        notificationModal.innerHTML = '<div class="notification-header" style="font-size:14px;">New Notification <button class="close-btn" style="float: right;" onclick="closeNotification(this)">✕</button></div><div class="notification-content"><a href="all_notifications.php"><img src="../img/carousel/paper-plane.png" alt="notification" class="facebook-icon"></a><span>' + title + '</span></div>';
        document.getElementById('notificationModalContainer').appendChild(notificationModal);
        notificationModal.style.display = "block";
        setTimeout(function() {
            closeNotification(notificationModal.querySelector('.close-btn'));
        }, 5000);
    }
}

function closeNotification(button) {
    button.closest('.notification-modal').remove();
}
</script>





</body>
    </html>
