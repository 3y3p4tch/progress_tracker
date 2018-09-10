<!doctype <!DOCTYPE html>

<?php
function customError($errno, $errstr, $err_f, $err_l) {
  echo "<b>Error:</b> [$errno] $errstr $err_f $err_l";
}

//set error handler
set_error_handler("customError");

/* These are our valid ldap and passwords */
$user = '170050059';
$pass = 'pass';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['ldap']) && isset($_POST['passwd'])) {
		if (($_POST['ldap'] == $user) && ($_POST['passwd'] == $pass)) {    
			
			if (isset($_POST['remember_me'])) {
				/* Set cookie to last 1 year */
				setcookie('ldap', $_POST['ldap'], time()+86400*365, '/');
				setcookie('passwd', md5($_POST['passwd']), time()+ 86400*365, '/');
			
			} else {
				/* Cookie expires when browser closes */
				setcookie('ldap', $_POST['ldap'], false, '/');
				setcookie('passwd', md5($_POST['passwd']), false, '/');
			}
			header('Location: index.php');
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
	<link rel="stylesheet" type="text/css" media="screen" href="main.css" />
	<script src="main.js"></script>
	<script src="assets/particles.min.js"></script>
</head>
<body>
		<header id="topnav" class="topnav">
			<a id="name" href="index.php">Voodle</a>
		</header>
	<!-- Login Page -->
	<div id="init_info">
		<!-- Info Content like what the site is about -->
	</div>

	<div class="login">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<span>Username</span>
			<input type="text" placeholder="CSE LDAP" name="ldap" id="ldap" size="15" pattern="[A-Za-z_0-9]*" required><br>
			<span>Password</span>
			<input type="password" placeholder="Password" name="passwd" id="passwd" size="15" required><br>
			<div style="padding: 0; text-align:center;"><input type="checkbox" checked="checked" name="remember_me"><span>Remember Me</span></div>
			<input type="submit" value="Login" style="padding: 10px; position: absolute; background-color: #ffa500; border: 1px solid red; right: 50%; transform: translateX(50%); cursor: pointer;">
		</form>
	</div>
</body>
</html>
