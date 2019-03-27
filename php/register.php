<?php
 // Check if form was submitted:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {
	// Build POST request:
	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$recaptcha_secret = '6LfgopcUAAAAAOoANlGdC8rXZ-LXaYLdEOA8h92u';
	$recaptcha_response = $_POST['recaptcha_response'];
	// Make and decode POST request:
	$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
	$recaptcha = json_decode($recaptcha);
	// Take action based on the score returned:
	if ($recaptcha->score >= 0.5) {
		// Change this to your connection info.
		$DATABASE_HOST = 'localhost';
		$DATABASE_USER = 'root';
		$DATABASE_PASS = 'usbw';
		$DATABASE_NAME = 'securitywebapp';
		// Try and connect using the info above.
		$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
		if (mysqli_connect_errno()) {
			// If there is an error with the connection, stop the script and display the error.
			die('Failed to connect to MySQL: ' . mysqli_connect_error());
		}
		// Now we check if the data was submitted, isset() function will check if the data exists.
		if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
			// Could not get the data that should have been sent.
			die('Please complete the registration form!');
		}
		// Make sure the submitted registration values are not empty.
		if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
			// One or more values are empty.
			die('Please complete the registration form');
		}
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$email = $_POST['email'];
		}
		if (filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS)) {
			$username = $_POST['username'];
		}
		if (strlen($_POST['password'] > 20 || strlen($_POST['password']) < 8)) {
			die("Please fill in a password that is longer than 8 characters and shorter than 20");
		}
		$containsLowerCase = preg_match('@[a-z]@', $_POST['password']);
		$containsUpperCase = preg_match('@[A-Z]@', $_POST['password']);
		$containsNumber = preg_match('@[0-9]@', $_POST['password']);
		$containsSymbol = preg_match('@[^/w]@', $_POST['password']);
		if (!$containsLowerCase || !$containsUpperCase || !$containsNumber || !$containsSymbol) {
			die("Please fill in a password that is longer than 8 characters, shorter than 20. 
			Contains atleast one of all of the following: lowercase, an uppercase, a number and a symbol");
		}

		// We need to check if the account with that username exists.
		if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
			// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
			$stmt->bind_param('s', $_POST['username']);
			$stmt->execute();
			$stmt->store_result();
			// Store the result so we can check if the account exists in the database.
			if ($stmt->num_rows > 0) {
				// Username already exists
				echo 'Username exists, please choose another!';
			} else {
				// Username doesnt exists, insert new account
				if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
					// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
					$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
					if (!$username == null || !$email == null) {
						$stmt->bind_param('sss', $username, $password, $email);
						$stmt->execute();
						echo 'You have successfully registered, you can now login!';
						echo '<br>';
						echo 'Your recaptcha score is: ', $recaptcha->score, ' the higher the score, the less likely you are to be a bot (0.0 to 1.0)';
						echo '<form action="/index.html"><button>Go back to the login page</button</form>';
					} else {
						echo 'You are using charachters that are not allowed in the username field or your email address is invalid';
					}
				} else {
					// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
					echo 'Could not prepare statement!';
				}
			}
			$stmt->close();
		} else {
			// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
			echo 'Could not prepare statement!';
		}
		$con->close();
	} else {
		echo ("You shall not pass");
	}
}
