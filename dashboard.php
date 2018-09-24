<!doctype html>

<?php
// for logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
	setcookie('ldap','', time() - 3600);
	setcookie('passwd','', time() - 3600);
	session_start();
	session_unset();
	session_destroy();
	$location = urlencode($_SERVER['PHP_SELF']);
	header('location: login.php?location='.$location);
	exit();
}

// for authentication
$conn = sqlsrv_connect('LAPTOP-DJ46JC9S');
if( $conn === false ) {
     die( print_r( sqlsrv_errors(), true));
}
$sql = "SHOW DATABASES";
echo (sqlsrv_query($conn, $sql));
if(isset($_COOKIE['ldap']) && isset($_COOKIE['passwd'])) {
	if ($_COOKIE['ldap'] == '170050059' && $_COOKIE['passwd'] == md5('pass')) {
		echo "<script>var username = ";
		echo $_COOKIE['ldap'];
		echo "</script>";
		session_start();
		$_SESSION['username'] = $_COOKIE['ldap'];
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
	<!-- MathJax for displaying latex -->
	<script type="text/javascript" async
	src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML">
	</script>
	<!-- using jquery for easy scripting -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- using font awesome for icons -->
	<link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
	<link rel='stylesheet' href='./assets/fonts.css'>
	<link rel="stylesheet" type="text/css" media="screen" href="dashboard.css" />
	<script src="dashboard.js"></script>
</head>
<body>
	<div id="topnav" class='clearfix'>
		<div style="background-color: rgba(0,0,0,0); color: white; float: left; padding: 12.5px 16px; font-family: 'Cinzel Decorative'; user-select: none;">
			Voodle
		</div>
		<div class='dropdown'>
			<div style='display: block;'>
				<span id="_"></span>
				<script>$('#_').html('Welcome '+username);</script>
				<i class='fa fa-caret-down'></i>
			</div>
			<div class='dropdown-content'>
				<a href='#profile'>Profile</a>
				<a href="<?php echo $_SERVER['PHP_SELF'] .'?logout=true'?>">Logout</a>
			</div>
		</div>
		<a href="#news">News</a>
		<a href="#contact">Contact</a>
		<a href="#about">About</a>
	</div>
	<div class='right-side-bar'>
		<p>Hi there, my name is Saurav Yadav. This right here is the left sidebar.</p>
	</div>
	<div class='site'>
		<p>This right here is the main site.</p>
		<h2>Assignment:</h2>
		<div style='width: 100%'>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
		<textarea id='details' name='description' placeholder='Write your assignment details here...' required=true rows=5 style='width: 100%; box-sizing: border-box; overflow: hidden'></textarea>
		<div id='preview_latex' style='white-space: pre-wrap; margin: 10px 0; overflow-wrap: break-word'></div>
		<input type='submit' value='Submit'>
		</form>
		</div>
 	</div>
</body>
</html>
