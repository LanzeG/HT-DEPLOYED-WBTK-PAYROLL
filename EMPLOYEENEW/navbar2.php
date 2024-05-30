<?php
include("../DBCONFIG.PHP");
include("../BASICLOGININFO.PHP");

session_start();

$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];

$employeeQuery = "SELECT first_name, last_name, img_tmp, acct_type FROM employees WHERE emp_id = '$empid'";
$employeeResult = mysqli_query($conn, $employeeQuery) or die("FAILED TO CHECK EMP ID " . mysqli_error($conn));

$employeeData = mysqli_fetch_assoc($employeeResult);

if ($employeeData) {
    $employeeFullName = $employeeData['first_name'] . " " . $employeeData['last_name'];
    $imgTmp = $employeeData['img_tmp'];
    $accountType = $employeeData['acct_type'];
} else {
    $employeeFullName = "Unknown Employee";
    $imgTmp = "";
    $accountType = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style11.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <script src="../ADMINNEW/notificationScript.js"></script>
    <link rel="stylesheet" href="../ADMINNEW/notificationStyle.css">

    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
       
            /*===== LINK ACTIVE =====*/
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
                            // find other submenus with class=show
                            var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                            // if it exists, then close all of them
                            if (opened_submenu) {
                                new bootstrap.Collapse(opened_submenu);
                            }
                        }
                    }
                }); // addEventListener
            }) // forEach
        });
    </script>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <style>
        .sidebar li .submenu {
            list-style: none;
            margin: 0;
            padding: 0;
            padding-left: 1rem;
            padding-right: 1rem;
          
        }
        .header_notification {
            font-size: 20px;
            cursor: pointer;
        }
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 30px; /* Adjust this value based on your design */
            right: 0;
            min-width: 200px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        }
        
        .info
        {
            margin:0px;
        }

@media (max-width: 767.98px) { 
  .info
{
font-size: 18px;
}
}
    </style>

    <title>Document</title>
</head>

<body >
    <header class="header body-pd" id="header">
        <!-- <div class="header_toggle "> <i class='bx bx-menu' id="header-toggle"></i> <span class="info">EMPLOYEE MANAGEMENT</span> </div> -->
        <div class="header_toggle d-flex align-items-center">
        <button class="btn" id="toggle-btn" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
            <i class="fa-solid fa-bars" style="font-size: 20px;"></i>
        </button> 
        <h4 class="info">EMPLOYEE MANAGEMENT</h4>
    </div>
        <!-- <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div> -->
        <div id="" class="notification-container">
             <span class="icon-wrapper" style="margin-left: 5px; padding-bottom: 10px;">
    <a href="user_profile.php" style="color: black;">
        <span class="icon-text"><?php echo  $employeeFullName; ?></span>
        <span class="icon fas fa-user profile-icon"></span>
    </a>
