<?php
session_start();
require 'C:\Users\Stijn\Desktop\SecurityWebApp\vendor\autoload.php';
$ga = new PHPGangsta_GoogleAuthenticator();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'usbw';
$DB_NAME = 'securitywebapp';
// Try and connect using the info above.
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    die('Failed to connect to MySQL: ' . mysqli_connect_error());
}
if($stmt = $con->prepare('SELECT secret FROM accounts WHERE id = ?')){
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $stmt->bind_result($secret);
        $stmt->fetch();
    }
}
$logstmt = $con->prepare('INSERT INTO logs (user_id, ip_address, description) VALUES (?,?,?)');
$checkResult = $ga->verifyCode($secret, $_POST['oneCode'], 2); // 2 = 2*30sec clock tolerance
if ($checkResult) {
    $_SESSION['loggedin'] = true;
    $description = "user logged in succesfully with 2FA";
    $logstmt->bind_param("iss", $_SESSION['id'], $_SERVER['REMOTE_ADDR'], $description);
    header('Location: /php/home.php');
} else {
    $description = "user could not produce the correct authenicator code";
    $logstmt->bind_param("iss", $_SESSION['id'], $_SERVER['REMOTE_ADDR'], $description);
    header('Location: /php/logout.php');
}

 