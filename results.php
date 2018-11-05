<?php
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

session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['userID'])) {
	header('Location: login.php');
	exit();
}

if(isset($_POST['update_sidebar'])) {
	$client_time = $_POST['update_sidebar'];
	$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
	if ($conn === false) {
		echo json_encode(array('message' => "Server not Reachable"));
		exit();
	}
	$sql = "SELECT session_name, start_time, duration FROM sessions_ INNER JOIN instructors ON sessions_.userID = instructors.userID WHERE sessions_.userID = ?";
	$stmt = sqlsrv_query($conn, $sql, array($_SESSION['userID']));
	if ($stmt === false) {
		echo json_encode(array('message' => "Server Error"));
		exit();
	}
	$answer = array();
	while( $row = sqlsrv_fetch_array( $stmt) ) {
    	array_push($answer, array('name' => $row['session_name'], 'start' => $row['start_time'], 'duration' => $row['duration'], 'now' => time()));
	}
	echo json_encode($answer);
	exit();
}
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Voodle</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- using jquery for easy scripting -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- using font awesome for icons -->
	<link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Josefin+Slab:400,700|Quicksand:400,700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" media="screen" href="results.css" />
	<script src='./assets/chart_module/chart.js/dist/Chart.js'></script>
	<script src="results.js"></script>
</head>

<body>
	<input id='sidebar-toggle-checkbox' type='checkbox' style='display: none'>
	<div id="topnav" class='clearfix'>
		<label for='sidebar-toggle-checkbox'><h3 id='sidebar-toggle' style='color: white; float: left; margin: 0 25px; cursor: pointer; line-height: 48px'><i class="fas fa-bars"></i></h3></label>
		<div id='logo' style="background-color: rgba(0,0,0,0); color: white; float: left; padding: 0 16px; font-family: 'Cinzel Decorative'; user-select: none;">
			Voodle
		</div>
		<div id='_' style='float: right; color: white; position: relative;'>
			<a id='logout' href="<?php echo $_SERVER['PHP_SELF'] .'?logout=true'?>" style='float: left; color: white; text-decoration: none; text-align: center; padding: 0 4px;'><i style='padding: 0 16px' class="fas fa-sign-out-alt"></i>Logout</a>
		</div>
	</div>
	<div style='display: flex; position: absolute; width: 100%; flex-direction: row; height: calc(100% - 48px); overflow: hidden'>
		<div id='right-side-bar'>
			<span style='color: white; text-align: center; display: block; cursor: default; padding: 12px 4px;'><i style='padding: 0 16px' class='fas fa-user'></i><?php echo $_SESSION['username']?></span>
			<ul id='sessions_list' class='expand-dropdown'><span><i class="fas fa-angle-down"></i>Your Sessions</span>
				<!-- <li>Voodle<i class="fas fa-feather-alt clearfix" style='float: right;'></i></li>
				<li>Temp<i class="fas fa-feather-alt clearfix" style='float: right;'></i></li>
				<li>Android</li>
				<li>Kaneki Ken</li> -->
			</ul>
		</div>
		<!-- For updating sidebar every 2 seconds -->
		<script>
		async function update_sidebar () {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var response = JSON.parse(this.responseText);
					if ('message' in response)
						alert(response['message']);
					else {
						if (response.length === 0) {
							$('#sessions_list').append('<li><i class="fas fa-plus" style="margin: 0 8px 0 0"></i>Create a session</li>');
						}
						else for(var i = 0; i < response.length; i++) {
							$('#sessions_list').append('<li>'+response[i]['name']+'</li>');
						}
					}
				}
			};
			xhttp.open("POST", "<?php echo $_SERVER['PHP_SELF']?>" , true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("update_sidebar");
		}
		update_sidebar();
		</script>
		<div id='site'>
		</div>
	</div>
</body>
</html>