</span>
        <button class="bell-icon" style="background: none; border: none; ">&#128276;</button>
        
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

    <div class="notification-badge" style="font-family:arial ;">0</div> <!-- Placeholder for the badge -->
    <div class="notification-dropdown">
        <!-- Notifications will be displayed here -->
    </div>
    </header>


    <div class="l-navbar" id="nav-bar">
        <nav class="nav sidebar">
            <div>   <a href="#" class="nav_logo" style="text-decoration: none;">
    <img src="../img/cube.png" alt="Manage Account Icon" style="width: 20px; height: 20px;">
    <span class="nav_logo-name" style="font-family: 'Poppins', sans-serif;">Manage Account</span>
    </a>
            <div class="nav_list">
            <a href="employee-dashboard.php" class="nav_link  mb-2 " data-bs-toggle="tooltip" data-bs-placement="top" title="Dashboard">
        <img src="../img/presentation.png" alt="Dashboard Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Dashboard
    </a>
            <a href="user_profile.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="top" title="Profile">
        <img src="../img/human.png" alt="Ovetime Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">User Profile
    </a>
            <a href="LeaveApplication.php" class="nav_link"data-bs-toggle="tooltip" data-bs-placement="top" title="Apply Leave">
        <img src="../img/write.png" alt="Leave Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Apply Leave
    </a>
            <a href="dlforms.php" class="nav_link"data-bs-toggle="tooltip" data-bs-placement="top" title="Apply Leave">
        <img src="../img/carousel/curriculum-vitae.png" alt="Leave Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Forms
    </a>
   
            <li class="nav-item has-submenu ">
    <a href="#" class="nav_link nav-link mb-2 mt-2 " data-bs-toggle="tooltip" data-bs-placement="top" title="My Records">
        <img src="../img/folder.png" alt="My Records Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">My Records <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i>
    </a>


    <!-- <a href="admin/adminPAYROLLINFO.php" class="nav_link nav-link mb-2 mt-2 "> <i class="fa-solid fa-receipt nav-icon"></i> <span class="nav_name">Manage Payroll</span> </a> -->
		<ul class="submenu collapse">
             <?php if ($accountType === 'Faculty' || $accountType === 'Faculty w/ Admin'): ?>
                <li><a class="nav-link text-white" href="empSchedule.php"><i class="fas fa-calendar-alt nav_icon"></i> Schedule</a></li>
            <?php endif; ?>
			<li><a class="nav-link text-white" href="empNEWAttendance.php"><i class='fa-solid fa-user nav_icon'></i> Attendance</a></li>
			<li><a class="nav-link text-white" href="empNEWPAYROLL.php"><i class="fa-solid fa-money-check"></i> Payroll </a></li>
			<li><a class="nav-link text-white" href="deductions.php"><i class="fa-solid fa-money-check"></i> Deductions </a></li>
            <li><a class="nav-link text-white" href="empLoans.php"><i class="fa-solid fa-hand-holding-hand"></i> Loans </a></li>
            
          </ul>
	</li>
    <a href="empFeedback.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="top" title="Raise Issue">
        <img src="../img/megaphone.png" alt="Raise Issue Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Raise Concern
    </a>

  
                     <!-- <a href="#" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Apply Overtime</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Data Management</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link mb-2 mt-2 "> <i class='bx bx-bookmark nav_icon'></i> <span class="nav_name">Attendace Management</span> </a>
                      <a href="adminPAYROLLINFO.php" class="nav_link mb-2 "> <i class='bx bx-folder nav_icon'></i> <span class="nav_name">Payroll</span> </a>
                       <a href="admin/adminTimesheet.php" class="nav_link mb-2"> <i class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Reports </span></a> -->
                     </div>
            </div> <a href="../LOGOUT.PHP" class="nav_link"> <i class='fa-solid fa-arrow-right-from-bracket nav_icon'></i> <span class="nav_name">Sign Out</span> </a>
        </nav>
    </div>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->

    <div class="offcanvas offcanvas-start w-75" data-bs-backdrop="static" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <button type="button" class="btn-close btn-close-white" style="color:white;" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav sidebar">
            <div>   <a href="#" class="nav_logo" style="text-decoration: none;">
    <img src="../img/cube.png" alt="Manage Account Icon" style="width: 20px; height: 20px;">
    <span class="nav_logo-name" style="font-family: 'Poppins', sans-serif;">Manage Account</span>
    </a>
            <div class="nav_list">
            <a href="employee-dashboard.php" class="nav_link  mb-2 " data-bs-toggle="tooltip" data-bs-placement="top" title="Dashboard">
        <img src="../img/presentation.png" alt="Dashboard Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Dashboard
    </a>
            <a href="user_profile.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="top" title="Profile">
        <img src="../img/human.png" alt="Ovetime Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">User Profile
    </a>
            <a href="LeaveApplication.php" class="nav_link"data-bs-toggle="tooltip" data-bs-placement="top" title="Apply Leave">
        <img src="../img/write.png" alt="Leave Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Apply Leave
    </a>
    
      <a href="dlforms.php" class="nav_link"data-bs-toggle="tooltip" data-bs-placement="top" title="Apply Leave">
        <img src="../img/carousel/curriculum-vitae.png" alt="Leave Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Forms
    </a>
   

   
            <li class="nav-item has-submenu ">
    <a href="#" class="nav_link nav-link mb-2 mt-2 " data-bs-toggle="tooltip" data-bs-placement="top" title="My Records">
        <img src="../img/folder.png" alt="My Records Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">My Records <i class="fa-solid fa-caret-down" style="color: #00b464; margin-left:5px;"></i>
    </a>


    <!-- <a href="admin/adminPAYROLLINFO.php" class="nav_link nav-link mb-2 mt-2 "> <i class="fa-solid fa-receipt nav-icon"></i> <span class="nav_name">Manage Payroll</span> </a> -->
		<ul class="submenu collapse">
		    <?php if ($accountType === 'Faculty' || $accountType === 'Faculty w/ Admin'): ?>
                <li><a class="nav-link text-white" href="empSchedule.php"><i class="fas fa-calendar-alt nav_icon"></i> Schedule</a></li>
            <?php endif; ?>
			<li><a class="nav-link text-white" href="empNEWAttendance.php"><i class='fa-solid fa-user nav_icon'></i> Attendance</a></li>
			<li><a class="nav-link text-white" href="empNEWPAYROLL.php"><i class="fa-solid fa-money-check"></i> Payroll </a></li>
			<li><a class="nav-link text-white" href="deductions.php"><i class="fa-solid fa-money-check"></i> Deductions </a></li>
            <li><a class="nav-link text-white" href="empLoans.php"><i class="fa-solid fa-hand-holding-hand"></i> Loans </a></li>
            
          </ul>
	</li>
    <a href="empFeedback.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="top" title="Raise Issue">
        <img src="../img/megaphone.png" alt="Raise Issue Icon" style="width: 20px; height: 20px;"> <span  style="
        font-family: 'Poppins', sans-serif; font-size: 1rem;">Raise Concern
    </a>

  
                     <!-- <a href="#" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Apply Overtime</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Data Management</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link mb-2 mt-2 "> <i class='bx bx-bookmark nav_icon'></i> <span class="nav_name">Attendace Management</span> </a>
                      <a href="adminPAYROLLINFO.php" class="nav_link mb-2 "> <i class='bx bx-folder nav_icon'></i> <span class="nav_name">Payroll</span> </a>
                       <a href="admin/adminTimesheet.php" class="nav_link mb-2"> <i class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Reports </span></a> -->
                        <div class="" style="padding-bottom:300px">
            <a href="../LOGOUT.PHP" class="nav_link" > <i class="fa-solid fa-arrow-right-from-bracket"></i> <span class="nav_name">SignOut</span> </a>

            </div>
                     
                     </div>
            </div>
          
        </nav>
    </div>
