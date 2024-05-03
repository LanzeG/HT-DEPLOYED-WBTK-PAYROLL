<?php
include("./DBCONFIG.PHP");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_password_reset($fname, $lname,$getemail,$token)
{
        $mail = new PHPMailer(true);
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'timekeepingweb@gmail.com';                     //SMTP username
        $mail->Password   = 'fupe wrtk rpkg opxm';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('timekeepingweb@gmail.com', 'no-reply@WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM ');
        $mail->addAddress($getemail);     //Add a recipient
    
        //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'One-time Token WBTK';
       
        $mail->AltBody = 'hello';

        $mail->Body    = '<html>
        <!doctype html>
        <html lang="en-US">
        
        <head>
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
            <title>Reset Password Email Template</title>
            <meta name="description" content="Reset Password Email Template.">
            <style type="text/css">
            <style type="loginnew.css">
                a:hover {text-decoration: underline !important;}
            </style>
        </head>
        
        <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
            <!--100% body table-->
            <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
                style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: "Open Sans", sans-serif;">
                <tr>
                    <td>
                        <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                            align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="height:80px;">&nbsp;</td>
                            </tr>
                            <tr>
                                  </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="height:20px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                        style="max-width:670px;background:#fff; border-radius:3px; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06); border-radius:20px; border: 1px solid #D3D3D3;">
                                        <tr>
                                            <td style="height:40px;">&nbsp;</td>
                                        </tr>
                                        <div style="text-align: center; margin-bottom:20px;">
                                        <img src="https://3dicons.sgp1.cdn.digitaloceanspaces.com/v1/dynamic/color/sheild-dynamic-color.png" alt="Footer Image" style="max-width: 150px;">
                                    </div>
                                    
                                        <tr>
                                            <td style="padding:0 35px;" >
                                                <p style="color:#1e1e2d; font-weight:500; margin:0;font-size:15px;font-family: Poppins, sans-serif;">Dear <strong> '.$fname.'</strong>,</p>
                                                <span style="display:inline-block;  vertical-align:middle; margin:20px 0 16px; border-bottom:1px solid gray; width:auto; font-family: Poppins, sans-serif;"></span>

                                                <p style="color:#455056; font-size:15px;line-height:24px; margin:0; font-family: Poppins, sans-serif;">
                                                A unique link to reset your
                                                password has been generated for you.</p>
                                            
                                                <a  href="https://wbtkpayrollportal.com/passwordchange.php?token='.$token.'&email='.$getemail.'"style="background:#20e277;text-decoration:none !important; font-weight:500; margin-top:15px; color:#fff !important; text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:10px; font-family: Poppins, sans-serif;">One-time Token </a>

                                                <p style="color:#455056; font-size:15px;line-height:24px; margin:0; font-family: Poppins, sans-serif; margin-top:15px;">
                                                Please note that the one-time token is valid for 24 hours. If the email code expires, you will need to request a new one.</p>
                                                
                                                <p style="color:#455056; font-size:12px;line-height:24px; margin:0; font-family: Poppins, sans-serif; margin-top:25px;">
                                                Best regards,</p>
                                                <p style="color:#455056; font-size:12px;line-height:24px; margin:0; font-family: Times New Roman, Times, serif;
                                                ">
                                                The WBTK Thesis Team</p>



                                                <p style="color:#455056; font-size:12px;line-height:24px; margin:0; font-family: Poppins, sans-serif; margin-top:25px;">
                                                If you have any questions or concerns about this email, please do not hesitate to contact our support team at timekeepingweb@gmail.com. We are always here to help and ensure that you have the best possible experience with our WBTK Payroll system</p>
                                               
                                                
                                                    
                                            </td>
                                        </tr>
                                        <td style="height:20px;">&nbsp;</td>
                                        <tr>
                                        <td style="text-align:center; margin-top:20px;">
                                        
                                     </td>
                                            <td style="height:40px;">&nbsp;</td>
                                        </tr>
                               
                                </td>
                            <tr>
                          
                        </tr>
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                        <tr>
                        <td style="text-align:center;">
                           
                        </td>
                    </tr>
                    </table>
                            <tr>
                                <td style="height:80px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
         
        </body>
            
        </html>
        <style>
        .cool-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 50px;
          }
    
          .cool-button:hover {
            background-color: #3e8e41;
          }

        </style>
        
        <html> ';

        
        
        
        // "This is the HTML message body <b>in bold!</b>
        // <a href='http://localhost/thesisgithub/thesis-1/passwordchange.php?token=$token&email=$getemail'> click me</a>";
        
        $mail->send();
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Email sent successfully!',
            timer: 5000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            customClass: {
                popup: 'swal2-popup-custom', // custom class name
            },
        }).then(function() {
            window.location = 'default.php';
        });
    </script>";

}


$response = array();

if(isset($_POST['newpassword']) && isset($_POST['email'])) {
    // Retrieve form data
    $newPassword = $_POST['newpassword'];
    $email = $_POST['email'];

    // Validate the new password (e.g., length, complexity)

    // Update the password in the database
    $updateQuery = "UPDATE employees SET pass_word = '$newPassword', verify_token = NULL, token_created_at = NULL WHERE email = '$email'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if($updateResult) {
        // Password updated successfully
        $response['success'] = true;
        $response['message'] = "Password changed successfully!";
    } else {
        // Error updating password
        $response['success'] = false;
        $response['message'] = "Error: " . mysqli_error($conn);
    }
} else {
    // Invalid request
    $response['success'] = false;
    $response['message'] = "Invalid request";
}

// Send JSON response
echo json_encode($response);
?>

