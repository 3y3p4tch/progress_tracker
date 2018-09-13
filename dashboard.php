<!doctype html>

<?php

if(isset($_COOKIE['ldap']) && isset($_COOKIE['passwd'])) {
	if ($_COOKIE['ldap'] == '170050059' && $_COOKIE['passwd'] == md5('pass')) {
		echo "<script>var username = ";
		echo $_COOKIE['ldap'];
		echo "</script>";
	}
	else {
		header('Location: login.php');
		exit();
	}
}

else {
	header('Location: login.php');
	exit();
}
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>Voodle</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- using jquery for easy scripting -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- using font awesome for icons -->
	<link rel="stylesheet" href="./assets/font-awesome.min.css">
	<link rel='stylesheet' href='./assets/fonts.css'>
	<link rel="stylesheet" type="text/css" media="screen" href="dashboard.css" />
	<script src="dashboard.js"></script>
</head>
<body>
	<div id="topnav">
		<div class='dropdown'>
			<div style='display: block;'>
				<span id="_"></span>
				<script>$('#_').html('Welcome '+username);</script>
				<i class='fa fa-caret-down'></i>
			</div>
			<div class='dropdown-content'>
				<a href='#profile'>Profile</a>
				<a href='#logout'>Logout</a>
			</div>
		</div>
		<a href="#news">News</a>
		<a href="#contact">Contact</a>
		<a href="#about">About</a>
	</div>
</body>
</html>
