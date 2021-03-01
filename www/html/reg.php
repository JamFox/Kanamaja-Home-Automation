<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The page supports both dark and light color schemes,
         and the page author prefers / default is dark -->
		 <meta name="color-scheme" content="light dark">
	<!-- Bootstrap -->
	<link href="css/bootstrap-nightshade.min.css" rel="stylesheet">
	<!-- Custom styles -->
	<link href="css/signin.css" rel="stylesheet">
	<!-- Page specific -->
    <title>KASS Hasher</title>
    <link rel="icon" href="images/rubergkawai.png" type="image/png">
</head>

<body class="text-center">
	<main class="form-signin">
		<img class="mb-4" src="images/padlock.png" alt="" width="72" height="72">
		<h1 class="h3 mb-3 fw-normal">Kanamaja Advanced Surveillance System Password Hasher</h1>
		<form method="post">
			<label for="password" class="visually-hidden">Password</label>
			<input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
			<button class="mt-2 w-100 btn btn-lg btn-primary" type="submit">Hash it!</button>
			<a href="index.nginx-debian.html" class="mt-1 w-100 btn btn-lg btn-outline-primary" role="button">Back to login</a>
		</form>
		<?php
		if (isset($_POST['password'])) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			echo "Your hashed password: $password";
		}
		?>
		<!-- Darkmode button -->
		<div class="w-50 btn-group ms-auto" role="group">
			<a id="darkmode-button" class="mt-2 btn btn-outline-secondary">Dark mode<i class="d-none d-light-inline" title="Toggle dark mode"></i></a>
		</div>
		<p class="mt-5 mb-3 text-muted">Sponsored by RubergVPN, 2021</p>
	</main>
	<!-- Bootstrap Bundle with Popper -->
	<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
	<!-- Darkmode scripts -->
	<script src="js/darkmode.min.js"></script>
	<script>
		document.querySelector("#darkmode-button").onclick = function (e) {
			darkmode.toggleDarkMode();
		}
		document.querySelector("#darkmode-off-button").onclick = function (e) {
			darkmode.setDarkMode(false);  // save=true is default
		}
		document.querySelector("#darkmode-on-button").onclick = function (e) {
			darkmode.setDarkMode(true);  // save=true is default
		}
		document.querySelector("#darkmode-forget").onclick = function (e) {
			darkmode.resetDarkMode();
		}
	</script>
</body>

</html>