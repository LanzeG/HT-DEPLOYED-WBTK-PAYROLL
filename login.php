<?php
include("DBCONFIG.PHP");
include("LoginControl.php");
// include("BASICLOGININFO.PHP");
// Start the session
session_start();
// Check if the user is logged in
if (isset($_SESSION['adminId'])) {
  // User is logged in, redirect to the dashboard or another page
  header("Location: adminnew/admintry.php");
  exit;
} else if (isset($_SESSION['empId'])){
  header("Location: employeenew/try.php");

}

// Prevent caching of this page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TIMEKEEPING AND PAYROLL</title>
 

  <link rel="icon" type="image/png" href="./img/images.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords"
        content="Login Form" />
    <!-- //Meta tag Keywords -->

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!--/Style-CSS -->
    <link rel="stylesheet" href="loginnew.css" type="text/css" media="all" />
    <!--//Style-CSS -->

    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
    
   

<!-- form section start -->
<section class="w3l-mockup-form">
    <div class="container">
        <!-- /form -->
        <div class="workinghny-form-grid" >
            <div class="main-mockup" >
                <!-- <div class="alert-close">
                    <span class="fa fa-close"></span>
                </div> -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="w3l_form align-self">
    <div class="left_grid_info">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./img/carousel/folder3d.png" class="d-block w-100" alt="Image 1">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/chart3d.png" class="d-block w-100" alt="Image 2">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/like3d.png" class="d-block w-100" alt="Image 3">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/shield3d.png" class="d-block w-100" alt="Image 4">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/card3d.png" class="d-block w-100" alt="Image 5">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/paper-plane.png" class="d-block w-100" alt="Image 5">
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel/explorer3d.png" class="d-block w-100" alt="Image 5">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <!-- <span class="sr-only">Previous</span> -->
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <!-- <span class="sr-only">Next</span> -->
            </a>
           
                    </div>
              </div>
        </div>

                <div class="content-wthree" >
                    <h2>Login</h2>
                    <p>WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS  </p>
                    <form class="form-control " method="post" id="loginform" action="LoginControl.php" action="">
                            <input type="text" class="input" name="adminUser" id="admID" placeholder="Enter Your Email" required>
                            <input type="password" class="input" name="adminPass" id="admPASS" placeholder="Enter Your Password" style="margin-bottom: 2px;" required>
                            <p><a href="forgot-password.php" style="margin-bottom: 15px; display: block; text-align: right; color:blue;">Forgot Password?</a></p>
                             <button name="login_btn" name="login_btn" class="submit-btn" type="submit">Login</button>
                        </form>
                    <div class="social-icons">
                        <p>Having trouble? <a href="contact-admin.php" style="color: blue;">Contact Admin</a>.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- //form -->
    </div>
</section>
      <script src="js/jquery.min.js"></script>
      <script src="js/maruti.login.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

<style>
    

    .carousel-item {
        display: none;
        transition: transform 0.6s ease;
    }

    .carousel-item.active {
        display: block;
        transform: translateX(0);
    }

    .carousel-item-next,
    .carousel-item-prev {
        position: absolute;
        top: 0;
        width: 100%;
    }

    .carousel-item-next {
        transform: translateX(100%);
    }

    .carousel-item-prev {
        transform: translateX(-100%);
    }

    .carousel-item-left,
    .carousel-item-right {
        position: relative;
        transform: translateX(0);
    }


    .title1 {
      padding-top: 4rem;


    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-image: linear-gradient(190deg, #FFFFFF, #c1d8fb);
      height: 100vh;
    }

    .title {
      font-size: 28px;
      font-weight: 800;
    }

    
    .submit-btn {
      margin-top: 10px;
      height: 50px;

      border-radius: 10px;
      border: 0;
      outline: none;
      color: #ffffff;
      background: #2d79f3;
      font-size: 18px;
      font-weight: 300;
      box-shadow: 0px 0px 0px 0px #ffffff, 0px 0px 0px 0px #000000;
      transition: all 0.3s cubic-bezier(0.15, 0.83, 0.66, 1);
      cursor: pointer;
    }

    .submit-btn:hover {
      box-shadow: 0px 0px 0px 2px #ffffff, 0px 0px 0px 4px #0000003a;
    }
  @media only screen and (max-width: 600px) {
    .title1 {
      display: none;
    }

  } 
</style>

<?php
if (isset($_SESSION['status'])) {
     echo "<script>
        Swal.fire({
            icon: 'error',
               text: 'Invalid username or password.',
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

        unset($_SESSION['status']);
     }
?>


   <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function (c) {
            $('.alert-close').on('click', function (c) {
                $('.main-mockup').fadeOut('slow', function (c) {
                    $('.main-mockup').remove();
                });
            });
        });
    </script>



<script>
    $(document).ready(function() {

        $('#carouselExampleIndicators').carousel();
        $('.circle').click(function() {
            var index = $(this).index();
            $('#carouselExampleIndicators').carousel(index);
            $('.circle').removeClass('active');
            $(this).addClass('active');
        });

        $('#carouselExampleIndicators').hover(function() {
            $(this).carousel('pause');
        }, function() {
            $(this).carousel('cycle');
        });

        function nextSlide() {
            $('#carouselExampleIndicators').carousel('next');
            setTimeout(nextSlide, 5000);
        }

        setTimeout(nextSlide, 500);
    });
</script>
</body>
<!-- <?php
include("footer.php");
?> -->

</html>