<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>TIMEKEEPING AND PAYROLL</title>

    <link rel="icon" type="image/png" href="./img/images.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Change Password" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="loginnew.css" type="text/css" media="all" />
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: linear-gradient(190deg, #FFFFFF, #c1d8fb);
      height: 100vh;
    }
</style>

<body>

    <!-- form section start -->
    <section class="w3l-mockup-form">
        <div class="container">
            <!-- Form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <!-- <div class="alert-close">
                        <span class="fa fa-close"></span>
                    </div> -->
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="./img/image2.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">               

<?php

include("DBCONFIG.PHP");

// Initialize error message variable
$errorMsg = "";

// Check if the token and email are present in the URL parameters
if(isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $expirationTime = strtotime('-1 minute');

    // Query to check if the token is valid and not expired
    $query = "SELECT * FROM employees WHERE email = '$email' AND verify_token = '$token' AND token_created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
?>
    <h2>Change Password</h2>
    <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS </p>
    <form id="loginform" method="post">
       <input type="hidden" name="passtoken" value="<?php if(isset($_GET['token'])){echo $_GET['token'];}?>">
        <input type="hidden" placeholder="E-mail address" id="email" name="email" value="<?php if(isset($_GET['email'])){echo $_GET['email'];}?>">
        <div class="form-group">
           <label for="newpassword">Password</label>
            <input id="newpassword" name="newpassword" type="password" class="password" required>
        </div>

        <div class="form-group">
            <label for="confirmpassword">Confirm Password</label>
            <input id="confirmpassword" name="confirmpassword" type="password" class="confirm-password" required>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit">Change Password</button>
        </div>
    </form>
        <div class="social-icons">
            <p>Back to! <a href="default.php">Login</a>.</p>
        </div>
       </div>
    </div>
</div>
        <?php
        } else{
        ?>
            <h2>Token Expired</h2>
            <p>Sorry, the token has expired. Please request a new password reset link.</p>
            <a href="default.php" style="display: block; margin-top: 10px; text-decoration:none; color: blue;">Back to Login</a>


        <?php
        }
    } else{
    ?>
        <h2>Oops! Missing Information</h2>
        <p>Please use the secure link provided in your email to access this page.</p>
    <?php
    }
    ?>
            <!-- //Form -->
</div>
</section>
    <!-- //form section end -->

    <!-- Custom Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("loginform").addEventListener("submit", function (event) {
        event.preventDefault();

        // Get form data
        var newPassword = document.getElementById("newpassword").value;
        var confirmPassword = document.getElementById("confirmpassword").value;
        var email = document.getElementById("email").value;

        // Check if passwords match
        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                text: 'Passwords do not match.',
                timer: 5000,
                timerProgressBar: true,
                toast: true,
                position: 'center',
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-popup-custom',
                },
            });
            return; // Prevent form submission
        }

        // Make AJAX request
        $.ajax({
            type: "POST",
            url: "email.php",
            data: {
                newpassword: newPassword,
                email: email
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Password change was successful
                    console.log('success');
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                        timer: 5000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'center',
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                        },
                    }).then((value) => {
                        window.location.href = "default.php";
                    });
                } else {
                    // Password change failed
                    Swal.fire({
                        icon: 'error',
                        text: response.message,
                        timer: 5000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'center',
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                        },
                    });
                }
            },
            error: function (xhr, status, error) {
                // Handle AJAX error
                console.error(xhr.responseText);
            }
        });
    });
});

</script>
</body>

</html>

