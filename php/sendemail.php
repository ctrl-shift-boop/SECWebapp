<?php
require 'C:\Users\Stijn\Desktop\SecurityWebApp\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['submit_email']) && $_POST['email']) {
    $DB_HOST = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = 'usbw';
    $DB_NAME = 'securitywebapp';
    $email = $_POST['email'];
    // Try and connect using the info above.
    $con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $stmt = $con->prepare('SELECT email, password FROM accounts WHERE email = ?');
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->bind_result($email, $password);
    $stmt->fetch();
    $stmt->close();
    $link = "<a href='localhost/php/passwordrecovery.php?reset=" . $password . "'>Click To Reset password (PLEASE RESET IT IMMEDIATLY)</a>";
    $mail = new PHPMailer();
    $mail->CharSet =  "utf-8";
    $mail->IsSMTP();
    // enable SMTP authentication
    $mail->SMTPAuth = true;
    // GMAIL username
    $mail->Username = "ictsescrtv17@gmail.com";
    // GMAIL password
    $mail->Password = "yYqPmDTcd2ADKSQGJDV6";
    $mail->SMTPSecure = "ssl";
    // sets GMAIL as the SMTP server
    $mail->Host = "smtp.gmail.com";
    // set the SMTP port for the GMAIL server
    $mail->Port = "465";
    $mail->From = 'ictsescrtv17@gmail.com';
    $mail->FromName = 'Jan Jansen';
    $mail->AddAddress($_POST['email']);
    $mail->Subject  =  'Reset Password';
    $mail->IsHTML(true);
    $mail->Body    = 'Click On This Link to Reset Password ' . $link . '';
    if ($mail->Send()) {
        echo "Check Your Email and Click on the link sent to your email";
    } else {
        echo "Mail Error - >" . $mail->ErrorInfo;
    }
}
$con->close();

 