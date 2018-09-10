<!doctype <!DOCTYPE html>

<?php

if(isset($_COOKIE['ldap']) && isset($_COOKIE['passwd'])) {
	if ($_COOKIE['ldap'] == '170050059' && $_COOKIE['passwd'] == md5('pass')) {
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="main.css" />
	<script src="main.js"></script>
</head>
<body>
</body>
</html>
