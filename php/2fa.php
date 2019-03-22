<?php
require 'C:\Users\Stijn\Desktop\SecurityWebApp\vendor\autoload.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
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
?>
<html>

<head>
    <meta charset="utf-8">
    <title>2FA</title>
    <link href="/css/home.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>

<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1><a href="home.php">Security Webapp</a></h1>
            <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>
    <div class="content">
        <p>Use this in Google Authenticator<br>
            <?php 
            echo "The secret is: " . $secret . "<br>";
            $qrCodeUrl = $ga->getQRCodeGoogleUrl('Blog', $secret);
            echo "<img src=" . $qrCodeUrl . "></img>  ";
            $stmt = $con->prepare('UPDATE accounts SET secret = ? WHERE id = ?');
            $stmt->bind_param('ss', $secret, $_SESSION['id']);
            $stmt->execute();
            $stmt->close();
            ?>
        </p>
    </div>
</body>

</html> 