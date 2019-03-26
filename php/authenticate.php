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
if (!isset($_POST['username'], $_POST['password'])) {
	// Could not get the data that should have been sent.
	die('Username and/or password does not exist!');
}
// Prepare the logging SQL
$logstmt = $con->prepare('INSERT INTO logs (user_id, ip_address, description) VALUES (?,?,?)');
// Prepare our SQL 
if ($stmt = $con->prepare('SELECT id, password, secret FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		$stmt->bind_result($id, $password, $secret);
		$stmt->fetch();
		// Account exists, now we verify the password.
		if (password_verify($_POST['password'], $password)) {
			if(!$secret == NULL){
				// First step of Authenication is a succes
				$_SESSION['loggedin'] = false;
				$_SESSION['name'] = $_POST['username'];
				$_SESSION['id'] = $id;
				header('Location: /secondstep.html');
			}else{
				// Verification success! User has loggedin!
				$_SESSION['loggedin'] = true;
				$_SESSION['name']= $_POST['username'];
				$_SESSION['id'] = $id;
				$description = "user succesfully logged in";
				$logstmt->bind_param("iss", $_SESSION['id'], $_SERVER['REMOTE_ADDR'], $description);
				$logstmt->execute();
				header('Location: /php/home.php');
			}
		} else {
			$description = "user by user_id " . $id . " filled in incorrect password";
			echo 'Incorrect username and/or password!';
			$logstmt->bind_param("iss", $id, $_SERVER['REMOTE_ADDR'], $description);
			$logstmt->execute();
		}
	} else {
		$description = "Someone with ip " . $_SERVER['REMOTE_ADDR'] . " tried to login";
		echo 'Incorrect username and/or password!';
		$logstmt->bind_param("iss", 0, $_SERVER[ 'REMOTE_ADDR'], $description);
		$logstmt->excute();
	}
	$logstmt->close();
	$stmt->close();
} else {
	echo 'Could not prepare statement!';
}
 