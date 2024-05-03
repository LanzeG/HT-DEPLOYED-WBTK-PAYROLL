<?php
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


<body>
    <section class="w3l-mockup-form">
        <div class="container">
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="./img/direct.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">               
    <h2>Contact Admin</h2>
    <p>If you're experiencing issues logging in, please fill out the form below:</p>
    <form id="contact-form" action="https://api.web3forms.com/submit" method="POST">
        <input type="hidden" name="access_key" value="ed524e4a-0387-4386-bb62-697b4a3007df">
        <div class="form-group">
            <input id="email" name="email" type="email" required placeholder="Your Email">
        </div>
        <div class="form-group">
            <textarea id="concern" name="Concern" rows="4" required placeholder="Describe Your Concern"></textarea>
        </div>
        <div id="captcha" class="h-captcha" data-captcha="true" data-callback="captchaCompleted"></div>
        <button id="submit-btn" type="submit" style="display: none;">Submit Form</button>
    </form>
    <div class="social-icons">
        <p>Back to <a href="default.php">Login</a>.</p>
    </div>
    <script>
        function captchaCompleted() {
            document.getElementById("submit-btn").style.display = "block";
            document.getElementById("captcha").style.display = "none";
        }
    </script>
    <script src="https://web3forms.com/client/script.js" async defer></script>
</div>
</section>
</body>
</html>

<style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: linear-gradient(190deg, #FFFFFF, #c1d8fb);
      height: 100vh;
    }
        label {
            display: block;
        }
        textarea {
            width: 100%;
            height: 70px; 
            resize: vertical; 
            -webkit-appearance: none;
            outline: none;
            font-family: 'Poppins', sans-serif;
            border-radius: 4px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -o-border-radius: 4px;
            -ms-border-radius: 4px;
            outline: none;
            margin-bottom: 15px;
            font-size: 16px;
            color: #999;
            text-align: left;
            padding: 14px 20px;
            width: 100%;
            display: inline-block;
            box-sizing: border-box;
            border: none;
            outline: none;
            background: transparent;
            border: 1px solid #e5e5e5;
            transition: 0.3s all ease;
        }
        .h-captcha {
    max-width: 100%;
}
    </style>