<?php
session_start();
// Change this to your connection info
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'ruberg';
$DATABASE_PASS = 'ruberg';
$DATABASE_NAME = 'auth';
// Try connection using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// isset() will check if the data we wanted from user exists
if (!isset($_POST['username'], $_POST['password'])) {
	exit('Please fill both the username and password fields!');
}
// Prepare our SQL to prevent SQL injection
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc)
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
		$stmt->bind_result($id, $password);
		$stmt->fetch();
		// Account exists, now we verify the password.
		// IMPORTANT: use password_hash when storing passw database entries
		if (password_verify($_POST['password'], $password)) {
			// Verification success! User has logged-in!
			// Create sessions, so we know the user is logged in, remembers the data on server
			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $_POST['username'];
			$_SESSION['id'] = $id;
			header('Location: dashboard.php');
		} else {
			header('Location: 401.html');
		}
	} else {
		header('Location: 401.html');
	}
	$stmt->close();
}
