<!doctype <!DOCTYPE html>

<?php
function customError($errno, $errstr, $err_f, $err_l) {
  echo "<b>Error:</b> [$errno] $errstr $err_f $err_l";
}

//set error handler
set_error_handler("customError");
// //////////////////////////////*******To be removed when login.php is complete*******///////////////////////////////////////////////////////
/* These are our valid username and passwords */

function sql_query ($user, $passwd) {
	if ($user != '' && $passwd != '') {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if( $conn === false ) {
			return false;
		}
		$sql = "SELECT username, passwd FROM instructors WHERE username = ? AND passwd = ?";
		$stmt = sqlsrv_query($conn, $sql, array($user, $passwd));
		if( $stmt === false ) {
			return false;
		}
		if( sqlsrv_fetch( $stmt ) === false) {
			return false;
		}
		$sql_username = sqlsrv_get_field($stmt, 0);
		$sql_passwd = sqlsrv_get_field($stmt, 1);
		if ($user == $sql_username && $passwd == $sql_passwd) {
			return true;
		}
		return false;
	}
}

session_start();
if (isset($_SESSION['username'])) {
	header('location: dashboard.php');
	exit();
}

else if (isset($_COOKIE['username']) && isset($_COOKIE['passwd'])) {// for authentication
	$user = $_COOKIE['username']; $passwd = $_COOKIE['passwd'];
	if (sql_query($user, $passwd)) {
		$_SESSION['username'] = $user;
		$_SESSION['passwd'] = $passwd;
		header('Location: dashboard.php');
		exit();
	}
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['username']) && isset($_POST['passwd'])) {
		$user = $_POST['username']; $passwd = md5($_POST['passwd']);
		if (sql_query($user, $passwd)) {    
			
			if (isset($_POST['remember_me'])) {
				/* Set cookie to last 1 year */
				setcookie('username', $user, time()+86400*365);
				setcookie('passwd', $passwd, time()+ 86400*365);
			}
			$_SESSION['username'] = $user;
			header('Location: dashboard.php');
			exit();
			
		} else {
			echo '<script>alert("Invalid username or password")</script>';
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
			<input type="text" placeholder="CSE LDAP" name="username" id="username" size="15" autocomplete='username' pattern="[A-Za-z_0-9]*" oninput='check_username(this)' required><br>
			<span>Password</span>
			<input type="password" placeholder="Password" name="passwd" id="passwd" autocomplete='current-password' size="15" oninput='check_password(this)' required><br>
			<label class='container'><input type="checkbox" checked="checked" name="remember_me"><span class='checkmark'></span><span style='display: inline-block;'>Remember Me</span></label>
			<input type="submit" value="Login" style="margin: 0; padding: 10px; position: relative; background-color: #ffa500; border: 1px solid red; width: 100%; cursor: pointer;">
		</form>
	</div>
</body>
</html>
