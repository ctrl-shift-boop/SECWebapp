<?php
session_start();
// Change this to your connection info.
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
// Now we check if the data was submitted, isset will check if the data exists.
if (empty($_POST['password'])) {
    // Could not get the data that should have been sent.
    die('fill in a password');
}
if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $_POST['password'])) {
    die("Please fill in a password that is longer than 8 characters, shorter than 20. Contains atleast one of all of the following: lowercase, an uppercase, a number and a symbol");
}
// Prepare our SQL 
if ($stmt = $con->prepare('UPDATE accounts SET password = ? WHERE id = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('ss', $password, $_SESSION['id']);
    $stmt->execute();
    //header('Location: /php/profile.php');
}
