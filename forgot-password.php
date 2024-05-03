<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>TIMEKEEPING AND PAYROLL</title>
    <link rel="icon" type="image/png" href="./img/images.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="loginnew.css" type="text/css" media="all" />
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <section class="w3l-mockup-form">
        <div class="container">
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <!-- <div class="alert-close">
                        <span class="fa fa-close"></span>
                    </div> -->
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="./img/image3.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Forgot Password</h2>
                        <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS </p>
                        <form action="" class="form-control" method="POST">
                            <input type="email" class="input" name="email" placeholder="E-mail address" required>
                            <button name="recover" class="submit-btn" type="submit" value="Recover">Send Reset Link</button>
                        </form>
                        <div class="social-icons">
                            <p>Back to <a href="default.php" style="color:blue;">Login</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
    
</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function (c) {
            $('.alert-close').on('click', function (c) {
                $('.main-mockup').fadeOut('slow', function (c) {
                    $('.main-mockup').remove();
                });
            });
        });
    </script>
</body>
</html>
<?php
include("DBCONFIG.PHP");

session_start();

require_once "email.php";
if(isset($_POST['recover'])) {
    // Retrieve the email entered in the form
    $email = $_POST['email'];
    $token = md5(rand());

    // Query to retrieve the email from the database
    $check_email = "SELECT * FROM employees WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($check_email_run) > 0) {
        $row = mysqli_fetch_array($check_email_run);
        $lname = $row['user_name'];
        $fname = $row['first_name'];
        $getemail = $row['email'];

        $update_token = "UPDATE employees SET verify_token='$token', token_created_at=NOW() WHERE email ='$getemail'";
        $update_token_run = mysqli_query($conn, $update_token);

        if ($update_token_run) {
            send_password_reset($fname, $lname, $email, $token);
                // Email sent successful
        }

    } else {
       
            echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Email not Found.',
              timer: 5000,
              timerProgressBar: true,
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              customClass: {
                
                popup: 'swal2-popup-custom', // custom class name
              },
            });
        </script>";


           
          }

    // if($result) {
    //     // Check if the email exists in the database
    //     if(mysqli_num_rows($result) > 0) {
    //         // Email exists in the database, do something (e.g., send reset link)
    //         echo "<script>Swal.fire({icon: 'success', title: 'Success', text: 'Email sent successfully!'}).then(function() { window.location = 'forgot-password.php'; });</script>";
    //     } else {
    //         // Email doesn't exist in the database
    //         echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Email not found in the database.'});</script>";
    //     }
    // } else {
    //     // Error in executing the query
    //     echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Error: " . mysqli_error($connection) . "'});</script>";
    // }
}
?>

