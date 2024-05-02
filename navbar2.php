<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="ADMINNEW/notificationScript.js"></script>
    <link rel="stylesheet" href="ADMINNEW/notificationStyle.css">

    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        // show navbar
                        nav.classList.toggle('show');
                        // change icon
                        toggle.classList.toggle('bx-x');
                        // add padding to body
                        bodypd.classList.toggle('body-pd');
                        // add padding to header
                        headerpd.classList.toggle('body-pd');
                    });
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

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

@media (max-width: 767.98px) { 
  .info
{
font-size: 20px;
}
}
    </style>

    <title>Document</title>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <!-- <div class="header_toggle "> <i class='bx bx-menu' id="header-toggle"></i> <span class="info">EMPLOYEE MANAGEMENT</span> </div> -->
        <div class="header_toggle"> 
  <i class='bx bx-menu' id="header-toggle"></i> 
  <span class="info">EMPLOYEE MANAGEMENT</span>
</div>
        <!-- <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div> -->
        <div id="" class="notification-container">
        <button class="bell-icon" style="background: none; border: none;">&#128276;</button>
 
    <div class="notification-badge">0</div> <!-- Placeholder for the badge -->
    <div class="notification-dropdown">
        <!-- Notifications will be displayed here -->
    </div>
    </header>


    <div class="l-navbar" id="nav-bar">
        <nav class="nav sidebar">
            <div> <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">Manage Account</span> </a>  
            <div class="nav_list">
            <a href="try.php" class="nav_link  mb-2 "  > <i class='bx bx-grid-alt nav_icon'></i>Dashboard</a> 
            <a href="newapplyovertime.php" class="nav_link"> <i class="fa-solid fa-clock"></i><span class="nav_name">Apply Overtime</span> </a> 
            <a href="LeaveApplication.php" class="nav_link"> <i class="fa-solid fa-file"></i> <span class="nav_name">Apply Leave</span> </a>

   
            <li class="nav-item has-submenu ">
    <a href="#" class="nav_link nav-link mb-2 mt-2 " ><i class="fa-solid fa-folder"></i><span class="nav_name">My Records</span> </a>

    <!-- <a href="admin/adminPAYROLLINFO.php" class="nav_link nav-link mb-2 mt-2 "> <i class="fa-solid fa-receipt nav-icon"></i> <span class="nav_name">Manage Payroll</span> </a> -->
		<ul class="submenu collapse">
			<li><a class="nav-link text-white" href="empNEWAttendance.php"><i class='fa-solid fa-user nav_icon'></i> Attendance</a></li>
			<li><a class="nav-link text-white" href="empNEWPAYROLL.php"><i class="fa-solid fa-money-check"></i> Payroll </a></li>
            <li><a class="nav-link text-white" href="empLoans.php"><i class="fa-solid fa-hand-holding-hand"></i> Loans </a></li>
            
          </ul>
	</li>
    <a href="empFeedback.php" class="nav_link"> <i class="fa-solid fa-circle-exclamation"></i> <span class="nav_name">Raise Issue</span> </a>

  
                     <!-- <a href="#" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span class="nav_name">Apply Overtime</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link"> <i class='bx bx-message-square-detail nav_icon'></i> <span class="nav_name">Data Management</span> </a>  -->
                     <!-- <a href="admin/adminMasterfile.php" class="nav_link mb-2 mt-2 "> <i class='bx bx-bookmark nav_icon'></i> <span class="nav_name">Attendace Management</span> </a>
                      <a href="adminPAYROLLINFO.php" class="nav_link mb-2 "> <i class='bx bx-folder nav_icon'></i> <span class="nav_name">Payroll</span> </a>
                       <a href="admin/adminTimesheet.php" class="nav_link mb-2"> <i class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Reports </span></a> -->
                     </div>
            </div> <a href="LOGOUT.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">SignOut</span> </a>
        </nav>
    </div>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->

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
=======
            console.log('Updating dropdown with data:', data);
            notificationDropdown.empty();

            if (data.count > 0) {
                $.each(data.notifications, function (index, notification) {
                    const link = getNotificationLink(notification); // Function to determine the link based on notification type
                    const notificationItem = $('<div>').addClass('notification-item').append($('<a>').attr('href', link).text(notification.message));
                    notificationDropdown.append(notificationItem);
                });
            } else {
                const noNotificationItem = $('<div>').text('No new notifications');
                notificationDropdown.append(noNotificationItem);
            }

            // Add "See All Notifications" link
            const seeAllLink = $('<div>').addClass('notification-item see-all').append($('<a>').attr('href', 'all_notifications.php').text('See All Notifications'));
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
    font-size: 15px; /* Adjusted font size for small screens */
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

    // Clear any existing interval
    clearInterval(window.notificationInterval);

    window.notificationInterval = setInterval(loadNotifications, 30000);
});

// Function to show notification
function showNotification(message) {
    if (window.location.href.indexOf("all_notifications.php") === -1) {
        var notificationModal = $('<div class="notification-modal"><div class="notification-header" style="font-size:14px;">New Notification <button class="close-btn" style="float: right;" onclick="closeNotification(this)">✕</button></div><div class="notification-content"><a href="all_notifications.php"><img src="img/images.png" alt="Facebook Icon" class="facebook-icon"></a><span>' + message + '</span></div></div>');
        $('#notificationModalContainer').append(notificationModal);
        notificationModal.fadeIn().addClass('show');
        setTimeout(function() {
            closeNotification(notificationModal.find('.close-btn'));
        }, 5000);
    }
}


// Function to close notification
function closeNotification(button) {
    $(button).closest('.notification-modal').fadeOut(function() {
        $(this).remove();
    });
}
</script>
