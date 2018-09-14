<!doctype <!DOCTYPE html>

<?php
function customError($errno, $errstr, $err_f, $err_l) {
  echo "<b>Error:</b> [$errno] $errstr $err_f $err_l";
}

//set error handler
set_error_handler("customError");
// //////////////////////////////*******To be removed when login.php is complete*******///////////////////////////////////////////////////////
/* These are our valid ldap and passwords */
$user = '170050059';
$pass = 'pass';

if (isset($_COOKIE['ldap']) && isset($_COOKIE['passwd'])) {
	if (($_COOKIE['ldap'] == $user) && ($_COOKIE['passwd'] == md5($pass))) {
		header('location: dashboard.php');
		exit();
	}
}

session_start();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if (isset($_GET['location']) && $_GET['location'] != '') {
	$location = urldecode($_GET['location']);
	}
	else {
		$location = 'dashboard.php';
	}
	$_SESSION['location'] = $location;
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['ldap']) && isset($_POST['passwd'])) {
		if (($_POST['ldap'] == $user) && ($_POST['passwd'] == $pass)) {    
			
			if (isset($_POST['remember_me'])) {
				/* Set cookie to last 1 year */
				setcookie('ldap', $_POST['ldap'], time()+86400*365);
				setcookie('passwd', md5($_POST['passwd']), time()+ 86400*365);
			
			} else {
				/* Cookie expires when browser closes */
				setcookie('ldap', $_POST['ldap'], false);
				setcookie('passwd', md5($_POST['passwd']), false);
			}
			header('Location: '.$_SESSION['location']);
			unset($_SESSION['location']);
			exit();
			
		} else {
			echo 'Username/Password Invalid';
		}
	}
}
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>Voodle</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="login.css" />
	<script src="login.js"></script>
	<script src="assets/particles.min.js"></script>
	<link rel='stylesheet' href='./assets/fonts.css'>
</head>
<body>
	<!-- Login Page -->
	<div id="init_info">
		<!-- Info Content like what the site is about -->
	</div>

	<div id='login'>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<span>Username</span>
			<input type="text" placeholder="CSE LDAP" name="ldap" id="ldap" size="15" pattern="[A-Za-z_0-9]*" required><br>
			<span>Password</span>
			<input type="password" placeholder="Password" name="passwd" id="passwd" size="15" required><br>
			<label class='container'><input type="checkbox" checked="checked" name="remember_me"><span class='checkmark'></span><span style='display: inline-block;'>Remember Me</span></label>
			<input type="submit" value="Login" style="margin: 0; padding: 10px; position: relative; background-color: #ffa500; border: 1px solid red; width: 100%; cursor: pointer;">
		</form>
	</div>
</body>
</html>
