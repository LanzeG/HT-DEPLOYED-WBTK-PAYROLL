// function updateNotifications() {
//     // Make an AJAX request to your notifications.php
//     $.ajax({
//         url: 'notifications.php',
//         type: 'GET',
//         dataType: 'json',
//         success: function (response) {
//             // Update or create dropdown items
//             var dropdownMenu = $('#notification-icon .notification-dropdown');
//             dropdownMenu.empty();

//             // If there are unread notifications, display them
//             if (response.count > 0) {
//                 // Update the badge count and show it
//                 $('#notification-icon .notification-badge').text(response.count).show();

//                 // Iterate through each notification in the response
//                 $.each(response.notifications, function (index, notification) {
//                     // Specify the appropriate links for each notification based on its type
//                     var link = '#'; // Default link

//                     if (notification.type === 'Overtime') {
//                         link = './OVERTIME/adminOT.php';
//                     } else if (notification.type === 'Leave') {
//                         link = './LEAVES/adminLEAVES.php';
//                     }

//                     // Make notifications clickable and link to the appropriate page
//                     var notificationItem = $('<div class="notification-item"><a href="' + link + '">' + notification.message + '</a></div>');
//                     dropdownMenu.append(notificationItem);
//                 });
//             } else {
//                 // No new notifications
//                 dropdownMenu.append('<div class="notification-item"><a href="all_notifications.php">See All Notifications</a></div>');
//             }

//             // Always add "See All Notifications" link
            

//             // Hide the badge when there are no notifications
//             $('#notification-icon .notification-badge').toggle(response.count > 0);
//         },
//         error: function (error) {
//             console.error('Error checking notifications:', error.responseText);
//         }
//     });
// }