</div>





    
    <script>
    $(document).ready(function () {
        const bellIcon = $('.bell-icon');
        const notificationDropdown = $('.notification-dropdown');
        const badge = $('.notification-badge');

        function fetchNotifications(markAsRead) {
            console.log(`Fetching notifications (markAsRead: ${markAsRead})...`);
            $.ajax({
                url: `empnotification.php?mark_as_read=${markAsRead}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    console.log('Received data:', data);
                    updateDropdown(data);
                    updateBadge(data.count);
                },
                error: function (error) {
                    console.error('Error fetching notifications:', error);
                }
            });
        }

        function updateDropdown(data) {
    console.log('Updating dropdown with data:', data);
    notificationDropdown.empty();

    if (data.count > 0) {
    $.each(data.notifications, function (index, notification) {
        const link = getNotificationLink(notification); // Function to determine the link based on notification type
        const notificationItem = $('<div>').addClass('notification-item').append($('<a>').attr('href', link).css('color', '#050505').text(notification.message));
        notificationDropdown.append(notificationItem);
    });
} else {
    const noNotificationItem = $('<div>').css('color', '#050505').text('No new notifications');
    notificationDropdown.append(noNotificationItem);
}

// Add "See All Notifications" link
const seeAllLink = $('<div>').addClass('notification-item see-all').append($('<a>').attr('href', 'all_notifications.php').css('color', '#050505').text('See All Notifications'));
notificationDropdown.append(seeAllLink);

}

function getNotificationLink(notification) {
    // Function to determine the link based on notification type
    if (notification.type === 'Overtime') {
        return 'newapplyovertime.php';
    } else if (notification.type === 'Leave') {
        return 'LeaveApplication.php';
    } else if (notification.type === 'Loan') {
        return 'empLoans.php';
    } else if (notification.type === 'Payroll') {
    return 'empNEWPAYROLL.php';
    }
    else if (notification.type === 'Announcement') {
    return 'empAnnouncement.php?notification_id=' + notification.notification_id;
    }
    
    else {
        // Default link
        return '#';
    }
}


        function updateBadge(count) {
            console.log('Updating badge with count:', count);
            badge.text(count);
            badge.css('display', (count > 0) ? 'block' : 'none');
        }

        bellIcon.click(function () {
            console.log('Bell icon clicked');
            notificationDropdown.toggle();

            if (notificationDropdown.is(':visible')) {
                fetchNotifications(true);
            }
        });

        $(document).click(function (event) {
            if (!$(event.target).hasClass('bell-icon') && !$(event.target).closest('.notification-dropdown').length) {
                notificationDropdown.hide();
            }
        });

        // Fetch notifications on page load and update badge
        fetchNotifications(false);
    });
</script>
</body>
    <!--Container Main start-->
    
</html>

<style>
    .header_toggle {
  /* Your existing styles for header_toggle */
}

.info {
  font-size: 23px; /* Default font size */
}

@media screen and (max-width: 768px) {
  .info {
    font-size: 18px; /* Adjusted font size for small screens */
  }
}
</style>
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
    margin-top:2px;
}
</style>

<div class="notification-modal-container" id="notificationModalContainer">
    <!-- NOTIFICATION NG MODAL -->
</div>

<script>
$(document).ready(function() {
    //para hindi mag duplicate ang parehas na notification
    var shownNotifications = [];

    function loadNotifications() {
        $.ajax({
            url: 'empnotification.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
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
        });
    }

    loadNotifications();

    setInterval(loadNotifications, 5000); 
});

function showNotification(message) {
    if (window.location.href.indexOf("all_notifications.php") === -1) {
        var notificationModal = $('<div class="notification-modal"><div class="notification-header" style="font-size:14px;">New Notification <button class="close-btn" style="float: right;" onclick="closeNotification(this)">✕</button></div><div class="notification-content"><a href="all_notifications.php"><img src="../img/carousel/paper-plane.png" alt="Facebook Icon" class="facebook-icon"></a><span>' + message + '</span></div></div>');
        $('#notificationModalContainer').append(notificationModal);
        notificationModal.fadeIn().addClass('show');
        setTimeout(function() {
            closeNotification(notificationModal.find('.close-btn'));
        }, 5000);
    }
}

function closeNotification(button) {
    $(button).closest('.notification-modal').fadeOut(function() {
        $(this).remove();
    });
}
</script>
