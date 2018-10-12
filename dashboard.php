<!doctype html>

<?php
ini_set('session.cookie_domain', 'localhost');
// for logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
	setcookie('username','', time() - 3600);
	setcookie('passwd','', time() - 3600);
	session_start();
	session_unset();
	session_destroy();
	header('location: login.php');
	exit();
}

// // for authentication
// $conn = sqlsrv_connect('LAPTOP-DJ46JC9S');
// if( $conn === false ) {
//      die( print_r( sqlsrv_errors(), true));
// }
// $sql = "SHOW DATABASES";
// echo (sqlsrv_query($conn, $sql));
session_start();
if (!isset($_SESSION['username'])) {
	header('Location: login.php');
	exit();
}

if(isset($_POST['details'])) {
	echo $_POST['details'];
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
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Josefin+Slab:400,700|Quicksand:400,700" rel="stylesheet">
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
				<script>$('#_').html('Welcome '+<?php echo $_SESSION['username']?>);</script>
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
			<textarea name='assignment_details' id='assignment_details' style='display: none'></textarea>
		</form>
		<div style='box-sizing: border-box; box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 1px 1px rgba(0,0,0,0.16); border-radius: 2px;'>
			<ul id='editor_topbar' class='clearfix'>
				<li class='bold'><i class='fa fa-bold'></i></li>
				<li class='italic'><i class='fa fa-italic'></i></li>
				<li class='underline'><i class='fa fa-underline'></i></li>
				<li class='strikethrough'><i class='fa fa-strikethrough'></i></li>
				<li class='subscript'><i class='fa fa-subscript'></i></li>
				<li class='superscript'><i class='fa fa-superscript'></i></li>
				<li class='insertUnorderedList'><i class='fa fa-list-ul'></i></li>
				<li class='insertOrderedList'><i class='fa fa-list-ol'></i></li>
				<li class='indent'><i class='fa fa-indent'></i></li>
				<li class='outdent'><i class='fa fa-outdent'></i></li>
				<li class='justifyLeft'><i class='fa fa-align-left'></i></li>
				<li class='justifyCenter'><i class='fa fa-align-center'></i></li>
				<li class='justifyFull'><i class='fa fa-align-justify'></i></li>
				<li class='justifyRight'><i class='fa fa-align-right'></i></li>
				<span id='preview_latex'>Preview LaTeX</span>
			</ul>
			<iframe id='iframe' width='100%'></iframe>
		</div>
		<span id='submit' style="background: blanchedalmond; padding: 10px 20px; box-sizing: border-box; margin: 10px 0; display: inline-block; cursor: pointer; user-select: none; border-radius: 2px;">Create Assignment<i class="fa fa-chevron-right" style='margin-left: 8px'></i></span>
		<script>document.getElementById('submit').addEventListener('click', function() {
				$.ajax({
					type: 'POST',
					url: <?php echo "'".$_SERVER['PHP_SELF']."'";?>,
					data: {'user':<?php echo $_SESSION['username']?>,'details': $('#iframe').html()},
					success: function(msg) {alert('Successfully submitted details.')}
				});
			})</script>
		</div>
 	</div>
</body>
</html>
