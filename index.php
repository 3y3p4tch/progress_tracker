<!doctype <!DOCTYPE html>
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
	
			
			<!-- Resize navbar -->
			<script>
				function expand_nav() {
					var x = document.getElementById("topnav");
						if (x.className === "topnav") {
							x.className += " responsive";
						}
						else {
							x.className = "topnav";
						}
				}
				function myFunction(x) {
					x.classList.toggle("change");
				}
			</script>
				<!-- Resize navbar -->
			
			<a href="#" class="icon" onclick="expand_nav()">
				<div class="container" onclick="myFunction(this)">
					<div class="bar1"></div>
					<div class="bar2"></div>
					<div class="bar3"></div>
				</div>
			</a>
		</header>
	<!-- Login Page -->
	<div id="init_info">
		<!-- Info Content like what the site is about -->
	</div>

	<div class="login">
		<form action="index.php" method="post">
			<span>Username</span>
			<input type="text" placeholder="CSE LDAP" id="ldap" size="15" required><br>
			<span>Password</span>
			<input type="password" placeholder="Password" id="passwd" size="15" required><br>
			<div style="padding: 0; text-align:center;"><input type="checkbox" value="1" name="Remember Me"><span>Remember Me</span></div>
			<input type="submit" value="Login" style="padding: 10px; position: absolute; background-color: #ffa500; border: 1px solid red; right: 50%; transform: translateX(50%); cursor: pointer;">
		</form>
	</div>
</body>
</html>